#!/bin/bash

# import heading function
wget -qN https://github.com/rern/title_script/raw/master/title.sh; . title.sh; rm title.sh

# check installed #######################################
if ! grep -q 'Addons' /srv/http/app/templates/header.php; then
	echo -e "Addons not found."
	exit
fi

title -l = "$bar Uninstall Addons ..."

# restore file
sed -i '/id="addons"/ d' /srv/http/app/templates/header.php
sed -i '/<!--addons/, /<\/script>/d' /srv/http/app/templates/footer.php

# remove files #######################################
echo -e "$bar Remove files ..."
rm -rv /srv/http/{addonbash.php,addondl.php,addons.php}

# refresh #######################################
echo -e "$bar Clear PHP OPcache ..."
systemctl reload php-fpm
echo

if pgrep midori >/dev/null; then
	killall midori
	sleep 1
	xinit &>/dev/null &
	echo -e '\nLocal browser restarted.\n'
fi

redis-cli hdel addons main &> /dev/null

title -l = "$bar Addons uninstalled successfully."

rm $0
