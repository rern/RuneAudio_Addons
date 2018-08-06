<?php
ignore_user_abort( TRUE ); // for 'connection_status()' to work
include 'addonshead.php';
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
    <link rel="stylesheet" href="assets/css/addons.css">
    <link rel="stylesheet" href="assets/css/addonsinfo.css">
    <link rel="shortcut icon" href="assets/img/favicon.ico">
</head>
<body>

<div id="loader" style="display: none;">
	<div id="loaderbg"></div>
	<div id="loadercontent"><i class="fa fa-addons"></i>connecting...</div>
</div>

<?php include 'addonslist.php';?>
<!-- ...................................................................................... -->
<script>
// hide <pre> vertical scrollbar on desktop
var div = document.createElement( 'div' );
div.style.cssText = 
	'width: 100px;'
	+'msOverflowStyle: scrollbar;'
	+'overflow: scroll;'
	+'visibility: hidden;'
	;
document.body.appendChild( div );
var scrollbarWidth = div.offsetWidth - div.clientWidth;
document.body.removeChild( div );

if ( scrollbarWidth !== 0 ) {
	var css = 
		'.hidescrollv {\n'
		+'	width: 100%;\n'
		+'	overflow: hidden;\n'
		+'}\n'
		+'pre {\n'
		+'	width: calc(100% + '+ ( scrollbarWidth + 1 ) +'px);\n'
		+'}';
	var style = document.createElement( 'style' );
	style.appendChild( document.createTextNode( css ) );
	document.head.appendChild( style );
}

// js for '<pre>' must be here before 'function bash()'.
// php 'flush' loop waits for all outputs before going to next lines.
// but must 'setTimeout()' for '<pre>' to load to fix 'undefined'.
setTimeout( function() {
	pre = document.getElementsByTagName( 'pre' )[ 0 ];
	var h0 = pre.scrollHeight;
	var h1;
	intscroll = setInterval( function() {
		h1 = pre.scrollHeight;
		if ( h1 > h0 ) {
			pre.scrollTop = pre.scrollHeight;
			h0 = h1;
		}
	}, 1000 );
}, 1000 );
</script>

<!-- php 'flush' on uninstall 'addo', addonsinfo.js file will be gone if put below 'flush' -->
<script src="assets/js/vendor/jquery-2.1.0.min.js"></script>
<script src="assets/js/addonsinfo.js"></script>

<div class="container">
	<a id="close" class="close-root"><i class="fa fa-times fa-2x disabled"></i></a>
	<h1>ADDONS TERMINAL</h1>
	<legend class="bl">Please wait until finished...</legend>

	<div class="hidescrollv">
	<pre>
<!-- ...................................................................................... -->
<?php
$alias = $_POST[ 'alias' ];
$type = $_POST[ 'type' ];
$opt = $_POST[ 'opt' ];
$dash = round( $_POST[ 'prewidth' ] / 7.55 );
$addon = $addons[ $alias ];
$installurl = $addon[ 'installurl' ];
$conflict = $addon[ 'conflict' ];
$conflictcommandtxt = '';
$conflictcommand = '';
if ( $conflict && file_exists( '/usr/local/bin/uninstall_'.$conflict.'.sh' ) ) {
	$conflictcommandtxt = 'uninstall_'.$conflict.'.sh';
	$conflictcommand = '/usr/bin/sudo /usr/local/bin/'.$conflictcommandtxt;
}

$optarray = explode( ' ', $opt );
if ( end( $optarray ) === '-b' ) $installurl = str_replace( 'raw/master', 'raw/'.prev( $optarray ), $installurl );

$installfile = basename( $installurl );
$title = preg_replace( '/\**$/', '', $addon[ 'title' ] );

$install = <<<cmd
	wget -qN --no-check-certificate $installurl 
	if [[ $? != 0 ]]; then 
		echo -e '\e[38;5;7m\e[48;5;1m ! \e[0m Install file download failed.'
		echo 'Please try again.'
		exit
	fi
	chmod 755 $installfile
	$conflictcommand
	/usr/bin/sudo ./$installfile $opt
cmd;
$uninstall = <<<cmd
	/usr/bin/sudo /usr/local/bin/uninstall_$alias.sh
cmd;

