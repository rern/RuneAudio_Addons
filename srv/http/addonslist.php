<?php
// sorting          : 'title' ascending
// fixed order      : 'alias', 'title', 'version'
// non-install type : 'version' omitted
// optional         : 'buttonlabel', 'option', 'thumbnail'
// star badge       : '*' after 'title'

$addons = array(
// array start ----------------------------------------------------------------------------------------------------
array(
	'alias'        => 'addo',
	'title'        => 'Addons Menu',
	'version'      => $addonsversion, // only this one, edit version number in /changelog.md
	'maintainer'   => 'r e r n',
	'description'  => 'This Addons main page.<br>'
			.'<span>Addons Menu installed before 20170906 needs uninstall then reinstall via SSH terminal.</span>',
	'thumbnail'    => 'https://github.com/rern/_assets/raw/master/RuneAudio_Addons/addonsthumb.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio_Addons',
	'installurl'   => 'https://github.com/rern/RuneAudio_Addons/raw/master/install.sh',
),
array(
	'alias'        => 'bash',
	'title'        => 'BASH Command',
	'maintainer'   => 'r e r n',
	'description'  => 'Run BASH commands or scripts like on SSH terminal.',
	'buttonlabel'  => 'Run',
	'sourcecode'   => '',
	'installurl'   => '',
	'option'       => "{
		'prompt': {
			'message': 'BASH commands or /full/path/script:',
			'label': 'commands'
		}
	}"
),
array(
	'alias'        => 'enha',
	'title'        => 'RuneUI Enhancements *',
	'version'      => '20170901',
	'maintainer'   => 'r e r n',
	'description'  => 'More minimalism and more fluid layout.',
	'thumbnail'    => 'https://github.com/rern/_assets/raw/master/RuneUI_enhancement/thumbenha.gif',
	'sourcecode'   => 'https://github.com/rern/RuneUI_enhancement',
	'installurl'   => 'https://github.com/rern/RuneUI_enhancement/raw/master/install.sh',
	'option'       => "{
		'radio': {
			'message': 'Set zoom level for display directly connect to RPi.<br>
						<br>
						Local browser screen size:',
			'list': {
				'0.7 : width less than 800px': '0.7',
				'1.2 : HD - 1280px': '1.2',
				'*1.5 : Full HD - 1920px': '1.5'
			}
		}
	}"
),
array(
	'alias'        => 'gpio',
	'title'        => 'RuneUI GPIO *',
	'version'      => '20170901',
	'maintainer'   => 'r e r n',
	'description'  => 'GPIO connected relay module control.',
	'thumbnail'    => 'https://github.com/rern/_assets/raw/master/RuneUI_GPIO/GPIOs/4.jpg',
	'sourcecode'   => 'https://github.com/rern/RuneUI_GPIO',
	'installurl'   => 'https://github.com/rern/RuneUI_GPIO/raw/master/install.sh',
	'option'       => "{
		'alert': 'Get DAC configuration ready:<br>
				<br>
				For external power DAC > power on<br>
				Menu > MPD > setup and verify DAC works properly before continue.'
	}"
),
array(
	'alias'        => 'pass',
	'title'        => 'RuneUI Password *',
	'version'      => '20170901',
	'maintainer'   => 'r e r n',
	'description'  => 'RuneUI access restriction.',
	'thumbnail'    => 'https://github.com/rern/_assets/raw/master/RuneUI_password/thumbpass.gif',
	'sourcecode'   => 'https://github.com/rern/RuneUI_password',
	'installurl'   => 'https://github.com/rern/RuneUI_password/raw/master/install.sh',
),
array(
	'alias'        => 'aria',
	'title'        => 'Aria2 *',
	'version'      => '20170901',
	'maintainer'   => 'r e r n',
	'description'  => 'Download utility that supports HTTP(S), FTP, BitTorrent, and Metalink.'
			.'<br> Pre-configured and ready to use.',
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/aria2/thumbaria.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/aria2',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/aria2/install.sh',
	'option'       => "{
		'confirm': 'Start &quot;Aria2&quot; on system startup?'
	}"
),
array(
	'alias'        => 'tran',
	'title'        => 'Transmission *',
	'version'      => '20170901',
	'maintainer'   => 'r e r n',
	'description'  => 'Fast, easy, and free BitTorrent client.'
			.'<br> Pre-configured and ready to use.',
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/transmission/thumbtran.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/transmission',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/transmission/install.sh',
	'option'       => "{
		'password': {
			'message': 'Password for user &quot;root&quot; (blank = no password):',
			'label': 'Password'
		},
		'confirm': 'Install WebUI alternative (Transmission Web Control)?',
		'confirm1': 'Start &quot;Transmission&quot; on system startup?'
	}"
),
/*array(
	'alias'        => 'samb',
	'title'        => 'Samba Upgrade',
	'maintainer'   => 'r e r n',
	'description'  => 'Faster and more customized shares.',
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/samba/thumbsamb.png',
	'buttonlabel'  => 'Upgrade',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/samba',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/samba/installurl',
	'option'       => "{
		'password': {
			'message': 'Password for user &quot;root&quot; (blank = no password):',
			'label': 'Password'
		}
	}"
),*/

array(
	'alias'        => 'back',
	'title'        => 'Backup-Restore Update',
	'version'      => '20170901',
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
		'alert': 'Unmount and remove all USB drives before proceeding.'
	}"
),
array(
	'alias'        => 'font',
	'title'        => 'Fonts - Extended Characters',
	'version'      => '20170901',
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
	'maintainer'   => 'r e r n',
	'description'  => 'Message of the day - RuneAudio Logo and dimmed command prompt.',
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/motd/thumbmotd.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/motd',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/motd/install.sh',
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
	'description'  => 'Webradio files import.',
	'buttonlabel'  => 'Import',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/webradio',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/webradio/webradiodb.sh',
	'option'       => "{
		'alert': 'Get webradio files copied to:<br>
				/mnt/MPD/Webradio<br>
				<br>
				Ok to continue'
	}"
),
// array end ----------------------------------------------------------------------------------------------------
);
