#!/bin/bash

alias=addo

. /srv/http/addonstitle.sh
. /srv/http/addonsedit.sh

uninstallstart $@

echo -e "$bar Restore files ..."

[[ -e /srv/http/app/templates/header.php.backup ]] && backup=.backup

files="
/srv/http/index.php
/srv/http/app/templates/settings.php
/srv/http/app/templates/header.php$backup
/srv/http/app/templates/footer.php$backup
"
restorefile $files

echo -e "$bar Remove files ..."

rm -v /srv/http/addons*
rm -v /srv/http/app/templates/addons*
rm -v /srv/http/assets/css/addons*
rm -v /srv/http/assets/fonts/{addons*,Inconsolata*}
rm -v /srv/http/assets/js/addons*
rm -r /srv/http/assets/img/addons /srv/http/tmp

# must NOT remove - used by other addons
#rm -v /srv/http/assets/js/vendor/jquery.mobile.custom.min.js

crontab -l | { cat | sed '/addonsupdate.sh/ d'; } | crontab -

uninstallfinish $@

clearcache
