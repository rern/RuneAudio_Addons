<?php
// temp - minor updates

///////////////////////////////////////////////////////////////
$addons = array(

'rre1' => array(
	'title'       => 'RuneAudio+R e1 *',
	'version'     => '20190917',
	'revision'    => 'Initial release - R+Re 1.2',
	'maintainer'  => 'r e r n',
	'description' => 'Updates for RuneAudio <i class="fa fa-addons"></i> e1.',
	'buttonlabel' => 'Update',
	'nouninstall' => 1,
	'thumbnail'   => '/img/addons/thumbenha.gif',
	'sourcecode'  => 'https://github.com/rern/RuneAudio-Re1',
	'installurl'  => 'https://github.com/rern/RuneAudio-Re1/raw/master/install.sh',
),
'extr' => array(
	'title'       => 'RuneAudio+R e1 - Restore Extra Directories',
	'maintainer'  => 'r e r n',
	'description' => 'Restore extra directories: bookmarks, coverarts, lyrics, playlists and webradios.',
	'buttonlabel' => 'Restore',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/raw/master/extradir',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/extradir/install.sh',
	'hide'        => 1
),
'rrre' => array(
	'title'       => 'RuneAudio+R e1 -  Reset',
	'maintainer'  => 'r e r n',
	'description' => 'Reset RuneAudio <i class="fa fa-addons"></i> e1 for initial setup.',
	'buttonlabel' => '<i class="fa fa-reset"></i>Reset',
	'thumbnail'   => '/img/addons/thumbreset.png',
	'sourcecode'  => 'https://github.com/rern/RuneAudio-Re1/raw/master/setup.sh',
	'installurl'  => 'https://github.com/rern/RuneAudio-Re1/raw/master/setup.sh',
),
'radi' => array(
	'title'       => 'RuneAudio+R e1 - Import Webradio',
	'maintainer'  => 'r e r n',
	'description' => 'Import <code>*.pls</code> or <code>*.m3u</code> webradio files.',
	'buttonlabel' => '<i class="fa fa-input"></i>Import',
	'thumbnail'   => '/img/addons/thumbwebr.png',
	'sourcecode'  => 'https://github.com/rern/RuneAudio-Re1/blob/master/importwebradio.sh',
	'installurl'  => 'https://github.com/rern/RuneAudio-Re1/raw/master/importwebradio.sh',
	'option'      => array(
		'wait'      => 'Copy directory with webradio files:'
					  .'<br><code>Webradio/*</code> > <code>/mnt/MPD</code>'
					  .'<br>before continue.'
	),
),
'cove' => array(
	'title'       => 'Browse By CoverArt Thumbnails',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/coverarts/scan.sh',
	'hide'        => 1,
),
'aria' => array(
	'title'       => 'Aria2',
	'version'     => '20170901',
	'needspace'   => 15,
	'revision'    => 'Initial release',
	'maintainer'  => 'r e r n',
	'description' => 'Download utility that supports HTTP(S), FTP, BitTorrent, and Metalink.'
					.'<br>Pre-configured and ready to use.',
	'thumbnail'   => '/img/addons/thumbaria.png',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/aria2',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/aria2/install.sh',
),
'rank' => array(
	'title'       => 'Rank Mirror Package Servers',
	'maintainer'  => 'r e r n',
	'description' => 'Fix package download errors caused by unreachable servers.'
					.'<br>Rank mirror package servers by download speed and latency.',
	'thumbnail'   => '/img/addons/thumbrank.png',
	'buttonlabel' => '<i class="fa fa-bars"></i>Rank',
	'sourcecode'  => 'https://github.com/rern/RuneAudio/tree/master/rankmirrors',
	'installurl'  => 'https://github.com/rern/RuneAudio/raw/master/rankmirrors/rankmirrors.sh',
	'option'      => array(
		'radio'     => array(
			'message' => 'Download test for each server(seconds):',
			'list'    => array(
				'3'  => 3,
				'4'  => 4,
				'5'  => 5,
				'6'  => 6,
				'7'  => 7,
				'8'  => 8,
				'9'  => 9,
			),
			'checked' => 3,
		),
	),
),
'gpio' => array(
	'title'       => 'RuneUI GPIO',
	'version'     => '20190911',
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
),
'tran' => array(
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
	'option'      => array(
		'password'  => array(
			'message' => 'Password for user <w>root</w> (blank = no password):',
			'label'   => 'Password',
		),
	),
),

);
