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
sed -i '/addons.php/ d' /srv/http/app/templates/header.php

# remove files #######################################
echo -e "$bar Remove files ..."
rm -rv /srv/http/{addons.php,runbash.sh}

# refresh #######################################
echo -e "$bar Clear PHP OPcache ..."
curl '127.0.0.1/clear'
echo

if pgrep midori >/dev/null; then
	killall midori
	sleep 1
	xinit &>/dev/null &
	echo -e '\nLocal browser restarted.\n'
fi

title -l = "$bar Addons uninstalled successfully."

rm $0
