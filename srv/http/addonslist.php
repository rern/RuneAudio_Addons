<?php
// system data ////////////////////////////////////////////////
exec( '/usr/bin/sudo /usr/bin/fdisk -l /dev/mmcblk0', $fdisk );

$fdisk = array_values( $fdisk );
$sectorbyte = preg_replace( '/.*= (.*) bytes/', '${1}', implode( preg_grep( '/^Units/', $fdisk ) ) );
$sectorall = preg_replace( '/.* (.*) sectors/', '${1}', implode( preg_grep( '/sectors$/', $fdisk ) ) );
$sectorused = preg_split( '/\s+/', end( $fdisk ) )[ 2 ];
$unpartmb = round( ( $sectorall - $sectorused ) * $sectorbyte / 1024 / 1024 );

// data to be used in array ///////////////////////////////////
$redis = new Redis(); 
$redis->pconnect( '127.0.0.1' );
// udac //
$acards = $redis->hGetAll( 'acards' );
$ilength = count( $acards );
$i = 0;
foreach ( $acards as $key => $val ) {
	$default = ( $i == $ilength ) ? '' : '*';
	$i++;	
	$card = json_decode( $val, true );
	$extlabel = $card[ 'extlabel' ];
	$udaclist[ $default.$extlabel ] = $key.'@'.$extlabel;
}
///////////////////////////////////////////////////////////////

