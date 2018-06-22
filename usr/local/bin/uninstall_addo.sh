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
	rm -v /srv/http/assets/js/vendor/{hammer.min.js,propagating.js}
	rm /srv/http/assets/js/addons*
else
	sed -i '/class="fa fa-addons"/ {s|^|<?php /*enha; s|$|enha*/ ?>|}' /srv/http/app/templates/header.php
	sed -i '/class="fa fa-cubes"/d' /srv/http/app/templates/header.php
	sed -i '/addonsmenu.js/ d' /srv/http/app/templates/footer.php
	rm -v /srv/http/assets/css/addons.css
	rm -v /srv/http/assets/js/{addons.js,addonsmenu.js}
fi

# remove files #######################################
rm -v /srv/http/addons*
rm -rv /srv/http/assets/addons

crontab -l | { cat | sed '/addonsupdate.sh/ d'; } | crontab -

uninstallfinish $@

clearcache
