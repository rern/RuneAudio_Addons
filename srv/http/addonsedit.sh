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
#     comment [-n N] SEARCH [-n N] [SEARCH2]    /*alias js/php alias*/

#     commentH [-n N] SEARCH [-n N] [SEARCH2]   <?php /*alias html/php alias*/ ?>
#     commentP [-n N] SEARCH [-n N] [SEARCH2]

#     commentS [-n N] SEARCH [-n N] [SEARCH2]   #alias ...

#     insert [-n N] SEARCH                      //0alias
#     append [-n N] SEARCH                      js/php
#                                               //1alias

#     insertH [-n N] SEARCH                     <?php //0alias ?>
#     appendH [-n N] SEARCH                     html
#                                               <?php //1alias ?>
       
#     insertP [-n N] SEARCH                     <?php //0alias
#     appendP [-n N] SEARCH                     php
#                                               <?php //1alias

#     insertS [-n N] SEARCH                     #0alias
#     appendS [-n N] SEARCH                     ...
#                                               #1alias

#     restorefile FILE [FILE2 ...]              remove all insert / append / comment

# argument:
#     -n N                                      -n -N    N lines above SEARCH
#                                               -n +N    N lines below SEARCH
#     SEARCH                                    normal sed regex syntax
#                                               literal  . ^ $ * + ? ( ) [ { \ |  need \ escape
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
		if [[ $1 == -n ]]; then lines2=$2; shift; shift; else lines2=0; fi
		
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
		upper='<?php //0'$alias' ?>\n'  # <?php //0alias ?>
		lower='n<?php //1'$alias' ?>'   # <?php //1alias ?>
		shift
	elif [[ $1 == -p ]]; then
		upper='<?php //0'$alias'\n'     # <?php //0alias
		lower='n//1'$alias' ?>'         # //1alias ?>
		shift
	elif [[ $1 == -s ]]; then
		upper='#0'$alias'\n'            # #0alias
		lower='n#1'$alias               # #1alias
		shift
	else
		upper='/*0'$alias'*/\n'         # /*0alias*/
		lower='n/*1'$alias'*/'          # /*1alias*/
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
		" -e "\|^<?php //0$alias ?>$|, \|^<?php //1$alias ?>$| d
		" -e "\|^<?php //0$alias$|, \|^//1$alias ?>$| d
		" -e "\|^#0$alias$|, \|^#1$alias$| d
		" -e "\|^/\*0$alias\*/$|, \|^/\*1$alias\*/$| d
		" -e "s|\*${alias}/|\*/|g
		" "$file"
	done
}
