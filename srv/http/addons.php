<?php
require_once('addonshead.php');

echo '<h1>ADDONS</h1><a id="close" href="/"><i class="fa fa-times fa-2x"></i></a>';

$redis = new Redis(); 
$redis->pconnect('127.0.0.1');
$version = $redis->hGetAll('addons');

function addonblock($pkg) {
	global $version;
	$alias = $pkg['alias'];
	if ($version[$alias]) {
		$check = '<i class="fa fa-check blue"></i> ';
		if (!isset($pkg['version']) || $pkg['version'] == $version[$alias]) {
			$btnin = '<a class="btn btn-default disabled"><i class="fa fa-check"></i> Install</a>';
		} else {
			$btnin = '<a installurl="'.str_replace('install.sh', 'update.sh', $pkg['installurl']).'" class="btn btn-primary"><i class="fa fa-refresh"></i> Update</a>';
		}
		$btnun = '<a id="un'.$alias.'" class="btn btn-default"><i class="fa fa-close"></i> Uninstall</a>';
	} else {
		if (isset($pkg['option'])) {
			$option = 'option="'.$pkg['option'].'"';
		} else {
			$option = '';
		}
		$check = '';
		$btnin = '<a id="in'.$alias.'" installurl="'.$pkg['installurl'].'" '.$option.' class="btn btn-default"><i class="fa fa-check"></i> Install</a>';
		$btnun = '<a class="btn btn-default disabled"><i class="fa fa-close"></i> Uninstall</a>';
	}
	echo '
		<div class="boxed-group">
		<legend>'.$check.$pkg['title'].'</legend>
		<form class="form-horizontal">
			<p>'.$pkg['description'].' ( <a href="'.$pkg['sourcecode'].'">More detail</a> )</p>'
			.$btnin;
	if (isset($pkg['version']))
		echo ' &nbsp; '.$btnun;
	echo
		'</form>
		</div>';
}
/* 
### each package requires:
	# scripts naming:
		- 'install.sh'           name for install
		- 'uninstall_<alias>.sh  name for uninstall
		- 'update.sh'            name for update
		- non-install package can be any names
	# install.sh options:
		- ';' delimiter
		- start with '!'      = alert / ok to continue
		- start with '?'      = confirm / ok = 1, cancel = 0
		- start with '(none)' = prompt / ok = user input, blank-ok/cancel = 0
		- "\n" escape new line inside double quotes for options messages
	# version:
		- specified both in install.sh and $package = array(...)
		- installed vs $package = array(...) difference will show update button
		- update.sh must be in the same directory as install.sh
		- major changes use update.sh to uninstall then reinstall
		- non-install package:
		    omit to hide uninstall button
		    run once - specified in install.sh to disable install button after run

### each package syntax:
$package = array(
	'title'       => 'title',
	'version'     => 'n',
	'alias'       => 'alias',
	'description' => 'description.',
	'sourcecode'  => 'https://url/to/sourcecode',
	'installurl'  => 'https://url/for/wget/install.sh',
	'option'      => 'input text; ?yesno text; !wait text',
);
addonblock($package);
*/
$package = array(
	'title'       => 'Addons main',
	'version'     => '20170901',
	'alias'       => 'main',
	'description' => 'This Addons main page.',
	'sourcecode'  => 'https://github.com/rern/RuneAudio_Addons',
	'installurl'  => 'https://github.com/rern/RuneAudio_Addons/raw/master/install.sh',
);
addonblock($package);
$package = array(
	'title'       => 'Aria2',
	'version'     => '20170901',
	'alias'       => 'aria',
	'description' => 'Download utility that supports HTTP(S), FTP, BitTorrent, and Metalink.',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/aria2',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/aria2/install.sh',
);
addonblock($package);
$package = array(
	'title'       => 'Backup-Restore Update',
	'version'     => '20170901',
	'alias'       => 'back',
	'description' => 'Enable backup-restore settings and databases.',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/backup-restore',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/backup-restore/install.sh',
);
addonblock($package);
$package = array(
	'title'       => 'Expand Partition',
	'alias'       => 'expa',
	'description' => 'Expand default 2GB partition to full capacity of SD card.',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/expand_partition',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/expand_partition/expand.sh',
);
addonblock($package);
$package = array(
	'title'       => 'Fonts - Extended characters',
	'version'     => '20170901',
	'alias'       => 'font',
	'description' => 'Font files replacement for Extended Latin-based, Cyrillic-based, Greek and IPA phonetics.',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/font_extended',
	'installurl'  => 'https://github.com/rern/RuneAudio/tree/master/font_extended/install.sh',
);
addonblock($package);
$package = array(
	'title'       => 'motd - RuneAudio Logo for SSH Terminal',
	'version'     => '20170901',
	'alias'       => 'motd',
	'description' => 'Message of the day - RuneAudio Logo and dimmed command prompt.',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/motd',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/motd/install.sh',
);
addonblock($package);
$package = array(
	'title'       => 'Rank Mirror Packages Servers',
	'alias'       => 'rank',
	'description' => 'Fix packages download errors caused by unreachable servers.',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/rankmirrors',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/rankmirrors/rankmirrors.sh',
);
addonblock($package);
$package = array(
	'title'       => 'RuneUI Enhancements',
	'version'     => '20170901',
	'alias'       => 'enha',
	'description' => 'More minimalism and more fluid layout.',
	'sourcecode'  => 'https://github.com/rern/RuneUI_enhancement',
	'installurl'  => 'https://github.com/rern/RuneUI_enhancement/raw/master/install.sh',
	'option'      => "Set zoom level to display directly connect to RPi."
						."\n"
						."\nLocal browser screen size:"
						."\n0.7 : width less than 800px"
						."\n1.2 : HD - 1280px"
						."\n1.5 : Full HD - 1920px",
);
addonblock($package);
$package = array(
	'title'       => 'RuneUI GPIO',
	'version'     => '20170901',
	'alias'       => 'gpio',
	'description' => 'GPIO connected relay module control.',
	'sourcecode'  => 'https://github.com/rern/RuneUI_GPIO',
	'installurl'  => 'https://github.com/rern/RuneUI_GPIO/raw/master/install.sh',
	'option'      => "?DAC configuration from previous install found."
						."\nOverwrite?"
						.";!Get DAC configuration ready"
						."\nFor external power DAC > power on"
						."\n"
						."\nMenu > MPD > setup and verify DAC works properly before continue.",
);
addonblock($package);
$package = array(
	'title'       => 'RuneUI Password',
	'version'     => '20170901',
	'alias'       => 'pass',
	'description' => 'RuneUI access restriction.',
	'sourcecode'  => 'https://github.com/rern/RuneUI_password',
	'installurl'  => 'https://github.com/rern/RuneUI_password/raw/master/install.sh',
);
addonblock($package);
$package = array(
	'title'       => 'Samba Upgrade',
	'version'     => '20170901',
	'alias'       => 'samb',
	'description' => 'Faster and more customized shares.',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/samba',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/samba/installurl',
	'option'      => 'Password for user root (Cancel for no password)',
);
addonblock($package);
$package = array(
	'title'       => 'Transmission',
	'version'     => '20170901',
	'alias'       => 'tran',
	'description' => 'Fast, easy, and free BitTorrent client.',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/transmission',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/transmission/install.sh',
	'option'      => 'Password for Web Interface (Cancel for no password)'
						.'; ?Install WebUI alternative (Transmission Web Control)'
						.'; ?Start Transmission on system startup',
);
addonblock($package);
$package = array(
	'title'       => 'Webradio Import',
	'alias'       => 'webr',
	'description' => 'Webradio files import.',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/webradio',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/webradio/webradiodb.sh',
	'option'      => '!Get webradio files in /mnt/MPD/Webradio ready.',
);
addonblock($package);
?>
</div>

