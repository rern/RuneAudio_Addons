#!/bin/bash

# usage:
#     pre-defined variables:
#         alias=name                      # already in install.sh / uninstall_alias.sh
#         file=/path/file                 # before all commands of each file
#         string=( cat <<'EOF'            # before each insert and append
#         DO escape \ backslash\n\
#         DO end every lines with \\n\\ \n\
#         DON'T put spaces or any characters in closing heredoc\n\
#         DON'T end last line with \ backslash
#         EOF
#         )
#    command:
#         insert 'regex'                  # //alias lines onclose $string
#         insertphp 'regex'               # <?php /* and */ ?> lines onclose $string
#         append 'regex'                  # same as insert
#         appendphp 'regex'               # same as insertphp
#             comment after insert/append for the same line (avoid $string with 'regex')
#         comment 'regex' ['regex2']      # /* existing */
#         commentphp 'regex' ['regex2']   # <?php /* existing */ ?>
#             /* other comment */ CAN'T be inside 'regex' 'regex2'
#         restorefile $file               # specify each $file
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

add() {
	if [[ $1 == -p ]]; then
		upper='<?php //'$alias'0 ?>\n\'
		lower='\n<?php //'$alias'1 ?>'
		shift
	else
		upper='//'$alias'0\n\'
		lower='\n//'$alias'1'
	fi
	
	if [[ $1 == -i ]]; then
		shift
		sed -i "\|$1| i$upper$string$lower" "$file"
	else
		sed -i "\|$1| a$upper$string$lower" "$file"
	fi
}

commentphp() {
	if (( $# == 1 )); then
		comment -p "$1"
	else
		comment -p "$1" "$2"
	fi
}
insert() {
	add -i "$1"
}
insertphp() {
	add -p -i "$1"
}
append() {
	add "$1"
}
appendphp() {
	add -p "$1"
}

restorefile() {
	sed -i -e "s/^<?php \/\*$alias\|$alias\*\/ ?>$\|^\/\*$alias\|$alias\*\/$//
	" -e "/${alias}0 ?>$/, /${alias}1 ?>$/ d
	" -e "/${alias}0$/, /${alias}1$/ d
	" $1
}
