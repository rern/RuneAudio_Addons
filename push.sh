#!/bin/bash

sed -i 's|/addons.php|/settings&|' /srv/http/addons-progress.php &> /devnull

[[ -e /srv/http/addons.php ]] && file=/srv/http/addons.php || file=/srv/http/settings/addons.php

if ! grep -q verify $file; then
	wget -q https://github.com/rern/RuneAudio-Re6/raw/UPDATE/srv/http/settings/addons.php -O $file
	wget -q https://github.com/rern/RuneAudio-Re6/raw/UPDATE/srv/http/assets/css/addons.css -O /srv/http/assets/css/addons.css
fi
