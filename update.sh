#!/bin/bash

grep -q 'h=00' /srv/http/addonstitle.sh && exit
  
wget -qN --no-check-certificate https://github.com/rern/RuneAudio_Addons/raw/UPDATE/srv/http/addonstitle.sh -P /srv/http
