#!/bin/bash

# install.sh

# addons menu for web based installation

rm $0

# import heading function
wget -qN https://github.com/rern/title_script/raw/master/title.sh; . title.sh; rm title.sh

title -l = "$bar Install Addons menu ..."

if grep -q 'Addons' /srv/http/app/templates/header.php; then
    echo -e "$info Already installed."
    exit
fi

wgetnc https://github.com/rern/RuneAudio/raw/master/Addons/addons.php
wgetnc https://github.com/rern/RuneAudio/raw/master/Addons/runbash.sh
wgetnc https://github.com/rern/RuneAudio/raw/master/Addons/uninstall_addo.sh -P /usr/local/bin
chmod +x /srv/http/runbash.sh /usr/local/bin/uninstall_addo.sh

sed -e '/poweroff-modal/ i\
            <li><a href="/addons.php"><i class="fa fa-cubes"></i> Addons</a></li>
' /srv/http/app/templates/header.php

# refresh #######################################
echo -e "$bar Clear PHP OPcache ..."
curl '127.0.0.1/clear'
echo

if pgrep midori >/dev/null; then
	killall midori
	sleep 1
	xinit &>/dev/null &
	echo 'Local browser restarted.'
fi

timestop
title -l = "$bar Addons menu installed successfully."
echo 'Uninstall: uninstall_addo.sh'
title -nt "$info Refresh browser and go to Menu > Addons."
