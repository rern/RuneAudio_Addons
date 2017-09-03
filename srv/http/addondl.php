<?php
exec(
	'wget -qN https://github.com/rern/RuneAudio_Addons/raw/master/srv/http/addonslist.php -O /srv/http/addonslist.php',
	$output,
	$exit
);
if ($exit === 0) {	
	opcache_reset();
	echo 1;
}
