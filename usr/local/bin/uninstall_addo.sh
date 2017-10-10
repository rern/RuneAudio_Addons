#!/bin/bash

alias=addo

. /srv/http/addonstitle.sh

uninstallstart $1

# restore file
sed -i -e '/addonsinfo.css/ d
' -e '/id="addons"/ d
' /srv/http/app/templates/header.php

sed -i -e '/addonsmenu.js/ d
' -e '/addonsinfo.js/ d
' /srv/http/app/templates/footer.php

# remove files #######################################
echo -e "$bar Remove files ..."
rm -rv /srv/http/addons*
rm -rv /srv/http/assets/css/addons*
rm -rv /srv/http/assets/js/addons*

if [[ ! -e /usr/local/bin/uninstall_enha.sh ]]; then
	sed -i -e '/hammer.min.js/ d' /srv/http/app/templates/footer.php
	rm -v /srv/http/assets/js/vendor/hammer.min.js
fi
uninstallfinish $1

clearcache
