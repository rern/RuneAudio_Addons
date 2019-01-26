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

#0temp0
# 20190125 - move addons.php back to /srv/http
sed -i '/addons/ d' /srv/http/index.php
sed -i '/#0addo0/,/#1addo1/ d' /srv/http/index.php.backup &> /dev/null

string=$( cat <<EOF
<script language="javascript">
	location.href = "/addons.php"
</script>
EOF
)
mkdir -p /srv/http/addons
echo $string > /srv/http/addons/index.php

alias=temp
file=/etc/nginx/nginx.conf
sed -i '/#0temp0/,/#1temp1/ d' $file

string=$( cat <<EOF
        location /addons {
            alias  /var/www/addons;
            index  index.php;
        }
EOF
)
insertS 'location .pub'

restartnginx
alias=addo
#1temp1

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

# disable OPcache
file=/etc/php/conf.d/opcache.ini
grep -q 'enable=0' $file && exit

title -nt "$info Disable PHP OPcache"
redis-cli set opcache 0 &> /dev/null

sed -i 's/opcache.enable=.*/opcache.enable=0/' $file
systemctl restart php-fpm
