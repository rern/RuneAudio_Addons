#!/bin/bash

/usr/local/bin/uninstall_addo.sh u

wget -qN https://github.com/rern/RuneAudio_Addons/raw/update/install.sh -P /srv/http
chmod 755 /srv/http/install.sh
/srv/http/install.sh u
