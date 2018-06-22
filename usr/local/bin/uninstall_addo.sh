#!/bin/bash

alias=addo

. /srv/http/addonstitle.sh

uninstallstart $@

# restore file
restorefile /srv/http/app/templates/header.php /srv/http/app/templates/footer.php

# remove files #######################################
echo -e "$bar Remove files ..."
rm -v /srv/http/addons*
rm -v /srv/http/assets/css/addons*
rm -v /srv/http/assets/js/addons*
rm -rv /srv/http/assets/addons

if [[ -e /usr/local/bin/uninstall_enha.sh ]]; then
	file=/srv/http/app/templates/footer.php
	string=$( cat <<'EOF'
<script src="<?=$this->asset('/js/vendor/hammer.min.js')?>"></script>
<script src="<?=$this->asset('/js/vendor/propagating.js')?>"></script>
<script src="<?=$this->asset('/js/addonsinfo.js')?>"></script>
EOF
)
	appendH '$'
else
	rm -v /srv/http/assets/js/vendor/{hammer.min.js,propagating.js}
fi

crontab -l | { cat | sed '/addonsupdate.sh/ d'; } | crontab -

uninstallfinish $@

clearcache
