#!/bin/bash

version=20170905

# update.sh

rm $0

# import heading function
wget -qN https://github.com/rern/title_script/raw/master/title.sh; . title.sh; rm title.sh

if [[ ! -e /srv/http/addonbash.php ]]; then
    echo -e "$info RuneAudio Addons not found."
    exit
fi

title -l = "$bar Update RuneAudio Addons ..."

# modify files #######################################
echo -e "$bar Update files ..."

wgetnc https://github.com/rern/RuneAudio_Addons/raw/master/srv/http/assets/css/addons.css -P /srv/http/assets/css

redis-cli hset addons addo $version &> /dev/null

title -l = "$bar RuneAudio Addons updated successfully."
