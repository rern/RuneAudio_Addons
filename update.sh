#!/bin/bash

file=/srv/http/assets/css/addonsinfo.css
! grep -q 38px $file && wget https://github.com/rern/RuneAudio_Addons/raw/UPDATE/srv/http/assets/css/addonsinfo.css -O $file

file=/srv/http/assets/js/addons.js
! grep -q dual $file && wget https://github.com/rern/RuneAudio_Addons/raw/UPDATE/srv/http/assets/js/addons.js -O $file
