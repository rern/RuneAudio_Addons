// auto update addons menu
( function() {
	var btnupdate = document.getElementById( 'addo' ).getElementsByClassName( 'btn' )[ 0 ];
	if ( btnupdate.innerText === ' Update' ) {
		var ok = confirm(
			'There is an update for "Addons Menu".\n'
			+'\n'
			+'Update?'
		);
		if ( !ok ) return
    
		formtemp( btnupdate.getAttribute( 'cmd' ) );
	}
} )();

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
	list[ i ].onclick = function() {
		var alias = this.getAttribute( 'alias' );
		document.getElementById( alias ).scrollIntoView( true );
		window.scrollBy( 0, -15 );
	}
}
// sroll top
var legend = document.getElementsByTagName( 'legend' );
for ( var i = 0; i < legend.length; i++ ) {
	legend[ i ].onclick = function() {
		window.scrollTo( 0, 0 );
	}
}

// buttons click / click-hold
$( '.boxed-group .btn' ).each( function() {
	var $thisbtn = $( this );
	var hammerbtn = new Hammer( this );
	
	hammerbtn.on( 'press', function () {
		if ( $thisbtn.text().trim() !== 'Uninstall' ) return;
		info( {
			title:  gettitle( $thisbtn[0] ),
			message: 'Reinstall?',
			cancel: 1,
			ok: function() {
				var cmdup = $thisbtn.attr( 'cmdup' );
				formtemp( cmdup );				
			}
		} );
	} ).on( 'tap', function () {
		cmd = $thisbtn.attr( 'cmd' );
		title = gettitle( $thisbtn[ 0 ] );
		type = $thisbtn.text().trim();
		if ( [ 'Install', 'Uninstall', 'Update' ].indexOf(type) < 0 ) type = 'Start';
		info( {
			title: title,
			message: type +'?',
			cancel: 1,
			ok: function () {
				option = $thisbtn.attr( 'option' );
				if ( option ) {
					opt = '';
					j = 0;
					option = option.replace( /'/g, '"' );
					option = JSON.parse( option );
					getoptions();
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
		} );
		
	} );
	
} );
function getoptions() {
	okey = Object.keys( option );
	olength = okey.length;
	oj = okey[ j ];
	switch( oj ) {
		case 'alert':
			info ( {
				icon:    '<i class="fa fa-info-circle fa-lg">',
				title:   title,
				message: option[ oj ],
				ok:      function() {
					sendcommand();
				}
			} );
			break;
		case 'confirm':
			info ( {
				title:   title,
				message: option[ oj ],
				cancellabel: 'No',
				cancel:  function() {
					opt += '0 ';
					sendcommand();
				},
				oklabel: 'Yes',
				ok:     function() {
					opt += '1 ';
					sendcommand();
				}
			} );
			break;
		case 'prompt':
			info ( {
				title:   title,
				message: option[ oj ][ 'message' ],
				textbox: option[ oj ][ 'label' ],
				ok:      function() {
					var input = $( '#infoTextbox' ).val();
					opt += ( input ? input : 0 ) +' ';
					sendcommand();
				}
			} );
			break;
		case 'password':
			info ( {
				title:       title,
				message: option[ oj ][ 'message' ],
				passwordbox: option[ oj ][ 'label' ],
				ok:          function() {
					var pwd = $( '#infoPasswordbox' ).val();
					if ( pwd ) {
						verifypassword( msg, pwd, function() {
							opt += pwd +' ';
						} );
					} else {
						opt += '0 ';
					}
					sendcommand();
				}
			} );
			break;
		case 'radio':
			info ( {
				title:    title,
				message: option[ oj ][ 'message' ],
				radiobox: function() {
					var list = option[ oj ][ 'list' ];
					var radiobox = '';
					for ( var key in list ) {
						var checked = ( key[ 0 ] === '*' ) ? ' checked' : '';
						radiobox += '<input type="radio" name="inforadio" value="'+ list[ key ] +'"'+ checked +'> '+ key.replace( /^\*/, '' ) +'<br>';
					}
					return radiobox
				},
				ok:       function() {
					opt += $( '#infoRadio input[type=radio]:checked').val() +' ';
					sendcommand();
				}
			} );
			break;
		case 'checkbox':
			info ( {
				title:    title,
				message: option[ oj ][ 'message' ],
				checkbox: function() {
					var list = option[ oj ][ 'list' ];
					var checkbox = '';
					for ( var key in list ) {
						var checked = ( key[ 0 ] === '*' ) ? ' checked' : '';
						checkbox += '<input type="checkbox" value="'+ list[ key ] +'"'+ checked +'> '+ key.replace( /^\*/, '' ) +'<br>';
					}
					return checkbox
				},
				ok:       function() {
					$( '#infoCheck input[type=checkbox]:checked').each( function() {
						opt += $( this ).val() +' ';
					} );
					sendcommand();
				}
			} );
			break;
		case 'select':
			info ( {
				title:    title,
				message: option[ oj ][ 'message' ],
				selecthtml: function() {
					var list = option[ oj ][ 'list' ];
					var selecthtml = '';
					for ( var key in list ) {
						var selected = ( key[ 0 ] === '*' ) ? ' selected' : '';
						selecthtml += '<option value="'+ list[ key ] +'"'+ selected +'> '+ key.replace( /^\*/, '' ) +'</option>';
					}
					return selecthtml
				},
				ok:       function() {
					opt += $( '#infoSelectbox').val() +' ';
					sendcommand();
				}
			} );
			break;
	}
}
function sendcommand() {
	j++;
	console.log(j +' / '+ olength);
	if ( j < olength ) {
		getoptions();
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
function formtemp( command ) {		
		// width for title lines
		var prewidth = document.getElementsByClassName( 'container' )[ 0 ].offsetWidth - 50;
		
		document.body.innerHTML += 
			'<form id="formtemp" action="addonsbash.php" method="post">'
			+'<input type="hidden" name="cmd" value="'+ command +'">'
			+'<input type="hidden" name="prewidth" value="'+ prewidth +'">'
			+'</form>';
		document.getElementById( 'formtemp' ).submit();
}
