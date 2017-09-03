<?php
exec(
	'wget -qN https://github.com/rern/RuneAudio_Addons/raw/master/srv/http/addons.php -O /srv/http/addons.php',
	$output,
	$exit
);
if ($exit === 0) {	
	opcache_reset();
	echo 1;
}
