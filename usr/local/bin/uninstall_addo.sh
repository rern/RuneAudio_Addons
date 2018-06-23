#!/bin/bash

alias=addo

. /srv/http/addonstitle.sh
. /srv/http/addonsedit.sh

uninstallstart $@

# restore file
echo -e "$bar Restore and remove files ..."
if [[ ! -e /usr/local/bin/uninstall_enha.sh ]]; then
	restorefile /srv/http/app/templates/header.php /srv/http/app/templates/footer.php
	rm -v /srv/http/assets/css/addons*
	rm -v /srv/http/assets/js/addons*
	rm -v /srv/http/assets/js/vendor/{hammer.min.js,propagating.js}
else
	sed -i '/id="addons"/d' /srv/http/app/templates/header.php
	sed -i '/addonsmenu.js/ d' /srv/http/app/templates/footer.php
	rm -v /srv/http/assets/css/addons.css
	rm -v /srv/http/assets/js/{addons.js,addonsmenu.js}
fi

# remove files #######################################
rm -v /srv/http/addons*
rm -r /srv/http/assets/addons

crontab -l | { cat | sed '/addonsupdate.sh/ d'; } | crontab -

uninstallfinish $@

clearcache
