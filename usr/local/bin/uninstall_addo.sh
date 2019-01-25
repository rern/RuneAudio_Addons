#!/bin/bash

alias=addo

. /srv/http/addonstitle.sh
. /srv/http/addonsedit.sh

uninstallstart $@

echo -e "$bar Restore files ..."

files="
/srv/http/app/templates/header.php
/srv/http/app/templates/footer.php
/srv/http/app/templates/settings.php
"
restorefile $files

echo -e "$bar Remove files ..."

rm -v /srv/http/addons*
rm -v /srv/http/assets/css/addons.css
rm -v /srv/http/assets/fonts/{addons*,Inconsolata*}
rm -v /srv/http/assets/js/{addons.js,addonsmenu.js}
rm -r /srv/http/assets/img/addons /srv/http/tmp
# DO NOT remove - used by other addons
# addonsinfo.css, addonsinfo.js, bootstrap.min.css, jquery.mobile.custom.min.js

crontab -l | { cat | sed '/addonsupdate.sh/ d'; } | crontab -

uninstallfinish $@

restartlocalbrowser
