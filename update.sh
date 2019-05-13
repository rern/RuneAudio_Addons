#!/bin/bash

[[ -e /srv/http/assets/img/addons/addons.png ]] && exit

wget -qN --no-check-certificate https://github.com/rern/RuneAudio_Addons/raw/UPDATE/srv/http/assets/css/addonsinfo.css -O /srv/http/assets/css
wget -qN --no-check-certificate https://github.com/rern/RuneAudio_Addons/raw/UPDATE/srv/http/assets/img/addons/addons.png -O /srv/http/assets/img/addons
wget -qN --no-check-certificate https://github.com/rern/RuneAudio_Addons/raw/UPDATE/srv/http/assets/js/addonsinfo.js -O /srv/http/assets/js
