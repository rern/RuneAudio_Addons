<?php
$branch = ( isset( $_POST[ 'branch' ] ) ) ? $_POST[ 'branch' ] : '';

exec( '/usr/bin/sudo /srv/http/addonsdl.sh '.$branch, $output, $exit );

if ( $exit !== 0 ) die( 'failed' );

echo 1;

opcache_reset();
