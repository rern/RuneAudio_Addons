#!/bin/bash

if grep -q 'installed=0' /srv/http/addonstitle.sh; then
  wget -qN --no-check-certificate https://github.com/rern/RuneAudio_Addons/raw/UPDATE/srv/http/addonstitle.sh -P /srv/http
  chmod +x /srv/http/addonstitle.sh
fi
