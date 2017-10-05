#!/bin/bash

# existing 'addonslist.php'
addonslist=$( sed -n "/'addo'/,/^),/p" /srv/http/addonslist.php )
installurl=$( echo "$addonslist" | grep 'installurl.*=>' | cut -d "'" -f 4 )
gitpath=$( dirname $installurl )
branch=''

# for testing branch: $1=branchname
if (( $# != 0 )); then
	branch=$1
	gitpath=$( echo $( dirname $gitpath )/$branch ) # switch 'master' to 'branch'
	installurl=$gitpath/install.sh
fi

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

# new 'addonslist.php'
addonslist=$( sed -n "/'addo'/,/^),/p" /srv/http/addonslist.php )

versionlist=$( echo "$addonslist" | grep 'version.*=>' | cut -d "'" -f 4 )
versionredis=$( redis-cli hget addons addo )

if [[ $versionlist != $versionredis ]]; then
	/usr/local/bin/uninstall_addo.sh

	wget -qN $installurl -P /srv/http
	chmod 755 /srv/http/install.sh
	/srv/http/install.sh $branch
fi
