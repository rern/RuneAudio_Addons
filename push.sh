#!/bin/bash

[[ $( cat /srv/http/data/addons/rre6 ) > 20201107 ]] && exit

sed -i 's|/addons.php|/settings&|' /srv/http/addons-progress.php &> /dev/null

wget -q https://github.com/rern/RuneAudio-Re6/raw/UPDATE/srv/http/assets/css/addons.css -O /srv/http/assets/css/addons.css
wget -q https://github.com/rern/RuneAudio-Re6/raw/UPDATE/srv/http/settings/addons.php -O /srv/http/settings/addons.php
wget -q https://github.com/rern/RuneAudio-Re6/raw/UPDATE/srv/http/settings/addons-progress.php -O /srv/http/settings/addons-progress.php
