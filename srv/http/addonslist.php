<p>
Addons always gets the latest list from source.<br>
<br>
<span>20170901 - Initial release <a id="detail">Detail ▼</a></span><br>
	<ul id="message" style="display: none;">
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
</p>
<br>

<?php
/* 
### each package requires:

	# scripts naming: ( must be, except <...> )
		- '<install>.sh'         name for install
		- 'uninstall_<alias>.sh  name for uninstall ('<alias>' must be unique)
		- 'update.sh'            name for update
	# <install>.sh option(arguments):
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
		- update.sh must be in the same directory as <install>.sh
		- major changes use update.sh to uninstall then reinstall
		- non-install package:
		    omit to hide uninstall button
		    run once package  - any numbers in <install>.sh will disable install button after run
		    

### each package syntax: 
	- '* ...' - optional
	- 'value' - parsed for html, use html escape characters
    
$package = array(
	'title'       => 'title',
	'* version'   => 'version',
	'* thumbnail' => 'https://url/to/image/w100px',
	'alias'       => 'alias (must be unique)',
	'description' => 'description',
	'maintainer'  => 'maintainer',
	'sourcecode'  => 'https://url/to/sourcecode',
	'installurl'  => 'https://url/for/wget/install.sh',
	'* option'    => '!confirm;'
	                .'?yes/no;'
	                .'#password;'
	                ."input line 1\n"
	                    ."input line 2"
);
addonblock($package);
*/

$package = array(
	'title'       => 'Addons main',
	'version'     => '20170901',
	'thumbnail'   => 'https://github.com/rern/_assets/raw/master/RuneAudio_Addons/addonsthumb.png',
	'alias'       => 'addo',
	'description' => 'This Addons main page.',
	'maintainer'  => 'r e r n',
	'sourcecode'  => 'https://github.com/rern/RuneAudio_Addons',
	'installurl'  => 'https://github.com/rern/RuneAudio_Addons/raw/master/install.sh',
);
addonblock($package);
/*$package = array(
	'title'       => 'Aria2',
	'version'     => '20170901',
	'alias'       => 'aria',
	'description' => 'Download utility that supports HTTP(S), FTP, BitTorrent, and Metalink.',
	'maintainer'  => 'r e r n',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/aria2',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/aria2/install.sh',
	'option'      => '?Start &quot;Aria2&quot; on system startup;'
			.'!Package &quot;glibc&quot; slow download may take some times.'
);
addonblock($package);*/
/*$package = array(
	'title'       => 'Backup-Restore Update',
	'version'     => '20170901',
	'alias'       => 'back',
	'description' => 'Enable backup-restore settings and databases.',
	'maintainer'  => 'r e r n',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/backup-restore',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/backup-restore/install.sh',
);
addonblock($package);*/
$package = array(
	'title'       => 'Expand Partition',
	'alias'       => 'expa',
	'description' => 'Expand default 2GB partition to full capacity of SD card.',
	'maintainer'  => 'r e r n',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/expand_partition',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/expand_partition/expand.sh',
);
addonblock($package);
$package = array(
	'title'       => 'Fonts - Extended characters',
	'version'     => '20170901',
	'alias'       => 'font',
	'description' => 'Font files replacement for Extended Latin-based, Cyrillic-based, Greek and IPA phonetics.',
	'maintainer'  => 'r e r n',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/font_extended',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/font_extended/install.sh',
);
addonblock($package);
$package = array(
	'title'       => 'Login Logo for SSH Terminal',
	'version'     => '20170901',
	'thumbnail'   => 'https://github.com/rern/RuneAudio/raw/master/motd/thumbmotd.png',
	'alias'       => 'motd',
	'description' => 'Message of the day - RuneAudio Logo and dimmed command prompt.',
	'maintainer'  => 'r e r n',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/motd',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/motd/install.sh',
);
addonblock($package);
$package = array(
	'title'       => 'Rank Mirror Packages Servers',
	'alias'       => 'rank',
	'description' => 'Fix packages download errors caused by unreachable servers.',
	'maintainer'  => 'r e r n',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/rankmirrors',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/rankmirrors/rankmirrors.sh',
	'option'      => '?Update package database after ranking?'
);
addonblock($package);
$package = array(
	'title'       => 'RuneUI Enhancements',
	'version'     => '20170901',
	'thumbnail'   => 'https://github.com/rern/_assets/raw/master/RuneUI_enhancement/thumbenha.gif',
	'alias'       => 'enha',
	'description' => 'More minimalism and more fluid layout.',
	'maintainer'  => 'r e r n',
	'sourcecode'  => 'https://github.com/rern/RuneUI_enhancement',
	'installurl'  => 'https://github.com/rern/RuneUI_enhancement/raw/master/install.sh',
	'option'      => "Set zoom level for display directly connect to RPi.\n"
				."\n"
				."Local browser screen size:\n"
				."0.7 : width less than 800px\n"
				."1.2 : HD - 1280px\n"
				."1.5 : Full HD - 1920px"
);
addonblock($package);
/*$package = array(
	'title'       => 'RuneUI GPIO',
	'version'     => '20170901',
	'thumbnail'   => 'https://github.com/rern/_assets/raw/master/RuneUI_GPIO/GPIOs/4.jpg',
	'alias'       => 'gpio',
	'description' => 'GPIO connected relay module control.',
	'maintainer'  => 'r e r n',
	'sourcecode'  => 'https://github.com/rern/RuneUI_GPIO',
	'installurl'  => 'https://github.com/rern/RuneUI_GPIO/raw/master/install.sh',
	'option'      => "!Get DAC configuration ready:\n"
				."\n"
				."For external power DAC > power on\n"
				."Menu > MPD > setup and verify DAC works properly before continue.\n"
				."\n"
				."Continue install?"
);
addonblock($package);*/
$package = array(
	'title'       => 'RuneUI Password',
	'version'     => '20170901',
	'alias'       => 'pass',
	'description' => 'RuneUI access restriction.',
	'maintainer'  => 'r e r n',
	'sourcecode'  => 'https://github.com/rern/RuneUI_password',
	'installurl'  => 'https://github.com/rern/RuneUI_password/raw/master/install.sh',
);
addonblock($package);
/*$package = array(
	'title'       => 'Samba Upgrade',
	'version'     => '20170901',
	'alias'       => 'samb',
	'description' => 'Faster and more customized shares.',
	'maintainer'  => 'r e r n',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/samba',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/samba/installurl',
	'option'      => 'Password for user &quot;root&quot; (Cancel for no password)'
);
addonblock($package);*/
/*$package = array(
	'title'       => 'Transmission',
	'version'     => '20170901',
	'alias'       => 'tran',
	'description' => 'Fast, easy, and free BitTorrent client.',
	'maintainer'  => 'r e r n',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/transmission',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/transmission/install.sh',
	'option'      => '#Password for user &quot;root&quot; (Cancel for no password);'
			.'?Install WebUI alternative (Transmission Web Control);'
			.'?Start &quot;Transmission&quot; on system startup'
);
addonblock($package);*/
$package = array(
	'title'       => 'Webradio Import',
	'alias'       => 'webr',
	'description' => 'Webradio files import.',
	'maintainer'  => 'r e r n',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/webradio',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/webradio/webradiodb.sh',
	'option'      => "!Get webradio files copied to /mnt/MPD/Webradio.\n"
				."\n"
				."Continue import?"
);
addonblock($package);
?>
