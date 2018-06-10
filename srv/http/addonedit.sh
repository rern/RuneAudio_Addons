#!/bin/bash

# usage:   comment 'pattern' ['pattern2'] file
#          commentphp (use '<?php' comment)
#
#          insert 'pattern' 'string' file
#          insertphp (use '<?php' comment)
#          append 'pattern' 'string' file
#          appendphp (use '<?php' comment)
#
# pattern  sed search regex pattern (escaped + inside single quoted)
# pattern2 bottom line of range (comment only)
# string   string to add: insert - above, append - below

# string - singleline: DO escape \ and "

# string - heredoc: all characters and symbols can be used
#
# string=$( cat <<'EOF'
# aaa\n\
# bbb
# EOF
# )
#
#    DO end every lines with \n\
#    DO escape \ backslash
#    DON'T put spaces or any characters in closing heredoc tag
#    DON'T end last line with \ backslash

exsample=$( cat <<'EOF'
@#$&*()'"%-+=/;:!?€£¥_^[]{}§|~\\<>\n\
DO end every lines with \\n\\\n\
DO escape \\ backslash\n\
DON'T put spaces or any characters in closing heredoc tag\n\
DON'T end last line with \ backslash
EOF
)

comment() {
	if [[ $1 == -p ]]; then
		upper='<?php if(0){//enha ?>'
		lower='<?php }//enha ?>'
		shift
	else
		upper='if(0){//alias'
		lower='}//alias'
	fi
	if (( $# == 2 )); then
		sed -i -e "/$1/ i$upper" -e "/$1/ a$lower" "$2"
	else
		sed -i -e "/$1/ i$upper" -e "/$2/ a$lower" "$3"
	fi
}

add() {
	if [[ $1 == -p ]]; then
		upper='<?php if(0){//enha ?>\n\'
		lower='\n<?php }//enha ?>'
		shift
	else
		upper='if(0){//alias\n\'
		lower='\n}//alias'
	fi
	
	if [[ $1 == -i ]]; then
		shift
		sed -i "/$1/ i$upper$2$lower" "$3"
	else
		sed -i "/$1/ a$upper$2$lower" "$3"
	fi
}

commentphp() {
	comment -p "$@"
}
insert() {
	add -i "$@"
}
insertphp() {
	add -p -i "$@"
}
append() {
	add "$@"
}
appendphp() {
	add -p "$@"
}

restorefile() { $1 = file
	sed -i -e "/${alias}0 ?>\s*$/, /${alias}1 ?>\s*$/ d
	" -e "/${alias} ?>\s*$/ d
	" -e "/${alias}0\s*$/, /${alias}1\s*$/ d
	" -e "/${alias}\s*$/ d
	" $1
}