if ( $type === 'Uninstall' ) {
	$command = $uninstall;
	$commandtxt = "uninstall_$alias.sh";
} else if ( $type === 'Update' ) {
	$command = <<<cmd
		wget -qN --no-check-certificate $installurl
		if [[ $? != 0 ]]; then 
			echo -e '\e[38;5;7m\e[48;5;1m ! \e[0m Install file download failed.'
			echo 'Please try again.'
			exit
		fi
		chmod 755 $installfile
		
		$uninstall u
		
		/usr/bin/sudo ./$installfile u $opt
cmd;
	$commandtxt = <<<cmd
		wget -qN --no-check-certificate $installurl
		chmod 755 $installfile
		
		uninstall_$alias.sh u
		
		./$installfile u $opt
cmd;
} else {
	$command = $install;
	$commandtxt = <<<cmd
		wget -qN --no-check-certificate $installurl
		chmod 755 $installfile
		$conflictcommandtxt
		./$installfile $opt
cmd;
}
$commandtxt = preg_replace( '/\t*/', '', $commandtxt );

// if uninstall only - css file will be gone
if ( $alias === 'addo' && $type !== 'Update' ) {
	echo '<style>';
	include 'assets/css/addons.css';
	include 'assets/css/addonsinfo.css';
	echo '</style>';
	$close = '/';
} else {
	$close = '/addons';
}

echo $commandtxt.'<br>';

// for convert bash stdout to html
$replace = array(
	'/=(=+)=/'               => str_repeat( '=', $dash ), // fit line to width
	'/-(-+)-/'               => str_repeat( '-', $dash ), // fit line to width
	'/.\[38;5;6m.\[48;5;6m/' => '<a class="cc">',         // bar
	'/.\[38;5;0m.\[48;5;3m/' => '<a class="ky">',         // info, yesno
	'/.\[38;5;7m.\[48;5;1m/' => '<a class="wr">',         // warn
	'/.\[38;5;6m.\[48;5;0m/' => '<a class="ck">',         // tcolor
	'/.\[38;5;6m/'           => '<a class="ck">',         // lcolor
	'/.\[0m/'                => '</a>',                   // reset color
);
$skip = array( 'warning:', 'y/n', 'uninstall:' );
$skippacman = array( 'downloading core.db', 'downloading extra.db', 'downloading alarm.db', 'downloading aur.db' );

ob_implicit_flush();       // start flush: bypass buffer - output to screen
ob_end_flush();            // force flush: current buffer (run after flush started)

$popencmd = popen( "$command 2>&1", 'r' );                // start bash
while ( !feof( $popencmd ) ) {                            // each line
	$std = fread( $popencmd, 4096 );                      // read

	$std = preg_replace(                                  // convert to html
		array_keys( $replace ),
		array_values( $replace ),
		$std
	);
	foreach( $skip as $find ) {                           // skip line
		if ( stripos( $std, $find ) !== false ) continue 2;
	}
	foreach( $skippacman as $findp ) {                    // skip pacman line after output once
		if ( stripos( $std, $findp ) !== false ) $skip[] = $findp; // add skip string to $skip array
	}

	echo $std;                                            // stdout to screen
	
	if ( connection_status() !== 0 || connection_aborted() === 1 ) {
		$path = '/usr/bin/sudo /usr/bin/';
		exec( $path.'killall '.$installfile.' wget pacman &' );
		exec( $path.'rm /var/lib/pacman/db.lck /srv/http/*.zip /usr/local/bin/uninstall_'.$alias.'.sh &' );
		exec( $path.'redis-cli hdel addons '.$alias.' &' );
		pclose( $popencmd );
		die();
	}
}
pclose( $popencmd );
?>
<!-- ...................................................................................... -->
	</pre>
	</div>
</div>

<script>
	setTimeout( function() {
		clearInterval( intscroll );
		pre.scrollTop = pre.scrollHeight;
		document.getElementsByTagName( 'legend' )[ 0 ].innerHTML = '&nbsp;';
		var close = document.getElementsByClassName( 'close-root' )[ 0 ];
		close.children[ 0 ].classList.remove( 'disabled' );
		close.href = '<?=$close;?>';
		
		if ( '<?=$alias;?>' === 'bash' ) return;
		info( {
			icon:    'info-circle',
			title:   '<?=$title;?>',
			message: 'Please see result information on screen.',
		} );
	}, 1000 );
</script>

</body>
</html>
<!-- ...................................................................................... -->
<?php
opcache_reset();
?>
