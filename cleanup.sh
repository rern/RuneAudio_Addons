#!/bin/bash

sed -i '/id="addons"/ d' /srv/http/app/templates/header.php
sed -i '/addons.js/ d' /srv/http/app/templates/footer.php
rm -rv /srv/http/{addons*,title.sh}
rm -rv /srv/http/assets/{css/addons*,js/addons*}
rm -v /usr/local/bin/uninstall_addo.sh
redis-cli del addons
systemctl reload php-fpm

wget -qN --show-progress https://github.com/rern/RuneAudio_Addons/raw/master/install.sh; chmod +x install.sh; ./install.sh
