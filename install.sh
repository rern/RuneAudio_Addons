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

getinstallzip

. /srv/http/addonsedit.sh # available after getinstallzip

echo -e "$bar Modify files ..."

#----------------------------------------------------------------------------------
file=/srv/http/index.php
echo $file

string=$( cat <<'EOF'
    'addons',
EOF
)
append 'controllers = array'
#----------------------------------------------------------------------------------
file=/srv/http/app/templates/header.php
echo $file

[[ -e $file.backup ]] && file=$file.backup

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
appendH 'href="#poweroff-modal"'

string=$( cat <<'EOF'
<?php endif ?>
EOF
)
appendH '$'
#----------------------------------------------------------------------------------
file=/srv/http/app/templates/footer.php
echo $file

[[ -e $file.backup ]] && file=$file.backup

string=$( cat <<'EOF'
<input id="addonswoff" type="hidden" value="<?=$this->asset('/fonts/addons.woff')?>">
<input id="addonsttf" type="hidden" value="<?=$this->asset('/fonts/addons.ttf')?>">
<input id="addonsinfocss" type="hidden" value="<?=$this->asset('/css/addonsinfo.css')?>">
<input id="addonscss" type="hidden" value="<?=$this->asset('/css/addons.css')?>">
<input id="addonsinfojs" type="hidden" value="<?=$this->asset('/js/addonsinfo.js')?>">
EOF
)
insertH 'jquery-2.1.0.min.js'

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

# udaclist
acards=$( redis-cli hgetall acards )
readarray -t cards <<<"$acards"
i=0
for card in "${cards[@]}"; do
	if (( i % 2 )); then
		extlabel=$( echo "$card" | awk -F '","hwplatformid'  '{print $1}' | awk -F 'extlabel":"' '{print $2}' )
		redis-cli hset udaclist "$key" "$extlabel" &> /dev/null
	else
		key="$card"
	fi
	(( i++ ))
done

notifysec=$( grep notify.delay /srv/http/assets/js/runeui.js | tr -dc "1-9" )
grep -q 'use_cursor yes' /root/.xinitrc && pointer=1 || pointer=0
if ! grep '^chromium' /root/.xinitrc; then
	zoomlevel=$( grep '^zoom-level' /root/.config/midori/config | sed 's/zoom-level=//' )
else
	zoomlevel=$( grep '^chromium' /root/.xinitrc | sed 's/.*force-device-scale-factor=\(.*\)/\1/' )
fi
redis-cli hmset settings notify "$notifysec" zoom "$zoomlevel" pointer "$pointer" &>/dev/null
redis-cli hset addons update 0 &>/dev/null

# for backup file upload
dir=/srv/http/tmp
mkdir -p $dir
chown http:http $dir
chmod 777 $dir

installfinish $@

file=/etc/nginx/nginx.conf
if ! grep -q 'woff|ttf' $file; then
	commentS 'gif\|ico'
	string=$( cat <<'EOF'
        location ~* (.+)\.(?:\d+)\.(js|css|png|jpg|jpeg|gif|ico|svg|woff|ttf)$ {
EOF
)
	appendS 'gif\|ico'
	restartnginx
fi

clearcache
