<?php
require_once( 'addonshead.php' );

$cmd = $_POST[ 'cmd' ];
if ( strpos( $cmd, 'uninstall_addo.sh' ) && !strpos( $cmd, 'install.sh' ) ) {
	echo '<style>';
	require_once( 'assets/css/addons.css' );
	echo '</style>';
	$close = '/';
} else {
	$close = 'addons.php';
}
?>

<script>
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
	<legend>Please wait until finished...</legend>

	<div class="hidescrollv">
	<pre>

<?php
$dash = round( $_POST[ 'prewidth' ] / 7.55 );

function bash( $cmd ) {
	global $dash;
	ob_end_flush(); // flush top part buffer
	
	$popencmd = popen( "$cmd 2>&1", 'r' );

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
				strpos( $std, 'warning:' ) !== false || 
				stripos( $std, 'y/n' ) !== false ||
				stripos( $std, 'Uninstall:' ) !== false
		) continue;
			
		echo $std;
	}

	pclose( $popencmd );
}

ob_implicit_flush();      // start flush output without buffer


echo str_replace( '; ', "\n", $cmd );
echo '<br>';
bash( $cmd );
?>
	</pre>
	</div>
</div>

<script>
	clearInterval( intscroll );
	pre.scrollTop = pre.scrollHeight;
	setTimeout( function() {
		document.getElementsByTagName( 'legend' )[0].innerHTML = '&nbsp;';
		var close = document.getElementById( 'close' );
		close.children[0].classList.remove( 'disabled' );
		close.href = '<?=$close;?>';
		
		alert( 'Finished.\n\nPlease see result information on screen.' );
	}, 500 );
</script>

</body>
</html>

<?php
opcache_reset();
?>
