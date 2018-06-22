#!/bin/bash

alias=addo

. /srv/http/addonstitle.sh

uninstallstart $@

# restore file
if [[ ! -e /usr/local/bin/uninstall_enha.sh ]]; then
	restorefile /srv/http/app/templates/header.php /srv/http/app/templates/footer.php
	rm -v /srv/http/assets/js/vendor/{hammer.min.js,propagating.js}
else
	sed -i '/id="addons"/d' /srv/http/app/templates/header.php
	sed -i '/addonsmenu.js/ d' /srv/http/app/templates/footer.php
fi

# remove files #######################################
echo -e "$bar Remove files ..."
rm -v /srv/http/addons*
rm -v /srv/http/assets/css/addons*
rm -v /srv/http/assets/js/addons*
rm -rv /srv/http/assets/addons

crontab -l | { cat | sed '/addonsupdate.sh/ d'; } | crontab -

uninstallfinish $@

clearcache
