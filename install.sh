#!/bin/bash

alias=addo

# for testing branch
branch=master
(( $# != 0 )) && [[ $1 != u ]] && branch=$1

if [[ ! -e /srv/http/addonslist.php ]]; then
# dummy for 'installstart': sed -n "/'$alias'/,/^),/p" /srv/http/addonslist.php
	echo "
		'alias'   => 'addo',
		'title'   => 'Addons Menu',
),
	" > /srv/http/addonslist.php
fi

# import heading function
wget -qN https://github.com/rern/RuneAudio_Addons/raw/$branch/srv/http/addonstitle.sh -P /srv/http
. /srv/http/addonstitle.sh

installstart $1

echo -e "$bar Get files ..."
wgetnc https://github.com/rern/RuneAudio_Addons/archive/$branch.zip

echo -e "$bar Install new files ..."
rm -rf  /tmp/install
mkdir -p /tmp/install
bsdtar --exclude='.*' --exclude='*.md' -xvf $branch.zip --strip 1 -C /tmp/install

rm $branch.zip /tmp/install/* &> /dev/null
chown -R http:http /tmp/install/srv
chmod -R 755 /tmp/install

cp -rfp /tmp/install/* /
rm -rf /tmp/install

# modify files #######################################
echo -e "$bar Modify files ..."

file=/srv/http/app/templates/header.php
echo $file
sed -i -e '/addonsinfo.css/ d
' -e '/id="addons"/ d
' -e $'/runeui.css/ a\
    <link rel="stylesheet" href="<?=$this->asset(\'/css/addonsinfo.css\')?>">
' -e $'/poweroff-modal/ i\
            <li style="cursor: pointer;"><a id="addons"><i class="fa fa-cubes"></i> Addons</a></li>
' $file

file=/srv/http/app/templates/footer.php
echo $file

if ! grep -q 'hammer.min.js' $file; then
	echo '<script src="<?=$this->asset('"'"'/js/vendor/hammer.min.js'"'"')?>"></script>' >> $file
fi
sed -i '/addonsmenu.js\|addonsinfo.js/ d' $file
echo '<script src="<?=$this->asset('"'"'/js/addonsinfo.js'"'"')?>"></script>
<script src="<?=$this->asset('"'"'/js/addonsmenu.js'"'"')?>"></script>' >> $file

# set sudo no password #######################################
echo 'http ALL=NOPASSWD: ALL' > /etc/sudoers.d/http
[[ $(stat -c %a /usr/bin/sudo) != 4755 ]] && chmod 4755 /usr/bin/sudo

# refresh from dummy to actual 'addonslist.php' before 'installfinish' get 'version'
addonslist=$( sed -n "/'$alias'/,/^),/p" /srv/http/addonslist.php )

installfinish $1

if [[ -t 1 ]]; then
	clearcache
	title -nt "$info Refresh browser and go to Menu > Addons."
fi

