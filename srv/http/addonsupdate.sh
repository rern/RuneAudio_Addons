#!/bin/bash

if (( $# == 0 )); then
	wget -qN --no-check-certificate https://github.com/rern/RuneAudio_Addons/raw/master/srv/http/addonslist.php -P /tmp
	file=/tmp/addonslist.php
else
	file=/srv/http/addonslist.php
fi

list=( $( sed -n "/^'/ {
N;N;
s/=>/ /g
s/\s*\|'\|,//g
s/array.*version/ /
/array/ d
p}
" $file ) )
	
(( $# == 0 )) && rm /tmp/addonslist.php

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
	[[ $KEY != update && ${current[$KEY]} != 1 && ${current[$KEY]} != ${download[$KEY]} ]] && (( update++ ))
done

redis-cli hset addons update $update &> /dev/null
