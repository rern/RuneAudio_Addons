#!/bin/bash

alias=addo
title='Addons Menu'
# version=number - get from /changelog.md

rm $0

# import heading function
wget -qN https://github.com/rern/title_script/raw/master/title.sh; . title.sh; rm title.sh

if [[ -e /usr/local/bin/uninstall_$alias.sh ]]; then
    echo -e "$info $title already installed."
    exit
fi

[[ $1 != u ]] && title -l = "$bar Install $title ..."

echo -e "$bar Get files ..."
wgetnc https://github.com/rern/RuneAudio_Addons/archive/master.zip

echo -e "$bar Install new files ..."
rm -rf  /tmp/install
mkdir -p /tmp/install
bsdtar -xf master.zip --strip 1 -C /tmp/install

version=$( grep '^## ' /tmp/install/changelog.md | head -1 | cut -d ' ' -f 2 )
mv /tmp/install/changelog.md /tmp/install/srv/http

rm master.zip /tmp/install/* &> /dev/null
chown -R http:http /tmp/install/srv
chmod -R 755 /tmp/install

cp -rp /tmp/install/* /
rm -r /tmp/install

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
if ! grep -q 'addons.js' $file; then
	echo $file
	echo '<script src="<?=$this->asset('"'"'/js/addons.js'"'"')?>"></script>' >> $file
fi

# set sudo no password #######################################
echo 'http ALL=NOPASSWD: ALL' > /etc/sudoers.d/http
[[ $(stat -c %a /usr/bin/sudo) != 4755 ]] && chmod 4755 /usr/bin/sudo

redis-cli hset addons $alias $version &> /dev/null

if [[ $1 != u ]]; then
	title -l = "$bar $title installed successfully."
	[[ -t 1 ]] && echo 'Uninstall: uninstall_$alias.sh'
	title -nt "$info Refresh browser and go to Menu > Addons."
else
	title -l = "$bar $title updated successfully."
fi

[[ -t 1 ]] && clearcache
