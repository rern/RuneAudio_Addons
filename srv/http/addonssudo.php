<?php
exec('/srv/http/'.$_POST[ 'file' ]';', $output, $exit);

if ($exit === 0) {
	opcache_reset();
	echo 1;
}
