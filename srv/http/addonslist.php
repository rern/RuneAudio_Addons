<?php
// sorting          : 'title' ascending
// fixed order      : 'alias', 'title', 'version'
// non-install type : 'version' omitted
// optional         : 'buttonlabel', 'option', 'thumbnail'
// star badge       : '*' after 'title'

$pathrern = 'https://github.com/rern';
$pathassets = "$pathrern/_assets/raw/master";
$pathrune = "$pathrern/RuneAudio/raw/UPDATE";
$pathrunesource = "$pathrern/RuneAudio/tree/master";

$addons = array(
// array start ----------------------------------------------------------------------------------------------------
array(
	'alias'        => 'addo',
	'title'        => '0',
	'version'      => $addonsversion, // only this one, edit version number in /changelog.md
	'maintainer'   => 'r e r n',
	'description'  => 'This Addons Menu main page.'
			.'<br><white>Addons Menu installed before 20170930 must be updated.</white>',
	'thumbnail'    => "$pathassets/RuneAudio_Addons/addonsthumb.png",
	'sourcecode'   => "$pathrern/RuneAudio_Addons",
	'installurl'   => "$pathrern/RuneAudio_Addons/raw/UPDATE/install.sh",
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
			'message': '<white>BASH</white> commands or /full/path/script:',
			'label': 'commands'
		}
	}"
),
array(
	'alias'        => 'enha',
	'title'        => 'RuneUI Enhancements *',
	'version'      => '20170925',
	'maintainer'   => 'r e r n',
	'description'  => 'More <white>minimalism</white> and more <white>fluid</white> layout. (0.3+0.4b)',
	'thumbnail'    => "$pathassets/RuneUI_enhancement/thumbenha.gif",
	'sourcecode'   => "$pathrern/RuneUI_enhancement",
	'installurl'   => "$pathrern/RuneUI_enhancement/raw/UPDATE/install.sh",
	'option'       => "{
		'radio': {
			'message': 'Set <white>zoom level</white> for display directly connect to RPi.<br>
						<br>
						Local browser screen size:',
			'list': {
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
	'version'      => '20170901',
	'maintainer'   => 'r e r n',
	'description'  => 'GPIO connected relay module control.',
	'thumbnail'    => "$pathassets/RuneUI_GPIO/GPIOs/4.jpg",
	'sourcecode'   => "$pathrern/RuneUI_GPIO",
	'installurl'   => "$pathrern/RuneUI_GPIO/raw/UPDATE/install.sh",
	'option'       => "{
		'alert': 'Get <white>DAC configuration</white> ready:<br>
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
	'maintainer'   => 'r e r n',
	'description'  => 'RuneUI access restriction.',
	'thumbnail'    => "$pathassets/RuneUI_password/thumbpass.gif",
	'sourcecode'   => "$pathrern/RuneUI_password",
	'installurl'   => "$pathrern/RuneUI_password/raw/UPDATE/install.sh",
),
array(
	'alias'        => 'aria',
	'title'        => 'Aria2 *',
	'version'      => '20170901',
	'maintainer'   => 'r e r n',
	'description'  => 'Download utility that supports HTTP(S), FTP, BitTorrent, and Metalink.'
			.'<br> Pre-configured and ready to use.',
	'thumbnail'    => "$pathrune/aria2/thumbaria.png",
	'sourcecode'   => "$pathrunesource/aria2",
	'installurl'   => "$pathrune/aria2/install.sh",
	'option'       => "{
		'confirm': 'Start <white>Aria2</white> on system startup?'
	}"
),
array(
	'alias'        => 'tran',
	'title'        => 'Transmission *',
	'version'      => '20170901',
	'maintainer'   => 'r e r n',
	'description'  => 'Fast, easy, and free BitTorrent client.'
			.'<br> Pre-configured and ready to use.',
	'thumbnail'    => "$pathrune/transmission/thumbtran.png",
	'sourcecode'   => "$pathrunesource/transmission",
	'installurl'   => "$pathrune/transmission/install.sh",
	'option'       => "{
		'password': {
			'message': 'Password for user <white>root</white> (blank = no password):',
			'label': 'Password'
		},
		'checkbox': {
			'message': '',
			'list': {
				'*Install <white>WebUI</white> alternative?': '1',
				'*Start <white>Transmission</white> on system startup?': '1'
			}
		}
	}"
),
/*array(
	'alias'        => 'samb',
	'title'        => 'Samba Upgrade',
	'maintainer'   => 'r e r n',
	'description'  => 'Faster and more customized shares.',
	'thumbnail'    => "$pathrune/samba/thumbsamb.png',
	'buttonlabel'  => 'Upgrade',
	'sourcecode'   => "$pathrunesource/samba",
	'installurl'   => "$pathrune/samba/installurl",
	'option'       => "{
		'password': {
			'message': 'Password for user <white>root</white> (blank = no password):',
			'label': 'Password'
		}
	}"
),*/

