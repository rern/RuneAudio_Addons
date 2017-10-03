#!/bin/bash

alias=addo

. /srv/http/addonstitle.sh

uninstallstart $1

# restore file
sed -i '/id="addons"/ d' /srv/http/app/templates/header.php

sed -i -e '/addonsinfo.js/ d
' -e '/addonsmenu.js/ d
' -e '/hammer.min.js/ d
' /srv/http/app/templates/footer.php

# remove files #######################################
echo -e "$bar Remove files ..."
rm -rv /srv/http/addons*
rm -rv /srv/http/assets/css/addons*
rm -rv /srv/http/assets/js/addons*

[[ ! -e /usr/local/bin/uninstall_enha.sh ]] && rm -v srv/http/assets/js/vendor/hammer*

uninstallfinish $1
