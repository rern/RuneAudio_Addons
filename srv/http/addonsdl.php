<?php
$branch = ( isset( $_GET[ 'branch' ] ) ) ? $_GET[ 'branch' ] : '';

exec( '/srv/http/addonsdl.sh '.$branch, $output, $exit );

// clear cache must be before echo
opcache_reset();

echo $exit;
