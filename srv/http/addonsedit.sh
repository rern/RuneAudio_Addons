#!/bin/bash

# insert / append / comment : for /*...*/  <?php /*...*/ ?>  #...

# pre-defined variable:
#     alias=name                                already in install.sh / uninstall_alias.sh
#     file=/path/file                           before all commands of each file
#     string=( cat <<'EOF'                      before each insert and append
#     place code without escapes
#     last line
#     EOF
#     )
# usage:
#     comment [-n N] SEARCH [-n N] [SEARCH2]    /*alias js,php,css alias*/

#     commentH [-n N] SEARCH [-n N] [SEARCH2]   <?php /*alias html,php alias*/ ?>
#     commentP [-n N] SEARCH [-n N] [SEARCH2]

#     commentS [-n N] SEARCH [-n N] [SEARCH2]   #alias ...

#     insert [-n N] SEARCH                      //0alias0
#     append [-n N] SEARCH                      js,php,css
#                                               //1alias1

#     insertH [-n N] SEARCH                     <?php //0alias0 ?>
#     appendH [-n N] SEARCH                     html
#                                               <?php //1alias1 ?>
       
#     insertP [-n N] SEARCH                     <?php //0alias0
#     appendP [-n N] SEARCH                     php
#                                               <?php //1alias1

#     insertS [-n N] SEARCH                     #0alias0
#     appendS [-n N] SEARCH                     ...
#                                               #1alias1

#     restorefile FILE [FILE2 ...]              remove all insert / append / comment

# argument:
#     -n N                                      -n -N    N lines above SEARCH
#                                               -n +N    N lines below SEARCH
#     SEARCH                                    normal sed regex syntax
#                                               |  as delimiter - no need to escape  /
#                                               literal  ^ $ . * [ ] \  need  \  escape
#                                               ' "'" '  escape single quote inside itself
#                                               " \" "   escape double quote inside itself
#                                               or use  .  as placeholder instead of escape

# tips:
#     insert/append with SEARCH itself in $string
#         must be after comment to the same SEARCH (avoid commented after insert)
#         must be combined with insert/append to the same SEARCH (avoid double insert)

comment() {
	if [[ $1 == -h || $1 == -p ]]; then
		front='<?php /*'$alias  # <?php /*alias
		back=$alias'*/ ?>'      # alias*/ ?>
		shift
	elif [[ $1 == -s ]]; then
		front='#'$alias         # #alias
		back=                   # (none)
		shift
	else
		front='/*'$alias        # /*
		back=$alias'*/'         # */
	fi

	if [[ $1 == -n ]]; then lines=$2; shift; shift; else lines=0; fi # line +-

	regex=$( echo "$1" | sed -e 's|"|\\"|g' )                        # escape " in sed "..."

	linenum=$(( $( sed -n "\|$regex|=" $file ) + $lines ))           # get line number

	if (( $# == 1 )); then
		if [[ $front != '#'$alias ]]; then
			sed -i "$linenum { s|\*/|\*$alias/|g; s|^|$front|; s|$|$back| }" "$file"
		else
			sed -i "$linenum s|^|$front|" "$file"
		fi
	else
		if [[ $2 == -n ]]; then lines2=$3; shift; shift; else lines2=0; fi
		regex2=$( echo "$2" | sed -e 's|"|\\"|g' )
		
		linenum2=$(( $( sed -n "\|$regex2|=" $file ) + $lines2 ))
		
		if [[ $front != '#'$alias ]]; then
			sed -i "$linenum, $linenum2 s|\*/|\*$alias/|g" "$file"  # escape existing /* comment */
			sed -i -e "$linenum s|^|$front|" -e "$linenum2 s|$|$back|" "$file"
		else
			sed -i "$linenum, $linenum2 s|^|$front|" "$file"
		fi
	fi
}

insert() {
	if [[ $1 == -h ]]; then
		upper='<?php //0'$alias'0 ?>\n'  # <?php //0alias0 ?>
		lower='n<?php //1'$alias'1 ?>'   # <?php //1alias1 ?>
		shift
	elif [[ $1 == -p ]]; then
		upper='<?php //0'$alias'0\n'     # <?php //0alias0
		lower='n//1'$alias'1 ?>'         # //1alias1 ?>
		shift
	elif [[ $1 == -s ]]; then
		upper='#0'$alias'0\n'            # #0alias0
		lower='n#1'$alias'1'             # #1alias1
		shift
	else
		upper='/*0'$alias'0*/\n'         # /*0alias0*/
		lower='n/*1'$alias'1*/'          # /*1alias1*/
	fi
	
	if [[ $1 == -a ]]; then ia=a; shift; else ia=i; fi               # append / insert
	if [[ $1 == -n ]]; then lines=$2; shift; shift; else lines=0; fi
	# escape \ and close each line with \ 
	string=$( cat <<EOF
$( echo "$string" | sed 's|\\|\\\\|g; s|$|\\|' )
EOF
)
	# if 1st or $ last line
	if [[ $1 =~ [0-9]+$ ]]; then                        # line number specified
		linenum=$(( $1 + $lines ))
	elif [[ $1 == '$' ]]; then                          # last line specified
		linenum=$(( $( sed -n "$ =" $file ) + $lines ))
	else
		regex=$( echo "$1" | sed -e 's|"|\\"|g' )
		linenum=$(( $( sed -n "\|$regex|=" $file ) + $lines ))
	fi
	sed -i "$linenum $ia$upper$string$lower" "$file"
}

commentH() {
	comment -h "$@"
}
commentP() {
	comment -p "$@"
}
commentS() {
	comment -s "$@"
}
insertH() {
	insert -h "$@"
}
insertP() {
	insert -p "$@"
}
insertS() {
	insert -s "$@"
}
append() {
	insert -a "$@"
}
appendH() {
	insert -h -a "$@"
}
appendP() {
	insert -p -a "$@"
}
appendS() {
	insert -s -a "$@"
}

restorefile() {
	for file in "$@"; do
		sed -i -e "s/^<?php \/\*$alias\|$alias\*\/ ?>$//
		" -e "s/^#$alias//
		" -e "s/^\/\*$alias\|$alias\*\/$//g
		" -e "\|^<?php //0${alias}0 ?>$|, \|^<?php //1${alias}1 ?>$| d
		" -e "\|^<?php //0${alias}0$|, \|^//1${alias}1 ?>$| d
		" -e "\|^#0${alias}0$|, \|^#1${alias}1$| d
		" -e "\|^/\*0${alias}0\*/$|, \|^/\*1${alias}1\*/$| d
		" -e "s|\*${alias}/|\*/|g
		" "$file"
	done
}
