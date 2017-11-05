<?php
$addons = array(

array(
	'alias'        => 'addo',
	'title'        => 'Addons Menu',
	'version'      => '20171111',
	'revision'     => '<li>Check available disk space before update</li>
				<li>Show available disk space</li>',
	'maintainer'   => 'r e r n',
	'description'  => 'This Addons Menu main page.',
	'thumbnail'    => 'https://github.com/rern/_assets/raw/master/RuneAudio_Addons/addonsthumb.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio_Addons',
	'installurl'   => 'https://github.com/rern/RuneAudio_Addons/raw/master/install.sh',
),
array(
	'alias'        => 'bash',
	'title'        => 'BASH Command',
	'maintainer'   => 'r e r n',
	'description'  => 'Run BASH commands or scripts like on SSH terminal.',
	'thumbnail'    => 'https://github.com/rern/_assets/raw/master/thumbnail/thumbbash.png',
	'buttonlabel'  => 'Run',
	'sourcecode'   => '',
	'installurl'   => '',
	'option'       => "{
		'text': {
			'message': '<white>BASH</white> commands or /full/path/script:',
			'label': 'commands'
		}
	}"
),
array(
	'alias'        => 'dual',
	'title'        => 'Dual Boot: RuneAudio + OSMC *',
	'maintainer'   => 'r e r n',
	'description'  => 'Best of Audio Distro - <white>RuneAudio</white> 0.3 + Addons Menu ready (ArchLinux MPD)
					<br>Best of Video Distro - <white>OSMC</white> 2017-08-1 (Raspbian Kodi)
					<br>Best of Dual Boot - <white>NOOBS</white> 2.4',
	'thumbnail'    => 'https://github.com/rern/_assets/raw/master/RPi2-3.Dual.Boot-Rune.OSMC/thumbdual.gif',
	'buttonlabel'  => 'Link',
	'sourcecode'   => 'http://www.runeaudio.com/forum/dual-boot-noobs-rune-osmc-pi2-pi3-t3822.html',
	'installurl'   => '',
),
array(
	'alias'        => 'enha',
	'title'        => 'RuneUI Enhancements *',
	'version'      => '20171111',
	'revision'     => '<li>Add long-press for <white>show/hide items setting</white></li>
				<li>Add <white>Index bar</white> in Library</li>
				<li><white>Breadcrumb links</white> for path shortcut jump</li>
				<li>Fix <white>Library sorting</white></li>
				<li>Add <white>swipe L/R</white> to switch between Library - Playback - Queue</li>',
	'maintainer'   => 'r e r n',
	'description'  => 'More <white>minimalism</white> and more <white>fluid</white> layout.',
	'thumbnail'    => 'https://github.com/rern/_assets/raw/master/RuneUI_enhancement/thumbenha.gif',
	'sourcecode'   => 'https://github.com/rern/RuneUI_enhancement',
	'installurl'   => 'https://github.com/rern/RuneUI_enhancement/raw/master/install.sh',
	'option'       => "{
		'radio': {
			'message': 'Set <white>zoom level</white> for display directly connect to RPi.<br>
						<br>
						Local browser screen size:',
			'list'   : {
				'Width less than 800px: 0.7': '0.7',
				'HD - 1280px: 1.2': '1.2',
				'*Full HD - 1920px: 1.5': '1.5',
				'Custom': '?'
			}
		}
	}"
),
array(
	'alias'        => 'gpio',
	'title'        => 'RuneUI GPIO *',
	'version'      => '20171020',
	'revision'     => '<li>General improvements</li>
				<li>Switch to online package installation</li>',
	'maintainer'   => 'r e r n',
	'description'  => 'GPIO connected relay module control.',
	'thumbnail'    => 'https://github.com/rern/_assets/raw/master/RuneUI_GPIO/thumbgpio.gif',
	'sourcecode'   => 'https://github.com/rern/RuneUI_GPIO',
	'installurl'   => 'https://github.com/rern/RuneUI_GPIO/raw/master/install.sh',
	'option'       => "{
		'wait': 'Get <white>DAC configuration</white> ready:<br>
				<br>
				For external power: <white>DAC</white> > power on<br>
				<code>Menu</code> > <code>MPD</code> > <code>setup</code><br>
				Ensure <white>DAC</white> works properly before continue.'
	}"
),
array(
	'alias'        => 'pass',
	'title'        => 'RuneUI Password *',
	'version'      => '20170901',
	'revision'     => '<li>Initial release</li>',
	'only03'       => '1',
	'maintainer'   => 'r e r n',
	'description'  => 'RuneUI access restriction.',
	'thumbnail'    => 'https://github.com/rern/_assets/raw/master/RuneUI_password/thumbpass.gif',
	'sourcecode'   => 'https://github.com/RuneAddons/RuneUI_password',
	'installurl'   => 'https://github.com/RuneAddons/RuneUI_password/raw/master/install.sh',
),
array(
	'alias'        => 'aria',
	'title'        => 'Aria2 *',
	'version'      => '20170901',
	'revision'     => '<li>Initial release</li>',
	'maintainer'   => 'r e r n',
	'description'  => 'Download utility that supports HTTP(S), FTP, BitTorrent, and Metalink.
			<br>Pre-configured and ready to use.',
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/aria2/thumbaria.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/aria2',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/aria2/install.sh',
	'option'       => "{
		'yesno': 'Start <white>Aria2</white> on system startup?'
	}"
),
array(
	'alias'        => 'tran',
	'title'        => 'Transmission *',
	'version'      => '20171022',
	'revision'     => '<li>Switch from custom package to normal for easy update.</li>',
	'maintainer'   => 'r e r n',
	'description'  => 'Fast, easy, and free BitTorrent client. Pre-configured and ready to use.',
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/transmission/thumbtran.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/transmission',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/transmission/install.sh',
	'option'       => "{
		'password': {
			'message': 'Password for user <white>root</white> (blank = no password):',
			'label'  : 'Password'
		},
		'checkbox': {
			'message': '',
			'list'   : {
				'*Install <white>WebUI</white> alternative?': '1',
				'*Start <white>Transmission</white> on system startup?': '1'
			}
		}
	}"
),
array(
	'alias'        => 'samb',
	'title'        => 'Samba Upgrade *',
	'maintainer'   => 'r e r n',
	'description'  => 'Faster and more customized shares.',
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/samba/thumbsamb.png',
	'buttonlabel'  => 'Upgrade',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/samba',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/samba/install.sh',
	'option'       => "{
		'confirm' : 'Once installed, Samba <white>cannot be downgraded</white>.
				<br>Continue?',
		'wait'    : 'Connect a <white>USB drive</white> before continue.
				<br>1st drive will be used for shared directories.',
		'password': {
			'message': '(for connecting to <white>USB root share</white>)
					<br>Password for user <white>root</white> (blank = rune):',
			'label'  : 'Password'
		},
		'text1'   : {
			'message': '<white>File Server</white>:',
			'label'  : 'Name',
			'value'  : 'RuneAudio'
		},
		'text2'   : {
			'message': '<white>Read-Only</white> directory:',
			'label'  : 'Name'
		},
		'text3'   : {
			'message': '<white>Read-Write</white> directory:',
			'label'  : 'Name'
		}
	}"
),
array(
	'alias'        => 'mpdu',
	'title'        => 'MPD Upgrade *',
	'maintainer'   => 'r e r n',
	'description'  => 'Upgrade MPD to latest version <white>without errors</white>:
				conflicts, missing libs, missing packages, broken Midori.',
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/mpd/thumbmpdu.png',
	'buttonlabel'  => 'Upgrade',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/mpd',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/mpd/install.sh',
	'option'       => "{
		'confirm' : 'Once installed, MPD <white>cannot be downgraded</white>.
				<br>10 minutes upgrade may take 20+ minutes with slow download.
				<br>Continue?'
	}"
),
array(
	'alias'        => 'back',
	'title'        => 'Backup-Restore Update',
	'version'      => '20170901',
	'revision'     => '<li>Initial release</li>',
	'maintainer'   => 'r e r n',
	'description'  => 'Enable backup-restore settings and databases.',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/backup-restore',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/backup-restore/install.sh',
),
array(
	'alias'        => 'expa',
	'title'        => 'Expand Partition',
	'maintainer'   => 'r e r n',
	'description'  => 'Expand default 2GB partition to full capacity of SD card.',
	'buttonlabel'  => 'Expand',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/expand_partition',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/expand_partition/expand.sh',
	'option'       => "{
		'wait': 'Unmount and remove all <white>USB drives</white> before proceeding.'
	}"
),
array(
	'alias'        => 'font',
	'title'        => 'Fonts - Extended Characters',
	'version'      => '20170901',
	'revision'     => '<li>Initial release</li>',
	'maintainer'   => 'r e r n',
	'description'  => 'Font files replacement for Extended Latin-based, Cyrillic-based, Greek and IPA phonetics.',
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/font_extended/thumbfont.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/font_extended',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/font_extended/install.sh',
),
array(
	'alias'        => 'motd',
	'title'        => 'Login Logo for SSH Terminal',
	'version'      => '20170901',
	'revision'     => '<li>Initial release</li>',
	'maintainer'   => 'r e r n',
	'description'  => 'Message of the day - RuneAudio Logo and dimmed command prompt.',
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/motd/thumbmotd.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/motd',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/motd/install.sh',
),
array(
	'alias'        => 'spla',
	'title'        => 'Boot Logo *',
	'version'      => '20171010',
	'revision'     => '<li>Initial release</li>',
	'only03'       => '1',
	'maintainer'   => 'r e r n',
	'description'  => 'Display RuneAudio logo during boot - Splash screen.',
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/boot_splash/thumbspla.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/boot_splash',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/boot_splash/install.sh',
),
array(
	'alias'        => 'rank',
	'title'        => 'Rank Mirror Package Servers',
	'maintainer'   => 'r e r n',
	'description'  => 'Fix package download errors caused by unreachable servers.',
	'buttonlabel'  => 'Rank',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/rankmirrors',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/rankmirrors/rankmirrors.sh',
),
array(
	'alias'        => 'webr',
	'title'        => 'Webradio Import',
	'maintainer'   => 'r e r n',
	'description'  => 'Webradio files import. Adding files to <code>/mnt/MPD/Webradio/</code> alone will not work.
			<br>Add files at anytime then run this addon to refresh Webradio list.
			<br><white>Webradio Sorting</white> should be installed after import on 0.3.',
	'buttonlabel'  => 'Import',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/webradio',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/webradio/webradiodb.sh',
	'option'       => "{
		'wait': 'Get webradio files copied to:<br>
				<code>/mnt/MPD/Webradio</code><br>
				<br>
				<code>&emsp;Ok&emsp;</code> to continue'
	}"
),
	
);
?>
