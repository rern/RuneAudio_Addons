<?php
if ( isset( $_POST[ 'redis' ] ) ) {
	$redis = new Redis(); 
	$redis->pconnect( '127.0.0.1' );
	$update = $redis->hGet( 'addons', $_POST[ 'redis' ] );
	echo $update;
} else if ( isset( $_POST[ 'backup' ] ) ) {  // settings backup only
	$filename = 'Rbackup-'.date( 'ymd_His' ).'.tar.gz';
	$file = '/srv/http/tmp/'.$filename;
	$cmdlines = 'rm /srv/http/tmp/*'
		.'; /usr/bin/sudo /usr/bin/redis-cli save'
		.' && /usr/bin/sudo /usr/bin/bsdtar -czpf '.$file
			.' --exclude /etc/netctl/examples'
			.' /etc/netctl'
			.' /mnt/MPD/Webradio'
			.' /var/lib/redis/rune.rdb'
			.' /var/lib/mpd'
			.' /etc/mpd.conf'
			.' /etc/mpdscribble.conf'
			.' /etc/spop'
		.'; echo $?'
	;
	$result = exec( $cmdlines );
	echo $result == 0 ? '/tmp/'.$filename : false;
} else if ( isset( $_FILES[ 'file' ] ) ) {  // settings restore  only
	$filename = $_FILES[ 'file' ][ 'name' ];
	$filetmp = $_FILES[ 'file' ][ 'tmp_name' ];
	$filedest = '/srv/http/tmp/'.$filename;
	$filesize = filesize( $filetmp );
	if ( !$filesize ) die();

	echo move_uploaded_file( $filetmp, $filedest );
} else {
	$branch = ( isset( $_GET[ 'branch' ] ) ) ? $_GET[ 'branch' ] : '';
	exec( '/usr/bin/sudo /srv/http/addonsdl.sh '.$branch, $output, $exit );
	// clear cache must be before echo
	opcache_reset();
	echo $exit;
}
