<?php
// temp - minor updates

///////////////////////////////////////////////////////////////
$addons = [

'rre4' => [
	'title'       => 'RuneAudio+R e4',
	'version'     => '20200628',
	'revision'    => 'Fix bugs',
	'maintainer'  => 'r e r n',
	'description' => 'Updates for RuneAudio <i class="fa fa-addons"></i> e4.',
	'buttonlabel' => 'Update',
	'nouninstall' => 1,
	'thumbnail'   => '/assets/img/addons/thumbenha.gif',
	'sourcecode'  => 'https://github.com/rern/RuneAudio-Re4',
	'installurl'  => 'https://github.com/rern/RuneAudio-Re4/raw/master/install.sh',
/*	'warning'     => ( exec( 'cat /srv/http/data/addons/rre4' ) > '20200628' ? '' : 'Your <wh>RuneAudio+R</wh> cannot be updated.'
					.'<br>Please download latest image file:'
					."<br><br><a class='bl' href='https://www.runeaudio.com/forum/runeaudio-r-e4-t7084.html'>RuneAudio+R e4</a>" ),*/
	'hide'        => !file_exists( '/srv/http/data/addons/rre4' )
],
'gpio' => [
	'title'       => 'RuneUI GPIO',
	'version'     => '20200624',
	'revision'    => 'Support RuneAudio+R e3',
	'maintainer'  => 'r e r n',
	'description' => 'GPIO-connected relay module control for power on / off audio equipments.',
	'thumbnail'   => '/assets/img/addons/thumbgpio.gif',
	'sourcecode'  => 'https://github.com/rern/RuneUI_GPIO',
	'installurl'  => 'https://github.com/rern/RuneUI_GPIO/raw/master/install.sh',
],
'radi' => [
	'title'       => 'Import Webradio',
	'maintainer'  => 'r e r n',
	'description' => 'Import webradio files from other versions of RuneAudio.',
	'buttonlabel' => '<i class="fa fa-input"></i>Import',
	'thumbnail'   => '/assets/img/addons/thumbwebr.png',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/webradio',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/webradio/importwebradio.sh',
	'option'      => [
		'wait'      => 'Copy directory with webradio files:'
					  .'<br><code>Webradio/*</code> > <code>/mnt/MPD</code>'
					  .'<br>before continue.'
	],
],
'plsi' => [
	'title'       => 'Import Playlists',
	'maintainer'  => 'r e r n',
	'description' => 'Import playlists from other versions of RuneAudio.',
	'buttonlabel' => '<i class="fa fa-input"></i>Import',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/playlist',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/playlist/importplaylist.sh',
	'option'      => [
		'wait'      => 'Copy playlist files to <code>/var/lib/mpd/playlists</code>'
					  .'<br>before continue.'
	],
	'hide'        => exec( '/usr/bin/grep -q "argv[ 2 ]" /srv/http/mpdplaylist.php && echo 0 || echo 1' )
],
'rank' => [
	'title'       => 'Rank Mirror Package Servers',
	'maintainer'  => 'r e r n',
	'description' => 'Fix package download errors caused by unreachable servers.'
					.'<br>Rank mirror package servers by download speed and latency.',
	'thumbnail'   => '/assets/img/addons/thumbrank.png',
	'buttonlabel' => '<i class="fa fa-bars"></i>Rank',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/rankmirrors',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/rankmirrors/rankmirrors.sh',
	'option'      => [
		'select'     => [
			'message' => 'Download test for each server:',
			'label'   => 'Seconds',
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
'aria' => [
	'title'       => 'Aria2',
	'version'     => '20190901',
	'revision'    => 'Initial release',
	'maintainer'  => 'r e r n',
	'description' => 'Download utility that supports HTTP(S], FTP, BitTorrent, and Metalink.'
					.'<br>Pre-configured and ready to use.',
	'thumbnail'   => '/assets/img/addons/thumbaria.png',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/aria2',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/aria2/install.sh',
],
'tran' => [
	'title'       => 'Transmission',
	'version'     => '20190911',
	'revision'    => 'Support RuneAudio+R e1'
					.'<br>...'
					.'<br>Fix bugs by reverting back to custom compiled package.'
					.'<br>...'
					.'<br>Update alternateive WebUI source',
	'maintainer'  => 'r e r n',
	'description' => 'Fast, easy, and free BitTorrent client. Pre-configured and ready to use.',
	'thumbnail'   => '/assets/img/addons/thumbtran.png',
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
