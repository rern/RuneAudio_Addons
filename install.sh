#!/bin/bash

# install.sh

# addons menu for web based installation

rm $0

# import heading function
wget -qN https://github.com/rern/title_script/raw/master/title.sh; . title.sh; rm title.sh

title -l = "$bar Install Addons menu ..."

if grep -q 'Addons' /srv/http/app/templates/header.php; then
    echo -e "$info Already installed."
    exit
fi

wgetnc https://github.com/rern/RuneAudio_Addons/raw/master/uninstall_addo.sh -P /usr/local/bin
chmod +x /srv/http/runbash.sh /usr/local/bin/uninstall_addo.sh

wgetnc https://github.com/rern/RuneAudio_Addons/raw/master/addonbash.php

sed -e '/poweroff-modal/ i\
            <li><a href id="addons"><i class="fa fa-cubes"></i> Addons</a></li>
' /srv/http/app/templates/header.php

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
' >> /srv/http/app/templates/footer.php

echo '<?php
$result = exec('wget -qN https://github.com/rern/RuneAudio_Addons/raw/master/addons.php -O /srv/http/addons.php; echo $?');
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
