#!/bin/bash

version=20170901

# install.sh

# addons menu for web based installation

rm $0

# import heading function
wget -qN https://github.com/rern/title_script/raw/master/title.sh; . title.sh; rm title.sh

if [[ -e /srv/http/addonbash.php ]]; then
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
chmod 755 /tmp/install/usr/local/bin/uninstall*

cp -r /tmp/install/* /
rm -r /tmp/install

# modify files #######################################
echo -e "$bar Modify files ..."

header=/srv/http/app/templates/header.php
echo $header
sed -i '/poweroff-modal/ i\
            <li><a id="addons" style="cursor: pointer;"><i class="fa fa-cubes"></i> Addons</a></li>
' $header

footer=/srv/http/app/templates/footer.php
echo $footer
echo '<!--addons-->
<script>
$( "#addons" ).click( function() {
	$( "#loader" ).removeClass( "hide" );
	$.get( "addondl.php", function( data ) {
		$( "#loader" ).addClass( "hide" );
		if ( data ) {
			window.location.href = "addons.php";
		} else {
			alert( "Addons server not reachable." );
		}
	});
});
</script>
' >> $footer

echo '<?php
exec("wget -qN https://github.com/rern/RuneAudio_Addons/raw/master/srv/http/addons.php -O /srv/http/addons.php", $output, $result);
if ($result == 1) exec("/usr/bin/sudo /usr/bin/systemctl reload php-fpm; sleep 1");
echo $result;
' > /srv/http/addondl.php

# set permission #######################################
echo 'http ALL=NOPASSWD: ALL' > /etc/sudoers.d/http

redis-cli hset addons main $version &> /dev/null

title -l = "$bar Addons menu installed successfully."
echo 'Uninstall: uninstall_addo.sh'
title -nt "$info Refresh browser and go to Menu > Addons."

# clear opcache and restart local browser #######################################
systemctl reload php-fpm

if pgrep midori > /dev/null; then
	killall midori
	sleep 1
	xinit &> /dev/null &
fi
