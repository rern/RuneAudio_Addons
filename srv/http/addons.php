<?php
require_once( 'addonshead.php' );
require_once( 'addonslog.php' );
require_once( 'addonslist.php' );
// >>>-------------------------------------------------------------------------------------------------
echo '
	<div class="container">
	<h1>ADDONS</h1><a id="close" href="/"><i class="fa fa-times fa-2x"></i></a>
';
// <<<-------------------------------------------------------------------------------------------------
$redis = new Redis(); 
$redis->pconnect( '127.0.0.1' );
$GLOBALS[ 'version' ] = $redis->hGetAll( 'addons' );
$GLOBALS[ 'list' ] = '';
$GLOBALS[ 'blocks' ] = '';

// sort
$arraytitle = array_column( $addons, 'title' );
array_multisort( $arraytitle, SORT_NATURAL | SORT_FLAG_CASE, $addons );

$length = count( $addons );
for ( $i = 0; $i < $length; $i++ ) {
	addonblock( $addons[ $i ] );
}
// >>>-------------------------------------------------------------------------------------------------
echo '
	Currently available:
	<ul id="list">'.
	$list.'
	</ul>'.
	$log.'
	<br>
';
echo $blocks;
// <<<-------------------------------------------------------------------------------------------------

function addonblock( $pkg ) {
	$alias = $pkg[ 'alias' ];
	$title = $pkg[ 'title' ];
	$thumbnail = isset( $pkg[ 'thumbnail' ] ) ? $pkg[ 'thumbnail' ] : '';
	$buttonlabel = isset( $pkg[ 'buttonlabel' ]) ? $pkg[ 'buttonlabel' ] : 'Install';
	$installurl = $pkg[ 'installurl' ];
	$filename = end( explode( '/', $installurl ) );
	$cmdinstall = 'wget -qN '.$installurl.'; chmod 755 '.$filename.'; /usr/bin/sudo ./'.$filename;
	$cmduninstall = '/usr/bin/sudo /usr/local/bin/uninstall_'.$alias.'.sh';
		
	if ( $GLOBALS[ 'version' ][ $alias ]) {
		$check = '<i class="fa fa-check"></i> ';
		if ( !isset( $pkg[ 'version' ] ) || $pkg[ 'version' ] == $GLOBALS[ 'version' ][ $alias ] ) {
			// !!! mobile browsers: <button>s submit 'formtemp' with 'get' > 'failed', use <a> instead
			$btnin = '<a class="btn btn-default disabled"><i class="fa fa-check"></i> '.$buttonlabel.'</a>';
		} else {
			$btnin = '<a cmd="'.$cmduninstall.' u; [[ $? != 1 ]] && '.$cmdinstall.' u" class="btn btn-primary"><i class="fa fa-refresh"></i> Update</a>';
		}
		$btnun = '<a cmd="'.$cmduninstall.'" class="btn btn-default"><i class="fa fa-close"></i> Uninstall</a>';
	} else {
		if ( isset( $pkg[ 'option' ])) {
			$option = 'option="'.$pkg[ 'option' ].'"';
		} else {
			$option = '';
		}
		$check = '';
		$btnin = '<a cmd="'.$cmdinstall.'" '.$option.' class="btn btn-default"><i class="fa fa-check"></i> '.$buttonlabel.'</a>';
		$btnun = '<a class="btn btn-default disabled"><i class="fa fa-close"></i> Uninstall</a>';
	}
	
	// addon list
	if ( $alias !== 'addo' ) {
		$listtitle = preg_replace( '/\*$/', ' <span>&star;</span>', $title );
		$GLOBALS[ 'list' ] .= '<li alias="'.$alias.'">'.$listtitle.'</li>';
	}
	// addon blocks
	$GLOBALS[ 'blocks' ] .= '
		<div id="'.$alias.'" class="boxed-group">';
	if ( $thumbnail ) $GLOBALS[ 'blocks' ] .= '
		<div style="float: left; width: calc( 100% - 110px);">';
	$GLOBALS[ 'blocks' ] .= '
			<legend>'.$check.strip_tags( preg_replace( '/\s*\*$/', '', $title ) ).'&emsp;<p>by<span>'.strip_tags( $pkg[ 'maintainer' ] ).'</span></p><a>&#x25B2</a></legend>
			<form class="form-horizontal">
				<p>'.$pkg[ 'description' ].' <a href="'.$pkg[ 'sourcecode' ].'">&emsp;detail &nbsp;<i class="fa fa-external-link"></i></a></p>'
				.$btnin; if ( isset( $pkg[ 'version' ] ) ) $GLOBALS[ 'blocks' ] .= ' &nbsp; '.$btnun;
	$GLOBALS[ 'blocks' ] .= '
			</form>';
	if ( $thumbnail ) $GLOBALS[ 'blocks' ] .= '
		</div>
		<div style="float: right; width: 100px;">
			<a href="'.$pkg[ 'sourcecode' ].'"><img src="'.$thumbnail.'"></a>
		</div>
		<div style="clear: both;"></div>';
	$GLOBALS[ 'blocks' ] .= '
		</div>';
}
// >>>-------------------------------------------------------------------------------------------------
?>

