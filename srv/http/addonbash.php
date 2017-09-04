<?php
require_once('addonshead.php');

$cmd = $_POST['cmd'];
$close = strpos($cmd, 'addo') ? '/' : 'addons.php'; 
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

<pre>
<?php
function bash($cmd) {
	while (@ ob_end_flush()); // end all output buffers if any

	$proc = popen("$cmd 2>&1", 'r');

	while (!feof($proc)) {
		$std = fread($proc, 4096);
		$std = preg_replace('/.\\[38;5;6m.\\[48;5;6m/', '<a class="cc">', $std); // bar
		$std = preg_replace('/.\\[38;5;0m.\\[48;5;3m/', '<a class="ky">', $std); // info, yesno
		$std = preg_replace('/.\\[38;5;7m.\\[48;5;1m/', '<a class="wr">', $std); // warn
		$std = preg_replace('/.\\[38;5;6m.\\[48;5;0m/', '<a class="ck">', $std); // tcolor
		$std = preg_replace('/.\\[38;5;6m/', '<a class="ck">', $std);            // lcolor
		$std = preg_replace('/.\\[0m/', '</a>', $std);                           // reset color
		// skip lines
		if ( strpos($std, 'warning:') !== false || strpos($std, '[Y/n]') !== false) {
			@ flush();
		} else {
			echo $std;
		}
	}

	pclose($proc);
}

bash($cmd);
?>
</pre>

</div>

</body>
</html>
