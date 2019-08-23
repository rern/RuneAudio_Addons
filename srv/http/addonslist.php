<?php
$redis = new Redis();
$redis->connect( '127.0.0.1' );
$runee1 = file_exists( '/srv/http/startup.sh' );
// temp
if ( $runee1 && !$redis->hExists( 'addons', 'rre1' ) ) $redis->hSet( 'addons', 'rre1', 20190822 );

$rune05 = $redis->get( 'release' ) === '0.5';
$runee1 = file_exists( '/srv/http/startup.sh' );
$redisaddons = $redis->hGetAll( 'addons' );
$enha = $redisaddons[ 'enha' ];
// checked items
$enhacheck = array();
if ( $redis->hGet( 'mpdconf', 'ffmpeg' ) === 'yes' ) $enhacheck[] = 0;
if ( $redis->hGet( 'AccessPoint', 'enabled' ) == 1 ) $enhacheck[] = 1;
if ( ( $rune05 && $redis->hget( 'local_browser', 'enable' ) == 1 )
		|| $redis->get( 'local_browser' ) == 1
) {
	$enhacheck[] = 2;
	$localbrowser = 1;
}
if ( $redis->hGet( 'airplay', 'enable' ) == 1 ) $enhacheck[] = 3;
if ( $redis->hGet( 'dlna', 'enable' ) == 1 ) $enhacheck[] = 4;

$acards = $redis->hGetAll( 'acards' );
$udaclist = array(); // fix: undefined $udaclist error( empty $acards)
foreach( $acards as $key => $value ) {
	$value = json_decode( $value );
	$name = $value->extlabel ?: $value->name;
	$udaclist[ $name ] = $key;
}
ksort( $udaclist );

