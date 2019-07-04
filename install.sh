#!/bin/bash

# $1-branch ; $2-branch flag '-b' (syntax for all addons in addonsdl.sh)
# for '$branch' before 'addonstitle.sh' exist ( ./install UPDATE -b : branch=UPADTE )
if [[ $# == 2 && $2 == '-b' ]]; then
	branch=$1
else
	branch=master
fi

# for 'installstart' before 'addonslist.php' exist
if [[ ! -e /srv/http/addonslist.php || ! -e /srv/http/addonstitle.sh ]]; then
	gitpath=https://github.com/rern/RuneAudio_Addons/raw/$branch/srv/http
	wget -qN --no-check-certificate $gitpath/addonslist.php -P /srv/http
	wget -qN --no-check-certificate $gitpath/addonstitle.sh -P /srv/http
	chown http:http /srv/http/addons*
fi

# change version number in RuneAudio_Addons/srv/http/addonslist.php

alias=addo

. /srv/http/addonstitle.sh

installstart $@

packagestatus openssl-cryptodev # $version, $installed
if [[ -z $installed ]]; then
	echo -e "$bar Upgrade common packages ..."
	
	rankmirrors
	echo -e "\n$bar Upgrade OpenSSL ..."
	echo -e "y \n" | pacman -S openssl-cryptodev
fi

packagestatus glibc
if [[ -z $installed ]]; then
	rankmirrors
	echo -e "\n$bar GNU C Library ..."
	pacman -S --noconfirm glibc
fi

getinstallzip

. /srv/http/addonsedit.sh # available after getinstallzip

echo -e "$bar Modify files ..."
#----------------------------------------------------------------------------------
file=/srv/http/app/templates/header.php
echo $file

string=$( cat <<'EOF'
    <style>
        @font-face {
            font-family: addons;
            src: url( '<?=$this->asset('/fonts/addons.woff') ?>' ) format( 'woff' ),
                 url( '<?=$this->asset('/fonts/addons.ttf') ?>' ) format( 'truetype' );
            font-weight: normal;
            font-style: normal;
        }
    </style>
    <link rel="stylesheet" href="<?=$this->asset('/css/addonsinfo.css')?>">
<?=( $this->uri(1) === 'addons' ? '<link rel="stylesheet" href="'.$this->asset('/css/addons.css').'">' : '' ) ?>
EOF
)
appendH 'runeui.css'

string=$( cat <<'EOF'
<?php if ( $this->uri(1) !== 'addons' ): ?>
EOF
)
appendH '^-->'

string=$( cat <<'EOF'
            <li><a id="addons"><i class="fa fa-addons"></i> Addons</a></li>
EOF
)
insertH -n -2 'playback-controls'

string=$( cat <<'EOF'
<?php endif ?>
EOF
)
appendH '$'
#----------------------------------------------------------------------------------
file=/srv/http/app/templates/footer.php
echo $file

string=$( cat <<'EOF'
<script src="<?=$this->asset('/js/vendor/jquery.mobile.custom.min.js')?>"></script>
EOF
)
appendH 'jquery-2.1.0.min.js'

string=$( cat <<'EOF'
<?php if ($this->section == 'index'): ?>
<script src="<?=$this->asset('/js/addonsinfo.js')?>"></script>
<script src="<?=$this->asset('/js/addonsmenu.js')?>"></script>
<?php elseif ($this->section == 'addons'): ?>
<script src="<?=$this->asset('/js/addonsinfo.js')?>"></script>
<script src="<?=$this->asset('/js/addons.js')?>"></script>
<?php endif ?>
EOF
)
appendH '$'
#----------------------------------------------------------------------------------
file=/srv/http/app/templates/settings.php
echo $file

commentH -n -2 'Restore configuration' -n -2 'id="modal-sysinfo"'
#----------------------------------------------------------------------------------

# set sudo no password
cat << 'EOF' > /etc/sudoers.d/http
http ALL=NOPASSWD: ALL
EOF
chmod 4755 /usr/bin/sudo

# update check
file=/etc/systemd/system/addons.service
echo $file

cat << 'EOF' > $file
[Unit]
Description=Addons update check
After=network-online.target
[Service]
Type=idle
ExecStart=/srv/http/addonsupdate.sh &
[Install]
WantedBy=multi-user.target
EOF

crontab -l | { cat; echo '00 01 * * * /srv/http/addonsupdate.sh &'; } | crontab - &> /dev/null
systemctl daemon-reload
systemctl enable addons cronie
systemctl start addons cronie

# for backup file upload
dir=/srv/http/tmp
mkdir -p $dir
chown http:http $dir
chmod 777 $dir

# fix - 0.5 bugs
if [[ $( redis-cli get release ) == '0.5' ]]; then
	# missing output data
	[[ -z $( redis-cli hgetall acards ) ]] && /srv/http/command/refresh_ao &> /dev/null
	# /srv/http permission change
	pattern="^\s*sysCmd('find /srv/http/ -type f -exec chmod"
	file=/srv/http/app/libs/runeaudio.php
	comment "$pattern"
	files="/srv/http/command/convert_dos_files_to_unix_script.sh /srv/http/command/mpd_update.sh /srv/http/command/restore.sh"
	for f in $files; do
		file=$f
		commentS "$pattern"
	done
fi

installfinish $@

# disable OPcache
title -nt "$info Disable PHP OPcache"
redis-cli set opcache 0 &> /dev/null

file=/etc/php/conf.d/opcache.ini
if grep -q 'opcache.enable=1' $file; then
	sed -i 's/opcache.enable=1/opcache.enable=0/' $file
	systemctl restart php-fpm
fi

file=/etc/nginx/nginx.conf
if ! grep -q 'ttf|woff' $file; then
	commentS 'gif\|ico'
	string=$( cat <<'EOF'
        location ~* (.+)\.(?:\d+)\.(js|css|png|jpg|jpeg|gif|ico|svg|ttf|woff)$ {
EOF
)
	appendS 'gif\|ico'
	nginx -s reload
fi
