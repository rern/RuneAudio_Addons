<?php
if ( isset( $_POST[ 'redis' ] ) ) {
	$redis = new Redis(); 
	$redis->pconnect( '127.0.0.1' );

	$update = $redis->hGet( 'addons', $_POST[ 'redis' ] );
	echo $update;
	die();
}

$branch = ( isset( $_GET[ 'branch' ] ) ) ? $_GET[ 'branch' ] : '';

exec( '/usr/bin/sudo /srv/http/addonsdl.sh '.$branch, $output, $exit );

// clear cache must be before echo
opcache_reset();

echo $exit;
