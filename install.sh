#!/bin/bash

alias=addo

if [[ ! -e /srv/http/addonslist.php ]]; then
# for installstart
	echo "
		'alias'   => 'addo',
		'title'   => 'Addons Menu',
		'version' => '1',
	" > /srv/http/addonslist.php
fi

# import heading function
wget -qN https://github.com/rern/RuneAudio_Addons/raw/master/srv/http/title.sh; . title.sh; rm title.sh

installstart $1

echo -e "$bar Get files ..."
wgetnc https://github.com/rern/RuneAudio_Addons/archive/master.zip

echo -e "$bar Install new files ..."
rm -rf  /tmp/install
mkdir -p /tmp/install
bsdtar -xf master.zip --strip 1 -C /tmp/install

mv /tmp/install/changelog.md /tmp/install/srv/http

rm master.zip /tmp/install/* &> /dev/null
chown -R http:http /tmp/install/srv
chmod -R 755 /tmp/install

cp -rp /tmp/install/* /
rm -r /tmp/install

version=$( grep '^## ' /srv/http/changelog.md | head -1 | cut -d ' ' -f 2 )
sed -i "s/\$addonsversion/'$version'/" /srv/http/addonslist.php

[[ $1 == u ]] && /srv/http/addonsdl.sh u # 'u' skip redownload, changelog to addonslog.php on update

# modify files #######################################
echo -e "$bar Modify files ..."

file=/srv/http/app/templates/header.php
if ! grep -q 'id="addons"' $file; then
	echo $file
	sed -i '/poweroff-modal/ i\
            <li style="cursor: pointer;"><a id="addons"><i class="fa fa-cubes"></i> Addons</a></li>
	' $file
fi

file=/srv/http/app/templates/footer.php
echo $file
! grep -q 'addons.js' $file &&
echo '<script src="<?=$this->asset('"'"'/js/addonsmenu.js'"'"')?>"></script>' >> $file
! grep -q 'hammer.min.js' $file && 
echo '<script src="<?=$this->asset('"'"'/js/vendor/hammer.min.js'"'"')?>"></script>' >> $file

# set sudo no password #######################################
echo 'http ALL=NOPASSWD: ALL' > /etc/sudoers.d/http
[[ $(stat -c %a /usr/bin/sudo) != 4755 ]] && chmod 4755 /usr/bin/sudo

installfinish $1

title -nt "$info Refresh browser and go to Menu > Addons."

[[ -t 1 ]] && clearcache
