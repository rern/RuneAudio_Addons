<?php
$redis = new Redis(); 
$redis->pconnect('127.0.0.1');
$version = $redis->hGetAll('addons');

function addonblock($pkg) {
	global $version;
	$alias = $pkg['alias'];
	if ($version[$alias]) {                                                                                         // * installed
		$check = '<i class="fa fa-check blue"></i> ';
		if (!isset($pkg['version']) || $pkg['version'] == $version[$alias]) {                                       // omit 'version' or 'version' not changed
			$btnin = '<a class="btn btn-default disabled"><i class="fa fa-check"></i> Install</a>';                 //     disabled install button
		} else {                                                                                                    // 'version' changed
			$btnin = '<a id="up'.$alias.'" class="btn btn-primary"><i class="fa fa-refresh"></i> Update</a>';       //     change install -> update button 
		}
		$btnun = '<a id="un'.$alias.'" class="btn btn-default"><i class="fa fa-close"></i> Uninstall</a>';          // uninstall button
	} else {                                                                                                        // * not yet install
		if (isset($pkg['option'])) {
			$option = 'option="'.$pkg['option'].'"';
		} else {
			$option = '';
		}
		$check = '';
		$btnin = '<a id="in'.$alias.'" '.$option.' class="btn btn-default"><i class="fa fa-check"></i> Install</a>'; // install button with option
		$btnun = '<a class="btn btn-default disabled"><i class="fa fa-close"></i> Uninstall</a>';                   // disabled uninstall button
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
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>RuneAudio - Addons</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="msapplication-tap-highlight" content="no" />
    <link rel="stylesheet" href="assets/css/runeui.css">
    <link rel="stylesheet" href="assets/css/gpiosettings.css">
    <link rel="shortcut icon" href="assets/img/favicon.ico">
<style>
.container {
	padding : 0 15px;
}
h1 {
	display: inline-block;
	width: calc(100% - 25px);
}
#close {
	font-size: 24px;
	width: 25px;
}
#addons .boxed-group {
	padding: 10px 20px;
}
legend {
	margin-bottom: 5px;
}
.blue {
	color: #0095d8;
}
#addons .btn {
	text-transform: capitalize;
}
</style>
</head>
<body>

<?php require_once 'addoninfo.php'?>

<div id="addons" class="container">

<h1>ADDONS</h1><a id="close" href="/"><i class="fa fa-times fa-lg"></i></a>
<?php
/* each package block syntax:
$package = array(
	'title'       => 'title',
	'version'     => 'n',      // omit for non-install package
	'alias'       => 'alias',
	'description' => 'description.',
	'sourcecode'  => 'https://url/to/sourcecode',
	'option'      => 'input; ?yesno; !wait', // prompt, confirm, alert
);
addonblock($package);
*/
$package = array(
	'title'       => 'Aria2',
	'version'     => '1.1',
	'alias'       => 'aria',
	'description' => 'Download utility that supports HTTP(S), FTP, BitTorrent, and Metalink.',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/aria2',
);
addonblock($package);
$package = array(
	'title'       => 'Backup-Restore Update',
	'version'     => '1',
	'alias'       => 'back',
	'description' => 'Enable backup-restore settings and databases.',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/backup-restore',
);
addonblock($package);
$package = array(
	'title'       => 'Expand Partition',
	'alias'       => 'expa',
	'description' => 'Expand default 2GB partition to full capacity of SD card.',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/expand_partition',
);
addonblock($package);
$package = array(
	'title'       => 'Fonts - Extended characters',
	'version'     => '1',
	'alias'       => 'font',
	'description' => 'Font files replacement for Extended Latin-based, Cyrillic-based, Greek and IPA phonetics.',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/font_extended',
);
addonblock($package);
$package = array(
	'title'       => 'motd - RuneAudio Logo for SSH Terminal',
	'version'     => '1',
	'alias'       => 'motd',
	'description' => 'Message of the day - RuneAudio Logo and dimmed command prompt.',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/motd',
);
addonblock($package);
$package = array(
	'title'       => 'Rank Mirror Packages Servers',
	'alias'       => 'rank',
	'description' => 'Fix packages download errors caused by unreachable servers.',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/rankmirrors',
);
addonblock($package);
$package = array(
	'title'       => 'RuneUI Enhancements',
	'version'     => '1',
	'alias'       => 'enha',
	'description' => 'More minimalism and more fluid layout.',
	'sourcecode'  => 'https://github.com/rern/RuneUI_enhancement',
	'option'      => 'Zoom level for local browser (1.5 : Full HD, 0.7 : <800px)',
);
addonblock($package);
$package = array(
	'title'       => 'RuneUI GPIO',
	'version'     => '1',
	'alias'       => 'gpio',
	'description' => 'GPIO connected relay module control.',
	'sourcecode'  => 'https://github.com/rern/RuneUI_enhancement',
	'option'      => '?Overwrite DAC configuration from previous install'
						.'!Get DAC configuration ready',
);
addonblock($package);
$package = array(
	'title'       => 'RuneUI Password',
	'version'     => '1',
	'alias'       => 'pass',
	'description' => 'RuneUI access restriction.',
	'sourcecode'  => 'https://github.com/rern/RuneUI_password',
);
addonblock($package);
$package = array(
	'title'       => 'Samba Upgrade',
	'version'     => '1',
	'alias'       => 'samb',
	'description' => 'Faster and more customized shares.',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/samba',
	'option'      => 'Password for user root (Cancel for no password)',
);
addonblock($package);
$package = array(
	'title'       => 'Transmission',
	'version'     => '1',
	'alias'       => 'tran',
	'description' => 'Fast, easy, and free BitTorrent client.',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/transmission',
	'option'      => 'Password for Web Interface (Cancel for no password)'
						.'; ?Install WebUI alternative (Transmission Web Control)'
						.'; ?Start Transmission on system startup',
);
addonblock($package);
$package = array(
	'title'       => 'Webradio Import',
	'alias'       => 'webr',
	'description' => 'Webradio files import.',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/twebradio',
	'option'      => 'Copy webradio files to /mnt/MPD/Webradio',
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
			var options = this.getAttribute('option').replace(/; /g, ';');
			options = options.split(';');
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
						var ans = prompt(oj);
						ans = ans ? ans : 0;
						opt += ans +' ';
					}
				}
			}
		}
		window.location.href = 'addonbash.php?id='+ this.id + opt;
	}
}
</script>

</body>
</html>
