#!/bin/bash

alias=addo

. /srv/http/addonstitle.sh
. /srv/http/addonsedit.sh

uninstallstart $@

# restore file
echo -e "$bar Restore and remove files ..."
if [[ ! -e /usr/local/bin/uninstall_enha.sh ]]; then
	sed -i '/addonsinfo.css/ d' /srv/http/app/templates/header.php
	sed -i '/hammer.min.js\|propagating.js\|addonsinfo.js/ d' /srv/http/app/templates/footer.php
	
	restorefile /srv/http/app/templates/header.php /srv/http/app/templates/footer.php
	
	rm -v /srv/http/assets/css/addons*
	rm -v /srv/http/assets/js/addons*
	rm -v /srv/http/assets/js/vendor/{hammer.min.js,propagating.js}
else
	sed -i '/id="addons"/d' /srv/http/app/templates/header.php
	sed -i '/addonsmenu.js/ d' /srv/http/app/templates/footer.php
	rm -v /srv/http/assets/css/addons.css
	rm -v /srv/http/assets/js/{addons.js,addonsmenu.js}
	
	# 0temp0 fix
	if ! grep 'addonsinfo.css' /srv/http/app/templates/header.php; then
		string=$( cat <<'EOF'
    <link rel="stylesheet" href="<?=$this->asset('/css/addonsinfo.css')?>">
EOF
)
		sed -i "/runeui.css/ a$string" $file  # no enclosure tags for sharing with RuneUIe
	fi
	if ! grep 'hammer.min.js' /srv/http/app/templates/footer.php; then
		string=$( cat <<'EOF'
<script src="<?=$this->asset('/js/vendor/hammer.min.js')?>"></script>
<script src="<?=$this->asset('/js/vendor/propagating.js')?>"></script>
<script src="<?=$this->asset('/js/addonsinfo.js')?>"></script>
EOF
)
		sed -i "$ a$string" $file  # no enclosure tags for sharing with RuneUIe
	fi
	# 1temp1 fix
fi

# remove files #######################################
rm -v /srv/http/addons*
rm -r /srv/http/assets/addons

crontab -l | { cat | sed '/addonsupdate.sh/ d'; } | crontab -

uninstallfinish $@

clearcache
