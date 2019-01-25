#!/bin/bash

alias=addo

. /srv/http/addonstitle.sh
. /srv/http/addonsedit.sh

uninstallstart $@

echo -e "$bar Restore files ..."

files="
/srv/http/index.php
/srv/http/index.php.backup
/srv/http/app/templates/settings.php
/srv/http/app/templates/header.php
/srv/http/app/templates/footer.php
"
restorefile $files

echo -e "$bar Remove files ..."

rm -v /srv/http/addons*
rm -v /srv/http/assets/css/addons.css
rm -v /srv/http/assets/fonts/{addons*,Inconsolata*}
rm -v /srv/http/assets/js/{addons.js,addonsmenu.js}
rm -r /srv/http/assets/img/addons /srv/http/tmp
if [[ ! -e /srv/http/gpiosettings.php && ! -e /srv/http/enhance.php ]]; then
	rm -v /srv/http/assets/css/bootstrap.min.css
	rm -v /srv/http/assets/js/vendor/jquery.mobile.custom.min.js
fi
# DO NOT remove - used by other addons
# addonsinfo.css
# addonsinfo.js

crontab -l | { cat | sed '/addonsupdate.sh/ d'; } | crontab -

uninstallfinish $@

restartlocalbrowser
