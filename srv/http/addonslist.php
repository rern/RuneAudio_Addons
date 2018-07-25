<?php
$redis = new Redis(); 
$redis->pconnect( '127.0.0.1' );

$runeversion = ( $redis->get( 'release' ) == '0.4b' ) ? '0.4b' : '0.3';

$redisaddons = $redis->hGetAll( 'addons' );

$udaclist = $redis->hGetAll( 'udaclist' );
if ( !$udaclist ) {
	$acards = $redis->hGetAll( 'acards' );
	foreach ( $acards as $key => $val ) {
		$card = json_decode( $val, true );
		$extlabel = $card[ 'extlabel' ];
		$udaclist[ $extlabel ] = $key.'@'.$extlabel;
		$redis->hSet( 'udaclist', $extlabel, $key.'@'.$extlabel );
	}
}

///////////////////////////////////////////////////////////////

$addons = array(

/*
'airo' => array(
	'title'        => 'Setting - AirPlay Output',
	'maintainer'   => 'r e r n',
	'description'  => 'Change AirPlay output (for Shairport Sync only)',
	'buttonlabel'  => 'Change',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/raw/master/shairport-sync',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/shairport-sync/shairport-sync-output.sh',
	'option'       => array(
		'wait'      => 'Set AirPlay output:'
					.'<br><white>Connect and power on DAC</white> before proceed.'
					.'<br>It will be set as AirPlay output.'
	),
),
'shai' => array(
	'title'        => 'AirPlay Upgrade',
	'version'      => '20180808',
	'revision'     => 'Initial release',
	'maintainer'   => 'r e r n',
	'description'  => 'Upgrade AirPlay default package, Shairport, to <white>Shairport Sync 3.2.1</white>.'
					.'<br>Elapsed and song duration are supported.',
	'thumbnail'    => '/assets/addons/thumbshai.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/shairport-sync',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/shairport-sync/install.sh',
	'option'       => array(
		'wait'       => '<white>Connect and power on DAC</white> before proceed.'
					.'<br>It will be set as AirPlay output.'
					.'<br>(This can be change later with an addon'
					.'<br><white>Setting - AirPlay Output</white>)'
	),
),
'redi' => array(
	'title'        => 'Redis Upgrade',
	'maintainer'   => 'r e r n',
	'description'  => 'Upgrade Redis to latest version <white>without errors</white>:'
					.'<br>Update <code>redis.service</code>',
	'thumbnail'    => '/assets/addons/thumbredi.png',
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
	'title'        => 'Addons',
	'version'      => '20180724',
	'revision'     => 'Switch from hammer.js to jquery.mobile which is leaner.'
					.'<br>General improvements'
					.'<br>...'
					.'<br>UI improvement'
					.'<br>...'
					.'<br>Add disk bar for | used | free | expandable |'
					.'<br>Use local thumbnails to improve loading speed'
					.'<br>Normalize code editing template',
	'maintainer'   => 'r e r n',
	'description'  => 'This Addons main page.',
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
	'hide'         => $runeversion === '0.3' ? 0 : 1,
),
'chro' => array(
	'title'        => 'Chromium Browser',
	'version'      => '20180321',
	'needspace'    => 300,
	'revision'     => 'Fix missing packages - Chromium 65.0.3325.181-1'
					.'<br>...'
					.'<br>Fix symbol lookup errors in new update.',
	'maintainer'   => 'r e r n',
	'description'  => 'An alternative local browser',
	'thumbnail'    => '/assets/addons/thumbchro.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/raw/master/chromium',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/chromium/install.sh',
	'option'       => array(
		'wait'    => 'After installed, Chromium needs a <white>reboot</white>.',
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
	'thumbnail'    => '/assets/addons/thumbpart.png',
	'buttonlabel'  => 'Expand',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/expand_partition',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/expand_partition/expand.sh',
	'hide'         => $redisaddons[ 'expa' ] ? 1 : 0,
	'option'       => array(
		'wait'       => '<white>USB drives</white> should be unmount and removed before proceeding.'
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
'mido' => array(
	'title'        => 'Midori Upgrade',
	'maintainer'   => 'r e r n',
	'description'  => 'Upgrade Midori to latest version <white>without errors</white>:'
					.'<br>MPD Upgrade also needs this upgrade',
	'thumbnail'    => '/assets/addons/thumbmido.png',
	'buttonlabel'  => 'Upgrade',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/raw/master/midori',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/midori/install.sh',
	'hide'         => $redisaddons[ 'enha' ] ? 0 : 1,
	'option'       => array(
		'confirm'    => 'Once upgraded, Midori <white>cannot be downgraded</white>.'
					.'<br>Continue?'
	),
),
'mpdu' => array(
	'title'        => 'MPD Upgrade *',
	'needspace'    => 192,
	'maintainer'   => 'r e r n',
	'description'  => 'Upgrade MPD to latest version, 0.20.20 as of 20180711:'
					.'<br>Fix conflicts, missing lib symlinks, missing packages'
					.'<br>But <white>broken Midori</white>, local browser which needs upgrade as well.',
	'thumbnail'    => '/assets/addons/thumbmpdu.png',
	'buttonlabel'  => 'Upgrade',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/mpd',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/mpd/install.sh',
	'option'       => array(
		'confirm'    => 'Once installed, MPD <white>cannot be downgraded</white>.'
					.'<br>Midori, local browser, must be upgrade as well.'
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
	'title'        => 'RuneUI Enhancements **',
	'version'      => '20180722',
	'revision'     => 'Now Airplay is working properly.'
					.'<br>Fix bugs'
					.'<br>...'
					.'<br>Rearrange overlay controls(tap cover art top for guide)'
					.'<br>Overlay repeat = toggle: repeat | repeat single | off'
					.'<br>...'
					.'<br>Improve most icons and overlay controls guide'
					.'<br>Change Webradio cover art'
					.'<br>Add show/hide Webradio elapsed time'
					.'<br>Fix: omit A/An/The sorting in file mode and (/[/. are omitted as well'
					.'<br>Improve boot splash image',
	'maintainer'   => 'r e r n',
	'description'  => 'More <white>minimalism</white> and more <white>fluid</white> layout.',
	'thumbnail'    => '/assets/addons/thumbenha.gif',
	'sourcecode'   => 'https://github.com/rern/RuneUI_enhancement',
	'installurl'   => 'https://github.com/rern/RuneUI_enhancement/raw/master/install.sh',
	'conflict'     => 'paus',
	'option'       => array(
		'wait'    => 'After installed, web browser needs <white>clear cache/data</white>'
						.'<br>If first install, RuneAudio needs <white>reboot</white>',
		'radio'      => array(
			'message'  => 'Set <white>zoom level</white> for display directly connect to RPi.'
						.'<br>(This can be changed later.)'
						.'<br>Local screen size:',
			'list'     => array(
				'Width less than 800px: 0.7' => '0.7',
				'HD - 1280px: 1.5'           => '1.5',
				'*Full HD - 1920px: 1.8'     => '1.8',
				'Full HD - 1920px: 2.0'      => '2.0',
				'Custom'                     => '?'
			),
		),
		'radio1'      => array( 
			'message'  => 'Local browser should be <white>disabled</white>'
						.'<br>if no need to display on RPi connected screen.'
						.'<br>It will save 6% CPU load + 45MB memory.'
						.'<br>(Re-enable: <code>Menu</code> > <code>Settings</code> > <code>Local browser</code>)'
						.'<br>',
			'list'     => array(
				'*Enable' => '1',
				'Disable' => '0'
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
	'version'      => '20180724',
	'needspace'    => 5,
	'revision'     => 'Switch from hammer.js to jquery.mobile'
					.'<br>...'
					.'<br>Improve UI'
					.'<br>On / Off: from Menu > GPIO'
					.'<br>Settings: from Menu > long-press GPIO'
					.'<br>...'
					.'<br>Now use only plain BASH and Python - no PIP install needed',
	'maintainer'   => 'r e r n',
	'description'  => 'GPIO-connected relay module control for power on / off audio equipments.',
	'thumbnail'    => '/assets/addons/thumbgpio.gif',
	'sourcecode'   => 'https://github.com/rern/RuneUI_GPIO',
	'installurl'   => 'https://github.com/rern/RuneUI_GPIO/raw/master/install.sh',
),
'lyri' => array(
	'title'        => 'RuneUI Lyrics',
	'version'      => '20180630',
	'rollback'     => 'fb3de20151f9fdf866de9ea51d6f03d678211428',
	'revision'     => 'Fix missing edit snd close buttons'
					.'<br>General improvements'
					.'<br>'
					.'<br>...'
					.'<br>Update with RuneUI Enhancement 20180321',
	'maintainer'   => 'r e r n',
	'description'  => 'Improve lyrics feature in 0.4b / add lyrics feature in 0.3',
	'thumbnail'    => '/assets/addons/thumblyri.gif',
	'sourcecode'   => 'https://github.com/RuneAddons/Lyrics',
	'installurl'   => 'https://github.com/RuneAddons/Lyrics/raw/master/install.sh',
),
'pass' => array(
	'title'        => 'RuneUI Password',
	'version'      => '20170901',
	'rollback'     => 'e0bf023ec38ff5d9802654b82455c20c64079af6',
	'revision'     => 'Initial release',
	'maintainer'   => 'r e r n',
	'description'  => 'RuneUI access restriction.',
	'thumbnail'    => '/assets/addons/thumbpass.png',
	'sourcecode'   => 'https://github.com/RuneAddons/Password',
	'installurl'   => 'https://github.com/RuneAddons/Password/raw/master/install.sh',
	'hide'         => $runeversion === '0.3' ? 0 : 1,
),
'paus' => array(
	'title'        => 'RuneUI Pause button',
	'version'      => '20180217',
	'revision'     => 'Initial release',
	'maintainer'   => 'r e r n',
	'description'  => 'Add a separate <code><i class=\"fa fa-pause\"></i></code> button',
	'thumbnail'    => '/assets/addons/thumbpaus.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/raw/master/pause_button',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/pause_button/install.sh',
	'hide'         => $redisaddons[ 'enha' ] ? 1 : 0,
),
'uire' => array(
	'title'        => 'RuneUI Reset',
	'maintainer'   => 'r e r n',
	'description'  => 'Remove all installed addons and reset RuneUI to default with Addons reinstalled.'
					.'It can be used as an alternative to reflashing the SD card.',
	'buttonlabel'  => 'Reset',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/raw/master/ui_reset',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/ui_reset/install.sh',
	'option'       => array(
		'confirm'    => 'All RuneUI addons and custom UI modifications'
					.'<br><white>will be removed</white>.'
					.'<br>Continue?'
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
	'version'      => '20180715',
	'needspace'    => 9,
	'revision'     => 'Fix bugs by reverting back to custom compiled package.'
					.'<br>...'
					.'<br>Update alternateive WebUI source',
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
			'list'     => $udaclist,
			'checked'  => 'bcm2835 ALSA_1@RaspberryPi Analog Out'

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
'noti' => array(
	'title'        => 'Setting - RuneUI Notification Duration',
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
				'Custom'      => '?'
			),
			'checked'  => $redis->get( 'notifysec' )
		),
	),
),
'zoom' => array(
	'title'        => 'Setting - Zoom Level of Local Browser',
	'maintainer'   => 'r e r n',
	'description'  => 'Change Zoom Level of Local Browser (for Midori and Chromium only)',
	'buttonlabel'  => 'Change',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/raw/master/zoom_browser',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/zoom_browser/zoom.sh',
	'option'       => array(
		'radio'      => array(
			'message'  => 'Set zoom level (current = <white>'.$zoomlevel.'</white>) :',
			'list'     => array(
				'Width less than 800px: 0.7' => '0.7',
				'HD - 1280px: 1.5'           => '1.5',
				'Full HD - 1920px: 1.8'      => '1.8',
				'Full HD - 1920px: 2.0'      => '2.0',
				'Custom'                     => '?'
			),
			'checked'  => $redis->get( 'zoomlevel' )
		),
	),
),
'brow' => array(
	'title'        => 'Setting - Switch Midori <-> Chromium',
	'maintainer'   => 'r e r n',
	'description'  => 'Switch Local Browser between Midori and Chromium',
	'buttonlabel'  => 'Switch',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/raw/master/switch_browser',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/switch_browser/switch.sh',
	'hide'         => $redisaddons[ 'chro' ] ? 0 : 1,
	'option'       => array(
		'radio'      => array(
			'message'  => 'Select local browser:',
			'list'     => array(
				'Midori'   => '1',
				'Chromium' => '2'
			),
			'checked'  => $redis->get( 'browser' )
		),
	),
),
	
);
