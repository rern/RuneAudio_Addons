#!/bin/bash

# import heading function
wget -qN https://github.com/rern/title_script/raw/master/title.sh; . title.sh; rm title.sh

# check installed #######################################
if [[ ! -e /srv/http/addonsbash.php ]]; then
	echo -e "Addons not found."
	exit 1
fi

$type=Uninstall
[[ ${@:$#} == -u ]] && update=1; $type=Update

title -l = "$bar $type Addons ..."

# restore file
sed -i '/id="addons"/ d' /srv/http/app/templates/header.php
sed -i '/addons.js/ d' /srv/http/app/templates/footer.php

# remove files #######################################
echo -e "$bar Remove files ..."
rm -rv /srv/http/{addon*,assets/css/addons.css,assets/js/addons.js}

redis-cli hdel addons addo &> /dev/null

title -l = "$bar Addons uninstalled successfully."

# clear opcache if run from terminal #######################################
[[ -t 1 ]] && systemctl reload php-fpm

# restart local browser #######################################
if pgrep midori > /dev/null; then
	killall midori
	sleep 1
	xinit &> /dev/null &
fi

rm $0
