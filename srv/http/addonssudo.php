<?php
exec('/usr/bin/sudo /srv/http/'.$_POST[ 'file' ].';', $output, $exit);

if ($exit !== 0) die();

echo $output;

opcache_reset();