<script>
var btn = document.getElementsByClassName('btn');
for (var i = 0; i < btn.length; i++) {
	btn[i].onclick = function(e) {
		var opt = '';
		if (this.getAttribute('option')) {
			var options = this.getAttribute('option').replace(/; /g, ';').split(';');
			if (options.length > 0) {
				opt = '&opt=';
				for (var j = 0; j < options.length; j++) {
					var oj = options[j];
					if (oj[0] == '!') {
						alert(oj.slice(1));
					} else if (oj[0] == '?') {
						var yesno = confirm(oj.slice(1));
						yesno = yesno ? 1 : 0;
						opt += yesno +' ';
					} else {
						var input = prompt(oj);
						input = input ? input : 0;
						opt += input +' ';
					}
				}
			}
		}
		if (this.id[0] == 'i') {
			window.location.href = 'addonbash.php?cmd='+ encodeURIComponent(
				'wget -qN '+ this.getAttribute('installurl')
				+'; chmod 755 install.sh; /usr/bin/sudo ./install.sh '+ opt
			);
		} else if (this.id[0] == 'u') {
			window.location.href = 'addonbash.php?cmd='+ encodeURIComponent(
				'/usr/bin/sudo /usr/local/bin/uninstall_'+ this.id.slice(2) +'.sh'
			);
		} else {
			window.location.href = 'addonbash.php?cmd='+ encodeURIComponent(
				'wget -qN '+ this.getAttribute('installurl') 
				+'; chmod 755 update.sh; /usr/bin/sudo ./update.sh');
		}
	}
}
</script>

</body>
</html>
