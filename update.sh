#!/bin/bash

if ! grep -q flushdot /srv/http/addonsbash.php; then
	wget -qN --no-check-certificate https://github.com/rern/RuneAudio_Addons/raw/UPDATE/srv/http/addonsbash.php -P /srv/http
  wget -qN --no-check-certificate https://github.com/rern/RuneAudio_Addons/raw/UPDATE/srv/http/assets/css/addons.css -P /srv/http/assets/css
fi
