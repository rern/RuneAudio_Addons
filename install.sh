#!/bin/bash

# $1-branch ; $2-branch flag '-b' (syntax for all addons in addonsdl.sh)
# for '$branch' before 'addonstitle.sh' exist ( ./install UPDATE -b : branch=UPADTE )
if [[ $# == 2 && $2 == '-b' ]]; then
	branch=$1
else
	branch=master
fi

# for 'installstart' before 'addonslist.php' exist
if [[ ! -e /srv/http/addonslist.php ]]; then
	echo "
		'alias'      => 'addo',
		'title'      => 'Addons Menu',
		'installurl' => 'https://github.com/rern/RuneAudio_Addons/raw/master/install.sh',
),
	" > /srv/http/addonslist.php
fi

wget -qN --no-check-certificate https://github.com/rern/RuneAudio_Addons/raw/$branch/srv/http/addonstitle.sh -P /srv/http
wget -qN --no-check-certificate https://github.com/rern/RuneAudio_Addons/raw/$branch/srv/http/addonsedit.sh -P /srv/http

# change version number in RuneAudio_Addons/srv/http/addonslist.php

alias=addo

. /srv/http/addonstitle.sh
. /srv/http/addonsedit.sh

installstart $@

#0temp0
# to be removed after switch from hammer.js
#sed -i '/hammer.min.js\|propagating.js/ d' /srv/http/app/templates/footer.php
#1temp1

getinstallzip

echo -e "$bar Modify files ..."
#----------------------------------------------------------------------------------
file=/srv/http/app/templates/header.php
echo $file

string=$( cat <<'EOF'
    <link rel="stylesheet" href="<?=$this->asset('/css/addonsinfo.css')?>">
EOF
)
appendH 'runeui.css'

string=$( cat <<'EOF'
            <li><a id="addons"><i class="fa"></i> Addons</a></li>
EOF
)
appendH -n +1 'logout.php'
#----------------------------------------------------------------------------------
file=/srv/http/app/templates/footer.php
echo $file

string=$( cat <<'EOF'
<script src="<?=$this->asset('/js/vendor/hammer.min.js')?>"></script>
<script src="<?=$this->asset('/js/vendor/propagating.js')?>"></script>
<script src="<?=$this->asset('/js/addonsinfo.js')?>"></script>
<script src="<?=$this->asset('/js/addonsmenu.js')?>"></script>
EOF
)
appendH 'code.jquery.com'

# separate to keep out of uninstall
if ! grep 'jquery.mobile.custom.min.js' $file; then
	string=$( cat <<'EOF'
<script src="<?=$this->asset('/js/vendor/jquery.mobile.custom.min.js')?>"></script>
EOF
)
	sed -i "$ a$string" $file
fi
#----------------------------------------------------------------------------------
file=/etc/nginx/nginx.conf
if ! grep -q 'ico|svg' $file; then
	echo $file
	commentS 'gif\|ico'
	string=$( cat <<'EOF'
        location ~* (.+)\.(?:\d+)\.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
EOF
)
	appendS 'gif\|ico'
	
	svg=0
else
	svg=1
fi

# set sudo no password
echo 'http ALL=NOPASSWD: ALL' > /etc/sudoers.d/http
chmod 4755 /usr/bin/sudo

# update check
file=/etc/systemd/system/addons.service
echo $file
echo '[Unit]
Description=Addons Menu update check
After=network-online.target
[Service]
Type=idle
ExecStart=/srv/http/addonsupdate.sh &
[Install]
WantedBy=multi-user.target
' > $file

crontab -l | { cat; echo '00 01 * * * /srv/http/addonsupdate.sh &'; } | crontab - &> /dev/null
systemctl daemon-reload
systemctl enable addons cronie
systemctl start addons cronie

redis-cli hset addons update 0 &>/dev/null

notifysec=$( grep notify.delay /srv/http/assets/js/runeui.js | tr -dc "1-9" )
if ! grep '^chromium' /root/.xinitrc; then
	zoomlevel=$( grep '^zoom-level' /root/.config/midori/config | sed 's/zoom-level=//' )
	browser=1
else
	zoomlevel=$( grep '^chromium' /root/.xinitrc | sed 's/.*force-device-scale-factor=\(.*\)/\1/' )
	browser=2
fi
redis-cli mset notifysec $notifysec zoomlevel $zoomlevel browser $browser &>/dev/null

# refresh from dummy to actual 'addonslist.php' before 'installfinish' get 'version'
addonslist=$( sed -n "/'$alias'/,/^),/p" /srv/http/addonslist.php )

installfinish $@
title -nt "$info Please clear browser cache."

clearcache

[[ $svg == 0 ]] && restartnginx
