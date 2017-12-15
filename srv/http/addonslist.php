<?php
$GLOBALS[ 'addons' ] = array(

'ngin' => array(
	'title'        => 'NGINX Upgrade',
	'maintainer'   => 'r e r n',
	'description'  => 'Upgrade from default **NGINX** 1.4.7 to 1.13.7 <white>without errors</white>:'
					.'<br>RuneAudio needs NGINx with pushstream',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/raw/master/nginx',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/nginx/install.sh',
),
'RuneYoutube' => array(
	'title'        => 'Rune Youtube',
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
'lyri' => array(
	'title'        => 'Lyrics',
	'version'      => '20171204',
	'revision'     => 'Fix non movable cursor in editing box on touch device'
					.'<br>Switch <white>long-press lyrics</white> to <white>edit button<white> for editing'
					.'<br>Keep position between view and edit',
	'maintainer'   => 'r e r n',
	'description'  => 'Improve lyrics feature in 0.4b / add lyrics feature in 0.3',
	'sourcecode'   => 'https://github.com/RuneAddons/Lyrics',
	'installurl'   => 'https://github.com/RuneAddons/Lyrics/raw/master/install.sh',
),
'addo' => array(
	'title'        => 'Addons Menu',
	'version'      => '20171210',
	'revision'     => 'General improvements',
	'maintainer'   => 'r e r n',
	'description'  => 'This Addons Menu main page.',
	'thumbnail'    => 'https://github.com/rern/_assets/raw/master/RuneAudio_Addons/addonsthumb.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio_Addons',
	'installurl'   => 'https://github.com/rern/RuneAudio_Addons/raw/master/install.sh',
),
'bash' => array(
	'title'        => 'BASH Command',
	'maintainer'   => 'r e r n',
	'description'  => 'Run BASH commands or scripts like on SSH terminal.',
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
'enha' => array(
	'title'        => 'RuneUI Enhancements *',
	'version'      => '20171204',
	'revision'     => 'Fix RuneUI stuck at spinning icon <white>if disable local browser</white>'
					.'<br>Fix left and right of the screen not response after swipe to playback'
					.'<br>Keep library list position after db search'
					.'<br>Fix <white>missing context menu</white> after Library search',
	'maintainer'   => 'r e r n',
	'description'  => 'More <white>minimalism</white> and more <white>fluid</white> layout.',
	'thumbnail'    => 'https://github.com/rern/_assets/raw/master/RuneUI_enhancement/thumbenha.gif',
	'sourcecode'   => 'https://github.com/rern/RuneUI_enhancement',
	'installurl'   => 'https://github.com/rern/RuneUI_enhancement/raw/master/install.sh',
	'option'       => array(
		'radio'      => array(
			'message'  => 'Set <white>zoom level</white> for display directly connect to RPi.'
						.'<br>'
						.'<br>Local browser screen size:',
			'list'     => array(
				'Width less than 800px: 0.7' => '0.7',
				'HD - 1280px: 1.2'           => '1.2',
				'*Full HD - 1920px: 1.5'     => '1.5',
				'Custom'                     => '?'
			),
		),
	),
),
'gpio' => array(
	'title'        => 'RuneUI GPIO *',
	'version'      => '20171207',
	'needspace'    => 15,
	'revision'     => 'Fix uninstall breaks runeui.css'
					.'<br>Remove pnotify3.js for addonsinfo.js',
	'maintainer'   => 'r e r n',
	'description'  => 'GPIO connected relay module control.',
	'thumbnail'    => 'https://github.com/rern/_assets/raw/master/RuneUI_GPIO/thumbgpio.gif',
	'sourcecode'   => 'https://github.com/rern/RuneUI_GPIO',
	'installurl'   => 'https://github.com/rern/RuneUI_GPIO/raw/master/install.sh',
	'option'       => array(
		'wait'       => 'Get <white>DAC configuration</white> ready:'
					.'<br>'
					.'<br>For external power: <white>DAC</white> > power on'
					.'<br><code>Menu</code> > <code>MPD</code> > <code>setup</code>'
					.'<br>Ensure <white>DAC</white> works properly before continue.'
	),
),
'pass' => array(
	'title'        => 'Password *',
	'version'      => '20170901',
	'revision'     => 'Initial release',
	'only03'       => '1',
	'maintainer'   => 'r e r n',
	'description'  => 'RuneUI access restriction.',
	'thumbnail'    => 'https://github.com/rern/_assets/raw/master/RuneUI_password/thumbpass.gif',
	'sourcecode'   => 'https://github.com/RuneAddons/Password',
	'installurl'   => 'https://github.com/RuneAddons/Password/raw/master/install.sh',
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
		'wait'       => 'Connect a <white>USB drive</white> before continue.'
					.'<br>1st drive will be used for shared directories.',
		'password'   => array(
			'message'  => '(for connecting to <white>USB root share</white>)'
						.'<br>Password for user <white>root</white> (blank = rune):',
			'label'    => 'Password'
		),
		'text1'      => array(
			'message'  => '<white>File Server</white>:',
			'label'    => 'Name',
			'value'    => 'RuneAudio'
		),
		'text2'      => array(
			'message'  => '<white>Read-Only</white> directory:',
			'label'    => 'Name'
		),
		'text3'      => array(
			'message'  => '<white>Read-Write</white> directory:',
			'label'    => 'Name'
		),
	),
),
'mpdu' => array(
	'title'        => 'MPD Upgrade *',
	'needspace'    => 192,
	'maintainer'   => 'r e r n',
	'description'  => 'Upgrade MPD to latest version <white>without errors</white>:'
					.'<br>conflicts, missing libs, missing packages, broken Midori.',
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/mpd/thumbmpdu.png',
	'buttonlabel'  => 'Upgrade',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/mpd',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/mpd/install.sh',
	'option'       => array(
		'confirm'    => 'Once installed, MPD <white>cannot be downgraded</white>.'
					.'<br>10 minutes upgrade may take 20+ minutes'
					.'<br>with slow download.'
					.'<br>Continue?'
	),
),
'back' => array(
	'title'        => 'Backup-Restore Update',
	'version'      => '20170901',
	'revision'     => 'Initial release',
	'maintainer'   => 'r e r n',
	'description'  => 'Enable backup-restore settings and databases.',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/backup-restore',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/backup-restore/install.sh',
),
'expa' => array(
	'title'        => 'Expand Partition',
	'maintainer'   => 'r e r n',
	'description'  => 'Expand default 2GB partition to full capacity of SD card.',
	'buttonlabel'  => 'Expand',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/expand_partition',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/expand_partition/expand.sh',
	'option'       => array(
		'wait'       => 'Unmount and remove all <white>USB drives</white> before proceeding.'
	),
),
'font' => array(
	'title'        => 'Fonts - Extended Characters',
	'version'      => '20170901',
	'needspace'    => 9,
	'revision'     => 'Initial release',
	'maintainer'   => 'r e r n',
	'description'  => 'Font files replacement for Extended Latin-based, Cyrillic-based, Greek and IPA phonetics.',
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/font_extended/thumbfont.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/font_extended',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/font_extended/install.sh',
),
'motd' => array(
	'title'        => 'Login Logo for Terminal',
	'version'      => '20170901',
	'revision'     => 'Initial release',
	'maintainer'   => 'r e r n',
	'description'  => 'Message of the day - RuneAudio Logo and dimmed command prompt.',
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/motd/thumbmotd.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/motd',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/motd/install.sh',
),
'spla' => array(
	'title'        => 'Boot Logo *',
	'version'      => '20171010',
	'revision'     => 'Initial release',
	'only03'       => '1',
	'maintainer'   => 'r e r n',
	'description'  => 'Display RuneAudio logo during boot - Splash screen.',
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/boot_splash/thumbspla.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/boot_splash',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/boot_splash/install.sh',
),
'rank' => array(
	'title'        => 'Rank Mirror Package Servers',
	'maintainer'   => 'r e r n',
	'description'  => 'Fix package download errors caused by unreachable servers.',
	'buttonlabel'  => 'Rank',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/rankmirrors',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/rankmirrors/rankmirrors.sh',
),
'webr' => array(
	'title'        => 'Webradio Import',
	'maintainer'   => 'r e r n',
	'description'  => 'Webradio files import. Adding files to <code>/mnt/MPD/Webradio/</code> alone will not work.'
					.'<br>Add files at anytime then run this addon to refresh Webradio list.'
					.'<br><white>Webradio Sorting</white> should be installed after import on 0.3.',
	'buttonlabel'  => 'Import',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/webradio',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/webradio/webradiodb.sh',
	'option'       => array(
		'wait'       => 'Get webradio files copied to:'
					.'<br><code>/mnt/MPD/Webradio</code>'
					.'<br>'
					.'<br><code>&emsp;Ok&emsp;</code> to continue'
	),
),
	
);
