<?php
include 'addonslist.php';
$addopath = 'addo'.$addons[ 'addo' ][ 'version' ];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Addons</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="msapplication-tap-highlight" content="no" />
    <link rel="stylesheet" href="assets/css/runeui.css">
    <link rel="stylesheet" href="assets/css/<?=$addopath ?>/addons.css">
    <link rel="stylesheet" href="assets/css/<?=$addopath ?>/addonsinfo.css">
    <link rel="shortcut icon" href="assets/img/favicon.ico">
    <link rel="apple-touch-icon" sizes="57x57" href="assets/img/apple-touch-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="114x114" href="assets/img/apple-touch-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="72x72" href="assets/img/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="144x144" href="assets/img/apple-touch-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="60x60" href="assets/img/apple-touch-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="120x120" href="assets/img/apple-touch-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="76x76" href="assets/img/apple-touch-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="152x152" href="assets/img/apple-touch-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/apple-touch-icon-180x180.png">
    <meta name="apple-mobile-web-app-title" content="RuneAudio">
    <link rel="icon" type="image/png" href="assets/img/favicon-192x192.png" sizes="192x192">
    <link rel="icon" type="image/png" href="assets/img/favicon-160x160.png" sizes="160x160">
    <link rel="icon" type="image/png" href="assets/img/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/png" href="assets/img/favicon-16x16.png" sizes="16x16">
    <link rel="icon" type="image/png" href="assets/img/favicon-32x32.png" sizes="32x32">
    <meta name="msapplication-TileColor" content="#000000">
    <meta name="msapplication-TileImage" content="/img/mstile-144x144.png">
    <meta name="msapplication-config" content="/img/browserconfig.xml">
    <meta name="application-name" content="RuneAddons">
</head>
<body>

<div id="loader" style="display: none;">
	<div id="loaderbg"></div>
	<div id="loadercontent"><i class="fa fa-addons"></i>connecting...</div>
</div>
