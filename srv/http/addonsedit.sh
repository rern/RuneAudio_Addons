#!/bin/bash

# insert / append / comment : for /*...*/  <?php /*...*/ ?>  #...
#
# pre-defined variables:
#     alias=name                      # already in install.sh / uninstall_alias.sh
#     file=/path/file                 # before all commands of each file
#     string=( cat <<'EOF'            # before each insert and append
#     place code inside this heredoc literally
#     last line
#     EOF
#     )
# usage:
#     comment SEARCH [SEARCH2]        # /*alias line(s) alias*/
#     commentP SEARCH [SEARCH2]       # <?php /*alias line(s) alias*/ ?>
#     commentS SEARCH [SEARCH2]       # #alias (each line)
#     insert SEARCH                   # //alias0
#                                     # string
#                                     # //alias1
#     insertP SEARCH                  # <?php //alias0 ?>
#                                     # string
#                                     # <?php //alias1 ?>
#     insertS SEARCH                  # #0alias
#                                     # string
#                                     # #1alias
#     append SEARCH                   # same as insert
#     appendP SEARCH                  # same as insertP
#     appendS SEARCH                  # same as insertS
#     restorefile FILE [FILE2 ...]    # remove all insert / append / comment
# options:
#     SEARCH pattern must be quoted and escaped
#          "  $  `  \  inside "..."  use  \"  \$  \`  \\  or use  .  as wildcard
#          '  inside '...'           use  "'"             or use  .  as wildcard
#     insert/append with SEARCH itself in $string
#          must be after comment to the same SEARCH (avoid commented after insert)
#          must be combined with insert/append to the same SEARCH (avoid double insert)

comment() {
	if [[ $1 == -p ]]; then
		front='<?php /*'$alias
		back=$alias'*/ ?>'
		shift
	elif [[ $1 == -s ]]; then
		front='#'$alias
		back=
		shift
	else
		front='/*'$alias
		back=$alias'*/'
	fi
	# escape regex: reserved characters
	regex=$( echo "$1" | sed -e 's|[]"\|$*.^[]|\\&|g' )
	if (( $# == 1 )); then
		if [[ $front != '#'$alias ]]; then
			sed -i "\|$regex| { s|\*/|\*$alias/|; s|^|$front|; s|$|$back| }" "$file"
		else
			sed -i "\|$regex| s|^|$front|" "$file"
		fi
	else
		regex2=$( echo "$2" | sed -e 's|[]"\|$*.^[]|\\&|g' )
		if [[ $front != '#'$alias ]]; then
			# escape existing /* comment */
			sed -i "\|$regex|, \|$regex2| s|\*/|\*$alias/|" "$file"
			sed -i -e "\|$regex| s|^|$front|" -e "\|$regex2| s|$|$back|" "$file"
		else
			sed -i "\|$regex|, \|$regex2| s|^|$front|" "$file"
		fi
	fi
}

insert() {
	if [[ $1 == -p ]]; then
		upper='<?php //0'$alias' ?>\n'
		lower='n<?php //1'$alias' ?>'
		shift
	elif [[ $1 == -s ]]; then
		upper='#0'$alias'\n'
		lower='n#1'$alias
		shift
	else
		upper='/*0'$alias'*/\n'
		lower='n/*1'$alias'*/'
	fi
	
	ia=i
	[[ $1 == -a ]] && ia=a && shift
	# escape \ and close each line with \ 
	string=$( cat <<EOF
$( echo "$string" | sed 's|\\|\\\\|g; s|$|\\|' )
EOF
)
	# if line number or $ last line
	if [[ $1 =~ [0-9]+$ || $1 == '$' ]]; then
		sed -i "$1 $ia$upper$string$lower" "$file"
	else
		regex=$( echo "$1" | sed -e 's|[]"\|$*.^[]|\\&|g' )
		sed -i "\|$regex| $ia$upper$string$lower" "$file"
	fi
}

commentP() {
	comment -p "$@"
}
commentS() {
	comment -s "$@"
}
insertP() {
	insert -p "$1"
}
insertS() {
	insert -s "$1"
}
append() {
	insert -a "$1"
}
appendP() {
	insert -p -a "$1"
}
appendS() {
	insert -s -a "$1"
}

restorefile() {
	for file in "$@"; do
		sed -i -e "\|^<?php //0$alias ?>$|, \|^<?php //1$alias ?>$| d
		" -e "/#0$alias/, /#1$alias/ d
		" -e "\|^/\*0$alias\*/$|, \|^/\*1$alias\*/$| d
		" -e "s/^<?php \/\*$alias\|$alias\*\/ ?>$\|^#$alias\/^\/\*$alias\|$alias\*\/$//g
		" -e "s|\*${alias}/|\*/|
		" "$file"
	done
}
