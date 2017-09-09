#!/bin/bash

version=20170909 # addonslist.php must be updated

# install.sh

# addons menu for web based installation

rm $0

# import heading function
wget -qN https://github.com/rern/title_script/raw/master/title.sh; . title.sh; rm title.sh

if [[ -e /srv/http/addonsbash.php ]]; then
    echo -e "$info Already installed."
    exit
fi

# install RuneAudio Addons #######################################
title -l = "$bar Install Addons menu ..."
echo -e "$bar Get files ..."
wgetnc https://github.com/rern/RuneAudio_Addons/archive/master.zip

echo -e "$bar Install new files ..."
rm -rf  /tmp/install
mkdir -p /tmp/install
bsdtar -xf master.zip --strip 1 -C /tmp/install
rm master.zip /tmp/install/* &> /dev/null
chown -R http:http /tmp/install/srv
chmod -R 755 /tmp/install

cp -rp /tmp/install/* /
rm -r /tmp/install
echo

# modify files #######################################
echo -e "$bar Modify files ..."

file=/srv/http/app/templates/header.php
if ! grep -q 'id="addons"' $file; then
	echo $file
	sed -i '/poweroff-modal/ i\
            <li><a id="addons" style="cursor: pointer;"><i class="fa fa-cubes"></i> Addons</a></li>
	' $file
fi

file=/srv/http/app/templates/footer.php
if ! grep -q 'addons.js' $file; then
	echo $file
	echo '<script src="<?=$this->asset('"'"'/js/addons.js'"'"')?>"></script>' >> $file
fi

# set sudo no password #######################################
echo 'http ALL=NOPASSWD: ALL' > /etc/sudoers.d/http
[[ $(stat -c %a /usr/bin/sudo) != 4755 ]] && chmod 4755 /usr/bin/sudo

redis-cli hset addons addo $version &> /dev/null

title -l = "$bar Addons menu installed successfully."
[[ -t 1 ]] && echo 'Uninstall: uninstall_addo.sh'
title -nt "$info Refresh browser and go to Menu > Addons."

# clear opcache if run from terminal #######################################
[[ -t 1 ]] && systemctl reload php-fpm

# restart local browser #######################################
if pgrep midori > /dev/null; then
	killall midori
	sleep 1
	xinit &> /dev/null &
fi
