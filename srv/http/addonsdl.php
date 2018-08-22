<?php
if ( isset( $_POST[ 'redis' ] ) ) {
	$redis = new Redis(); 
	$redis->pconnect( '127.0.0.1' );
	$update = $redis->hGet( 'addons', $_POST[ 'redis' ] );
	echo $update;
} else if ( isset( $_FILES[ 'file' ] ) ) {
	$filename = $_FILES[ 'file' ][ 'name' ];
	$filetmp = $_FILES[ 'file' ][ 'tmp_name' ];
	$filesize = filesize( $filetmp );
	
	if ( !$filesize ) die();

	echo move_uploaded_file( $filetmp, '/srv/http/tmp/'.$filename );
} else {
	$branch = ( isset( $_GET[ 'branch' ] ) ) ? $_GET[ 'branch' ] : '';
	exec( '/usr/bin/sudo /srv/http/addonsdl.sh '.$branch, $output, $exit );
	// clear cache must be before echo
	opcache_reset();
	echo $exit;
}
