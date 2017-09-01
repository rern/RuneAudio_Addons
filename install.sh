#!/bin/bash

# install.sh

# addons menu for web based installation

rm $0

# import heading function
wget -qN https://github.com/rern/title_script/raw/master/title.sh; . title.sh; rm title.sh

if grep -q 'Addons' /srv/http/app/templates/header.php; then
    echo -e "$info Already installed."
    exit
fi

# install RuneAudio Addons #######################################
title -l = "$bar Install Addons menu ..."
echo -e "$bar Get files ..."
wgetnc https://github.com/rern/RuneAudio_Addons/archive/master.zip

echo -e "$bar Install new files ..."
mkdir -p /tmp/install
bsdtar -xf master.zip --strip 1 -C /tmp/install
rm master.zip /tmp/install/{.*,*.md,install.sh} &> /dev/null
chmod 755 /tmp/install/root/* /tmp/install/usr/local/bin/uninstall*

cp -r /tmp/install/* /
rm -r /tmp/install

# modify files #######################################
echo -e "$bar Modify files ..."

header=/srv/http/app/templates/header.php
echo $header
sed -e '/poweroff-modal/ i\
            <li><a href id="addons"><i class="fa fa-cubes"></i> Addons</a></li>
' $header

footer=/srv/http/app/templates/footer.php
echo $footer
echo '
<script><!--addons-->
$("#addons").click(function() {
	$.get("addondl.php", function(data) {
		if (data == 0) {
			window.location.href = "addons.php";
		} else {
			alert("Addons server not reachable.");
		}
	});
});
</script>
' >> $footer

echo '<?php
$result = exec('wget -qN https://github.com/rern/RuneAudio_Addons/raw/master/srv/http/addons.php -O /srv/http/addons.php; echo $?');
if ($result == 0) exec('/usr/bin/sudo /usr/bin/systemctl reload php-fpm &; sleep 1');
echo $result;
' > /srv/http/addondl.php

# refresh #######################################
echo -e "$bar Clear PHP OPcache ..."
systemctl reload php-fpm
echo
if pgrep midori >/dev/null; then
	killall midori
	sleep 1
	xinit &>/dev/null &
	echo 'Local browser restarted.'
fi

redis-cli hset addons main 1 &> /dev/null

timestop
title -l = "$bar Addons menu installed successfully."
echo 'Uninstall: uninstall_addo.sh'
title -nt "$info Refresh browser and go to Menu > Addons."
