#!/bin/bash

# required variables
alias=addo

# import heading function
wget -qN https://github.com/rern/title_script/raw/master/title.sh; . title.sh; rm title.sh

uninstallstart $1

# restore file
sed -i '/id="addons"/ d' /srv/http/app/templates/header.php
sed -i '/addons.js/ d' /srv/http/app/templates/footer.php

# remove files #######################################
echo -e "$bar Remove files ..."
rm -rv /srv/http/{addon*,assets/css/addons.css,assets/js/addons.js}

uninstallfinish $1

[[ -t 1 ]] && clearcache
