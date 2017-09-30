<?php
exec( '/usr/bin/sudo /srv/http/addonsdl.sh;', $output, $exit );

if ( $exit !== 0 ) die( 'failed' );

echo $output;

opcache_reset();
