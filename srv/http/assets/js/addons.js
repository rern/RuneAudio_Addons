// changelog show/hide
$( '#revision' ).click( function() {
	$( '#detail' ).toggle();
	$( this ).toggleClass( 'revisionup' );
} );

// sroll up click
$( '#list li' ).click( function() {
	var alias = this.getAttribute( 'alias' );
	document.getElementById( alias ).scrollIntoView( true );
	window.scrollBy( 0, -15 );
} );
// sroll top
$( 'legend' ).click( function() {
	window.scrollTo( 0, 0 );
} );

// buttons click / click-hold
$( '.btnun' ).each( function() {
	var $thisbtn = $( this );
	var hammerbtn = new Hammer( this );
	hammerbtn.on( 'press', function () {
		opt = '';
		alias = $thisbtn.parent().attr( 'alias' );
		title = gettitle( $thisbtn );
		type = 'Update';
		info( {
			title:  title,
			message: 'Reinstall?',
			cancel: 1,
			ok: function() {
				formtemp();
			}
		} );
	} );
} );	
$( '.boxed-group .btn' ).click( function () {
	var $thisbtn = $( this );
	opt = '';
	alias = $thisbtn.parent().attr( 'alias' );
	type = $thisbtn.text().trim();
	title = gettitle( $( this ) );
	info( {
		title: title,
		message: type +'?',
		cancel: 1,
		ok: function () {
			option = $thisbtn.attr( 'option' );
			if ( option ) {
				j = 0;
				option = option.replace( /'/g, '"' ); // double quote only for JSON.parse()
				option = JSON.parse( option );
				getoptions();
			} else {
				formtemp();
			}
		}
	} );
	if ( alias === 'bash' ) $( '#infoOk' ).click();
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
// -------------------------------------------------------------------------------------------------
		case 'alert':
			info( {
				icon         :  '<i class="fa fa-info-circle fa-2x">',
				title        : title,
				message      : option[ oj ],
				ok           : function() {
					sendcommand();
				}
			} );
			break;
// -------------------------------------------------------------------------------------------------
		case 'confirm':
			info( {
				title        : title,
				message      : option[ oj ],
				cancellabel  : 'No',
				cancel       : function() {
					opt += '0 ';
					sendcommand();
				},
				oklabel      : 'Yes',
				ok           : function() {
					opt += '1 ';
					sendcommand();
				}
			} );
			break;
// -------------------------------------------------------------------------------------------------
		case 'prompt':
			var ojson = option[ oj ];
			info( {
				title        : title,
				message      : ojson[ 'message' ],
				textlabel    : ojson[ 'label' ],
				ok         : function() {
					var input = $( '#infoTextbox' ).val();
					opt += ( input ? input : 0 ) +' ';
					sendcommand();
				}
			} );
			break;
// -------------------------------------------------------------------------------------------------
		case 'password':
			var ojson = option[ oj ];
			var msg = ojson[ 'message' ];
			info( {
				title        : title,
				message      : msg,
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
// -------------------------------------------------------------------------------------------------
		case 'radio':
			var ojson = option[ oj ];
			info( {
				title        : title,
				message      : ojson[ 'message' ],
				radiohtml    : function() {
					var list = ojson[ 'list' ];
					var radiohtml = '';
					for ( var key in list ) {
						var checked = ( key[ 0 ] === '*' ) ? ' checked' : '';
						radiohtml += '<label><input type="radio" name="inforadio" value="'+ list[ key ] +'"'+ checked +'>\
							&ensp;'+ key.replace( /^\*/, '' ) +'</label><br>';
					}
					return radiohtml
				},
				ok           : function() {
					var radiovalue = $( '#infoRadio input[type=radio]:checked').val();
					opt += radiovalue +' ';
					sendcommand();
				}
			} );
			$( '#infoRadio input' ).change( function() {
				if ( $( this ).val() === '?' ) {
					info( {
						title       : title,
						message     : ojson[ 'message' ],
						textlabel   : 'Custom',
						ok          : function() {
							var input = $( '#infoTextbox' ).val();
							opt += ( input ? input : 0 ) +' ';
							sendcommand();
						}
					} );
				}
			} );
			break;
// -------------------------------------------------------------------------------------------------
		case 'checkbox':
			var ojson = option[ oj ];
			info( {
				title        : title,
				message      : ojson[ 'message' ],
				checkboxhtml : function() {
					var list = ojson[ 'list' ];
					var checkboxhtml = '';
					for ( var key in list ) {
						var checked = ( key[ 0 ] === '*' ) ? ' checked' : '';
						checkboxhtml += '<label><input type="checkbox" value="'+ list[ key ] +'"'+ checked +'>\
							&ensp;'+ key.replace( /^\*/, '' ) +'</label><br>';
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
// -------------------------------------------------------------------------------------------------
		case 'select':
			var ojson = option[ oj ];
			info( {
				title        : title,
				message      : ojson[ 'message' ],
				selectlabel  : ojson[ 'label' ],
				selecthtml   : function() {
					var list = ojson[ 'list' ];
					var selecthtml = '';
					for ( var key in list ) {
						var selected = ( key[ 0 ] === '*' ) ? ' selected' : '';
						selecthtml += '<option value="'+ list[ key ] +'"'+ selected +'>'
							+ key.replace( /^\*/, '' ) +'</option>';
					}
					return selecthtml
				},
				ok           : function() {
					opt += $( '#infoSelectbox').val() +' ';
					sendcommand();
				}
			} );
			$( '#infoSelectbox' ).change( function() {
				if ( $( '#infoSelectbox :selected' ).val() === '?' ) {
					info( {
						title        : title,
						message      : ojson[ 'message' ],
						textlabel    : 'Custom',
						ok           : function() {
							var input = $( '#infoTextbox' ).val();
							opt += ( input ? input : 0 ) +' ';
							sendcommand();
						}
					} );
				}
			} );
			break;
// -------------------------------------------------------------------------------------------------
	}
}

function sendcommand() {
	j++;
	if ( j < olength ) {
		getoptions();
	} else {
		if ( alias === 'bash' ) {
			if ( opt[ 0 ] !== '/' ) {
				opt = '/usr/bin/'+ opt;
				opt = opt.replace( /\s*;\s*/g, '; /usr/bin/' );
			}
		}
		$( '#loader' ).show();
		formtemp();
	}
}
// post submit with temporary form (separate option to hide password)
function formtemp() {
		var prewidth = document.getElementsByClassName( 'container' )[ 0 ].offsetWidth - 50; // width for title lines
		
		$( 'body' ).append(
			'<form id="formtemp" action="addonsbash.php" method="post">'
			+'<input type="hidden" name="alias" value="'+ alias +'">'
			+'<input type="hidden" name="type" value="'+ type +'">'
			+'<input type="hidden" name="opt" value="'+ opt +'">'
			+'<input type="hidden" name="prewidth" value="'+ prewidth +'">'
			+'</form>'
		);
		$( '#formtemp' ).submit();
}
