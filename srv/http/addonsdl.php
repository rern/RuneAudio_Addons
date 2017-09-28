<?php
exec('/usr/bin/sudo /srv/http/addonsdl.sh;', $output, $exit);

if ($exit === 0) { // bash exit 0 = success
	opcache_reset();
	echo 1;
}
