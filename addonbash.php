<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>RuneAudio - Addon Install</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="msapplication-tap-highlight" content="no" />
    <link rel="stylesheet" href="assets/css/runeui.css">
    <link rel="stylesheet" href="assets/css/gpiosettings.css">
    <link rel="shortcut icon" href="assets/img/favicon.ico">
<style>
	p {
		color: #7795b4;
	}
	pre {
		color: #ddd;
	}
	.cc {
		color: #00ffff;
		background: #00ffff;
	}
	.ky {
		color: #000;
		background: #ffff00;
	}
	.ck {
		color: #00ffff;
	}
	#addons .boxed-group {
		padding: 20px;
	}
	#addons .btn {
		text-transform: capitalize;
	}
</style>
</head>
<body>
<div id="addons" class="container">
<h1>Addon Install ...</h1><a id="close" href="addons.php"><i class="fa fa-times fa-lg"></i></a>
<p>Please wait until finished.</p>
<pre>
<?php
$id = $_GET['id'];
$wget = 'wget -qN --show-progress https://github.com/rern';
$wgetsub = "$wget/RuneAudio/raw/master";
$wgetinst = 'install.sh; chmod +x install.sh; /usr/bin/sudo ./install.sh';
$uninst = '/usr/bin/sudo /usr/local/bin/uninstall_';
$addon = array(
'inaria' => "$wgetsub/aria2/$wgetinst 1",
'inback' => "$wgetsub/backup-restore/$wgetinst",
'inexpa' => "$wgetsub/expand_partition/$wgetinst",
'infont' => "$wgetsub/font_extended/$wgetinst",
'inmotd' => "$wgetsub/motd/$wgetinst",
'inrank' => "$wgetsub/rankmirrors/$wgetinst",
'insamb' => "$wgetsub/samba/$wgetinst",
'intran' => "$wgetsub/transmission/$wgetinst",
'inwebr' => "$wgetsub/webradio/$wgetinst",
'inenha' => "$wget/RuneUI_enhancement/raw/master/$wgetinst",
'ingpio' => "$wget/RuneUI_GPIO/raw/master/$wgetinst",
'ingpio' => "$wget/RuneUI_password/raw/master/$wgetinst",
'unaria' => $uninst.'aria.sh',
'unback' => $uninst.'back.sh',
'unfont' => $uninst.'font.sh',
'unmotd' => $uninst.'motd.sh',
'unsamb' => $uninst.'samb.sh',
'untran' => $uninst.'tran.sh',
'unenha' => $uninst.'enha.sh',
'ungpio' => $uninst.'gpio.sh',
'unpass' => $uninst.'pass.sh'
);

function bash($cmd) {
	while (@ ob_end_flush()); // end all output buffers if any

	$proc = popen("$cmd 2>&1", 'r');

	while (!feof($proc)) {
		$std = fread($proc, 4096);
		$std = preg_replace('/.\\[38;5;6m.\\[48;5;6m/', '<a class="cc">', $std); // bar
		$std = preg_replace('/.\\[38;5;0m.\\[48;5;3m/', '<a class="ky">', $std); // info
		$std = preg_replace('/.\\[38;5;6m.\\[48;5;0m/', '<a class="ck">', $std); // tcolor
		$std = preg_replace('/.\\[38;5;6m/', '<a class="ck">', $std); // lcolor
		$std = preg_replace('/.\\[0m/', '</a>', $std); // reset color
		echo "$std";
		@ flush();
	}

	pclose($proc);
}

bash($addon[$id]);
?>
</pre>
</div>
<script>
setInterval(function() {
	window.scrollTo(0,document.body.scrollHeight);
}, 1000);
</script>

</body>
</html>
