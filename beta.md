```php
'enha' => array(
	'title'        => 'RuneUI Enhancements **',
	'version'      => '20180801',
	'revision'     => 'Fix Library display setting bug'
					.'<br>Keep Playlist / saved Playlists display on page switched'
					.'<br>Fix RuneUI not load after uninstalled'
					.'<br>Context menus can be toggled'
					.'<br>Change context menu dialog boxes'
					.'<br>Show warning of existing Webradio name / playlist name'
					.'<br>Replace 4 major files, runeui.js, header.php, playback.php and footer.php, for more stable handling.'
					.'<br>...'
					.'<br>Switch from hammer.js to jquery.mobile which is leaner.'
					.'<br>Fix local browser zoom setting and spacing'
					.'<br>Fix missing shuffle/repeat indicator on small screen if buttons not set to hidden'
					.'<br>...'
					.'<br>Now Airplay is working properly.',
),
'airo' => array(
	'title'        => 'Setting - AirPlay Output',
	'maintainer'   => 'r e r n',
	'description'  => 'Change AirPlay output (for Shairport Sync only)',
	'buttonlabel'  => 'Change',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/raw/master/shairport-sync',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/shairport-sync/shairport-sync-output.sh',
	'option'       => array(
		'wait'      => 'Set AirPlay output:'
					.'<br><white>Connect and power on DAC</white> before proceed.'
					.'<br>It will be set as AirPlay output.'
	),
),
'shai' => array(
	'title'        => 'AirPlay Upgrade',
	'version'      => '20180808',
	'revision'     => 'Initial release',
	'maintainer'   => 'r e r n',
	'description'  => 'Upgrade AirPlay default package, Shairport, to <white>Shairport Sync 3.2.1</white>.'
					.'<br>Elapsed and song duration are supported.',
	'thumbnail'    => '/assets/addons/thumbshai.png',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/tree/master/shairport-sync',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/shairport-sync/install.sh',
	'option'       => array(
		'wait'       => '<white>Connect and power on DAC</white> before proceed.'
					.'<br>It will be set as AirPlay output.'
					.'<br>(This can be change later with an addon'
					.'<br><white>Setting - AirPlay Output</white>)'
	),
),
'redi' => array(
	'title'        => 'Redis Upgrade',
	'maintainer'   => 'r e r n',
	'description'  => 'Upgrade Redis to latest version <white>without errors</white>:'
					.'<br>Update <code>redis.service</code>',
	'thumbnail'    => '/assets/addons/thumbredi.png',
	'buttonlabel'  => 'Upgrade',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/raw/master/redis',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/redis/install.sh',
	'option'       => array(
		'confirm'    => 'Once upgraded, Redis <white>cannot be downgraded</white>.'
					.'<br>Continue?'
	),
),
'ngin' => array(
	'title'        => 'NGINX Upgrade',
	'maintainer'   => 'r e r n',
	'description'  => 'Upgrade NGINX to 1.14.0 <white>without errors</white>:'
					.'<br>preserve configuration and pushstream support',
	'thumbnail'    => '/assets/addons/thumbngin.png',
	'buttonlabel'  => 'Upgrade',
	'sourcecode'   => 'https://github.com/rern/RuneAudio/raw/master/nginx',
	'installurl'   => 'https://github.com/rern/RuneAudio/raw/master/nginx/install.sh',
	'option'       => array(
		'confirm'    => 'Once upgraded, NGINX <white>cannot be downgraded</white>.'
					.'<br>Continue?'
	),
),
```
