#!/bin/bash

rm $0

uninstall_addo.sh

wget -qN --show-progress https://github.com/rern/RuneAudio_Addons/raw/master/install.sh; chmod +x install.sh; ./install.sh

