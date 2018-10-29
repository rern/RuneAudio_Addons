#!/bin/bash

# existing 'addonslist.php'
addonslist=$( sed -n "/'addo'/,/^),/p" /srv/http/addonslist.php )
installurl=$( echo "$addonslist" | grep 'installurl.*=>' | cut -d "'" -f 4 )
gitpath=$( dirname $installurl )
branch=master

# for testing branch: $1=branchname
if (( $# != 0 )); then
	branch=$1
	gitpath=$( echo $( dirname $gitpath )/$branch ) # switch 'master' to 'branch'
	installurl=$gitpath/install.sh
fi

wget -qN --no-check-certificate $gitpath/srv/http/addonslist.php -P /srv/http
[[ $? != 0 ]] && exit 1

# new 'addonslist.php'
addonslist=$( sed -n "/'addo'/,/^),/p" /srv/http/addonslist.php )

versionlist=$( echo "$addonslist" | grep 'version.*=>' | cut -d "'" -f 4 )
versionredis=$( redis-cli hget addons addo )

if [[ $versionlist > $versionredis ]]; then
	if (( $( df | grep '/$' | awk '{print $4}' ) < 1000 )); then
		# get directory size if enough after delete files
		(( $( du /var/cache/pacman/pkg | awk '{print $1}' ) < 1000 )) && exit 2
		rm /var/cache/pacman/pkg/*
		(( $( df | grep '/$' | awk '{print $4}' ) < 1000 )) && exit 2
	fi
	curl -s -v -X POST 'http://localhost/pub?id=addons' -d 1
	wget -qN --no-check-certificate $installurl -P /srv/http
	chmod 755 /srv/http/install.sh || exit 1
	
	/usr/local/bin/uninstall_addo.sh
	/srv/http/install.sh $branch -b
fi

. /srv/http/addonsupdate.sh 1

# get settings: notify pointer zoom
file=/etc/X11/xinit/start_chromium.sh
if [[ ! -e $file ]]; then
	if ! grep '^chromium' /root/.xinitrc; then
		zoom=$( grep '^zoom-level' /root/.config/midori/config | cut -d'=' -f2 )
	else
		zoom=$( grep 'force-device-scale-factor' /root/.xinitrc | cut -d'=' -f3 )
	fi
	file=/root/.xinitrc
else
	zoom=$( grep 'force-device-scale-factor' /etc/X11/xinit/start_chromium.sh | cut -d'=' -f3 )
fi
pointer=$( grep 'use_cursor' $file | cut -d' ' -f5 )
if [[ -e /srv/http/assets/js/enhance.js ]]; then
	notify=$( grep 'PNotify.prototype.options.delay' /srv/http/assets/js/enhance.js | cut -d' ' -f3 | tr -d '0;' )
else
	notify=$( grep 'notify.delay' /srv/http/assets/js/runeui.js | cut -d'?' -f2 | cut -d' ' -f2 )
fi
redis-cli hmset settings notify $notify pointer $pointer zoom $zoom &> /dev/null

exit 0 # force exit code = 0
