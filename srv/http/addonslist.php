<?php
$addonsversion = '20171001';
$revision = 
'<a id="revision"><white>'.$addonsversion.'</white>&ensp;revision&ensp;▼</a><br>
<div  id="detail" style="display: none;">
	<ul>
		<li>Fix missing installed status</li>
		<li>Fully auto update</li>
		<li>Improve terminal messages and errors handling</li>
	</ul>
	<a href="https://github.com/rern/RuneAudio_Addons/blob/update/changelog.md" target="_blank">
		changelog &nbsp;<i class="fa fa-external-link"></i>
	</a><br>
	<br>
</div>
';
// fixed order      : 'alias' must be at 1st of each array
// non-install type : 'version' line omitted (run once: set version to database on install)
// optional         : 'buttonlabel', 'option', 'thumbnail'
// star badge       : '*' after 'title' value

$addons = array(
// array start ----------------------------------------------------------------------------------------------------
array(
	'alias'        => 'addo',
	'title'        => 'Addons Menu',
	'version'      => $addonsversion,
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
	'thumbnail'    => 'https://github.com/rern/_assets/raw/master/RuneUI_enhancement/thumbenha.gif',
	'sourcecode'   => 'https://github.com/rern/RuneUI_enhancement',
	'installurl'   => 'https://github.com/rern/RuneUI_enhancement/raw/master/install.sh',
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
	'thumbnail'    => 'https://github.com/rern/_assets/raw/master/RuneUI_GPIO/GPIOs/4.jpg',
	'sourcecode'   => 'https://github.com/rern/RuneUI_GPIO',
	'installurl'   => 'https://github.com/rern/RuneUI_GPIO/raw/master/install.sh',
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
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/transmission/thumbtran.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/transmission',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/transmission/install.sh',
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
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/samba/thumbsamb.png',
	'buttonlabel'  => 'Upgrade',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/samba',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/samba/installurl',
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
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/backup-restore',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/backup-restore/install.sh',
),
array(
	'alias'        => 'expa',
	'title'        => 'Expand Partition',
	'maintainer'   => 'r e r n',
	'description'  => 'Expand default 2GB partition to full capacity of SD card. (0.3+0.4b)',
	'buttonlabel'  => 'Expand',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/expand_partition',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/expand_partition/expand.sh',
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
	'description'  => 'Fix package download errors caused by unreachable servers. (0.3+0.4b)',
	'buttonlabel'  => 'Rank',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/rankmirrors',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/rankmirrors/rankmirrors.sh',
),
array(
	'alias'        => 'radi',
	'title'        => 'Webradio Import',
	'maintainer'   => 'r e r n',
	'description'  => 'Webradio files import. Adding files to <code>/mnt/MPD/Webradio/</code> alone will not work.'
			.'<br>Add files at anytime then start this addon to refresh Webradio list'
			.'<br><white>Any import before 20170922 should run this addon again to improve sorting.</white>',
	'buttonlabel'  => 'Import',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/webradio',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/webradio/webradiodb.sh',
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
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/webradio',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/webradio/install.sh',
),
// array end ----------------------------------------------------------------------------------------------------
);
