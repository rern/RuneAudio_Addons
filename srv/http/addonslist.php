<?php
// system data ////////////////////////////////////////////////
$redis = new Redis(); 
$redis->pconnect( '127.0.0.1' );

$redisaddons = $redis->hGetAll( 'addons' );

$runeversion = ( $redis->get( 'release' ) == '0.4b' ) ? '0.4b' : '0.3';

$available = $redis->get( 'available' );
$expandable = $redis->get( 'expandable' );
if ( !$available || !$expandable ) {
	$freemb = round( disk_free_space( '/' ) / 1000000 );
	$available = $freemb < 1000 ? $freemb.' MB' : round( $freemb / 1000, 2 ).' GB';
	if ( $unpartmb < 10 ) {
		$expandable = '';
	} else {
		$expandable = ' (expandable: ';
		$expandable.= $unpartmb < 1000 ? $unpartmb.' MB' : round( $unpartmb / 1000, 2 ).' GB';
	}
	$redis->set( 'available', $available );
	$redis->set( 'expandable', $expandable );
}

$udaclist = $redis->hGetAll( 'udaclist' );
if ( !$udaclist ) {
	$acards = $redis->hGetAll( 'acards' );
	$default = '*';
	foreach ( $acards as $key => $val ) {
		$card = json_decode( $val, true );
		$extlabel = $card[ 'extlabel' ];
		$udaclist[ $default.$extlabel ] = $key.'@'.$extlabel;
		$redis->hSet( 'udaclist', $default.$extlabel, $key.'@'.$extlabel );
		$default = '';
	}
}

$notifysec = $redis->get( 'notifysec' );
if ( !$notifysec ) {
	$notifysec = exec( 'grep notify.delay /srv/http/assets/js/runeui.js | tr -dc "1-9"' );
	$redis->set( 'notifysec', $notifysec );
}

///////////////////////////////////////////////////////////////

