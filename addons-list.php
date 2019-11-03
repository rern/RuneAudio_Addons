<?php
// temp - minor updates

///////////////////////////////////////////////////////////////
$addons = [

'rre2' => [
	'title'       => 'RuneAudio+R e2 *',
	'version'     => '20191101',
	'revision'    => 'Initial Release',
	'maintainer'  => 'r e r n',
	'description' => 'Updates for RuneAudio <i class="fa fa-addons"></i> e2.',
	'buttonlabel' => 'Update',
	'nouninstall' => 1,
	'thumbnail'   => '/img/addons/thumbenha.gif',
	'sourcecode'  => 'https://github.com/rern/RuneAudio-Re2',
	'installurl'  => 'https://github.com/rern/RuneAudio-Re2/raw/master/install.sh',
	'hide'        => 1
],
'rest' => [
	'title'       => 'RuneAudio+R e2 - Restore settings',
	'maintainer'  => 'r e r n',
	'description' => 'Restore database and settings from backup.',
	'buttonlabel' => 'Restore',
	'sourcecode'  => 'https://github.com/rern/RuneOS/raw/master/usr/local/bin/runerestore.sh',
	'installurl'  => 'https://github.com/rern/RuneOS/raw/master/usr/local/bin/runerestore.sh',
	'option'      => [
		'wait'      => 'Copy existing database and settings:'
					  .'<br>directory <code>data</code> > <code>/srv/http</code>'
					  .'<br>before continue.'
	],
],
'cove' => [
	'title'       => 'Browse By CoverArt Thumbnails',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/coverarts/scan.sh',
	'hide'        => 1,
],
'rank' => [
	'title'       => 'Rank Mirror Package Servers',
	'maintainer'  => 'r e r n',
	'description' => 'Fix package download errors caused by unreachable servers.'
					.'<br>Rank mirror package servers by download speed and latency.',
	'thumbnail'   => '/img/addons/thumbrank.png',
	'buttonlabel' => '<i class="fa fa-bars"></i>Rank',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/rankmirrors',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/rankmirrors/rankmirrors.sh',
	'option'      => [
		'radio'     => [
			'message' => 'Download test for each server(seconds):',
			'list'    => [
				'3'  => 3,
				'4'  => 4,
				'5'  => 5,
				'6'  => 6,
				'7'  => 7,
				'8'  => 8,
				'9'  => 9,
			],
			'checked' => 3,
		],
	],
],
'gpio' => [
	'title'       => 'RuneUI GPIO',
	'version'     => '20191006',
	'needspace'   => 5,
	'revision'    => 'Support RuneAudio+R e1'
					.'<br>...'
					.'<br>Link setting location to common directory'
					.'<br>...'
					.'<br>Improve notifications - show devices name',
	'maintainer'  => 'r e r n',
	'description' => 'GPIO-connected relay module control for power on / off audio equipments.',
	'thumbnail'   => '/img/addons/thumbgpio.gif',
	'sourcecode'  => 'https://github.com/rern/RuneUI_GPIO',
	'installurl'  => 'https://github.com/rern/RuneUI_GPIO/raw/master/install.sh',
],
'aria' => [
	'title'       => 'Aria2',
	'version'     => '20170901',
	'needspace'   => 15,
	'revision'    => 'Initial release',
	'maintainer'  => 'r e r n',
	'description' => 'Download utility that supports HTTP(S], FTP, BitTorrent, and Metalink.'
					.'<br>Pre-configured and ready to use.',
	'thumbnail'   => '/img/addons/thumbaria.png',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/aria2',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/aria2/install.sh',
],
'tran' => [
	'title'       => 'Transmission',
	'version'     => '20190911',
	'needspace'   => 9,
	'revision'    => 'Support RuneAudio+R e1'
					.'<br>...'
					.'<br>Fix bugs by reverting back to custom compiled package.'
					.'<br>...'
					.'<br>Update alternateive WebUI source',
	'maintainer'  => 'r e r n',
	'description' => 'Fast, easy, and free BitTorrent client. Pre-configured and ready to use.',
	'thumbnail'   => '/img/addons/thumbtran.png',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/transmission',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/transmission/install.sh',
	'option'      => [
		'password'  => [
			'message' => 'Password for user <w>root</w> (blank = no password):',
			'label'   => 'Password',
		],
	],
],

];
