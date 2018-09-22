#!/bin/bash

# change version number in RuneAudio_Addons/srv/http/addonslist.php

alias=uire

[[ ! -e /srv/http/addonstitle.sh ]] && wget -qN --no-check-certificate https://github.com/rern/RuneAudio_Addons/raw/master/srv/http/addonstitle.sh -P /srv/http
. /srv/http/addonstitle.sh

title -l '=' "$bar Reset RuneUI ..."
timestart

echo -e "$bar Get files ..."

version=$( redis-cli get buildversion )
if [[ $version == 20170229 ]]; then
	file=ui_reset.tar.xz
else
	file=ui_reset03.tar.xz
fi
# cwd '/srv/http' will be renoved
cd /tmp
wgetnc https://github.com/rern/RuneAudio/raw/master/ui_reset/$file
if [[ $? != 0 ]]; then
	title -l '=' "$warn Get files failed. Please try again."
	exit
fi

crontab -l | { cat | sed '/addonsupdate.sh/ d'; } | crontab -

# enha
# gpio
rm -f /root/gpio*.py
rm /etc/sudoers.d/http
#motd
rm -f /etc/motd.logo /etc/profile.d/motd.sh
mv /etc/motd{.backup,} 2> /dev/null
# udac
sed -i -e '/SUBSYSTEM=="sound"/ s/^#//
' -e '/refresh_ao on\|refresh_ao off/ d
' /etc/udev/rules.d/rune_usb-audio.rules
# keep chromium command line if installed
if pacman -Q chromium &> /dev/null; then
	zoomlevel=$( redis-cli get zoomlevel )
	#zoomlevel=$( redis-cli get setzoom )
	sed -i -e "/midori/ s/^/#/
	" -e "$ a\
chromium --no-sandbox --app=http://localhost --start-fullscreen --force-device-scale-factor=$zoomlevel
	" /root/.xinitrc
fi

if [[ ! $version == 20170229 ]]; then
	file=ui_reset03.tar.xz
	# spla
	systemctl disable ply-image
	systemctl enable getty@tty1.service
	rm -f /etc/systemd/system/ply-image.service
	rm -f /usr/local/bin/ply-image
	rm -fr /usr/share/ply-image
fi

rm -f /usr/local/bin/uninstall_{addo,back,enha,font,gpio,lyri,paus,RuneYoutube,udac}.sh
rm -fr /srv

bsdtar -xvf $file -C /
rm $file

chown -R http:http /srv
chmod -R 755 /srv

redis-cli hdel addons addo back enha font gpio lyri paus RuneYoutube udac &> /dev/null
redis-cli del pathlyrics settings udaclist volumemute &> /dev/null

title "$bar Install Addons ..."
wgetnc https://github.com/rern/RuneAudio_Addons/raw/master/install.sh
chmod +x install.sh
./install.sh

timestop

title "$bar $( tcolor RuneUI ) reset and $( tcolor Addons ) reinstalled successfully."

reinitsystem
