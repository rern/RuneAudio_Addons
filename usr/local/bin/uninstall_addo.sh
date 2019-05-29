#!/bin/bash

alias=addo

. /srv/http/addonstitle.sh
. /srv/http/addonsedit.sh

uninstallstart $@

echo -e "$bar Restore files ..."

files="
/etc/nginx/nginx.conf
/srv/http/app/templates/header.php
/srv/http/app/templates/footer.php
/srv/http/app/templates/settings.php
/srv/http/app/libs/runeaudio.php
/srv/http/command/convert_dos_files_to_unix_script.sh
/srv/http/command/mpd_update.sh
/srv/http/command/restore.sh
"
restorefile $files

echo -e "$bar Remove files ..."

rm -v /srv/http/addons*
rm -v /srv/http/assets/css/addons.css
rm -v /srv/http/assets/fonts/{addons*,Inconsolata*}
rm -v /srv/http/assets/js/{addons.js,addonsmenu.js}
rm -v /srv/http/assets/js/vendor/jquery.documentsize.min.js
rm -r /srv/http/assets/img/addons /srv/http/tmp
# DO NOT remove - used by other addons
# addonsinfo.css, addonsinfo.js, bootstrap.min.css, jquery.mobile.custom.min.js

crontab -l | { cat | sed '/addonsupdate.sh/ d'; } | crontab -

uninstallfinish $@

restartlocalbrowser
