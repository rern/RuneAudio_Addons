<?php
require_once('addonshead.php');

$cmd = $_POST['cmd'];
if ( strpos($cmd, 'uninstall_addo.sh') && !strpos($cmd, 'install.sh') ) {
	echo '<style>';
	require_once('assets/css/addons.css');
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
setTimeout(function() { 
	var pre = document.getElementsByTagName("pre")[0];
	var h0 = pre.scrollHeight;
	var h1;
	setInterval( function() {
		h1 = pre.scrollHeight;
		if ( h1 > h0 ) {
			pre.scrollTop = pre.scrollHeight;
			h0 = h1;
		}
	}, 1000 );
}, 100);
</script>

<div class="container">
	
	<h1>ADDONS TERMINAL</h1><a id="close" href="<?=$close;?>"><i class="fa fa-times fa-2x"></i></a>
	<p>Please wait until finished.</p>

	<div class="hidescrollv">
	<pre>
<?php
$dash = round($_POST['prewidth'] / 7.55);

function bash($cmd) {
	global $dash;
	while (@ ob_end_flush()); // end all buffer
	ob_implicit_flush();      // start flush output without buffer
	
	$popencmd = popen("$cmd 2>&1", 'r');

	while (!feof($popencmd)) {
		$std = fread($popencmd, 4096);
		
		$std = preg_replace('/=(=+)=/', str_repeat('=', $dash), $std);           // fit line to width
		$std = preg_replace('/-(-+)-/', str_repeat('-', $dash), $std);           // fit line to width
		$std = preg_replace('/.\\[38;5;6m.\\[48;5;6m/', '<a class="cc">', $std); // bar
		$std = preg_replace('/.\\[38;5;0m.\\[48;5;3m/', '<a class="ky">', $std); // info, yesno
		$std = preg_replace('/.\\[38;5;7m.\\[48;5;1m/', '<a class="wr">', $std); // warn
		$std = preg_replace('/.\\[38;5;6m.\\[48;5;0m/', '<a class="ck">', $std); // tcolor
		$std = preg_replace('/.\\[38;5;6m/', '<a class="ck">', $std);            // lcolor
		$std = preg_replace('/.\\[0m/', '</a>', $std);                           // reset color
		// skip lines
		if ( !(strpos($std, 'warning:') !== false || stripos($std, 'y/n') !== false) ) {
			echo "$std";
		}
	}

	pclose($popencmd);
}

bash($cmd);

echo '
	</pre>
	</div>

</div>';

opcache_reset();
?>

</body>
</html>
