#!/bin/bash

/usr/local/bin/uninstall_addo.sh

wget -qN https://github.com/rern/RuneAudio_Addons/raw/master/install.sh -P /srv/http
chmod 755 /srv/http/install.sh
/srv/http/install.sh

[[ $? == 0 ]] && echo success
