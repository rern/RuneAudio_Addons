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

update=0;
for KEY in "${!current[@]}"; do
	[[ $KEY != update &&${current[$KEY]} != ${download[$KEY]} ]] && (( update++ ))
done

redis-cli hset display update $update &> /dev/null

echo $update
