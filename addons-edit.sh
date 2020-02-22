#!/bin/bash

# insert / append / comment : for /*...*/  <?php /*...*/ ?>  #...

# pre-defined variable:
#     alias=name                                already in install.sh / uninstall_alias.sh
#     file=/path/file                           before all commands of each file
#     string=$( cat <<'EOF'                      before each insert and append
#     place code without escapes
#     last line
#     EOF
#     )
# usage:
#     match [-n N] SEARCH [-n N] [SEARCH2]      test sed search pattern

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

#    insertAsset SEARCH FILE.ext               <?php //0alias0 ?>
#    appendAsset SEARCH FILE.ext               <style> @font-face { ... } </style>
#                                              <link rel="stylesheet" href="<?=$this->asset('/css/FILE.css')?>">
#                                              <script src="<?=$this->asset('/js/FILE.js')?>"></script>
#                                              <?php //1alias1 ?>

#     restorefile FILE [FILE2 ...]              remove all insert / append / comment

# argument:
#     -n N                                      -n -N    N lines above SEARCH
#                                               -n +N    N lines below SEARCH
#     SEARCH                                    normal sed regex syntax inside quotes
#                                               '..."'"...'  escape single quote inside itself
#                                               "...\"..."   escape double quote inside itself
#                                               |  as delimiter for no escape  /
#                                               literal  | ^ $ . * [ ] \  need  \  escape
#                                               or use  .  as placeholder instead of escape

# tips:
# test run SEARCH:
#     . /srv/http/addonsedit.sh
#     file=/path/file
#     match [-n N] SEARCH [-n N] [SEARCH2]
# cache busting - insert/append FILE.ttf/FILE.woff/FILE.css/FILE.js with insertAsset/appendAsset
# insert/append with SEARCH itself in $string
#     must be after comment to the same SEARCH (avoid commented after insert)
#     must be combined with insert/append to the same SEARCH (avoid double insert)

