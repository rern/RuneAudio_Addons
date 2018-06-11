#!/bin/bash

# usage:
#     pre-defined variables:
#         alias=name                      # already in install.sh / uninstall_alias.sh
#
#         file=/path/file                 # before all commands of each file
#         string=( cat <<'EOF'            # before each insert and append
#         DON'T put spaces or any characters in closing EOF
#         only EOF tag at 1st of the ending line with nothing else
#         EOF
#         )
#    command:
#         comment 'regex' ['regex2']      # /* existing */
#         commentP 'regex' ['regex2']     # <?php /* existing */ ?>
#             /* other comment */ CAN'T be inside 'regex' 'regex2'
#         insert 'regex'                  # //alias and //alias enclose $string
#         insertP 'regex'                 # <?php /* and */ ?> lines enclose $string
#         append 'regex'                  # same as insert
#         appendP 'regex'                 # same as insertphp
#             insert/append with 'regex' in $string must be after comment to avoid double
#
#         restorefile file [file2 ...]    # remove all insert / append / comment
#    'regex':
#         search pattern must be single quoted and escaped properly
#         default delimiter = |

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
		sed -i -e "\|$1| { s|^|$front|; s|$|$back| }" "$file"
	else
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
		" $file
	done
}
