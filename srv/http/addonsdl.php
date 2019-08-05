<?php
$sudo = '/usr/bin/sudo /usr/bin';
if ( isset( $_POST[ 'redis' ] ) ) {
	$redis = new Redis(); 
	$redis->connect( '127.0.0.1' );
	$update = $redis->hGet( 'addons', $_POST[ 'redis' ] );
	echo $update;
} else if ( isset( $_POST[ 'bash' ] ) ) {
	exec( $sudo.'/'.$_POST[ 'bash' ] );
} else if ( isset( $_POST[ 'backup' ] ) ) {  // settings backup only
	$filename = 'rune-'.date( 'Ymd_His' ).'.tar.gz';
	$file = '/srv/http/tmp/'.$filename;
	$cmdlines = 'rm /srv/http/tmp/*'
		."; $sudo/redis-cli save"
		." && $sudo/bsdtar -czpf $file"
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
	echo $exit;
}
