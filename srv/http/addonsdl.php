<?php
$branch = ( isset( $_GET[ 'branch' ] ) ) ? $_GET[ 'branch' ] : '';

exec( '/usr/bin/sudo /srv/http/addonsdl.sh '.$branch, $output, $exit );

opcache_reset();

echo $exit;
