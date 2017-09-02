<?php
require_once('addonshead.php');

echo '<div class="container">'
	.'<h1>ADDONS</h1><a id="close" href="/"><i class="fa fa-times fa-2x"></i></a>';

$redis = new Redis(); 
$redis->pconnect('127.0.0.1');
$version = $redis->hGetAll('addons');

function addonblock($pkg) {
	global $version;
	$alias = $pkg['alias'];
	$installurl = $pkg['installurl'];
	$filename = end(explode('/', $installurl));
	if ($version[$alias]) {
		$check = '<i class="fa fa-check blue"></i> ';
		if (!isset($pkg['version']) || $pkg['version'] == $version[$alias]) {
			$btnin = '<a class="btn btn-default disabled"><i class="fa fa-check"></i> Install</a>';
		} else {
			$command = 'wget -qN '.str_replace($filename, 'update.sh', $installurl).'; chmod 755 update.sh; /usr/bin/sudo ./update.sh';
			$btnin = '<a installurl="'.$command.'" class="btn btn-primary"><i class="fa fa-refresh"></i> Update</a>';
		}
		$command = '/usr/bin/sudo /usr/local/bin/uninstall_'.$alias.'.sh';
		$btnun = '<a installurl="'.$command.'" class="btn btn-default"><i class="fa fa-close"></i> Uninstall</a>';
	} else {
		if (isset($pkg['option'])) {
			$option = 'option="'.$pkg['option'].'"';
		} else {
			$option = '';
		}
		$check = '';
		$command = 'wget -qN '.$installurl.'; chmod 755 '.$filename.'; /usr/bin/sudo ./'.$filename;
		$btnin = '<a installurl="'.$command.'" '.$option.' class="btn btn-default"><i class="fa fa-check"></i> Install</a>';
		$btnun = '<a class="btn btn-default disabled"><i class="fa fa-close"></i> Uninstall</a>';
	}
	echo '
		<div class="boxed-group">
		<legend>'.$check.$pkg['title'].'<p class="">by <span> '.$pkg['maintainer'].'</span></p></legend>
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
	# scripts naming: ( must be, except <...> )
		- '<install>.sh'         name for install
		- 'uninstall_<alias>.sh  name for uninstall
		- 'update.sh'            name for update
	# <install>.sh option(arguments):
		- each input will be appended as <install>.sh arguments
		- ';' = delimiter each input
		- message starts with '!'      = 'js alert'   wait     => ok = continue
		- message starts with '?'      = 'js confirm' yes/no   => ok = 1, cancel = 0
		- message starts with '#'      = 'js prompt'  password => ok = password, blank-ok/cancel = 0
		- message starts with '(none)' = 'js prompt'  input    => ok = input, blank-ok/cancel = 0
		  ('\n' = escaped new line inside double quoted message)
	# version:
		- specified both in <install>.sh and $package = array(...)
		- installed version stored in database
		- $package = array(...) vs database difference will show update button
		- update.sh must be in the same directory as <install>.sh
		- major changes use update.sh to uninstall then reinstall
		- non-install package:
		    omit to hide uninstall button
		    run once - specified any numbers in <install>.sh to disable install button after run

### each package syntax:
$package = array(
	'title'       => 'title',
	'version'     => 'n',
	'alias'       => 'alias',
	'description' => 'description.',
	'maintainer'  => 'maintainer',
	'sourcecode'  => 'https://url/to/sourcecode',
	'installurl'  => 'https://url/for/wget/install.sh',
	'option'      => 'input text; ?yesno text; !wait text',
);
addonblock($package);
*/
$package = array(
	'title'       => 'Addons main',
	'version'     => '20170902',
	'alias'       => 'main',
	'description' => 'This Addons main page.',
	'maintainer'  => 'r e r n',
	'sourcecode'  => 'https://github.com/rern/RuneAudio_Addons',
	'installurl'  => 'https://github.com/rern/RuneAudio_Addons/raw/master/install.sh',
);
addonblock($package);
$package = array(
	'title'       => 'Aria2',
	'version'     => '20170901',
	'alias'       => 'aria',
	'description' => 'Download utility that supports HTTP(S), FTP, BitTorrent, and Metalink.',
	'maintainer'  => 'r e r n',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/aria2',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/aria2/install.sh',
	'option'      => '?Start Aria2 on system startup',
);
addonblock($package);
$package = array(
	'title'       => 'Backup-Restore Update',
	'version'     => '20170901',
	'alias'       => 'back',
	'description' => 'Enable backup-restore settings and databases.',
	'maintainer'  => 'r e r n',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/backup-restore',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/backup-restore/install.sh',
);
addonblock($package);
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
	'installurl'  => 'https://github.com/rern/RuneAudio/tree/master/font_extended/install.sh',
);
addonblock($package);
$package = array(
	'title'       => 'Login Logo for SSH Terminal',
	'version'     => '20170901',
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
);
addonblock($package);
$package = array(
	'title'       => 'RuneUI Enhancements',
	'version'     => '20170901',
	'alias'       => 'enha',
	'description' => 'More minimalism and more fluid layout.',
	'maintainer'  => 'r e r n',
	'sourcecode'  => 'https://github.com/rern/RuneUI_enhancement',
	'installurl'  => 'https://github.com/rern/RuneUI_enhancement/raw/master/install.sh',
	'option'      => "Set zoom level for display directly connect to RPi."
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
	'maintainer'  => 'r e r n',
	'sourcecode'  => 'https://github.com/rern/RuneUI_GPIO',
	'installurl'  => 'https://github.com/rern/RuneUI_GPIO/raw/master/install.sh',
	'option'      => "!Get DAC configuration ready"
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
	'option'      => 'Password for user root (Cancel for no password)',
);
addonblock($package);*/
$package = array(
	'title'       => 'Transmission',
	'version'     => '20170901',
	'alias'       => 'tran',
	'description' => 'Fast, easy, and free BitTorrent client.',
	'maintainer'  => 'r e r n',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/transmission',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/transmission/install.sh',
	'option'      => '#Password for Web Interface (Cancel for no password)'
			.'; ?Install WebUI alternative (Transmission Web Control)'
			.'; ?Start Transmission on system startup',
);
addonblock($package);
$package = array(
	'title'       => 'Webradio Import',
	'alias'       => 'webr',
	'description' => 'Webradio files import.',
	'maintainer'  => 'r e r n',
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
	btn[i].onclick = function() {
		// user confirmation
		var installurl = this.getAttribute('installurl');
		switch(installurl.split('/').pop().substr(0,2)) {
			case 'un': var type = 'Uninstall "'; break;
			case 'up': var type = 'Update "'; break;
			default  : var type = 'Install "';
		}
		var title = this
					.parentElement
					.previousElementSibling
					.innerHTML
						.replace(/<i.*i>/, '')
						.replace(/<p.*p>/, '');
		
		if (!confirm(type + title +'"?')) return;
		
		document.getElementById('loader').style.display = 'block';
		// split each option per user prompt
		var opt = ' ';
		if (this.getAttribute('option')) {
			var option = this.getAttribute('option').replace(/; /g, ';').split(';');
			if (option.length > 0) {
				for (var j = 0; j < option.length; j++) {
					var oj = option[j];
					switch(oj[0]) {
						case '!': alert(oj.slice(1)); break;
						case '?': opt += confirm(oj.slice(1)) ? 1 +' ' : 0 +' '; break;
						case '#': var pwd = setpwd(oj.slice(1));
								opt += pwd ? pwd +' ' : 0 +' '; break;
						default : var input = prompt(oj);
								opt += input ? input +' ' : 0 +' ';
					}
				}
			}
		}
		// create temporary form for post submit
		document.body.innerHTML += 
			'<form id="formtemp" action="addonbash.php" method="post">'
			+'<input type="hidden" name="cmd" value="'+ installurl + opt +'">'
			+'</form>';
		document.getElementById("formtemp").submit();
	}
}

var pwd1, pwd2;
function setpwd(msg) {
	pwd1 = prompt(msg);
	if (!pwd1) return;
	pwd2 = prompt('Retype:'+ msg);
	if (pwd1 !== pwd2) {
		alert('Passwords not matched. Try again.');
		setpwd(msg);
	}
	return pwd1
}
</script>

</body>
</html>
