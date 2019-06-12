#!/bin/bash

# existing 'addonslist.php'
branch=master
installurl=$( grep 'RuneAudio_Addons/raw/master/install.sh' /srv/http/addonslist.php | cut -d "'" -f 4 )

# for testing branch: $1=branch
if (( $# != 0 )); then
	branch=$1
	installurl=$( echo $installurl | sed s"/master/$branch/" )
fi

wget -qN --no-check-certificate $( dirname $installurl )/srv/http/addonslist.php -P /srv/http
[[ $? != 0 ]] && exit 1

versionlist=$( sed -n "/^'addo/ {n;n;p}" /srv/http/addonslist.php | cut -d "'" -f 4 )
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

exit 0 # force exit code = 0