</div>
<div id="bottom"></div>

<script>
// auto update addons menu
(function() {
	var btnupdate = document.getElementById( 'addo' ).getElementsByClassName( 'btn' )[0];
	if ( btnupdate.innerText === ' Update' ) {
		var ok = confirm(
			'There is an update for "Addons Menu".\n'
			+'\n'
			+'Update?'
		);
		if ( !ok ) return
    
		formtemp( btnupdate.getAttribute( 'cmd' ) );
	}
})();

// changelog show/hide
var detail = document.getElementById( 'detail' );
detail.onclick = function() {
	var msg = document.getElementById( 'message' );
	if ( msg.style.display == 'none' ) {
		msg.style.display = 'block';
		detail.innerHTML = 'changelog ▲';
	} else {
		msg.style.display = 'none';
		detail.innerHTML = 'changelog ▼';
	}
};

// sroll up click
var list = document.getElementById( 'list' ).children;
for ( var i = 0; i < list.length; i++ ) {
	list[i].onclick = function() {
		var alias = this.getAttribute( 'alias' );
		document.getElementById( alias ).scrollIntoView(true);
		window.scrollBy(0, -15);
	}
}
// sroll top
var legend = document.getElementsByTagName( 'legend' );
for ( var i = 0; i < legend.length; i++ ) {
	legend[i].onclick = function() {
		window.scrollTo(0, 0);
	}
}

// buttons click
var btn = document.getElementsByClassName( 'btn' );
for ( var i = 0; i < btn.length; i++ ) {
	btn[ i ].onclick = function() {
		var cmd = this.getAttribute( 'cmd' );
		var update = cmd.indexOf( '[[ $? != 1 ]]' );
		// user confirmation
		var type = this.innerHTML.split(' ').pop();
		if ( ['Install', 'Uninstall', 'Update'].indexOf(type) < 0 ) type = 'Start';
		var title = this
				.parentElement
				.previousElementSibling
					.innerText
						.replace( /^ */, '' )
						.replace( /.by.*/, '' );
		
		if ( !confirm( type +' "'+ title +'"?' ) ) return;
		// split each option per user prompt
		var yesno = 1;
		var opt = ' ';
		if ( this.getAttribute( 'option' ) ) {
			var option = this.getAttribute( 'option' ).replace( /; /g, ';' ).split( ';' );
			if ( option.length > 0 ) {
				for ( var j = 0; j < option.length; j++ ) {
					var oj = option[ j ];
					switch( oj[ 0 ] ) {
						case '!':
							if ( !confirm( oj.slice( 1 ) ) ) return;
							break;
						case '?':
							opt += confirm( oj.slice( 1 ) ) ? 1 +' ' : 0 +' ';
							break;
						case '#':
							var pwd = setpwd( oj.slice( 1 ) );
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
		// send command
		formtemp(cmd + opt);

	}
}

// post submit with temporary form
function formtemp(command) {		
		// width for title lines
		var prewidth = document.getElementsByClassName( 'container' )[ 0 ].offsetWidth - 50;
		
		document.body.innerHTML += 
			'<form id="formtemp" action="addonsbash.php" method="post">'
			+'<input type="hidden" name="cmd" value="'+ command +'">'
			+'<input type="hidden" name="prewidth" value="'+ prewidth +'">'
			+'</form>';
		document.getElementById( 'formtemp' ).submit();
}
// password verify
var pwd1, pwd2;
function setpwd( msg ) {
	pwd1 = prompt( msg );
	if ( !pwd1 ) return;
	pwd2 = prompt( 'Retype:'+ msg );
	if ( pwd1 !== pwd2 ) {
		alert( 'Passwords not matched. Try again.' );
		setpwd( msg );
	}
	return pwd1
}
</script>

</body>
</html>
