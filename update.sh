#!/bin/bash

file=/srv/http/assets/css/addonsinfo.css
[[ $( sed -n '/infoIcon/ {n;p}' $file | tr -d '\t' ) == 'float: left;' ]] && exit

sed -i -e '/infoIcon/ {n;d}
' -e '/vertical-align: 12px/ i\
	float: left;
' $file
