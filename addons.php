<?php
$redis = new Redis(); 
$redis->pconnect('127.0.0.1');
$addons = $redis->hGetAll('addons');
$aria = $addons['aria'];
$back = $addons['back'];
$expa = $addons['expa'];
$font = $addons['font'];
$motd = $addons['motd'];
$samb = $addons['samb'];
$tran = $addons['tran'];
$enha = $addons['enha'];
$gpio = $addons['gpio'];
$pass = $addons['pass'];
$check = '<i class="fa fa-check blue"></i>';
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
	#addons .boxed-group {
		padding: 20px;
	}
	#addons .btn {
		text-transform: capitalize;
	}
	i {
		margin-right: 10px;
	}
</style>
</head>
<body>

<div id="addons" class="container">
<h1>ADDONS</h1><a id="close" href="/"><i class="fa fa-times fa-lg"></i></a>

<div class="boxed-group">
<legend><?php if ($aria) echo $check;?>Aria2</legend>
<form class="form-horizontal">
<p>Download utility that supports HTTP(S), FTP, BitTorrent, and Metalink. ( More detail on <a href="https://github.com/rern/RuneAudio/blob/master/aria2">GitHub</a> )</p>
<a id="inaria" class="btn btn-default <?php if ($aria) echo 'disabled';?>">Install</a> &nbsp; <a id="unaria" class="btn btn-default <?php if (!$aria) echo 'disabled';?>">Uninstall</a>
</form>
</div>

<div class="boxed-group">
<legend><?php if ($back) echo $check;?>Backup-Restore Update</legend>
<form class="form-horizontal">
<p>Enable backup-restore settings and databases. ( More detail on <a href="https://github.com/rern/RuneAudio/blob/master/backup-restore">GitHub</a> )</p>
<a id="inback" class="btn btn-default <?php if ($back) echo 'disabled';?>">Install</a> &nbsp; <a id="unback" class="btn btn-default <?php if (!$back) echo 'disabled';?>">Uninstall</a>
</form>
</div>

<div class="boxed-group">
<legend><?php if ($expa) echo $check;?>Expand Partition</legend>
<form class="form-horizontal">
<p>Expand default 2GB partition to full capacity of SD card. ( More detail on <a href="https://github.com/rern/RuneAudio/blob/master/expand_partition">GitHub</a> )</p>
<a id="inexpa" class="btn btn-default <?php if ($expa) echo 'disabled';?>">Expand</a>
</form>
</div>

<div class="boxed-group">
<legend><?php if ($font) echo $check;?>Fonts - Extended characters</legend>
<form class="form-horizontal">
<p>Font files replacement for Extended Latin-based, Cyrillic-based, Greek and IPA phonetics. ( More detail on <a href="https://github.com/rern/RuneAudio/tree/master/font_extended">GitHub</a> )</p>
<a id="infont" class="btn btn-default <?php if ($font) echo 'disabled';?>">Install</a> &nbsp; <a id="unfont" class="btn btn-default <?php if (!$font) echo 'disabled';?>">Uninstall</a>
</form>
</div>

<div class="boxed-group">
<legend><?php if ($motd) echo $check;?>'motd' RuneAudio Logo for SSH Terminal</legend>
<form class="form-horizontal">
<p>Message of the day - RuneAudio Logo and dimmed command prompt. ( More detail on <a href="https://github.com/rern/RuneAudio/blob/master/motd">GitHub</a> )</p>
<a id="inmotd" class="btn btn-default <?php if ($motd) echo 'disabled';?>">Install</a> &nbsp; <a id="unmotd" class="btn btn-default <?php if (!$motd) echo 'disabled';?>">Uninstall</a>
</form>
</div>
	
<div class="boxed-group">
<legend>Rank Mirror Packages Servers</legend>
<form class="form-horizontal">
<p>Fix packages download errors. ( More detail on <a href="https://github.com/rern/RuneAudio/blob/master/rankmirrors">GitHub</a> )</p>
<a id="inrank" class="btn btn-default">Rank</a>
</form>
</div>

<div class="boxed-group">
<legend><?php if ($enha) echo $check;?>RuneUI Enhancements</legend>
<form class="form-horizontal">
<p>More minimalism and more fluid layout. ( More detail on <a href="https://github.com/rern/RuneUI_enhancement/blob/master">GitHub</a> )</p>
<a id="inenha" class="btn btn-default <?php if ($enha) echo 'disabled';?>">Install</a> &nbsp; <a id="unenha" class="btn btn-default <?php if (!$enha) echo 'disabled';?>">Uninstall</a>
</form>
</div>

<div class="boxed-group">
<legend><?php if ($gpio) echo $check;?>RuneUI GPIO</legend>
<form class="form-horizontal">
<p>GPIO connected relay module control. ( More detail on <a href="https://github.com/rern/RuneUI_GPIO/blob/master">GitHub</a> )</p>
<a id="ingpio" class="btn btn-default <?php if ($gpio) echo 'disabled';?>">Install</a> &nbsp; <a id="ungpio" class="btn btn-default <?php if (!$gpio) echo 'disabled';?>">Uninstall</a>
</form>
</div>

<div class="boxed-group">
<legend><?php if ($pass) echo $check;?>RuneUI Password</legend>
<form class="form-horizontal">
<p>GPIO connected relay module control. ( More detail on <a href="https://github.com/rern/RuneUI_password/blob/master">GitHub</a> )</p>
<a id="inpass" class="btn btn-default <?php if ($pass) echo 'disabled';?>">Install</a> &nbsp; <a id="unpass" class="btn btn-default <?php if (!$pass) echo 'disabled';?>">Uninstall</a>
</form>
</div>

<div class="boxed-group">
<legend><?php if ($samb) echo $check;?>Samba Upgrade</legend>
<form class="form-horizontal">
<p>Fast, easy, and free BitTorrent client. ( More detail on <a href="https://github.com/rern/RuneAudio/blob/master/transmission">GitHub</a> )</p>
<a id="insamb" class="btn btn-default <?php if ($samb) echo 'disabled';?>">Install</a> &nbsp; <a id="unsamb" class="btn btn-default <?php if (!$samb) echo 'disabled';?>">Uninstall</a>
</form>
</div>
	
<div class="boxed-group">
<legend><?php if ($tran) echo $check;?>Transmission</legend>
<form class="form-horizontal">
<p>Faster and more customized shares. ( More detail on <a href="https://github.com/rern/RuneAudio/blob/master/samba">GitHub</a> )</p>
<a id="intran" class="btn btn-default <?php if ($tran) echo 'disabled';?>">Install</a> &nbsp; <a id="untran" class="btn btn-default <?php if (!$tran) echo 'disabled';?>">Uninstall</a>
</form>
</div>

<div class="boxed-group">
<legend>Webradio import</legend>
<form class="form-horizontal">
<p>Webradio files import script. ( More detail on <a href="https://github.com/rern/RuneAudio/blob/master/webradio">GitHub</a> )</p>
<a id="inwebr" class="btn btn-default">Import</a>
</form>
</div>

</div>

<script>
var btn = document.getElementsByClassName('btn');
for (var i = 0; i < btn.length; i++) {
	btn[i].onclick = function(e) {
		window.location.href = 'addonbash.php?id='+ this.id;
	}
}
</script>

</body>
</html>
