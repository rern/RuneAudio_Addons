<?php
/* 
--------------------------------------------------------------------------------------------------------------
each addon requires:
--------------------------------------------------------------------------------------------------------------
1. bash script files:
	- install script   - <any_name>.sh
	- uninstall script - /usr/local/bin/uninstall_<unique_alias>.sh (no need for non-install addons)
	- (update)         - none
		- use 'uninstall > install' to update by default
		- different 'version' in this file and install file will show update button
		- 'exit 1' for 'already installed' check to stop reinstall from running
2. in this file:
	- array(...),
--------------------------------------------------------------------------------------------------------------		
array(
	'* version'     => 'version',
	'title'         => 'title',
	'maintainer'    => 'maintainer',
	'description'   => 'description',
	'* thumbnail'   => 'https://url/to/image/w100px',
	'* buttonlabel' => 'install button label',
	'sourcecode'    => 'https://url/to/sourcecode',
	'installurl'    => 'https://url/for/wget/install.sh',
	'alias'         => 'alias (must be unique)',
	'* option'      => '!confirm;'
	                  .'?yes/no;'
	                  .'#password;'
	                  ."input line 1\n"
	                      ."input line 2"
),
--------------------------------------------------------------------------------------------------------------
note: '* ...' = optional

version:
	for buttons enable/disable
	- specified both in 'array(...)' in this file and 'install script'
	- version from 'install script' stored in database then disable/enable buttons
	- database vs 'array(...)' difference will show update button
	- non-install addons:
		(none) + (none)          - install button always enable, no uninstall button
		(none) + 'install scipt' - install button disable after run (run once)
description:
	- html allowed
option:
	for user input
	- each input will be appended as <install>.sh arguments
	- ';' = delimiter each input
	- message (js alert/confirm/prompt):
		- starts with '!'      = 'js confirm' continue => ok = continue, cancel = exit install
		- starts with '?'      = 'js confirm' yes/no   => ok = 1,        cancel = 0
		- starts with '#'      = 'js prompt'  password => ok = password, blank-ok/cancel = 0
		- starts with '(none)' = 'js prompt'  input    => ok = input,    blank-ok/cancel = 0
		- message will be parsed for html value, use entity code for:
			 &quot;  = "
			 &#039;  = '
			 &amp;   = &
			 &lt;    = <
			 &gt;    = >
		- multiple lines:
			 "...\n" = \n escaped n    - new line (must be inside double quotes)
			."...\n" = .  starting dot - concatenate between lines

*/

