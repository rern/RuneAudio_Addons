#!/bin/bash

alias=addo

. /srv/http/addonstitle.sh
. /srv/http/addonsedit.sh

uninstallstart $@

echo -e "$bar Restore files ..."

restorefile /srv/http/app/templates/header.php /srv/http/app/templates/footer.php

echo -e "$bar Remove files ..."

rm -v /srv/http/assets/css/addons*
rm -v /srv/http/assets/fonts/Inconsolata.*
rm -v /srv/http/assets/js/addons*
#rm -v /srv/http/assets/js/vendor/{hammer.min.js,propagating.js}
rm -v /srv/http/addons*
rm -r /srv/http/assets/addons

crontab -l | { cat | sed '/addonsupdate.sh/ d'; } | crontab -

uninstallfinish $@

clearcache