$addons = array(

/*
'redi' => array(
	'title'        => 'Redis Upgrade',
	'maintainer'   => 'r e r n',
	'description'  => 'Upgrade Redis to latest version <white>without errors</white>:'
					.'<br>Update <code>redis.service</code>',
	'thumbnail'    => '/assets/addons/thumbredis.png',
	'buttonlabel'  => 'Upgrade',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/raw/master/redis',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/redis/install.sh',
	'option'       => array(
		'confirm'    => 'Once upgraded, Redis <white>cannot be downgraded</white>.'
					.'<br>Continue?'
	),
),
'ngin' => array(
	'title'        => 'NGINX Upgrade',
	'maintainer'   => 'r e r n',
	'description'  => 'Upgrade NGINX to 1.14.0 <white>without errors</white>:'
					.'<br>preserve configuration and pushstream support',
	'thumbnail'    => '/assets/addons/thumbngin.png',
	'buttonlabel'  => 'Upgrade',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/raw/master/nginx',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/nginx/install.sh',
	'option'       => array(
		'confirm'    => 'Once upgraded, NGINX <white>cannot be downgraded</white>.'
					.'<br>Continue?'
	),
),
*/
'addo' => array(
	'title'        => 'Addons Menu',
	'version'      => '20180619',
	'revision'     => 'Use local thumbnails to improve loading speed'
					.'<br>Standardize code editing template'
					.'<br>...'
					.'<br>Add <white>long-press Uninstall</white> = <white>Rollback / Downgrade</white> to previous version'
					.'<br>...'
					.'<br>Remove <white>Bash Commands</white> which may has a security issue',
	'maintainer'   => 'r e r n',
	'description'  => 'This Addons Menu main page.',
	'thumbnail'    => '/assets/addons/thumbaddo.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio_Addons',
	'installurl'   => 'https://github.com/rern/RuneAudio_Addons/raw/master/install.sh',
),
'aria' =>array(
	'title'        => 'Aria2 *',
	'version'      => '20170901',
	'needspace'    => 15,
	'revision'     => 'Initial release',
	'maintainer'   => 'r e r n',
	'description'  => 'Download utility that supports HTTP(S), FTP, BitTorrent, and Metalink.'
					.'<br>Pre-configured and ready to use.',
	'thumbnail'    => '/assets/addons/thumbaria.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/aria2',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/aria2/install.sh',
	'option'       => array(
		'yesno'      => 'Start <white>Aria2</white> on system startup?'
	),
),
'spla' => array(
	'title'        => 'Boot Logo',
	'version'      => '20171010',
	'revision'     => 'Initial release',
	'maintainer'   => 'r e r n',
	'description'  => 'Display RuneAudio logo during boot - Splash screen.',
	'thumbnail'    => '/assets/addons/thumbspla.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/boot_splash',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/boot_splash/install.sh',
	'hide'         => array(
		'only03'     => '1',
	),
),
'chro' => array(
	'title'        => 'Chromium Browser',
	'version'      => '20180321',
	'needspace'    => 300,
	'revision'     => 'Fix missing packages - Chromium 65.0.3325.181-1'
					.'<br>...'
					.'<br>Fix symbol lookup errors in new update.',
	'maintainer'   => 'r e r n',
	'description'  => 'Replace broken <white>Midori</white>, local browser, with <white>Chromium</white> after MPD upgrade',
	'thumbnail'    => '/assets/addons/thumbchro.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/raw/master/chromium',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/chromium/install.sh',
	'option'       => array(
		'wait'    => 'After installed, Chromium needs a <white>reboot</white>.',
		'radio'      => array(
			'message'  => 'Set <white>zoom level</white> for display directly connect to RPi.'
						.'<br>'
						.'<br>Local screen size:',
			'list'     => array(
				'Width less than 800px: 0.7' => '0.7',
				'HD - 1280px: 1.5'           => '1.5',
				'*Full HD - 1920px: 1.8'     => '1.8',
				'Custom'                     => '?'
			),
		),
	),
),
'dual' => array(
	'title'        => 'Dual Boot: RuneAudio + OSMC *',
	'maintainer'   => 'r e r n',
	'description'  => 'Best of Audio Distro - <white>RuneAudio</white> 0.3 + Addons Menu ready (ArchLinux MPD)'
					.'<br>Best of Video Distro - <white>OSMC</white> 2017-08-1 (Raspbian Kodi)'
					.'<br>Best of Dual Boot - <white>NOOBS</white> 2.4',
	'thumbnail'    => '/assets/addons/thumbdual.gif',
	'buttonlabel'  => 'Link',
	'sourcecode'   => 'http://www.runeaudio.com/forum/dual-boot-noobs-rune-osmc-pi2-pi3-t3822.html',
	'installurl'   => '',
),
'expa' => array(
	'title'        => 'Expand Partition',
	'maintainer'   => 'r e r n',
	'description'  => 'Expand default 2GB partition to full capacity of SD card.',
//	'thumbnail'    => '/assets/addons/thumbpart.png',
	'buttonlabel'  => 'Expand',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/expand_partition',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/expand_partition/expand.sh',
	'hide'         => ( $expandable ? 0 : 1 ),
	'option'       => array(
		'wait'       => 'Unmount and remove all <white>USB drives</white> before proceeding.'
	),
),
'motd' => array(
	'title'        => 'Login Logo for Terminal',
	'version'      => '20180115',
	'revision'     => 'Select color on install',
	'maintainer'   => 'r e r n',
	'description'  => 'Message of the day - RuneAudio Logo and dimmed command prompt.',
	'thumbnail'    => '/assets/addons/thumbmotd.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/motd',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/motd/install.sh',
	'option'       => array(
		'radio'      => array(
			'message'  => 'Set logo color:',
			'list'     => array(
				'*Rune blue' => 0,
				'Red'        => 1,
				'Green'      => 2,
				'Yellow'     => 3,
				'Blue'       => 4,
				'Magenta'    => 5,
				'Cyan'       => 6,
				'White'      => 7,
			),
		),
	),
),
'mpdu' => array(
	'title'        => 'MPD Upgrade *',
	'needspace'    => 192,
	'maintainer'   => 'r e r n',
	'description'  => 'Upgrade MPD to latest version, 0.20.18 as of 20180404:'
					.'<br>Fix conflicts, missing lib symlinks, missing packages'
					.'<br>But <white>broken Midori</white>, local browser.',
	'thumbnail'    => '/assets/addons/thumbmpdu.png',
	'buttonlabel'  => 'Upgrade',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/mpd',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/mpd/install.sh',
	'option'       => array(
		'confirm'    => 'Once installed, MPD <white>cannot be downgraded</white>.'
					.'<br>Local browser must be switched to <white>Chromium</white>'
					.'<br>10 minutes upgrade may take 20+ minutes'
					.'<br>with slow download.'
					.'<br>Continue?'
	),
),
'rank' => array(
	'title'        => 'Rank Mirror Package Servers',
	'maintainer'   => 'r e r n',
	'description'  => 'Fix package download errors caused by unreachable servers.',
	'thumbnail'    => '/assets/addons/thumbrank.png',
	'buttonlabel'  => 'Rank',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/rankmirrors',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/rankmirrors/rankmirrors.sh',
	'option'       => array(
		'confirm'    => 'Lately, mirror servers have been not so well as before.'
					.'<br>Ranked servers may not work as well as it should.'
					.'<br>Continue?'
	),
),
'back' => array(
	'title'        => 'RuneUI Backup-Restore Enable',
	'version'      => '20170901',
	'revision'     => 'Initial release',
	'maintainer'   => 'r e r n',
	'description'  => 'Enable backup-restore settings and databases.',
	'thumbnail'    => '/assets/addons/thumbback.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/backup-restore',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/backup-restore/install.sh',
),
'enha' => array(
	'title'        => 'RuneUI Enhancements *',
	'version'      => '20180610',
	'rollback'     => '7931d40ea43742ba9e0abaf37c0cb1803313b737',
	'revision'     => 'Improve main icons, sorting, overlay controls guide'
					.'<br>Change Webradio cover art'
					.'<br>Disable buttons in Webradio mode: repeat, shuffle, single, share, artist info'
					.'<br>Change boot splash image'
					.'<br>...'
					.'<br>Show Webradio sampling info in Playback while stop'
					.'<br>Show consistent <white>Webradio</white> icon, name and url in <white>Library</white> and <white>Playlist</white>'
					.'<br>Show <white>Webradio URL</white> in <white>Library</white>'
					.'<br>Show <white>Webradio name</white> on initial load of <white>saved playlist</white>'
					.'<br>Hide duration if Playlist contains Webradio'
					.'<br>Minimalize Playback on <white>empty Playlist</white>'
					.'<br>Webradio pause = <code>stop</code>'
					.'<br>Fix and improve random with <code>previous</code> <code>next</code>'
					.'<br>Add show/hide <code>MPD</code> in display setting'
					.'<br>Add <white>above countdown</white> = <code>MPD</code> button'
					.'<br>Add <white>below countdown</white> = <code>stop</code>'
					.'<br>Change cover art gestures (tap top shows overlay guide)'
					.'<br>Fix Library <code>back button</code> in Artists, Albums, Genres, Composers'
					.'<br>Fix <code>Composer</code> in <white>Library</white>'
					.'<br>Sort without leading articles, A / An / The',
	'maintainer'   => 'r e r n',
	'description'  => 'More <white>minimalism</white> and more <white>fluid</white> layout.',
	'thumbnail'    => '/assets/addons/thumbenha.gif',
	'sourcecode'   => 'https://github.com/rern/RuneUI_enhancement',
	'installurl'   => 'https://github.com/rern/RuneUI_enhancement/raw/master/install.sh',
	'conflict'     => 'paus',
	'option'       => array(
		'radio'      => array(
			'message'  => 'Set <white>zoom level</white> for display directly connect to RPi.'
						.'<br>'
						.'<br>Local screen size:',
			'list'     => array(
				'Width less than 800px: 0.7' => '0.7',
				'HD - 1280px: 1.5'           => '1.5',
				'*Full HD - 1920px: 1.8'     => '1.8',
				'Custom'                     => '?'
			),
		),
	),
),
'font' => array(
	'title'        => 'RuneUI Fonts - Extended Characters',
	'version'      => '20170901',
	'needspace'    => 9,
	'revision'     => 'Initial release',
	'maintainer'   => 'r e r n',
	'description'  => 'Font files replacement for Extended Latin-based, Cyrillic-based, Greek and IPA phonetics.',
	'thumbnail'    => '/assets/addons/thumbfont.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/font_extended',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/font_extended/install.sh',
),
'gpio' => array(
	'title'        => 'RuneUI GPIO *',
	'version'      => '20180320',
	'rollback'     => 'f5c0bf9e54253e5f6261644e0021f4e8dcfa407b',
	'needspace'    => 5,
	'revision'     => 'Fix bugs'
					.'<br>...'
					.'<br>Now use only plain BASH and Python - no PIP install needed'
					.'<br>...'
					.'<br>Split MPD configuration loading to <white>USB DAC Auto Switch</white>'
					.'<br>Switch to <white>high trigger</white> relay module',
	'maintainer'   => 'r e r n',
	'description'  => 'GPIO-connected relay module control for power on / off audio equipments.',
	'thumbnail'    => '/assets/addons/thumbgpio.gif',
	'sourcecode'   => 'https://github.com/rern/RuneUI_GPIO',
	'installurl'   => 'https://github.com/rern/RuneUI_GPIO/raw/master/install.sh',
),
'lyri' => array(
	'title'        => 'RuneUI Lyrics',
	'version'      => '20180321',
	'rollback'     => 'fb3de20151f9fdf866de9ea51d6f03d678211428',
	'revision'     => 'Fix bugs'
					.'<br>...'
					.'<br>Update with RuneUI Enhancement 20180321'
					.'<br>...'
					.'<br>Fix <white>long-press on song title</white> to not also open lyrics editor',
	'maintainer'   => 'r e r n',
	'description'  => 'Improve lyrics feature in 0.4b / add lyrics feature in 0.3',
	'thumbnail'    => '/assets/addons/thumblyri.gif',
	'sourcecode'   => 'https://github.com/RuneAddons/Lyrics',
	'installurl'   => 'https://github.com/RuneAddons/Lyrics/raw/master/install.sh',
),
'noti' => array(
	'title'        => 'RuneUI Notification Duration',
	'maintainer'   => 'r e r n',
	'description'  => 'Change RuneUI notification duration',
	'thumbnail'    => '/assets/addons/thumbnoti.gif',
	'buttonlabel'  => 'Change',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/raw/master/notify_duration',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/notify_duration/notify_duration.sh',
	'option'       => array(
		'radio'      => array(
			'message'  => 'Set notification duration(second):',
			'list'     => array(
				'1'           => 1,
				'2'           => 2,
				'3'           => 3,
				'4'           => 4,
				'5'           => 5,
				'6'           => 6,
				'7'           => 7,
				'8 (default)' => 8,
				'Custom'   => '?'
			),
			'checked'      => $notifysec
		),
	),
),
'pass' => array(
	'title'        => 'RuneUI Password',
	'version'      => '20170901',
	'rollback'     => 'e0bf023ec38ff5d9802654b82455c20c64079af6',
	'revision'     => 'Initial release',
	'maintainer'   => 'r e r n',
	'description'  => 'RuneUI access restriction.',
//	'thumbnail'    => '/assets/addons/thumbpass.png',
	'sourcecode'   => 'https://github.com/RuneAddons/Password',
	'installurl'   => 'https://github.com/RuneAddons/Password/raw/master/install.sh',
	'hide'         => array(
		'only03'     => '1',
	),
),
'paus' => array(
	'title'        => 'RuneUI Pause button',
	'version'      => '20180217',
	'revision'     => 'Initial release',
	'maintainer'   => 'r e r n',
	'description'  => 'Add a separate <code><i class=\"fa fa-pause\"></i></code> button',
//	'thumbnail'    => '/assets/addons/thumbpaus.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/raw/master/pause_button',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/pause_button/install.sh',
	'hide'         => array(
		'installed'  => 'enha',
	),
),
'RuneYoutube' => array(
	'title'        => 'RuneUI Youtube',
	'version'      => '20171120',
	'needspace'    => 15,
	'revision'     => 'Fix bugs',
	'maintainer'   => 'tuna',
	'description'  => 'This adds a youtube button to the Playlist screen of the rune audio player, '
					.'simply paste a youtube video URL or a youtube playlist URL, '
					.'wait for them to download, and these songs will be added to your playlist.',
	'thumbnail'    => '/assets/addons/thumbyout.png',
	'sourcecode'   => 'https://github.com/RuneAddons/RuneYoutube',
	'installurl'   => 'https://github.com/RuneAddons/RuneYoutube/raw/master/install.sh',
),
'samb' => array(
	'title'        => 'Samba Upgrade *',
	'needspace'    => 43,
	'maintainer'   => 'r e r n',
	'description'  => 'Faster and more customized shares.',
	'thumbnail'    => '/assets/addons/thumbsamb.png',
	'buttonlabel'  => 'Upgrade',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/samba',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/samba/install.sh',
	'option'       => array(
		'confirm'    => 'Once installed, Samba <white>cannot be downgraded</white>.'
					.'<br>Continue?',
		'password'   => array(
			'message'  => '(for connecting to <white>USB root share</white>)'
						.'<br>Password for user <white>root</white> (blank = rune):',
			'label'    => 'Password'
		),
		'skip'       => 'Keep current Samba settings and shares?',
		'wait'       => 'Connect a <white>USB drive</white> before continue.'
					.'<br>1st drive will be used for shared directories.',
		'text1'      => array(
			'message'  => '<white>File Server</white>:',
			'label'    => 'Name',
			'value'    => 'RuneAudio'
		),
		'text2'      => array(
			'message'  => '<white>Read-Only</white> directory:',
			'label'    => 'Name',
			'value'    => 'ro'
		),
		'text3'      => array(
			'message'  => '<white>Read-Write</white> directory:',
			'label'    => 'Name',
			'value'    => 'rw'
		),
	),
),
'tran' => array(
	'title'        => 'Transmission *',
	'version'      => '20180520',
	'needspace'    => 9,
	'revision'     => 'Use standard package instead of customized one.'
					.'<br>...'
					.'<br>Update alternateive WebUI source'
					.'<br>...'
					.'<br>Fix bugs by reverting back to custom package.',
	'maintainer'   => 'r e r n',
	'description'  => 'Fast, easy, and free BitTorrent client. Pre-configured and ready to use.',
	'thumbnail'    => '/assets/addons/thumbtran.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/transmission',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/transmission/install.sh',
	'option'       => array(
		'password'   => array(
			'message'  => 'Password for user <white>root</white> (blank = no password):',
			'label'    => 'Password',
		),
		'checkbox'   => array(
			'message'  => '',
			'list'     => array(
				'*Install <white>WebUI</white> alternative?'            => '1',
				'*Start <white>Transmission</white> on system startup?' => '1'
			),
		),
	),
),
'udac' => array(
	'title'        => 'USB DAC Plug and Play',
	'version'      => '20180219',
	'revision'     => '<white>Audio output</white> can be selected for preset when power off USB DAC'
					.'<br>...'
					.'<br>Use <white>udev rules</white> to auto switch'
					.'<br>...'
					.'<br>Remove manual refresh/reload',
	'maintainer'   => 'r e r n',
	'description'  => 'Automatically switch to/from MPD Audio output and reload configuration:'
					.'<br>- USB DAC <white>power on</white> - switch to <white>USB DAC</white>'
					.'<br>- USB DAC <white>power off</white> - switch to <white>preset Audio output</white>',
	'thumbnail'    => '/assets/addons/thumbudac.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/raw/master/USB_DAC',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/USB_DAC/install.sh',
	'option'       => array(
		'radio'      => array(
			'message'  => '<white>Audio output</white> when power off USB DAC:',
			'list'     => $udaclist
		),
	),
),
'webr' => array(
	'title'        => 'Webradio Import',
	'maintainer'   => 'r e r n',
	'description'  => 'Webradio files import. Adding files to <code>/mnt/MPD/Webradio/</code> alone will not work.'
					.'<br>Add files at anytime then run this addon to refresh Webradio list.'
					.'<br><white>Webradio Sorting</white> should be installed after import on 0.3.',
	'thumbnail'    => '/assets/addons/thumbwebr.png',
	'buttonlabel'  => 'Import',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/webradio',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/webradio/webradiodb.sh',
	'option'       => array(
		'wait'       => 'Get webradio <code>*.pls</code> files or folders copied to:'
					.'<br><code>/mnt/MPD/Webradio</code>'
					.'<br>'
					.'<br><code>&emsp;Ok&emsp;</code> to continue'
	),
),
	
);
