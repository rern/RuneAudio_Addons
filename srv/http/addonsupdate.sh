#!/bin/bash

/usr/bin/sudo /usr/local/bin/uninstall_addo.sh u

wget -qN https://github.com/rern/RuneAudio_Addons/raw/master/install.sh 
chmod 755 install.sh
/usr/bin/sudo ./install.sh u
