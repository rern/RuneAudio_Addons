<p>
This Addons always gets the latest list from source.<br>
<br>
<span>20170905 <a id="detail">Detail ▼</a></span><br>
<div  id="message" style="display: none;">
<ul>
	<li>add Backup-Restore update</li>
	<li>populate previous installed addons to redis database</li>
	<li>custom label for install button for non-install addons</li>
</ul>
<span>20170901 - Initial release</span><br>
<ul>
<!--	<li>Aria2</li>-->
	<li>Backup-Restore Update</li>
	<li>Expand Partition</li>
	<li>Fonts - Extended characters</li>
	<li>RuneAudio Logo for SSH Terminal</li>
	<li>Rank Mirror Package Servers</li>
	<li>RuneUI Enhancements</li>
<!--	<li>RuneUI GPIO</li>-->
	<li>RuneUI Password</li>
<!--	<li>Samba</li>-->
<!--	<li>Transmission</li>-->
	<li>Webradio Import</li>
</ul>
</div>
</p>
<br>

<?php
/* 
--------------------------------------------------------------------------------------------------------------

### each package requires:

	# bash script files:
		- install script
		- uninstall script (no need for non-install addons)
		- update script
			- only when available
			- must be in the same directory as install script
			- use for uninstall then reinstall on major changes
	# script files naming: ( must be, except <...> without '<>' )
		- '<install>.sh'         name for install
		- 'uninstall_<alias>.sh  name for uninstall ('<alias>' must be unique)
		- 'update.sh'            name for update (
	# <install>.sh options(arguments):
		- each input will be appended as <install>.sh arguments
		- ';' = delimiter each input
		- message starts with '!'      = 'js confirm' continue => ok = continue, cancel = exit install
		- message starts with '?'      = 'js confirm' yes/no   => ok = 1,        cancel = 0
		- message starts with '#'      = 'js prompt'  password => ok = password, blank-ok/cancel = 0
		- message starts with '(none)' = 'js prompt'  input    => ok = input,    blank-ok/cancel = 0
			multiple lines message:
				 "...\n" = \n escaped n    - new line (must be inside double quotes)
				."...\n" = .  starting dot - concatenate between lines
	# version:
		- specified both in <install>.sh and $package = array(...)
		- version from <install>.sh stored in database then disable/enable install/uninstall buttons
		- database vs $package = array(...) difference will show update button
		- non-install addons:
		    omit to hide uninstall button
		    run once addons  - any numbers, in <install>.sh only, will disable install button after run
		    
	# # a '$package = array()' in this file:
		- '* ...' - optional
		- 'value' - parsed for html, use html escape characters
		
--------------------------------------------------------------------------------------------------------------
*/
/*
--------------------------------------------------------------------------------------------------------------

$package = array(
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
);
addonblock($package);

--------------------------------------------------------------------------------------------------------------
*/

$package = array(
	'version'      => '20170905',
	'title'        => 'Addons main',
	'maintainer'   => 'r e r n',
	'description'  => 'This Addons main page.',
	'thumbnail'    => 'https://github.com/rern/_assets/raw/master/RuneAudio_Addons/addonsthumb.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio_Addons',
	'installurl'   => 'https://github.com/rern/RuneAudio_Addons/raw/master/install.sh',
	'alias'        => 'addo',
);
addonblock($package);
/*$package = array(
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
);
addonblock($package);*/
$package = array(
	'version'      => '20170901',
	'title'        => 'Backup-Restore Update',
	'maintainer'   => 'r e r n',
	'description'  => 'Enable backup-restore settings and databases.',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/backup-restore',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/backup-restore/install.sh',
	'alias'        => 'back',
);
addonblock($package);
$package = array(
	'title'        => 'Expand Partition',
	'maintainer'   => 'r e r n',
	'description'  => 'Expand default 2GB partition to full capacity of SD card.',
	'buttonlabel'  => 'Expand',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/expand_partition',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/expand_partition/expand.sh',
	'alias'        => 'expa',
);
addonblock($package);
$package = array(
	'version'      => '20170901',
	'title'        => 'Fonts - Extended characters',
	'maintainer'   => 'r e r n',
	'description'  => 'Font files replacement for Extended Latin-based, Cyrillic-based, Greek and IPA phonetics.',
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/font_extended/thumbfont.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/font_extended',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/font_extended/install.sh',
	'alias'        => 'font',
);
addonblock($package);
$package = array(
	'version'      => '20170901',
	'title'        => 'Login Logo for SSH Terminal',
	'maintainer'   => 'r e r n',
	'description'  => 'Message of the day - RuneAudio Logo and dimmed command prompt.',
	'thumbnail'    => 'https://github.com/rern/RuneAudio/raw/master/motd/thumbmotd.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/motd',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/motd/install.sh',
	'alias'        => 'motd',
);
addonblock($package);
$package = array(
	'title'        => 'Rank Mirror Packages Servers',
	'maintainer'   => 'r e r n',
	'description'  => 'Fix packages download errors caused by unreachable servers.',
	'buttonlabel'  => 'Rank',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/rankmirrors',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/rankmirrors/rankmirrors.sh',
	'alias'        => 'rank',
	'option'       => '?Update package database after ranking?'
);
addonblock($package);
$package = array(
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
);
addonblock($package);
/*$package = array(
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
);
addonblock($package);*/
$package = array(
	'version'      => '20170901',
	'title'        => 'RuneUI Password',
	'maintainer'   => 'r e r n',
	'description'  => 'RuneUI access restriction.',
	'thumbnail'    => 'https://github.com/rern/_assets/raw/master/RuneUI_password/thumbpass.gif',
	'sourcecode'   => 'https://github.com/rern/RuneUI_password',
	'installurl'   => 'https://github.com/rern/RuneUI_password/raw/master/install.sh',
	'alias'        => 'pass',
);
addonblock($package);
/*$package = array(
	'title'        => 'Samba Upgrade',
	'maintainer'   => 'r e r n',
	'description'  => 'Faster and more customized shares.',
	'buttonlabel'  => 'Upgrade',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/samba',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/samba/installurl',
	'alias'        => 'samb',
	'option'       => '#Password for user &quot;root&quot; (Cancel for no password):'
);
addonblock($package);*/
/*$package = array(
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
);
addonblock($package);*/
$package = array(
	'title'        => 'Webradio Import',
	'maintainer'   => 'r e r n',
	'description'  => 'Webradio files import.',
	'buttonlabel'  => 'Import',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/webradio',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/webradio/webradiodb.sh',
	'alias'        => 'webr',
	'option'      => "!Get webradio files copied to /mnt/MPD/Webradio.\n"
				."\n"
				."Continue import?"
);
addonblock($package);
?>
