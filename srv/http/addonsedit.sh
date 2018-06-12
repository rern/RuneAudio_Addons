#!/bin/bash

# usage:
#     pre-defined variables:
#         alias=name                      # already in install.sh / uninstall_alias.sh
#         file=/path/file                 # before all commands of each file
#         string=( cat <<'EOF'            # before each insert and append
#         1st line
#         code inside this heredoc need no escape
#         last line
#         EOF
#         )
#    command:
#         comment 'regex' ['regex2']      # /*alias line(s) alias*/
#         commentP 'regex' ['regex2']     # <?php /*alias line(s) alias*/ ?>
#             # /* existing comment */ can be between 'regex' 'regex2'
#         insert 'regex'                  # //alias0
#                                         # string
#                                         # //alias1
#         insertP 'regex'                 # <?php //alias0 ?>
#                                         # string
#                                         # <?php //alias1 ?>
#         append 'regex'                  # same as insert
#         appendP 'regex'                 # same as insertP
#             # 'regex' pattern must be single quoted and escaped properly
#             # sed default delimiter = |
#             # insert/append with 'regex' itself in $string
#                   must be after comment to the same 'regex' (avoid commented)
#                   must be combined with insert/append to the same 'regex' (avoid redundance)
#         restorefile file [file2 ...]    # remove all insert / append / comment

comment() {
	if [[ $1 == -p ]]; then
		front='<?php /*'$alias
		back=$alias'*/ ?>'
		shift
	else
		front='/*'$alias
		back=$alias'*/'
	fi
	
	if (( $# == 1 )); then
		sed -i "\|$1| { s|^|$front|; s|$|$back| }" "$file"
	else
		sed -i "\|$1|, \|$2| s|\*/|\*${alias}/|" "$file"
		sed -i -e "\|$1| s|^|$front|" -e "\|$2| s|$|$back|" "$file"
	fi
}

insert() {
	if [[ $1 == -p ]]; then
		upper='<?php //'$alias'0 ?>\n'
		lower='n<?php //'$alias'1 ?>'
		shift
	else
		upper='//'$alias'0\n'
		lower='n//'$alias'1'
	fi
	
	# escape \ and close eol with \ before passing to sed
	string=$( cat <<EOF
$( echo "$string" | sed 's/\\/\\\\/g; s/$/\\/' )
EOF
)
	
	if [[ $1 == -a ]]; then
		shift
		sed -i "\|$1| a$upper$string$lower" "$file"
	else
		sed -i "\|$1| i$upper$string$lower" "$file"
	fi
}

commentP() {
	comment -p "$@"
}
insertP() {
	insert -p "$1"
}
append() {
	insert -a "$1"
}
appendP() {
	insert -p -a "$1"
}

restorefile() {
	for file in "$@"; do
		sed -i -e "s/^<?php \/\*$alias\|$alias\*\/ ?>$\|^\/\*$alias\|$alias\*\/$//g
		" -e "/${alias}0 ?>$/, /${alias}1 ?>$/ d
		" -e "/${alias}0$/, /${alias}1$/ d
		" -e "s|\*${alias}/|\*/|
		" $file
	done
}
