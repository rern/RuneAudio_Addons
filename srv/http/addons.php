<?php
require_once( 'addonshead.php' );
require_once( 'addonslog.php' );
require_once( 'addonslist.php' );
// >>>-------------------------------------------------------------------------------------------------
echo '
	<div class="container">
	<h1>ADDONS</h1><a id="close" href="/"><i class="fa fa-times fa-2x"></i></a>
	<legend>Currently available:</legend>
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
	<ul id="list">'.
	$list.'
	</ul>'.
	$log.'
	<br>
';
echo $blocks;
// <<<-------------------------------------------------------------------------------------------------

function addonblock( $pkg ) {
	$thumbnail = isset( $pkg[ 'thumbnail' ] ) ? $pkg[ 'thumbnail' ] : '';
	$buttonlabel = isset( $pkg[ 'buttonlabel' ]) ? $pkg[ 'buttonlabel' ] : 'Install';
	$alias = $pkg[ 'alias' ];
	$installurl = $pkg[ 'installurl' ];
	if ( $alias !== 'bash' ) {
		$filename = end( explode( '/', $installurl ) );
		$cmdinstall = "wget -qN $installurl; chmod 755 $filename; /usr/bin/sudo ./$filename ";
	} else {
		$cmdinstall = '/usr/bin/sudo ';
	}
	$cmduninstall = "/usr/bin/sudo /usr/local/bin/uninstall_$alias.sh";
	$cmdupdate = "$cmduninstall u; [[ $? != 1 ]] && $cmdinstall u";
	
	if ( $GLOBALS[ 'version' ][ $alias ]) {
		$check = '<i class="fa fa-check"></i> ';
		if ( !isset( $pkg[ 'version' ] ) || $pkg[ 'version' ] == $GLOBALS[ 'version' ][ $alias ] ) {
			// !!! mobile browsers: <button>s submit 'formtemp' with 'get' > 'failed', use <a> instead
			$btnin = '<a class="btn btn-default disabled"><i class="fa fa-check"></i> '.$buttonlabel.'</a>';
		} else {
			$btnin = '<a cmd="'.$cmdupdate.'" class="btn btn-primary"><i class="fa fa-refresh"></i> Update</a>';
		}
		$btnun = '<a cmd="'.$cmduninstall.'" cmdup="'.$cmdupdate.'" class="btn btn-default"><i class="fa fa-close"></i> Uninstall</a>';
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
	$title = $pkg[ 'title' ];
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
				<p>'.$pkg[ 'description' ].' <a href="'.$pkg[ 'sourcecode' ].'" target="_blank">&emsp;detail &nbsp;<i class="fa fa-external-link"></i></a></p>'
				.$btnin; if ( isset( $pkg[ 'version' ] ) ) $GLOBALS[ 'blocks' ] .= ' &nbsp; '.$btnun;
	$GLOBALS[ 'blocks' ] .= '
			</form>';
	if ( $thumbnail ) $GLOBALS[ 'blocks' ] .= '
		</div>
		<div class="thumbnail" style="float: right; width: 100px;">
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

<script src="assets/js/vendor/jquery-2.1.0.min.js"></script>
<script src="assets/js/vendor/hammer.min.js"></script>
<script src="assets/js/addons.js"></script>

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

// buttons click / click-hold
$( '.boxed-group .btn' ).each( function() {
	var $thisbtn = $( this );
	var hammerbtn = new Hammer( this );
	
	hammerbtn.on( 'press', function () {
		if ( $thisbtn.text().trim() !== 'Uninstall' ) return;
//		if ( !confirm( 'Update "'+ gettitle( $thisbtn[0] ) +'"?' ) ) return;
		info({
			title:  gettitle( $thisbtn[0] ),
			message: 'Reinstall?',
			cancel: 1,
			ok: function() {
				var cmdup = $thisbtn.attr( 'cmdup' );
				formtemp( cmdup );				
			}
		});
	});
	
	hammerbtn.on( 'tap', function () {
		cmd = $thisbtn.attr( 'cmd' );
//		update = cmd.indexOf( '[[ $? != 1 ]]' );
		title = gettitle( $thisbtn[0] );
		type = $thisbtn.text().trim();
		if ( [ 'Install', 'Uninstall', 'Update' ].indexOf(type) < 0 ) type = 'Start';
//		if ( !confirm( type +' "'+ gettitle( $thisbtn[0] ) +'"?' ) ) return;
		info({
			title: title,
			message: type +'?',
			cancel: 1,
			ok: function () {
				// split each option per user prompt
//				var yesno = 1;
				opt = '';
				if ( $thisbtn.attr( 'option' ) ) {
					option = $thisbtn.attr( 'option' ).replace( /; /g, ';' ).split( ';' );
					j = 0;
					getoption();
				} else if ( cmd === '/usr/bin/sudo ' ) {
					if ( opt[ 0 ] !== '/' ) {
						opt = '/usr/bin/'+ opt;
						opt = opt.replace( /\s*;\s*/g, '; /usr/bin/' );
					}
					opt += ';' // ; for <br>
				
					$( '#loader' ).show();
					formtemp( cmd + opt );
				} else {
					formtemp( cmd );
				}
			}
		});
		
	});
	
} );

function getoption() {
	olength = option.length;
	oj = option[ j ];
	j++;
	switch( oj[ 0 ] ) { // get 1st character
		case '!':
			info ({
				icon: '<i class="fa fa-info-circle fa-lg">',
				title: title,
				message: oj.slice( 1 ),
				ok: function() {
					sendcommand();
				}
			});
			break;
		case '?':
			info ({
				title: title,
				message: oj.slice( 1 ),
				cancel: function() {
					opt += '0 ';
					sendcommand();
				},
				ok: function() {
					opt += '1 ';
					sendcommand();
				}
			});
			break;
		case '#':
			info ({
				title: title,
				message: oj.slice( 1 ),
				passwordbox: 'Password',
				cancel: function() {
					opt += '0 ';
					sendcommand();
				},
				ok: function() {
					opt += $( '#infoPasswordbox' ).val() +' ';
					sendcommand();
				}
			});
			break;
		default:
			info ({
				title: title,
				message: oj,
				textbox: 'input',
				cancel: function() {
					opt += '0 ';
					sendcommand();
				},
				ok: function() {
					opt += $( '#infoTextbox' ).val() +' ';
					sendcommand();
				}
			});
	}
}
function sendcommand() {
	if ( j < olength ) {
		getoption();
	} else {
		$( '#loader' ).show();
		formtemp( cmd + opt );
	}
}
function gettitle( btn ) {
	return btn.parentElement
				.previousElementSibling
					.innerText
						.replace( /^ */, '' )
						.replace( /.by.*/, '' )
	;
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