comment() {
	test=0 # reset for running from terminal
	if [[ $1 == -h || $1 == -p ]]; then
		front='<?php /*'$alias  # <?php /*alias
		back=$alias'*/ ?>'      # alias*/ ?>
		shift
	elif [[ $1 == -s ]]; then
		front='#'$alias         # #alias
		back=                   # (none)
		shift
	elif [[ $1 == -t ]]; then
		[[ -z $file ]] && echo 'file=(undefined)' && return
		test=1
		shift
		col=$( tput cols )
		line() {
			printf "\e[38;5;6m%*s\e[0m\n" $col | tr ' ' -
		}
	else
		front='/*'$alias        # /*alias
		back=$alias'*/'         # alias*/
	fi

	if [[ $1 == -n ]]; then lines=$2; shift; shift; else lines=0; fi # line +-

	regex=$( echo "$1" | sed -e 's|"|\\"|g' )                        # escape " in sed "..."
	linenum=( $( sed -n "\|$regex|=" $file ) )                       # array of all line(s)
	ilength=${#linenum[*]}

	if (( $lines != 0 )); then                                        # add line +- to array
		for (( i=0; i < ilength; i++ )); do
			linenum[$i]=$(( ${linenum[$i]} + $lines ))
		done
	fi
	if (( $# == 1 )); then
		if [[ $test == 1 ]]; then
			echo
			echo 'sed -n "\|'$regex'| p" "'$file'"'
			echo
			for (( i=0; i < ilength; i++ )); do
				echo -e "\e[38;5;6m#$(( i + 1 )) line: ${linenum[$i]}\e[0m"
				line
				echo $( sed -n "${linenum[$i]} p" "$file" )
				line
				echo
			done
			echo -e "\e[38;5;6mmatched: $ilength\e[0m"
			echo
			return
		fi

		for (( i=0; i < ilength; i++ )); do
			if [[ $front != '#'$alias ]]; then
				sed -i "${linenum[i]} { s|\*/|\*$alias/|g; s|^|$front|; s|$|$back| }" "$file"
			else
				sed -i "${linenum[i]} s|^|$front|" "$file"
			fi
		done
	else
		if [[ $2 == -n ]]; then lines2=$3; shift; shift; else lines2=0; fi
		regex2=$( echo "$2" | sed -e 's|"|\\"|g' )
		linenum2=( $( sed -n "\|$regex2|=" $file ) )
		ilength2=${#linenum2[*]}
		
		if (( $ilength != $ilength2 )); then
			echo "Range pairs not matched: $ilength x $regex <-> $ilength2 x $regex2"
			return
		fi

		if (( $lines2 != 0 )); then
			for (( i=0; i < ilength2; i++ )); do
				linenum2[$i]=$(( ${linenum2[$i]} + $lines2 ))
			done
		fi
	
		if [[ $test == 1 ]]; then
			echo
			echo 'sed -n "\|'$regex'|, \|'$regex2'| p" "'$file'"'
			echo
			for (( i=0; i < ilength; i++ )); do
				echo -e "\e[38;5;6m#$(( i + 1 )) line: ${linenum[i]} - ${linenum2[i]}\e[0m"
				line
				sed -n "${linenum[i]}, ${linenum2[i]} p" "$file"
				line
				echo
			done
			echo -e "\e[38;5;6mmatched: $ilength\e[0m"
			echo
			return
		fi
		
		for (( i=0; i < ilength; i++ )); do
			if [[ $front != '#'$alias ]]; then
				# escape existing /* comment */
				sed -i "${linenum[i]}, ${linenum2[i]} s|\*/|\*$alias/|g" "$file"
				sed -i -e "${linenum[i]} s|^|$front|" -e "${linenum2[i]} s|$|$back|" "$file"
			else
				sed -i "${linenum[i]}, ${linenum2[i]} s|^|$front|" "$file"
			fi
		done
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
	if [[ $1 == -a ]]; then ia=a; shift; else ia=i; fi      # append / insert
	if [[ $1 == -n ]]; then lines=$2; shift; shift; else lines=0; fi
	stringcount=$( echo "$string" | wc -l )                 # count insert lines
	insertcount=$(( stringcount + 2 ))                      # add 2 for upper-lower lines
	
	# escape \ and close each line with \ 
	string=$( cat <<EOF
$( echo "$string" | sed 's|\\|\\\\|g; s|$|\\|' )
EOF
)
	# if 1st or $ last line
	if [[ $1 =~ ^[0-9]+$ ]]; then                           # line number specified
		linenum=( $(( $1 + $lines )) )                      # array of single line
	elif [[ $1 == '$' ]]; then                              # last line specified
		linenum=( $(( $( sed -n '$ =' $file ) + $lines )) ) # array of single line
	else
		regex=$( echo "$1" | sed -e 's|"|\\"|g' )
		linenum=( $( sed -n "\|$regex|=" $file ) )          # array of all line(s)
		ilength=${#linenum[*]}
		if [[ $lines != 0 ]]; then                          # add line +- to array
			for (( i=0; i < ilength; i++ )); do
				linenum[$i]=$(( ${linenum[$i]} + $lines ))
			done
		fi
	fi
	increment=0
	ilength=${#linenum[*]}
	for (( i=0; i < ilength; i++ )); do
		lineins=$(( ${linenum[i]} + $increment ))           # increment line number after each insert
		sed -i "$lineins $ia$upper$string$lower" "$file"
		increment=$(( $increment + $insertcount ))          # number of line to move
	done
}

match() {
	comment -t "$@"
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

asset() {
	ia=$1
	shift
	if [[ $1 == -n ]]; then
		n='-n'
		lines=$2
		shift
		shift
	else
		n=
		lines=
	fi
	
	line=$1
	shift
	string=
	for filename in "$@"; do
		ext=${filename##*.}
		if [[ $ext == 'woff' || $ext == 'ttf' ]]; then
			name=${filename%.*}
			path=/srv/http/assets/fonts
			if [[ ! -e $path/${name}.woff || ! -e $path/${name}.ttf ]]; then
				echo $path/${name}.woff or $path/${name}.ttf missing.
			fi
			string+=$( cat <<EOF

	<style>
		@font-face {
			font-family: $name;
			src        : url( "/assets/fonts/enhance.<?=$time?>.woff" ) format( 'woff' ), url( "/assets/fonts/enhance.<?=$time?>.ttf" ) format( 'truetype' );
			font-weight: normal;
			font-style : normal;
		}
	</style>
EOF
)
		elif [[ $ext == 'css' ]]; then
			string+=$( cat <<EOF

	<link rel="stylesheet" href="<?=\$this->asset('/css/$filename')?>">
EOF
)
		else
			string+=$( cat <<EOF

<script src="<?=\$this->asset('/js/$filename')?>"></script>
EOF
)
		fi
	done
	string=$( echo -e "$string" | sed '1 d' ) # remove 1st blank line
	shift
	if [[ $line != '$' ]]; then
		[[ $ia == -i ]] && insertH $n $lines "$line" || appendH $n $lines "$line"
	else
		[[ $ia == -i ]] && insertH $n $lines '$' || appendH $n $lines '$'
	fi
}
insertAsset() {
	asset -i "$@"
}
appendAsset() {
	asset -a "$@"
}

restorefile() {
	for file in "$@"; do
		[[ ! -e $file ]] && continue
		
		echo "$file"
		sed -i -e "s/^<?php \/\*$alias\|$alias\*\/ ?>$//g
		" -e "s/^#$alias//
		" -e "s/^\/\*$alias\|$alias\*\/$//g
		" -e "s|\*${alias}/|\*/|g
		" -e "/0${alias}0/, /1${alias}1/ d
		" "$file"
	done
}