$addons = array(

/*
'redi' => array(
	'title'        => 'Redis Upgrade',
	'maintainer'   => 'r e r n',
	'description'  => 'Upgrade Redis to latest version <white>without errors</white>:'
					.'<br>Update <code>redis.service</code>',
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/nginx/thumbredis.png',
	'buttonlabel'  => 'Upgrade',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/raw/master/redis',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/redis/install.sh',
),
'ngin' => array(
	'title'        => 'NGINX Upgrade',
	'maintainer'   => 'r e r n',
	'description'  => 'Upgrade from default NGINX 0.3:1.4.7 / 0.4b:1.11.3 to 1.13.7 <white>without errors</white>:'
					.'<br>preserve configuration and pushstream support',
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/nginx/thumbnginx.png',
	'buttonlabel'  => 'Upgrade',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/raw/master/nginx',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/nginx/install.sh',
),
*/

'addo' => array(
	'title'        => 'Addons Menu',
	'version'      => '20180312',
	'revision'     => 'Auto check for updates and display a badge if any'
					.'<br>...'
					.'<br>Support <white>uninstall conflict addons</white>'
					.'<br>Support <white>hide redundant features addons</white>'
					.'<br>Support <white>hide addons by condition scripts</white>'
					.'<br>Support <white>FontAwesome</white> in revisions and descriptions',
	'maintainer'   => 'r e r n',
	'description'  => 'This Addons Menu main page.',
	'thumbnail'    => 'https://github.com/rern/_assets/raw/master/RuneAudio_Addons/addonsthumb.png',
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
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/aria2/thumbaria.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/aria2',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/aria2/install.sh',
	'option'       => array(
		'yesno'      => 'Start <white>Aria2</white> on system startup?'
	),
),
'bash' => array(
	'title'        => 'BASH Command',
	'maintainer'   => 'r e r n',
	'description'  => 'Run BASH commands or scripts like on SSH terminal. For non-interactive only or use pipe to make input.',
	'thumbnail'    => 'https://github.com/rern/_assets/raw/master/thumbnail/thumbbash.png',
	'buttonlabel'  => 'Run',
	'sourcecode'   => '',
	'installurl'   => '',
	'option'       => array(
		'text'       => array(
			'message'  => '<white>BASH</white> commands or /full/path/script:',
			'label'    => 'commands',
		),
	),
),
'spla' => array(
	'title'        => 'Boot Logo',
	'version'      => '20171010',
	'revision'     => 'Initial release',
	'maintainer'   => 'r e r n',
	'description'  => 'Display RuneAudio logo during boot - Splash screen.',
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/boot_splash/thumbspla.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/boot_splash',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/boot_splash/install.sh',
	'hide'         => array(
		'only03'     => '1',
	),
),
'chro' => array(
	'title'        => 'Chromium Browser',
	'version'      => '20180106',
	'needspace'    => 300,
	'revision'     => 'Fix symbol lookup errors in new update.',
	'maintainer'   => 'r e r n',
	'description'  => 'Replace broken <white>Midori</white>, local browser, with <white>Chromium</white> after MPD upgrade',
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/chromium/thumbchro.png',
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
	'thumbnail'    => 'https://github.com/rern/_assets/raw/master/RPi2-3.Dual.Boot-Rune.OSMC/thumbdual.gif',
	'buttonlabel'  => 'Link',
	'sourcecode'   => 'http://www.runeaudio.com/forum/dual-boot-noobs-rune-osmc-pi2-pi3-t3822.html',
	'installurl'   => '',
),
'expa' => array(
	'title'        => 'Expand Partition',
	'maintainer'   => 'r e r n',
	'description'  => 'Expand default 2GB partition to full capacity of SD card.',
	'buttonlabel'  => 'Expand',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/expand_partition',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/expand_partition/expand.sh',
	'hide'         => array(
		'exec'       => array(
			'[[ $( redis-cli hget addons expa ) != 1 && '.$unpartmb.' < 10 ]] && echo 1',
		),
	),
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
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/motd/thumbmotd.png',
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
	'description'  => 'Upgrade MPD to latest version, 0.20.14 as of 20180102:'
					.'<br>Fix conflicts, missing lib symlinks, missing packages'
					.'<br>But <white>broken Midori</white>, local browser.',
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/mpd/thumbmpdu.png',
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
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/rankmirrors/thumbrank.png',
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
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/backup-restore/thumbback.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/backup-restore',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/backup-restore/install.sh',
),
'enha' => array(
	'title'        => 'RuneUI Enhancements *',
	'version'      => '20180312',
	'revision'     => 'Lighter time knob, consistent volume color, shadow border coverart'
					.'<br>...'
					.'<br>Fix: click back from setting pages'
					.'<br>Fix: auto hide buttons hidden on click'
					.'<br>Preserve mute volume position on refresh/reload'
					.'<br>Volume number = mute button'
					.'<br>Time number = cover art center (play|pause|long-press stop)'
					.'<br>Disable scroll wheel on time and volume knob'
					.'<br>...'
					.'<br>Rewrite touch gestures to use coverart as main control'
					.'<br>Remove gestures on left/right of screen'
					.'<br>Improve bio page'
					.'<br>Fix: hide-by-touch/click elements reappeared on play/pause or track changed'
					.'<br>Fix: Browse Library button',
	'maintainer'   => 'r e r n',
	'description'  => 'More <white>minimalism</white> and more <white>fluid</white> layout.',
	'thumbnail'    => 'https://github.com/rern/_assets/raw/master/RuneUI_enhancement/thumbenha.gif',
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
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/font_extended/thumbfont.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/font_extended',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/font_extended/install.sh',
),
'gpio' => array(
	'title'        => 'RuneUI GPIO *',
	'version'      => '20180310',
	'needspace'    => 5,
	'revision'     => 'Now use only plain BASH and Python - no PIP install needed'
					.'<br>...'
					.'<br>Split MPD configuration loading to <white>USB DAC Auto Switch</white>'
					.'<br>...'
					.'<br>Switch to <white>high trigger</white> relay module',
	'maintainer'   => 'r e r n',
	'description'  => 'GPIO-connected relay module control for power on / off audio equipments.',
	'thumbnail'    => 'https://github.com/rern/_assets/raw/master/RuneUI_GPIO/thumbgpio.gif',
	'sourcecode'   => 'https://github.com/rern/RuneUI_GPIO',
	'installurl'   => 'https://github.com/rern/RuneUI_GPIO/raw/master/install.sh',
),
'lyri' => array(
	'title'        => 'RuneUI Lyrics',
	'version'      => '20180219',
	'revision'     => 'Fix <white>long-press on song title</white> to not also open lyrics editor'
					.'<br>...'
					.'<br>Disable in WebRadio',
	'maintainer'   => 'r e r n',
	'description'  => 'Improve lyrics feature in 0.4b / add lyrics feature in 0.3',
	'thumbnail'    => 'https://github.com/rern/_assets/raw/master/Lyrics/lyricsS.gif',
	'sourcecode'   => 'https://github.com/RuneAddons/Lyrics',
	'installurl'   => 'https://github.com/RuneAddons/Lyrics/raw/master/install.sh',
),
'pass' => array(
	'title'        => 'RuneUI Password',
	'version'      => '20170901',
	'revision'     => 'Initial release',
	'maintainer'   => 'r e r n',
	'description'  => 'RuneUI access restriction.',
	'thumbnail'    => 'https://github.com/rern/_assets/raw/master/RuneUI_password/thumbpass.gif',
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
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/pause_button/thumbpaus.gif',
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
	'thumbnail'    => 'https://github.com/RuneAddons/RuneYoutube/raw/master/cover.png',
	'sourcecode'   => 'https://github.com/RuneAddons/RuneYoutube',
	'installurl'   => 'https://github.com/RuneAddons/RuneYoutube/raw/master/install.sh',
),
'samb' => array(
	'title'        => 'Samba Upgrade *',
	'needspace'    => 43,
	'maintainer'   => 'r e r n',
	'description'  => 'Faster and more customized shares.',
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/samba/thumbsamb.png',
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
	'version'      => '20171210',
	'needspace'    => 9,
	'revision'     => 'Fix bugs by reverting back to custom package.',
	'maintainer'   => 'r e r n',
	'description'  => 'Fast, easy, and free BitTorrent client. Pre-configured and ready to use.',
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/transmission/thumbtran.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/transmission',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/transmission/install.sh',
	'option'       => array(
		'password'   => array(
			'message'  => 'Password for user <white>root</white> (blank = no password):',
			'label'    => 'Password'
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
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/USB_DAC/thumbudac.png',
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
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/webradio/thumbwebr.png',
	'buttonlabel'  => 'Import',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/webradio',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/webradio/webradiodb.sh',
	'option'       => array(
		'wait'       => 'Get webradio <code>*.pls</code> or <code>folders</code> copied to:'
					.'<br><code>/mnt/MPD/Webradio</code>'
					.'<br>'
					.'<br><code>&emsp;Ok&emsp;</code> to continue'
	),
),
	
);
