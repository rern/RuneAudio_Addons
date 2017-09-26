<?php
require_once( 'addonshead.php' );

$alias = $_POST[ 'alias' ];
$type = $_POST[ 'type' ];

$arrayalias = array_column( $addons, 'alias' );
$aliasindex = array_search( $alias, $arrayalias );
$addon = $addons[ $aliasindex ];
$title = $addon[ 'title' ];
$cmdinstall = '
	wget -qN '.$addon[ 'installurl' ].'; 
	[[ $? != 0 ]] && ( echo Download failed.; exit )
	chmod 755 install.sh
	/usr/bin/sudo ./install.sh';
$cmduninstall = '/usr/bin/sudo /usr/local/bin/uninstall_'.$alias.'.sh';
$option = '';

if ( $type === 'Uninstall' ) {
	$command = $cmduninstall;
} else if ( $type === 'Update' ) {
	$command = $cmduninstall.' u; [[ $? != 1 ]] && '.$cmdinstall.' u';
} else {
	$command = ( $alias !== 'bash' ) ? $cmdinstall : '/usr/bin/sudo';
	$option = $_POST[ 'opt' ];
}
	
// if uninstall only - css file will be gone
if ( $alias === 'addo' && $type !== 'Update' ) {
	echo '<style>';
	require_once( 'assets/css/addons.css' );
	require_once( 'assets/css/addonsinfo.css' );
	echo '</style>';
	$close = '/';
} else {
	$close = 'addons.php';
}
?>
<!-------------------------------------------------------------------------------------------------->
<script>
// hide <pre> vertical scrollbar on desktop
var div = document.createElement('div');
div.style.cssText = 
	'width: 100px;'
	+'msOverflowStyle: scrollbar;'
	+'overflow: scroll;'
	+'visibility: hidden;'
	;
document.body.appendChild(div);
var scrollbarWidth = div.offsetWidth - div.clientWidth;
document.body.removeChild(div);

if (scrollbarWidth !== 0) {
	var css = 
		'.hidescrollv {\n'
		+'	width: 100%;\n'
		+'	overflow: hidden;\n'
		+'}\n'
		+'pre {\n'
		+'	width: calc(100% + '+ ( scrollbarWidth + 1 ) +'px);\n'
		+'}';
	var style = document.createElement('style');
	style.appendChild(document.createTextNode(css));
	document.head.appendChild(style);
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

<div class="container">
	
	<h1>ADDONS TERMINAL</h1><a id="close"><i class="fa fa-times fa-2x disabled"></i></a>
	<legend class="bl">Please wait until finished...</legend>

	<div class="hidescrollv">
	<pre>
<!-------------------------------------------------------------------------------------------------->
<?php
$dash = round( $_POST[ 'prewidth' ] / 7.55 );

function bash( $command ) {
	global $dash;
	ob_end_flush(); // flush top part buffer
	
	$popencmd = popen( "$command 2>&1", 'r' );

	while ( !feof( $popencmd ) ) {
		$std = fread( $popencmd, 4096 );
		
		$std = preg_replace( '/=(=+)=/', str_repeat( '=', $dash ), $std );         // fit line to width
		$std = preg_replace( '/-(-+)-/', str_repeat( '-', $dash ), $std );         // fit line to width
		$std = preg_replace( '/.\\[38;5;6m.\\[48;5;6m/', '<a class="cc">', $std ); // bar
		$std = preg_replace( '/.\\[38;5;0m.\\[48;5;3m/', '<a class="ky">', $std ); // info, yesno
		$std = preg_replace( '/.\\[38;5;7m.\\[48;5;1m/', '<a class="wr">', $std ); // warn
		$std = preg_replace( '/.\\[38;5;6m.\\[48;5;0m/', '<a class="ck">', $std ); // tcolor
		$std = preg_replace( '/.\\[38;5;6m/', '<a class="ck">', $std );            // lcolor
		$std = preg_replace( '/.\\[0m/', '</a>', $std );                           // reset color
		// skip lines
		if (
				stripos( $std, 'warning:' ) !== false || 
				stripos( $std, 'y/n' ) !== false ||
				stripos( $std, 'Uninstall:' ) !== false
		) continue;
			
		echo $std;
	}

	pclose( $popencmd );
}

ob_implicit_flush();      // start flush output without buffer


// show each line of commands
$find = array( '/\/usr\/bin\/sudo /', '/\[.*exit \)/', '/ \[\[ \$\? != 1 \]\] && /', '/\t*/', '/^\n/' );
$cmd = preg_replace( $find, '', $command );
$cmd = preg_replace( '/\s*;\s*/', "\n", $cmd );
if ( $alias === 'bash' ) $cmd = str_replace( '/usr/bin/', '', $option ).'<br>';

echo $cmd.'<br>';

bash( $command.' '.$option );
?>
<!-------------------------------------------------------------------------------------------------->
	</pre>
	</div>
</div>

<script src="assets/js/vendor/jquery-2.1.0.min.js"></script>
<script src="assets/js/addonsinfo.js"></script>

<script>
	setTimeout( function() {
		clearInterval( intscroll );
		pre.scrollTop = pre.scrollHeight;
		document.getElementsByTagName( 'legend' )[ 0 ].innerHTML = '&nbsp;';
		var close = document.getElementById( 'close' );
		close.children[ 0 ].classList.remove( 'disabled' );
		close.href = '<?=$close;?>';
		
		if ( '<?=$alias;?>' === 'bash' ) return;
		info( {
			icon:    '<i class="fa fa-info-circle fa-2x">',
			title:   '<?=$title;?>',
			message: 'Please see result information on screen.',
		} );
	}, 1000 );
</script>

</body>
</html>
<!-------------------------------------------------------------------------------------------------->
<?php
opcache_reset();
?>
