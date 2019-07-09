<?php
$redis = new Redis();
$redis->connect( '127.0.0.1' );
$rune05 = $redis->get( 'release' ) === '0.5';
$redisaddons = $redis->hGetAll( 'addons' );
$enha = $redisaddons[ 'enha' ];
// checked items
$enhacheck = array();
if ( $redis->hGet( 'mpdconf', 'ffmpeg' ) === 'yes' ) $enhacheck[] = 0;
if ( $redis->hGet( 'AccessPoint', 'enabled' ) == 1 ) $enhacheck[] = 1;
if ( $rune05 ) {
	if ( $redis->hget( 'local_browser', 'enable' ) == 1 ) $enhacheck[] = 2;
} else {
	if ( $redis->get( 'local_browser' ) == 1 ) $enhacheck[] = 2;
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

// samba
if ( exec( '/usr/bin/pacman -Qs mpd-rune' ) ) {
	$optmpd = array(
		'confirm'   => 'Once installed, MPD <w>cannot be downgraded</w>.'
					  .( $rune05 ? '' : '<br>Midori, local browser, must be upgrade as well.' )
	);
} else {
	$optmpd = array(
		'confirm'  => 'Upgrade?'
	);
}
// chromium
if ( file_exists( '/usr/bin/chromium' ) ) {
	$xinitrc = file_get_contents( '/etc/X11/xinit/xinitrc' );
	if ( !preg_match( '/calibrator/', $xinitrc ) ) {
		$chromiumfile = '/etc/X11/xinit/xinitrc';
	} else {
		$chromiumfile = '/etc/X11/xinit/start_chromium.sh';
		$chromiumfile = file_exists( $chromiumfile ) ? $chromiumfile : '/root/.xinitrc';
	}
	$zoom = exec( "/usr/bin/sudo /usr/bin/grep 'chromium --' $chromiumfile | /usr/bin/cut -d'=' -f3" );
	$chromium = 1;
} else {
	$zoom = exec( "/usr/bin/sudo /usr/bin/grep '^zoom' /root/.config/midori/config | /usr/bin/cut -d'=' -f2" );
}
// samba
if ( exec( '/usr/bin/pacman -Qs samba4-rune' ) ) {
	$optsamb = array(
		'password'  => array(
			'message' => '(for connecting to <w>USB root share</w>)'
						.'<br>Password for user <w>root</w> (blank = existing or rune):',
			'label'   => 'Password'
		),
		'skip'      => 'Keep current Samba settings and shares?',
		'wait'      => 'Connect a <w>USB drive</w> before continue.'
					  .'<br>1st drive will be used for shared directories.',
		'text1'     => array(
			'message' => '<w>File Server</w>:',
			'label'   => 'Name',
			'value'   => 'RuneAudio'
		),
		'text2'     => array(
			'message' => '<w>Read-Only</w> directory:',
			'label'   => 'Name',
			'value'   => 'ro'
		),
		'text3'     => array(
			'message' => '<w>Read-Write</w> directory:',
			'label'   => 'Name',
			'value'   => 'rw'
		),
	);
} else {
	$optsamb = array(
		'confirm'  => 'Upgrade?'
	);
}
///////////////////////////////////////////////////////////////
$addons = array(

'addo' => array(
	'title'       => 'Addons',
	'version'     => '20190703',
	'revision'    => 'Minor improvements'
					.'<br>...'
					.'<br>Pre-install common packages, <w>glibc</w> and <w>openssl-cryptodev</w>...'
					.'<br>Rank mirror package servers before install/upgrade',
	'maintainer'  => 'r e r n',
	'description' => 'This Addons main page.',
	'thumbnail'   => '/img/addons/thumbaddo.png',
	'sourcecode'  => 'https://github.com/rern/RuneAudio_Addons',
	'installurl'  => 'https://github.com/rern/RuneAudio_Addons/raw/master/install.sh',
),
'enha' => array(
	'title'       => 'RuneUI Enhancement **',
	'version'     => '20190703',
	'revision'    => 'Fix replace/remove/save coverart bug'
					.'<br>Fix volume not update on other devices'
					.'<br>Fix albums/artists with % or # not show in Browse By CoverArt'
					.'<br>Much faster loading long saved playlists'
					.'<br>'
					.'<br>...'
					.'<br>Fix add webradio bug'
					.'<br>Fix playlists conversion during install'
					.'<br>Fix playlists with 1,000 tracks not show'
					.'<br>Fix saved playlists with 1,000 tracks not load'
					.'<br>Reinstate Last.fm as primary source for online coverarts'
					.'<br>...'
					.'<br>Add color picker to change colors of UI'
					.'<br>Highlight keyword in search result'
					.'<br>Faster saving for online fetched coverarts'
					.'<br>Extend keyboard navigation to menu and context menus',
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
),
'extr' => array(
	'title'       => 'RuneUIe - Restore Extra Directories',
	'maintainer'  => 'r e r n',
	'description' => 'Restore extra directories: bookmarks, coverarts, lyrics, playlists and webradios.',
	'buttonlabel' => 'Restore',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/raw/master/extradir',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/extradir/install.sh',
	'hide'        => !$enha,
),
'cove' => array(
	'title'       => 'Browse By CoverArt Thumbnails',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/coverarts/scan.sh',
	'hide'        => 1,
),
'kid3' => array(
	'title'       => 'RuneUIe Metadata Tag Editor',
	'depend'      => 'enha',
	'needspace'   => 250,
	'revision'    => 'Initial release',
	'maintainer'  => 'r e r n',
	'description' => 'Enable metadata editor feature in context menu.',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/raw/master/kid3-cli',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/kid3-cli/install.sh',
),
'pers' => array(
	'title'       => 'Persistent database and settings',
	'version'     => '20190417',
	'revision'    => 'Initial release',
	'maintainer'  => 'r e r n',
	'description' => 'Maintain database and settings across SD card reflashing. '
					.'Reuse if previously moved data is available. Otherwise move existings to USB or NAS.',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/persistent_settings',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/persistent_settings/install.sh',
	'hide'        => 1
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
'chro' => array(
	'title'       => 'Chromium Browser',
	'version'     => '20180321',
	'depend'      => 'mpdu',
	'needspace'   => 200,
	'revision'    => 'Fix missing packages - Chromium 65.0.3325.181-1'
					.'<br>...'
					.'<br>Fix symbol lookup errors in new update.',
	'maintainer'  => 'r e r n',
	'description' => 'A local browser replacement. Need MPD Upgrade before install.',
	'thumbnail'   => '/img/addons/thumbchro.png',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/raw/master/chromium',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/chromium/install.sh',
	'hide'        => $rune05,
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
'expa' => array(
	'title'       => 'Expand Partition',
	'maintainer'  => 'r e r n',
	'description' => 'Expand default 2GB partition to full capacity of SD card.',
	'thumbnail'   => '/img/addons/thumbpart.png',
	'buttonlabel' => 'Expand',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/expand_partition',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/expand_partition/expand.sh',
	'hide'        => $redisaddons[ 'expa' ],
),
'motd' => array(
	'title'       => 'Login Logo for Terminal',
	'version'     => '20180115',
	'revision'    => 'Select color on install',
	'maintainer'  => 'r e r n',
	'description' => 'Message of the day - RuneAudio Logo and dimmed command prompt.',
	'thumbnail'   => '/img/addons/thumbmotd.png',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/motd',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/motd/install.sh',
	'option'      => array(
		'radio'     => array(
			'message' => 'Set logo color:',
			'list'    => array(
				'<a style="color: #f#0095d8">UI blue</a>' => 0,
				'<a style="color: #ff0000">Red</a>'       => 1,
				'<a style="color: #00ff00">Green'         => 2,
				'<a style="color: #ffff00">Yellow'        => 3,
				'<a style="color: #ff00ff">Magenta'       => 5,
				'<a style="color: #00ffff">Cyan'          => 6,
				'<a style="color: #ffffff">White'         => 7,
			),
			'checked' => 0
		),
	),
),
'mpdu' => array(
	'title'       => 'MPD Upgrade *',
	'needspace'   => 300,
	'maintainer'  => 'r e r n',
	'description' => 'Upgrade MPD to latest version, 0.21.9 as of 20190520:'
					.'<br>Fix conflicts, missing lib symlinks, missing packages'
					.'<br>But local browser <w>Midori</w>, if enabled, needs to be replaced with Chromium.',
	'thumbnail'   => '/img/addons/thumbmpdu.png',
	'buttonlabel' => 'Upgrade',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/mpd',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/mpd/install.sh',
	'option'      => $optmpd,
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
'font' => array(
	'title'       => 'RuneUI Fonts - Extended Characters',
	'version'     => '20170901',
	'needspace'   => 9,
	'revision'    => 'Initial release',
	'maintainer'  => 'r e r n',
	'description' => 'Font files replacement for Extended Latin-based, Cyrillic-based, Greek and IPA phonetics.',
	'thumbnail'   => '/img/addons/thumbfont.png',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/font_extended',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/font_extended/install.sh',
	'hide'        => $rune05,
),
'gpio' => array(
	'title'       => 'RuneUI GPIO *',
	'version'     => '20190704',
	'needspace'   => 5,
	'revision'    => 'Link setting location to common directory'
					.'<br>...'
					.'<br>Improve notifications - show devices name',
	'maintainer'  => 'r e r n',
	'description' => 'GPIO-connected relay module control for power on / off audio equipments.',
	'thumbnail'   => '/img/addons/thumbgpio.gif',
	'sourcecode'  => 'https://github.com/rern/RuneUI_GPIO',
	'installurl'  => 'https://github.com/rern/RuneUI_GPIO/raw/master/install.sh',
),
'lyri' => array(
	'title'       => 'RuneUI Lyrics',
	'version'     => '20190701',
	'revision'    => 'Link saved location to common directory'
					.'<br>...'
					.'<br>Allow colors changed with RuneUIe'
					.'<br>...'
					.'<br>Fix - Blank page if RuneUI Enhancement uninstalled',
	'maintainer'  => 'r e r n',
	'description' => 'Improve lyrics feature in 0.4b / add lyrics feature in 0.3',
	'thumbnail'   => '/img/addons/thumblyri.gif',
	'sourcecode'  => 'https://github.com/RuneAddons/Lyrics',
	'installurl'  => 'https://github.com/RuneAddons/Lyrics/raw/master/install.sh',
),
'paus' => array(
	'title'       => 'RuneUI Pause Button',
	'version'     => '20180217',
	'revision'    => 'Initial release',
	'maintainer'  => 'r e r n',
	'description' => 'Add a separate <code><i class=\"fa fa-pause\"></i></code> button',
	'thumbnail'   => '/img/addons/thumbpaus.gif',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/raw/master/pause_button',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/pause_button/install.sh',
	'hide'        => $enha,
),
'uire' => array(
	'title'       => 'RuneUI Reset',
	'maintainer'  => 'r e r n',
	'description' => 'Remove all installed addons and reset RuneUI to default with Addons reinstalled.'
					.'It can be used as an alternative to reflashing the SD card.',
	'buttonlabel' => 'Reset',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/raw/master/ui_reset',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/ui_reset/reset.sh',
	'option'      => array(
		'confirm'   => 'All RuneUI addons and custom UI modifications'
					  .'<br><w>will be removed</w>.'
	),
),
/*
'RuneYoutube' => array(
	'title'       => 'RuneUI Youtube',
	'version'     => '20171120',
	'needspace'   => 15,
	'revision'    => 'Fix bugs',
	'maintainer'  => 'tuna',
	'description' => 'This adds a youtube button to the Playlist screen of the rune audio player, '
					.'simply paste a youtube video URL or a youtube playlist URL, '
					.'wait for them to download, and these songs will be added to your playlist.',
	'thumbnail'   => '/img/addons/thumbyout.png',
	'sourcecode'  => 'https://github.com/RuneAddons/RuneYoutube',
	'installurl'  => 'https://github.com/RuneAddons/RuneYoutube/raw/master/install.sh',
),
*/
'samb' => array(
	'title'       => 'Samba Upgrade *',
	'needspace'   => 43,
	'maintainer'  => 'r e r n',
	'description' => 'Faster and more customized shares.',
	'thumbnail'   => '/img/addons/thumbsamb.png',
	'buttonlabel' => 'Upgrade',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/samba',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/samba/install.sh',
	'option'      => $optsamb
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
'udac' => array(
	'title'       => 'USB DAC Plug and Play',
	'version'     => '20190422',
	'revision'    => 'Minor improvements'
					.'<br>...'
					.'<br>Fix bugs when running on 0.5'
					.'<br>...'
					.'<br><w>Audio output</w> can be selected for preset when power off USB DAC',
	'maintainer'  => 'r e r n',
	'description' => 'Automatically switch to/from MPD Audio output and reload configuration:'
					.'<br>- USB DAC <w>power on</w> - switch to <w>USB DAC</w>'
					.'<br>- USB DAC <w>power off</w> - switch to <w>preset Audio output</w>',
	'thumbnail'   => '/img/addons/thumbudac.png',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/raw/master/USB_DAC',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/USB_DAC/install.sh',
	'option'      => array(
		'radio'     => array(
			'message' => '<w>Audio output</w> when power off USB DAC:',
			'list'    => $udaclist,
			'checked' => $redis->get( 'ao' )
		),
	),
	'hide'        => !$acards
),
'webr' => array(
	'title'       => 'Webradio Import',
	'maintainer'  => 'r e r n',
	'description' => 'Webradio files import. Adding files to <code>/mnt/MPD/Webradio/</code> alone will not work.'
					.'<br>Add files at anytime then run this addon to refresh Webradio list.'
					.'<br><w>Webradio Sorting</w> should be installed after import on 0.3.',
	'thumbnail'   => '/img/addons/thumbwebr.png',
	'buttonlabel' => 'Import',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/webradio',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/webradio/webradiodb.sh',
	'option'      => array(
		'wait'      => 'Get webradio <code>*.pls</code> or <code>*.m3u</code> files or folders'
					  .'<br>copied to <code>/mnt/MPD/Webradio</code>'
	),
	'hide'        => $enha,
),
'noti' => array(
	'title'       => 'Setting - Notification Duration',
	'maintainer'  => 'r e r n',
	'description' => 'Change RuneUI notification duration',
	'thumbnail'   => '/img/addons/thumbnoti.gif',
	'buttonlabel' => 'Change',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/raw/master/set_notify',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/set_notify/set.sh',
	'option'      => array(
		'radio'     => array(
			'message' => 'Notification duration(seconds):',
			'list'    => array(
				'1'           => 1,
				'2'           => 2,
				'3'           => 3,
				'4'           => 4,
				'5'           => 5,
				'6'           => 6,
				'7'           => 7,
				'8 (default)' => 8,
				'Custom'      => '?'
			),
			'checked' => exec( 'grep setTimeout /srv/http/assets/js/enhancebanner.js | cut -d" " -f5' ) / 1000
		),
	),
),
'zoom' => array(
	'title'       => 'Setting - Zoom Level',
	'maintainer'  => 'r e r n',
	'description' => 'Change Zoom Level of local browser (for Midori and Chromium only)',
	'buttonlabel' => 'Change',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/raw/master/set_zoom',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/set_zoom/set.sh',
	'option'      => array(
		'radio'     => array(
			'message' => 'Zoom level:',
			'list'    => array(
				'VGA or less - 720px: 0.7'   => 0.7,
				'XGA - 1024px: 1.0'          => 1,
				'SXGA - 1280px: 1.5'         => 1.5,
				'Full HD - 1920px: 1.8'      => 1.8,
				'Full HD - 1920px: 2.0'      => 2.0,
				'Custom'                     => '?'
			),
			'checked' => $zoom,
		),
	),
	'hide'        => ( $redis->get( 'local_browser' ) === '0' || $redis->hGet( 'local_browser', 'enable' ) === '0' ),
),
'poin' => array(
	'title'       => 'Setting - Mouse Pointer',
	'maintainer'  => 'r e r n',
	'description' => 'Enable mouse pointer on local browser',
	'buttonlabel' => 'Change',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/raw/master/set_pointer',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/set_pointer/set.sh',
	'option'      => array(
		'radio'     => array(
			'message' => 'Mouse pointer on local browser:',
			'list'    => array(
				'Enable'  => 'yes',
				'Disable' => 'no',
			),
			'checked' => exec( "grep use_cursor /root/.xinitrc | sed 's/.*cursor \\(.*\\) &/\\1/'" ),
		),
	),
	'hide'        => $rune05,
),
'wifi' => array(
	'title'       => 'Setting - On/Off WiFi and Bluetooth',
	'maintainer'  => 'r e r n',
	'description' => 'On/Off WiFi and Bluetooth',
	'buttonlabel' => 'Change',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/raw/master/set_wlan-bt',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/set_wlan-bt/set.sh',
	'option'      => array(
		'radio'     => array(
			'message' => 'WiFi and Bluetooth:',
			'list'    => array(
				'Enable'  => 1,
				'Disable' => 0,
			),
			'checked' => file_exists( '/etc/systemd/system/multi-user.target.wants/netctl-auto@wlan0.service' ),
		),
	),
),
'soff' => array(
	'title'       => 'Setting - Screen Off Timeout',
	'maintainer'  => 'r e r n',
	'description' => 'Set screen off timeout, <w>DPMS (Energy Star) standby</w>, for local browser.',
	'buttonlabel' => 'Change',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/raw/master/set_screenoff',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/set_screenoff/set.sh',
	'option'      => array(
		'radio'     => array(
			'message' => 'Screen off timeout:',
			'list'    => array(
				'5 minutes'  => 5,
				'10 minutes' => 10,
				'15 minutes' => 15,
				'Disable'    => 0,
			),
			'checked' => exec( "export DISPLAY=:0; xset q | grep Standby: | awk '{print $6}'" ) / 60
		),
	),
	'hide'        => !$redis->get( 'local_browser' ),
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
),
'rest' => array(
	'title'       => 'Settings+Databases Restore',
	'revision'    => 'Initial release',
	'maintainer'  => 'r e r n',
	'description' => 'Restore all RuneAudio <w>settings and databases</w>.',
	'thumbnail'   => '/img/addons/thumbback.png',
	'buttonlabel' => 'Restore',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/backup-restore',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/backup-restore/restore.sh',
	'option'      => array(
		'file'      => array(
			'message' => 'Select a <code>*.tar.gz</code> backup file for restore:',
			'label'   => 'Restore',
			'type'    => '.tar.gz'
		),
	),
),

);
