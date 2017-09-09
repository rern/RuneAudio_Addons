<?php
exec('/srv/http/addonsdl.sh;', $output, $exit);

if ($exit === 0) {
	opcache_reset();
	echo 1;
}
