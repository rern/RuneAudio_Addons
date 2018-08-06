#!/bin/bash

alias=addo

. /srv/http/addonstitle.sh
. /srv/http/addonsedit.sh

uninstallstart $@

echo -e "$bar Restore files ..."

[[ -e /srv/http/app/templates/header.php.backup ]] && backup=.backup

files="
/srv/http/index.php
/srv/http/app/templates/header.php$backup
/srv/http/app/templates/footer.php$backup
"
restorefile $files

echo -e "$bar Remove files ..."

rm -v /srv/http/assets/css/addo*
rm -v /srv/http/assets/fonts/{addons.*,Inconsolata.*}
rm -v /srv/http/assets/js/addo*
rm -v /srv/http/addons*
rm -r /srv/http/assets/img/addons

# must NOT remove - used by other addons
#rm -v /srv/http/assets/js/vendor/jquery.mobile.custom.min.js

crontab -l | { cat | sed '/addonsupdate.sh/ d'; } | crontab -

uninstallfinish $@

clearcache
