<?php
exec( '/usr/bin/sudo /srv/http/addonsdl.sh;', $output, $exit );

if ( $exit !== 0 ) die( 'failed' );

echo 1;

opcache_reset();
