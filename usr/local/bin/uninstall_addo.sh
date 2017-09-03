#!/bin/bash

# import heading function
wget -qN https://github.com/rern/title_script/raw/master/title.sh; . title.sh; rm title.sh

# check installed #######################################
if [[ ! -e /srv/http/addonbash.php ]]; then
	echo -e "Addons not found."
	exit
fi

title -l = "$bar Uninstall Addons ..."

# restore file
sed -i '/id="addons"/ d' /srv/http/app/templates/header.php
sed -i '/addons.js/ d' /srv/http/app/templates/footer.php

# remove files #######################################
echo -e "$bar Remove files ..."
rm -rv /srv/http/{addonbash.php,addondl.php,addons.php,addonshead.php}

redis-cli hdel addons main &> /dev/null

title -l = "$bar Addons uninstalled successfully."

# clear opcache and restart local browser #######################################
systemctl reload php-fpm

if pgrep midori > /dev/null; then
	killall midori
	sleep 1
	xinit &> /dev/null &
fi

rm $0
