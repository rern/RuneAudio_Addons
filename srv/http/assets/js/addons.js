// auto update addons menu
( function() {
	var btnupdate = document.getElementById( 'addo' ).getElementsByClassName( 'btn' )[ 0 ];
	if ( btnupdate.innerText === ' Update' ) {
		info( {
			message: 'There is an update for <white>Addons Menu</white><br>\
						<br>\
						Update?',
			cancel: 1,
			ok: function() {
				formtemp( btnupdate.getAttribute( 'cmd' ) );				
			}
		} );
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
$( '.btnun' ).each( function() {
	var $thisbtn = $( this );
	var hammerbtn = new Hammer( this );
	
	hammerbtn.on( 'press', function () {
		info( {
			title:  gettitle( $thisbtn ),
			message: 'Reinstall?',
			cancel: 1,
			ok: function() {
				var cmdup = $thisbtn.attr( 'cmdup' );
				formtemp( cmdup );
			}
		} );
	} );
} );	
$( '.boxed-group .btn' ).click( function () {
	var $thisbtn = $( this );
	cmd = $thisbtn.attr( 'cmd' );
	title = gettitle( $( this ) );
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
				option = option.replace( /'/g, '"' ); // double quote only for JSON.parse()
				option = JSON.parse( option );
				getoptions();
			} else {
				formtemp( cmd );
			}
		}
	} );
	
} );

function gettitle( btn ) {
	return btn.parent().prev()
					.text() 
						.replace( /^ */, '' )
						.replace( /.by.*/, '' );
	;
}	
function getoptions() {
	okey = Object.keys( option );
	olength = okey.length;
	oj = okey[ j ]
	oj0 = oj.replace( /[0-9]/, '' ); // remove trailing # from option keys
	switch( oj0 ) {
		case 'alert':
			info( {
				icon:    '<i class="fa fa-info-circle fa-lg">',
				title:   title,
				message: option[ oj ],
				ok:      function() {
					sendcommand();
				}
			} );
			break;
		case 'confirm':
			info( {
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
			var ojson = option[ oj ];
			info( {
				title:   title,
				message: ojson[ 'message' ],
				textlabel: ojson[ 'label' ],
				ok:      function() {
					var input = $( '#infoTextbox' ).val();
					opt += ( input ? input : 0 ) +' ';
					sendcommand();
				}
			} );
			break;
		case 'password':
			var ojson = option[ oj ];
			var msg = ojson[ 'message' ];
			info( {
				title:       title,
				message: msg,
				passwordlabel: ojson[ 'label' ],
				ok:          function() {
					var pwd = $( '#infoPasswordbox' ).val();
					if ( pwd ) {
						verifypassword( msg, pwd, function() {
							opt += pwd +' ';
							sendcommand();
						} );
					} else {
						opt += '0 ';
						sendcommand();
					}
				}
			} );
			break;
		case 'radio':
			var ojson = option[ oj ];
			info( {
				title:    title,
				message: ojson[ 'message' ],
				radiohtml: function() {
					var list = ojson[ 'list' ];
					var radiohtml = '';
					for ( var key in list ) {
						var checked = ( key[ 0 ] === '*' ) ? ' checked' : '';
						radiohtml += '<input type="radio" name="inforadio" value="'+ list[ key ] +'"'+ checked +'><span>&ensp;'+ key.replace( /^\*/, '' ) +'</span><br>';
					}
					return radiohtml
				},
				ok:       function() {
					opt += $( '#infoRadio input[type=radio]:checked').val() +' ';
					sendcommand();
				}
			} );
			break;
		case 'checkbox':
			var ojson = option[ oj ];
			info( {
				title:    title,
				message: ojson[ 'message' ],
				checkboxhtml: function() {
					var list = ojson[ 'list' ];
					var checkboxhtml = '';
					for ( var key in list ) {
						var checked = ( key[ 0 ] === '*' ) ? ' checked' : '';
						checkboxhtml += '<input type="checkbox" value="'+ list[ key ] +'"'+ checked +'><span>&ensp;'+ key.replace( /^\*/, '' ) +'</span><br>';
					}
					return checkboxhtml
				},
				ok:       function() {
					$( '#infoCheckbox input[type=checkbox]').each( function() {
						opt += ( $( this ).is( ':checked' ) ? 1 : 0 ) +' ';
					} );
					sendcommand();
				}
			} );
			break;
		case 'select':
			var ojson = option[ oj ];
			info( {
				title:    title,
				message: ojson[ 'message' ],
				selectlabel: ojson[ 'label' ],
				selecthtml: function() {
					var list = ojson[ 'list' ];
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
	if ( j < olength ) {
		getoptions();
	} else {
		if ( cmd === '/usr/bin/sudo ' ) {
			if ( opt[ 0 ] !== '/' ) {
				opt = '/usr/bin/'+ opt;
				opt = opt.replace( /\s*;\s*/g, '; /usr/bin/' );
			}
		}
		$( '#loader' ).show();
		formtemp( cmd, opt );
	}
}
// post submit with temporary form (separate option to hide password)
function formtemp( cmd, opt ) {
		var prewidth = document.getElementsByClassName( 'container' )[ 0 ].offsetWidth - 50; // width for title lines
		
		document.body.innerHTML += 
			'<form id="formtemp" action="addonsbash.php" method="post">'
			+'<input type="hidden" name="cmd" value="'+ cmd +'">'
			+'<input type="hidden" name="opt" value="'+ ( opt ? opt : '' ) +'">'
			+'<input type="hidden" name="prewidth" value="'+ prewidth +'">'
			+'</form>';
		document.getElementById( 'formtemp' ).submit();
}
