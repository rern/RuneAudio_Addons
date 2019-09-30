<?php
$redis = new Redis();
$redis->connect( '127.0.0.1' );

///////////////////////////////////////////////////////////////
$addons = array(

'rre1' => array(
	'title'       => 'RuneAudio+R e1 *',
	'version'     => '20190917',
	'maintainer'  => 'r e r n',
	'description' => '<w>OBSOLETE:</w> Please upgrade to a <a href="https://www.runeaudio.com/forum/runeaudio-r-e1-an-improved-version-of-runeaudio-t6883-80.html#p28597">new version</a> <i class="fa fa-link"></i>',
	'buttonlabel' => 'Update',
	'nouninstall' => 1,
	'thumbnail'   => '/img/addons/thumbenha.gif',
	'sourcecode'  => 'https://github.com/rern/RuneAudio-Re1',
	'installurl'  => 'https://github.com/rern/RuneAudio-Re1/raw/master/install.sh',
),

);
