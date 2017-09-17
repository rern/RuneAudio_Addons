#!/bin/bash

alias=addo

. /srv/http/title.sh

uninstallstart $1

# restore file
sed -i '/id="addons"/ d' /srv/http/app/templates/header.php
sed -i '/addons.js/ d' /srv/http/app/templates/footer.php

# remove files #######################################
echo -e "$bar Remove files ..."
[[ $1 != u ]] && rm -rv /srv/http/addonslist.php
rm -rv /srv/http/{addons.php,addonsbash.php,addonsdl.php,addonsdl.sh,addonshead.php,title.sh}
rm -rv /srv/http/assets/{css/addons.css,js/addons.js}

uninstallfinish $1

[[ -t 1 ]] && clearcache
