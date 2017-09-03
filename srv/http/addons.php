<?php
require_once('addonshead.php');

echo '<div class="container">'
	.'<h1>ADDONS</h1><a id="close" href="/"><i class="fa fa-times fa-2x"></i></a>';

$redis = new Redis(); 
$redis->pconnect('127.0.0.1');
$version = $redis->hGetAll('addons');

function addonblock($pkg) {
	global $version;
	$alias = $pkg['alias'];
	$installurl = $pkg['installurl'];
	$filename = end(explode('/', $installurl));
	if ($version[$alias]) {
		$check = '<i class="fa fa-check blue"></i> ';
		if (!isset($pkg['version']) || $pkg['version'] == $version[$alias]) {
			$btnin = '<a class="btn btn-default disabled"><i class="fa fa-check"></i> Install</a>';
		} else {
			$command = 'wget -qN '.str_replace($filename, 'update.sh', $installurl).'; chmod 755 update.sh; /usr/bin/sudo ./update.sh';
			$btnin = '<a installurl="'.$command.'" class="btn btn-primary"><i class="fa fa-refresh"></i> Update</a>';
		}
		$command = '/usr/bin/sudo /usr/local/bin/uninstall_'.$alias.'.sh';
		$btnun = '<a installurl="'.$command.'" class="btn btn-default"><i class="fa fa-close"></i> Uninstall</a>';
	} else {
		if (isset($pkg['option'])) {
			$option = 'option="'.$pkg['option'].'"';
		} else {
			$option = '';
		}
		$check = '';
		$command = 'wget -qN '.$installurl.'; chmod 755 '.$filename.'; /usr/bin/sudo ./'.$filename;
		$btnin = '<a installurl="'.$command.'" '.$option.' class="btn btn-default"><i class="fa fa-check"></i> Install</a>';
		$btnun = '<a class="btn btn-default disabled"><i class="fa fa-close"></i> Uninstall</a>';
	}
	echo '
		<div class="boxed-group">
		<legend>'.$check.$pkg['title'].'<p class="">by <span> '.$pkg['maintainer'].'</span></p></legend>
		<form class="form-horizontal">
			<p>'.$pkg['description'].' ( <a href="'.$pkg['sourcecode'].'">More detail</a> )</p>'
			.$btnin;
	if (isset($pkg['version']))
		echo ' &nbsp; '.$btnun;
	echo
		'</form>
		</div>';
}

require_once('addonslist.php');
?>
</div>

<script>
var btn = document.getElementsByClassName( 'btn' );
for ( var i = 0; i < btn.length; i++ ) {
	btn[i].onclick = function() {
		// user confirmation
		var installurl = this.getAttribute( 'installurl' );
		switch( installurl.split( '/' ).pop().substr( 0, 2 ) ) {
			case 'un': var type = 'Uninstall "'; break;
			case 'up': var type = 'Update "'; break;
			default  : var type = 'Install "';
		}
		var title = this
				.parentElement
				.previousElementSibling
				.innerHTML
					.replace( /<i.*i>/, '' )
					.replace( /<p.*p>/, '' );
		
		if ( !confirm( type + title +'"?' ) ) return;

		// split each option per user prompt
		var yesno = 1;
		var opt = ' ';
		if ( this.getAttribute( 'option' ) ) {
			var option = this.getAttribute( 'option' ).replace( /; /g, ';' ).split( ';' );
			if ( option.length > 0 ) {
				for ( var j = 0; j < option.length; j++ ) {
					var oj = option[j];
					switch( oj[0] ) {
						case '!':
							if ( !confirm( oj.slice(1) ) ) return;
							opt += 1 +' ';
							break;
						case '?':
							opt += confirm( oj.slice(1) ) ? 1 +' ' : 0 +' ';
							break;
						case '#':
							var pwd = setpwd( oj.slice(1) );
							opt += pwd ? pwd +' ' : 0 +' ';
							break;
						default :
							var input = prompt( oj );
							opt += input ? input +' ' : 0 +' ';
					}
				}
			}
		}
		
		document.getElementById( 'loader' ).style.display = 'block';
		
		// create temporary form for post submit
		document.body.innerHTML += 
			'<form id="formtemp" action="addonbash.php" method="post">'
			+'<input type="hidden" name="cmd" value="'+ installurl + opt +'">'
			+'</form>';
		document.getElementById( 'formtemp' ).submit();
	}
}

var pwd1, pwd2;
function setpwd(msg) {
	pwd1 = prompt(msg);
	if (!pwd1) return;
	pwd2 = prompt('Retype:'+ msg);
	if (pwd1 !== pwd2) {
		alert('Passwords not matched. Try again.');
		setpwd(msg);
	}
	return pwd1
}
</script>

</body>
</html>
