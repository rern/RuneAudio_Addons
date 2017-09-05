#!/bin/bash

version=20170905

# update.sh

rm $0

# import heading function
wget -qN https://github.com/rern/title_script/raw/master/title.sh; . title.sh; rm title.sh

if [[ ! -e /srv/http/addonbash.php ]]; then
    echo -e "$info Addons Menu not found."
    exit
fi

title -l = "$bar Update Addons Menu ..."

# modify files #######################################
echo -e "$bar Update installed addons database ..."

### install.sh ##############################################################
# set previous install to redis database
function setinstalled() {
	if [[ -e $1 ]]; then
		[[ $( redis-cli hexists addons $2 ) == 0 ]] && redis-cli hset addons $2 20170901 &> /dev/null
	fi
}
setinstalled /srv/http/addonbash.php addo
setinstalled /srv/http/assets/css/custom.css enha
setinstalled /srv/http/assets/css/gpiosettings.css gpio
setinstalled /srv/http/login.php pass
setinstalled /srv/http/restore.php back
setinstalled /etc/motd.logo motd

# check expand partition
devpart=$( mount | grep 'on / type' | awk '{print $1}' )
part=${devpart/\/dev\//}
disk=/dev/${part::-2}
unpartb=$( sfdisk -F | grep $disk | awk '{print $6}' )
unpartmb=$( python2 -c "print($unpartb / 1000000)" )

[[ $unpartmb -lt 10 ]] && redis-cli hset addons expa 1 &> /dev/null

echo -e "$bar Update files ..."
# rename-move old uninstall files
mv uninstall.sh uninstall_enha.sh &> /dev/null
mv gpiouninstall.sh uninstall_gpio.sh &> /dev/null
mv pwduninstall.sh uninstall_pass.sh &> /dev/null

mv uninstall_*.sh /usr/local/bin &> /dev/null

### /srv/http/assets/css/addons.css ###########################################
file=/srv/http/assets/css/addons.css
if ! grep -q 'white-space: pre;' $file; then
	echo $file
	sed -i '/max-height: calc(100vh - 130px)/ a\
	white-space: pre;
	' $file
fi

### /srv/http/addons.php ###########################################
sed -i -e $'/thumbnail = isset/ a\
\t	\$buttonlabel = isset(\$pkg[\'buttonlabel\']) ? \$pkg[\'buttonlabel\'] : \'Install\';
' -e $'s/Install/\'\.\$buttonlabel\.\'/g
' /srv/http/addons.php

redis-cli hset addons addo $version &> /dev/null

title -l = "$bar Addons Menu updated successfully."
