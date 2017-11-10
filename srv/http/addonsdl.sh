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

wget -qN $gitpath/srv/http/addonslist.php -P /srv/http
if [[ $? == 5 ]]; then # 'certificate error' code
	curl -s -v -X POST 'http://localhost/pub?id=addons' -d 2
	
	systemctl stop ntpd
	ntpdate pool.ntp.org
	systemctl start ntpd
	
	exit 5
fi

# new 'addonslist.php'
addonslist=$( sed -n "/'addo'/,/^),/p" /srv/http/addonslist.php )

versionlist=$( echo "$addonslist" | grep 'version.*=>' | cut -d "'" -f 4 )
versionredis=$( redis-cli hget addons addo )

if [[ $versionlist != $versionredis ]]; then
	curl -s -v -X POST 'http://localhost/pub?id=addons' -d 1
	wget -qN $installurl -P /srv/http
	chmod 755 /srv/http/install.sh || exit 1
	
	/usr/local/bin/uninstall_addo.sh
	/srv/http/install.sh $branch -b
fi

exit 0 # force exit code = 0
