#!/bin/bash

alias=addo

. /srv/http/addonstitle.sh

uninstallstart $@

# restore file
sed -i -e '/addonsinfo.css\|id="addons"/ d
' /srv/http/app/templates/header.php

sed -i -e '/addonsmenu.js\|addonsinfo.js/ d
' /srv/http/app/templates/footer.php

# remove files #######################################
echo -e "$bar Remove files ..."
rm -v /srv/http/{addons*,restoreui.php}
rm -v /srv/http/assets/css/addons*
rm -v /srv/http/assets/js/addons*
rm -rv /srv/http/assets/addons

if [[ ! -e /usr/local/bin/uninstall_enha.sh ]]; then
	sed -i '/hammer.min.js\|propagating.js/ d' /srv/http/app/templates/footer.php
	rm -v /srv/http/assets/js/vendor/{hammer.min.js,propagating.js}
	rm -v /srv/http/enhance.php
fi

crontab -l | { cat | sed '/addonsupdate.sh/ d'; } | crontab -

uninstallfinish $@

clearcache