array(
	'alias'        => 'back',
	'title'        => 'Backup-Restore Update',
	'version'      => '20170901',
	'maintainer'   => 'r e r n',
	'description'  => 'Enable backup-restore settings and databases. (0.3+0.4b)',
	'sourcecode'   => "$pathrunesource/backup-restore",
	'installurl'   => "$pathrune/backup-restore/install.sh",
),
array(
	'alias'        => 'expa',
	'title'        => 'Expand Partition',
	'maintainer'   => 'r e r n',
	'description'  => 'Expand default 2GB partition to full capacity of SD card. (0.3+0.4b)',
	'buttonlabel'  => 'Expand',
	'sourcecode'   => "$pathrunesource/expand_partition",
	'installurl'   => "$pathrune/expand_partition/expand.sh",
	'option'       => "{
		'alert': 'Unmount and remove all <white>USB drives</white> before proceeding.'
	}"
),
array(
	'alias'        => 'font',
	'title'        => 'Fonts - Extended Characters',
	'version'      => '20170901',
	'maintainer'   => 'r e r n',
	'description'  => 'Font files replacement for Extended Latin-based, Cyrillic-based, Greek and IPA phonetics. (0.3+0.4b)',
	'thumbnail'    => "$pathrune/font_extended/thumbfont.png",
	'sourcecode'   => "$pathrunesource/font_extended",
	'installurl'   => "$pathrune/font_extended/install.sh",
),
array(
	'alias'        => 'motd',
	'title'        => 'Login Logo for SSH Terminal',
	'version'      => '20170901',
	'maintainer'   => 'r e r n',
	'description'  => 'Message of the day - RuneAudio Logo and dimmed command prompt.',
	'thumbnail'    => "$pathrune/motd/thumbmotd.png",
	'sourcecode'   => "$pathrunesource/motd",
	'installurl'   => "$pathrune/motd/install.sh",
),
array(
	'alias'        => 'rank',
	'title'        => 'Rank Mirror Package Servers',
	'maintainer'   => 'r e r n',
	'description'  => 'Fix package download errors caused by unreachable servers. (0.3+0.4b)',
	'buttonlabel'  => 'Rank',
	'sourcecode'   => "$pathrunesource/rankmirrors",
	'installurl'   => "$pathrune/rankmirrors/rankmirrors.sh",
),
array(
	'alias'        => 'radi',
	'title'        => 'Webradio Import',
	'maintainer'   => 'r e r n',
	'description'  => 'Webradio files import. Adding files to <code>/mnt/MPD/Webradio/</code> alone will not work.'
			.'<br>Add files at anytime then start this addon to refresh Webradio list'
			.'<br><white>Webradio Sorting</white> should be installed as well.</white>',
	'buttonlabel'  => 'Import',
	'sourcecode'   => "$pathrunesource/webradio",
	'installurl'   => "$pathrune/webradio/webradiodb.sh",
	'option'       => "{
		'alert': 'Get webradio files copied to:<br>
				<code>/mnt/MPD/Webradio</code><br>
				<br>
				<code>&emsp;Ok&emsp;</code> to continue'
	}"
),
array(
	'alias'        => 'webr',
	'title'        => 'Webradio Sorting',
	'version'      => '20170925',
	'maintainer'   => 'r e r n',
	'description'  => 'Fix Webradio sorting.',
	'sourcecode'   => "$pathrunesource/webradio",
	'installurl'   => "$pathrune/webradio/install.sh",
),
// array end ----------------------------------------------------------------------------------------------------
);
