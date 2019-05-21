#!/bin/bash

file=/srv/http/assets/css/addonsinfo.css
[[ $( sed -n '/infoIcon/ {n;p}' $file | tr -d '\t' ) == 'float: left;' ]] && exit

wget https://github.com/rern/RuneAudio_Addons/raw/UPDATE/srv/http/assets/css/addonsinfo.css -O $file
