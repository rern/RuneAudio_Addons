#!/bin/bash

[[ -e /srv/http/assets/img/addons/addons.png ]] && exit

file=/srv/http/assets/css/addonsinfo.css
wget -qN --no-check-certificate https://github.com/rern/RuneAudio_Addons/raw/UPDATE$file -O $file
file=/srv/http/assets/img/addons/addons.png
wget -qN --no-check-certificate https://github.com/rern/RuneAudio_Addons/raw/UPDATE$file -O $file
file=/srv/http/assets/jsaddonsinfo.js
wget -qN --no-check-certificate https://github.com/rern/RuneAudio_Addons/raw/UPDATE$file -O $file
