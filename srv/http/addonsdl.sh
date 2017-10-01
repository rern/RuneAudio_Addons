#!/bin/bash

gitpath=https://github.com/rern/RuneAudio_Addons/raw/master
if (( $# == 0 )); then # skip redownload on update Addons Menu
	dl=$( wget -qN $gitpath/srv/http/addonslist.php -P /srv/http )
	if [[ $? != 0 ]]; then
		if [[ $? == 5 ]]; then # github 'ca certificate failed' code > update time
			systemctl stop ntpd
			ntpdate pool.ntp.org
			systemctl start ntpd
			echo "$dl"
			[[ $? != 0 ]] && exit 1
		else
			exit 1
		fi
	fi
fi

versionredis=$( redis-cli hget addons addo )
versionlog=$( grep -m 1 '^$addonsversion =' /srv/http/addonslist.php | cut -d "'" -f 2 )
if [[ $versionredis != $versionlog ]]; then
	/usr/local/bin/uninstall_addo.sh

	wget -qN $gitpath/install.sh -P /srv/http
	chmod 755 /srv/http/install.sh
	/srv/http/install.sh
	exit
fi