$addons = array(
// array start ----------------------------------------------------------------------------------------------------
array(
	'version'      => $addonsversion,
	'title'        => 'Addons Menu',
	'maintainer'   => 'r e r n',
	'description'  => 'This Addons main page.<br>'
			.'<span>Addons Menu installed before 20170906 needs uninstall then reinstall via SSH terminal.</span>',
	'thumbnail'    => 'https://github.com/rern/_assets/raw/master/RuneAudio_Addons/addonsthumb.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio_Addons',
	'installurl'   => 'https://github.com/rern/RuneAudio_Addons/raw/master/install.sh',
	'alias'        => 'addo',
),
/*array(
	'version'      => '20170901',
	'title'        => 'Aria2',
	'maintainer'   => 'r e r n',
	'description'  => 'Download utility that supports HTTP(S), FTP, BitTorrent, and Metalink.',
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/aria2/thumbaria.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/aria2',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/aria2/install.sh',
	'alias'        => 'aria',
	'option'       => '?Start &quot;Aria2&quot; on system startup;'
			.'!Package &quot;glibc&quot; slow download may take some times.'
),*/
array(
	'version'      => '20170901',
	'title'        => 'Backup-Restore Update',
	'maintainer'   => 'r e r n',
	'description'  => 'Enable backup-restore settings and databases.',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/backup-restore',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/backup-restore/install.sh',
	'alias'        => 'back',
),
array(
	'title'        => 'Expand Partition',
	'maintainer'   => 'r e r n',
	'description'  => 'Expand default 2GB partition to full capacity of SD card.',
	'buttonlabel'  => 'Expand',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/expand_partition',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/expand_partition/expand.sh',
	'alias'        => 'expa',
),
array(
	'version'      => '20170901',
	'title'        => 'Fonts - Extended characters',
	'maintainer'   => 'r e r n',
	'description'  => 'Font files replacement for Extended Latin-based, Cyrillic-based, Greek and IPA phonetics.',
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/font_extended/thumbfont.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/font_extended',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/font_extended/install.sh',
	'alias'        => 'font',
),
array(
	'version'      => '20170901',
	'title'        => 'Login Logo for SSH Terminal',
	'maintainer'   => 'r e r n',
	'description'  => 'Message of the day - RuneAudio Logo and dimmed command prompt.',
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/motd/thumbmotd.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/motd',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/motd/install.sh',
	'alias'        => 'motd',
),
array(
	'title'        => 'Rank Mirror Package Servers',
	'maintainer'   => 'r e r n',
	'description'  => 'Fix package download errors caused by unreachable servers.',
	'buttonlabel'  => 'Rank',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/rankmirrors',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/rankmirrors/rankmirrors.sh',
	'alias'        => 'rank',
	'option'       => '?Update package database after ranking?'
),
array(
	'version'      => '20170901',
	'title'        => 'RuneUI Enhancements',
	'maintainer'   => 'r e r n',
	'description'  => 'More minimalism and more fluid layout.',
	'thumbnail'    => 'https://github.com/rern/_assets/raw/master/RuneUI_enhancement/thumbenha.gif',
	'sourcecode'   => 'https://github.com/rern/RuneUI_enhancement',
	'installurl'   => 'https://github.com/rern/RuneUI_enhancement/raw/master/install.sh',
	'alias'        => 'enha',
	'option'      => "Set zoom level for display directly connect to RPi.\n"
				."\n"
				."Local browser screen size:\n"
				."0.7 : width less than 800px\n"
				."1.2 : HD - 1280px\n"
				."1.5 : Full HD - 1920px"
),
/*array(
	'version'      => '20170901',
	'title'        => 'RuneUI GPIO',
	'maintainer'   => 'r e r n',
	'description'  => 'GPIO connected relay module control.',
	'thumbnail'    => 'https://github.com/rern/_assets/raw/master/RuneUI_GPIO/GPIOs/4.jpg',
	'sourcecode'   => 'https://github.com/rern/RuneUI_GPIO',
	'installurl'   => 'https://github.com/rern/RuneUI_GPIO/raw/master/install.sh',
	'alias'        => 'gpio',
	'option'      => "!Get DAC configuration ready:\n"
				."\n"
				."For external power DAC > power on\n"
				."Menu > MPD > setup and verify DAC works properly before continue.\n"
				."\n"
				."Continue install?"
),*/
array(
	'version'      => '20170901',
	'title'        => 'RuneUI Password',
	'maintainer'   => 'r e r n',
	'description'  => 'RuneUI access restriction.',
	'thumbnail'    => 'https://github.com/rern/_assets/raw/master/RuneUI_password/thumbpass.gif',
	'sourcecode'   => 'https://github.com/rern/RuneUI_password',
	'installurl'   => 'https://github.com/rern/RuneUI_password/raw/master/install.sh',
	'alias'        => 'pass',
),
/*array(
	'title'        => 'Samba Upgrade',
	'maintainer'   => 'r e r n',
	'description'  => 'Faster and more customized shares.',
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/samba/thumbsamb.png',
	'buttonlabel'  => 'Upgrade',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/samba',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/samba/installurl',
	'alias'        => 'samb',
	'option'       => '#Password for user &quot;root&quot; (Cancel for no password):'
),*/
/*array(
	'version'      => '20170901',
	'title'        => 'Transmission',
	'maintainer'   => 'r e r n',
	'description'  => 'Fast, easy, and free BitTorrent client.',
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/transmission/thumbtran.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/transmission',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/transmission/install.sh',
	'alias'        => 'tran',
	'option'       => '#Password for user &quot;root&quot; (Cancel for no password):;'
			.'?Install WebUI alternative (Transmission Web Control);'
			.'?Start &quot;Transmission&quot; on system startup'
),*/
array(
	'title'        => 'Webradio Import',
	'maintainer'   => 'r e r n',
	'description'  => 'Webradio files import.',
	'buttonlabel'  => 'Import',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/webradio',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/webradio/webradiodb.sh',
	'alias'        => 'webr',
	'option'      => "!Get webradio files copied to:\n"
				."/mnt/MPD/Webradio\n"
				."\n"
				."Continue import?"
),
// array end ----------------------------------------------------------------------------------------------------
);
