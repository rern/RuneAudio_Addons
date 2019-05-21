#!/bin/bash

file=/srv/http/assets/css/addonsinfo.css
grep -q 34px $file && exit

wget https://github.com/rern/RuneAudio_Addons/raw/UPDATE/srv/http/assets/css/addonsinfo.css -O $file
