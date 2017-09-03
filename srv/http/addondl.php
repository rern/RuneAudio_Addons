<?php
exec('wget -qN https://github.com/rern/RuneAudio_Addons/raw/master/srv/http/addons.php -O /srv/http/addons.php', $output, $exit);
if ($exit === 0) {	
	exec('/usr/bin/sudo /usr/bin/systemctl reload php-fpm; sleep 1');
	echo 1;
} else {
	echo 0;
}
