<?php
require_once('addonshead.php');
echo '
	<div class="container">
	<h1>ADDONS</h1><a id="close" href="/"><i class="fa fa-times fa-2x"></i></a>';
$redis = new Redis(); 
$redis->pconnect('127.0.0.1');
$GLOBALS['version'] = $redis->hGetAll('addons');

$GLOBALS['list'] = '';
$GLOBALS['blocks'] = '';

function addonblock($pkg) {
	$thumbnail = isset($pkg['thumbnail']) ? $pkg['thumbnail'] : '';
	$buttonlabel = isset($pkg['buttonlabel']) ? $pkg['buttonlabel'] : 'Install';
	$alias = $pkg['alias'];
	$installurl = $pkg['installurl'];
	$filename = end(explode('/', $installurl));
	$cmdinstall = 'wget -qN '.$installurl.'; chmod 755 '.$filename.'; /usr/bin/sudo ./'.$filename;
	$cmduninstall = '/usr/bin/sudo /usr/local/bin/uninstall_'.$alias.'.sh';
		
	if ($GLOBALS['version'][$alias]) {
		$check = '<i class="fa fa-check blue"></i> ';
		if (!isset($pkg['version']) || $pkg['version'] == $GLOBALS['version'][$alias]) {
			$btnin = '<a class="btn btn-default disabled"><i class="fa fa-check"></i> '.$buttonlabel.'</a>';
		} else {
			$btnin = '<a cmd="'.$cmduninstall.'; '.$cmdinstall.'" class="btn btn-primary"><i class="fa fa-refresh"></i> Update</a>';
		}
		$btnun = '<a cmd="'.$cmduninstall.'" class="btn btn-default"><i class="fa fa-close"></i> Uninstall</a>';
	} else {
		if (isset($pkg['option'])) {
			$option = 'option="'.$pkg['option'].'"';
		} else {
			$option = '';
		}
		$check = '';
		$btnin = '<a cmd="'.$cmdinstall.'" '.$option.' class="btn btn-default"><i class="fa fa-check"></i> '.$buttonlabel.'</a>';
		$btnun = '<a class="btn btn-default disabled"><i class="fa fa-close"></i> Uninstall</a>';
	}
	
	// addon list
	$GLOBALS['list'] .= '<li alias="'.$alias.'">'.$pkg['title'].'</li>';
	
	// addon blocks
	$GLOBALS['blocks'] .= '
		<div id="'.$alias.'" class="boxed-group">';
	if ($thumbnail) $GLOBALS['blocks'] .= '
		<div style="float: left; width: calc( 100% - 110px);">';
	$GLOBALS['blocks'] .= '
			<legend>'.$check.$pkg['title'].'<p>by<span>'.$pkg['maintainer'].'</span></p></legend>
			<form class="form-horizontal">
				<p>'.$pkg['description'].' <a href="'.$pkg['sourcecode'].'"> &nbsp; More &#x25B6</a></p>'
				.$btnin; if (isset($pkg['version'])) $GLOBALS['blocks'] .= ' &nbsp; '.$btnun;
	$GLOBALS['blocks'] .= '
			</form>';
	if ($thumbnail) $GLOBALS['blocks'] .= '
		</div>
		<div style="float: right; width: 100px;">
			<a href="'.$pkg['sourcecode'].'"><img src="'.$thumbnail.'"></a>
		</div>
		<div style="clear: both;"></div>';
	$GLOBALS['blocks'] .= '
		</div>';
}
require_once('addonslist.php');
?>
</div>

<script>
var detail = document.getElementById( 'detail' );
detail.onclick = function() {
	var msg = document.getElementById( 'message' );
	if ( msg.style.display == 'none' ) {
		msg.style.display = 'block';
		detail.innerHTML = 'Detail ▲';
	} else {
		msg.style.display = 'none';
		detail.innerHTML = 'Detail ▼';
	}
};

var list = document.getElementById( 'list' ).children;
for ( var i = 0; i < list.length; i++ ) {
	list[i].onclick = function() {
		var alias = this.getAttribute( 'alias' );
		document.getElementById( alias ).scrollIntoView(true);
	}
}

var btn = document.getElementsByClassName( 'btn' );
for ( var i = 0; i < btn.length; i++ ) {
	btn[i].onclick = function() {
		var cmd = this.getAttribute( 'cmd' );
		// user confirmation
		var type = this.innerHTML.split(' ').pop();
		if ( ['Install', 'Uninstall', 'Update'].indexOf(type) < 0 ) type = 'Start';
		var title = this
					.parentElement
					.previousElementSibling
						.innerText
						.replace( /by.*/, '' );
		
		if ( !confirm( type +' "'+ title +'"?' ) ) return;
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
		
		// width for title lines
		var prewidth = document.getElementsByClassName( 'container' )[0].offsetWidth - 50;
		
		// create temporary form for post submit
		document.body.innerHTML += 
			'<form id="formtemp" action="addonbash.php" method="post">'
			+'<input type="hidden" name="cmd" value="'+ cmd + opt +'">'
			+'<input type="hidden" name="prewidth" value="'+ prewidth +'">'
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
