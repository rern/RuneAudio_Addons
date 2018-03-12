#!/bin/bash

wget -qN --no-check-certificate https://github.com/rern/RuneAudio_Addons/raw/master/srv/http/addonslist.php -P /tmp
list=( $( sed -n "/^'/ {
N;N;
s/=>/ /g
s/\s*\|'\|,//g
s/array.*version/ /
/array/ d
p}
" /tmp/addonslist.php ) )
	
rm /tmp/addonslist.php

declare -A download
ilength=${#list[@]}
for (( i = 0; i < $ilength; i+= 2 )); do
	download[${list[i]}]=${list[i+1]}
done

list=( $( redis-cli hgetall addons ) )

declare -A current
ilength=${#list[@]}
for (( i = 0; i < $ilength; i+= 2 )); do
	current[${list[i]}]=${list[i+1]}
done

diff=0;
for KEY in "${!current[@]}"; do
	if [[ $KEY == diff ]]; then
		diffcurrent=${current[$KEY]}
	else
		[[ ${current[$KEY]} != ${download[$KEY]} ]] && (( diff++ ))
	fi
done

redis-cli hset addons diff $diff &> /dev/null

echo $diffcurrent
echo $diff