///////////////////////////////////////////////////////////////
$addons = array(

'addo' => array(
	'title'       => 'Addons',
	'version'     => '20190822',
	'revision'    => 'Add RuneAudio+R e1 support'
					.'<br>...'
					.'<br>Pre-install common packages, <w>glibc</w> and <w>openssl-cryptodev</w>...'
					.'<br>Rank mirror package servers before install/upgrade',
	'maintainer'  => 'r e r n',
	'description' => 'This Addons main page.',
	'thumbnail'   => '/img/addons/thumbaddo.png',
	'sourcecode'  => 'https://github.com/rern/RuneAudio_Addons',
	'installurl'  => 'https://github.com/rern/RuneAudio_Addons/raw/master/install.sh',
),
'rre1' => array(
	'title'       => 'RuneAudio <i class="fa fa-addons"></i> e1',
	'version'     => '20190822',
	'revision'    => 'Minor improvements',
	'maintainer'  => 'r e r n',
	'description' => 'Updates for RuneAudio <i class="fa fa-addons"></i> e1.',
	'buttonlabel' => 'Update',
	'nouninstall' => 1,
	'sourcecode'  => 'https://github.com/rern/RuneAudio-Re1',
	'installurl'  => 'https://github.com/rern/RuneAudio-Re1/raw/master/update.sh',
	'hide'        => !$runee1
),
'enha' => array(
	'title'       => 'RuneUI Enhancement **',
	'version'     => '20190709',
	'revision'    => 'General improvements'
					.'<br>...'
					.'<br>Fix replace/remove/save coverart bug'
					.'<br>Fix volume not update on other devices'
					.'<br>Fix albums/artists with % or # not show in Browse By CoverArt'
					.'<br>Much faster loading long saved playlists'
					.'<br>...'
					.'<br>Fix add webradio bug'
					.'<br>Fix playlists conversion during install'
					.'<br>Fix playlists with 1,000 tracks not show'
					.'<br>Fix saved playlists with 1,000 tracks not load'
					.'<br>Reinstate Last.fm as primary source for online coverarts',
	'maintainer'  => 'r e r n',
	'description' => 'Lots of new features. More <w>minimalism</w> and more <w>fluid</w> layout.',
	'thumbnail'   => '/img/addons/thumbenha.gif',
	'sourcecode'  => 'https://github.com/rern/RuneUI_enhancement',
	'installurl'  => 'https://github.com/rern/RuneUI_enhancement/raw/master/install.sh',
	'option'      => array(
		'radio'     => array(
			'message' => 'Set <w>zoom level</w> for display directly connect to RPi.'
						.'<br>(This can be changed later.)'
						.'<br>Local screen size:',
			'list'    => array(
				'Width less than 800px: 0.7' => 0.7,
				'HD - 1280px: 1.2'           => 1.2,
				'Full HD - 1920px: 1.5'      => 1.5,
				'Full HD - no buttons: 1.8'  => 1.8,
				'Custom'                     => '?'
			),
			'checked' => 1.8
		),
		'checkbox'  => array(
			'message' => 'Should be unchecked if not used:',
			'list'    => array(
				'<gr>Enable</gr> AAC/ALAC'      => 1,
				'<gr>Enable</gr> Access point'  => 1,
				'<gr>Enable</gr> Local browser' => 1,
				'<gr>Enable</gr> AirPlay'       => 1,
				'<gr>Enable</gr> UPnP/DLNA'     => 1
			),
			'checked' => $enhacheck
		),
	),
	'hide'        => $runee1
),
'extr' => array(
	'title'       => 'RuneAudio <i class="fa fa-addons"></i> e1 - Restore Extra Directories',
	'maintainer'  => 'r e r n',
	'description' => 'Restore extra directories: bookmarks, coverarts, lyrics, playlists and webradios.',
	'buttonlabel' => 'Restore',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/raw/master/extradir',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/extradir/install.sh',
	'hide'        => !$enha || $runee1
),
'rrre' => array(
	'title'       => 'RuneAudio <i class="fa fa-addons"></i> e1 Reset',
	'maintainer'  => 'r e r n',
	'description' => 'Reset RuneAudio <i class="fa fa-addons"></i> e1 for initial setup.',
	'buttonlabel' => 'Reset',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/raw/master/RuneAudio%2BRuneUIe.img/',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/RuneAudio%2BRuneUIe.img/setup.sh',
	'hide'        => !$runee1
),
'cove' => array(
	'title'       => 'Browse By CoverArt Thumbnails',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/coverarts/scan.sh',
	'hide'        => 1,
),
'aria' => array(
	'title'       => 'Aria2 *',
	'version'     => '20170901',
	'needspace'   => 15,
	'revision'    => 'Initial release',
	'maintainer'  => 'r e r n',
	'description' => 'Download utility that supports HTTP(S), FTP, BitTorrent, and Metalink.'
					.'<br>Pre-configured and ready to use.',
	'thumbnail'   => '/img/addons/thumbaria.png',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/aria2',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/aria2/install.sh',
	'option'      => array(
		'yesno'     => array(
			'message' => 'Start <w>Aria2</w> on system startup?'
		)
	),
),
'dual' => array(
	'title'       => 'Dual Boot: RuneAudio + OSMC *',
	'maintainer'  => 'r e r n',
	'description' => 'Best of Audio Distro - <w>RuneAudio</w> 0.3 + Addons Menu ready (ArchLinux MPD)'
					.'<br>Best of Video Distro - <w>OSMC</w> 2017-08-1 (Raspbian Kodi)'
					.'<br>Best of Dual Boot - <w>NOOBS</w> 2.4',
	'thumbnail'   => '/img/addons/thumbdual.gif',
	'buttonlabel' => 'Link',
	'installurl'  => 'http://www.runeaudio.com/forum/dual-boot-noobs-rune-osmc-pi2-pi3-t3822.html',
),
'mpdu' => array(
	'title'       => 'MPD Upgrade',
	'needspace'   => 300,
	'maintainer'  => 'r e r n',
	'description' => 'Upgrade MPD to latest version, 0.21.9 as of 20190520:'
					.'<br>Fix conflicts, missing lib symlinks, missing packages'
					.'<br>But local browser <w>Midori</w>, if enabled, needs to be replaced with Chromium.',
	'thumbnail'   => '/img/addons/thumbmpdu.png',
	'buttonlabel' => 'Upgrade',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/mpd',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/mpd/install.sh',
),
'rank' => array(
	'title'       => 'Rank Mirror Package Servers',
	'maintainer'  => 'r e r n',
	'description' => 'Fix package download errors caused by unreachable servers.'
					.'<br>Rank mirror package servers by download speed and latency.',
	'thumbnail'   => '/img/addons/thumbrank.png',
	'buttonlabel' => 'Rank',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/rankmirrors',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/rankmirrors/rankmirrors.sh',
	'option'      => array(
		'radio'     => array(
			'message' => 'Download test for each server(seconds):',
			'list'    => array(
				'3'  => 3,
				'4'  => 4,
				'5'  => 5,
				'6'  => 6,
				'7'  => 7,
				'8'  => 8,
				'9'  => 9,
				'10' => 10,
			),
			'checked' => 3,
		),
	),
),
'gpio' => array(
	'title'       => 'RuneUI GPIO *',
	'version'     => '20190824',
	'needspace'   => 5,
	'revision'    => 'Support RuneAudio+R e1'
					.'<br>...'
					.'<br>Link setting location to common directory'
					.'<br>...'
					.'<br>Improve notifications - show devices name',
	'maintainer'  => 'r e r n',
	'description' => 'GPIO-connected relay module control for power on / off audio equipments.',
	'thumbnail'   => '/img/addons/thumbgpio.gif',
	'sourcecode'  => 'https://github.com/rern/RuneUI_GPIO',
	'installurl'  => 'https://github.com/rern/RuneUI_GPIO/raw/master/install.sh',
	'hide'        => !$runee1
),
'tran' => array(
	'title'       => 'Transmission *',
	'version'     => '20180715',
	'needspace'   => 9,
	'revision'    => 'Fix bugs by reverting back to custom compiled package.'
					.'<br>...'
					.'<br>Update alternateive WebUI source',
	'maintainer'  => 'r e r n',
	'description' => 'Fast, easy, and free BitTorrent client. Pre-configured and ready to use.',
	'thumbnail'   => '/img/addons/thumbtran.png',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/transmission',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/transmission/install.sh',
	'option'      => array(
		'password'  => array(
			'message' => 'Password for user <w>root</w> (blank = no password):',
			'label'   => 'Password',
		),
		'checkbox'  => array(
			'message' => '',
			'list'    => array(
				'Install <w>WebUI</w> alternative?'            => 1,
				'Start <w>Transmission</w> on system startup?' => 1
			),
			'checked' => array( 0, 1 )
		),
	),
),
'back' => array(
	'title'       => 'Settings+Databases Backup',
	'revision'    => 'Initial release',
	'maintainer'  => 'r e r n',
	'description' => 'Backup all RuneAudio <w>settings and databases</w>.',
	'thumbnail'   => '/img/addons/thumbback.png',
	'buttonlabel' => 'Backup',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/backup-restore',
	'option'      => array(
		'confirm'   => 'Backup all RuneAudio <w>settings and databases</w>?',
	),
	'hide'        => $runee1
),

);
