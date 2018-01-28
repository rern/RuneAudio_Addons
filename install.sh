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


# change version number in RuneAudio_Addons/srv/http/addonslist.php

alias=addo

. /srv/http/addonstitle.sh

installstart $@

# temp fix - missing 'branch=master' in addonsdl.sh
[[ $1 == '-b' ]] && branch=master
# temp fix
sed -i '/old_renderMSG/,/}/ d' /srv/http/assets/js/custom.js &> /dev/null

getinstallzip

if [[ $( redis-cli get release ) == 0.4b ]]; then
    rm -r /srv/http/assets/default
	mv /srv/http/assets/default{04,}
else
    rm -r /srv/http/assets/default04
fi

# modify files #######################################
echo -e "$bar Modify files ..."

file=/srv/http/app/templates/header.php
echo $file
# remove lines for menu ready install, silver bullet
sed -i '/addonsinfo.css\|id="addons"/ d' $file
sed -i -e '/runeui.css/ a\
    <link rel="stylesheet" href="<?=$this->asset('"'"'/css/addonsinfo.css'"'"')?>">
' -e '/poweroff-modal/ i\
            <li style="cursor: pointer;"><a id="addons"><i class="fa fa-cubes"></i> Addons</a></li>
' $file

file=/srv/http/app/templates/footer.php
echo $file
# remove lines for menu ready install, silver bullet
sed -i '/addonsinfo.js\|addonsmenu.js/ d' $file
# remove trailing blank lines
sed -i -e :a -e '/^\n*$/{$d;N;};/\n$/ba
' -e '$ a\
<script src="<?=$this->asset('"'"'/js/vendor/hammer.min.js'"'"')?>"></script>\
<script src="<?=$this->asset('"'"'/js/addonsinfo.js'"'"')?>"></script>\
<script src="<?=$this->asset('"'"'/js/addonsmenu.js'"'"')?>"></script>
' $file

# set sudo no password #######################################
echo 'http ALL=NOPASSWD: ALL' > /etc/sudoers.d/http
chmod 4755 /usr/bin/sudo

# refresh from dummy to actual 'addonslist.php' before 'installfinish' get 'version'
addonslist=$( sed -n "/'$alias'/,/^),/p" /srv/http/addonslist.php )

installfinish $@

if [[ -t 1 ]]; then
	clearcache
	title -nt "$info Refresh browser and go to Menu > Addons."
fi

