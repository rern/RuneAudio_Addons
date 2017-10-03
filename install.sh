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
wget -qN https://github.com/rern/RuneAudio_Addons/raw/master/srv/http/addonstitle.sh -P /srv/http
. /srv/http/addonstitle.sh

installstart $1

echo -e "$bar Get files ..."
wgetnc https://github.com/rern/RuneAudio_Addons/archive/master.zip

echo -e "$bar Install new files ..."
rm -rf  /tmp/install
mkdir -p /tmp/install
bsdtar --exclude='.*' --exclude='*.md' -xvf master.zip --strip 1 -C /tmp/install

rm master.zip /tmp/install/* &> /dev/null
chown -R http:http /tmp/install/srv
chmod -R 755 /tmp/install

mv -f /tmp/install/* /
rm -rf /tmp/install

# modify files #######################################
echo -e "$bar Modify files ..."

file=/srv/http/app/templates/header.php
if ! grep -q 'id="addons"' $file; then
	echo $file
	sed -i $'/poweroff-modal/ i\
            <li style="cursor: pointer;"><a id="addons"><i class="fa fa-cubes"></i> Addons</a></li>
	' $file
fi

file=/srv/http/app/templates/footer.php
echo $file
! grep -q 'addons.js' $file &&
echo '<script src="<?=$this->asset('"'"'/js/addonsmenu.js'"'"')?>"></script>' >> $file

# set sudo no password #######################################
echo 'http ALL=NOPASSWD: ALL' > /etc/sudoers.d/http
[[ $(stat -c %a /usr/bin/sudo) != 4755 ]] && chmod 4755 /usr/bin/sudo

installfinish $1

# 'addo' has php variable as 'version' in addonslist.php
version=$( grep -m 1 '^$addonsversion =' /srv/http/addonslist.php | cut -d "'" -f 2 )
redis-cli hset addons addo $version &> /dev/null

if [[ -t 1 ]]; then # for initial install via ssh terminal
	title -nt "$info Refresh browser and go to Menu > Addons."
	clearcache
fi
