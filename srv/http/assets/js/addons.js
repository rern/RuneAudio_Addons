addons = JSON.parse( $( "#addonsjson" ).val() );
// revision show/hide
$( '.revision' ).click( function(e) {
	e.stopPropagation();
	$( this ).parent().parent().next().toggle();
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

// branch test
function branchtest( title, message ) {
	info( {
		  title    : title
		, message  : message
		, textlabel: 'Tree #/Branch'
		, textvalue: 'UPDATE'
		, cancel   : 1
		, ok       : function() {
			branch = $( '#infoTextBox' ).val();
			opt += branch +' -b';
			formtemp();
		}
	} );
}
$( '.boxed-group .btn' ).on( 'taphold', function ( e ) {
	$this = $( this );
	alias = $this.attr( 'alias' );
	title = addons[ alias ].title.replace( / *\**$/, '' );
	type = $this.text() === 'Install' ? 'Install' : 'Update';
	rollback = addons[ alias ].rollback ? addons[ alias ].rollback : '';
	opt = '';
	branch = '';
	if ( type === 'Install' || !rollback ) {
		branchtest( title, 'Install version?' );
		return 1;
	}
	info( {
		  title    : title
		, message  : 'Upgrade / Downgrade ?'
		, radiohtml: '<label><input type="radio" name="inforadio" value="1" checked>&ensp;Rollback to previous version</label><br>'
				+'<label><input type="radio" name="inforadio" value="Branch">&ensp;Tree # / Branch ...</label>'
		, cancel   : 1
		, ok       : function() {
			if ( $( '#infoRadio input[type=radio]:checked').val() == 1 ) {
				opt += rollback +' -b';
				formtemp();
			} else {
				branchtest( title, 'Upgrade / Downgrade to ?' );
			}
		}
	} );
} ).on( 'click', function ( e ) {
	$this = $( this );
	alias = $this.attr( 'alias' );
	title = addons[ alias ].title.replace( / *\**$/, '' );
	type = $this.text();
	opt = '';
	branch = '';
	if ( $this.attr( 'space' ) ) {
		info( {
			  icon   : 'warning'
			, title  : title
			, message: 'Warning - Disk space not enough:<br>'
					+ 'Need: <white>'+ addons[ alias ].needspace +' MB</white><br>'+ $( this ).attr( 'space' )
		} );
		return
	} else if ( $this.attr( 'conflict' ) ) {
		info( {
			  icon   : 'warning'
			, title  : title
			, message: 'Warning - Conflict Addon:<br>'
					+ '<white>'+ $this.attr( 'conflict' ) +'</white> must be uninstalled first.'
		} );
		return
	} else if ( $this.attr( 'depend' ) ) {
		info( {
			  icon   : 'warning'
			, title  : title
			, message: 'Warning - Depend Addon:<br>'
					+ '<white>'+ $this.attr( 'depend' ) +'</white> must be installed first.'
		} );
		return
	}
	
	if ( type === 'Link' ) {
		window.open( $this.prev().find( 'a' ).attr( 'href' ), '_blank' );
	} else {
		info( {
			  title  : title
			, message: type +'?'
			, cancel : 1
			, ok     : function () {
				$( '#loader' )
					.html( '<i class="fa fa-addons blink"></i><br>Executing ...' )
					.removeClass( 'hide' );
				option = addons[ alias ].option;
				if ( type === 'Update' || type === 'Uninstall' || !option ) {
					formtemp();
				} else {
					j = 0;
					getoptions();
				}
			}
		} );
	}
} );
$( '.thumbnail' ).click( function() {
	$sourcecode = $( this ).prev().find('form a').attr( 'href');
	if ( $sourcecode ) window.open( $sourcecode, '_self' );
} );

function getoptions() {
	okey = Object.keys( option );
	olength = okey.length;
	oj = okey[ j ];
	oj0 = oj.replace( /[0-9]/, '' ); // remove trailing # from option keys
	switch( oj0 ) {
// -------------------------------------------------------------------------------------------------
		case 'wait':
			info( {
				  icon         : 'info-circle'
				, title        : title
				, message      : option[ oj ]
				, ok           : function() {
					sendcommand();
				}
			} );
			break;
// -------------------------------------------------------------------------------------------------
		case 'confirm':
			info( {
				  title        : title
				, message      : option[ oj ]
				, cancel       : 1
				, ok           : function() {
					sendcommand();
				}
			} );
			break;
// -------------------------------------------------------------------------------------------------
		case 'yesno':
			var ojson = option[ oj ];
			info( {
				  title        : title
				, message      : ojson.message
				, cancellabel  : ojson.cancellabel ? ojson.cancellabel : 'No'
				, cancelcolor  : ojson.checked == 0 ? '#0095d8' : ''
				, cancel       : function() {
					opt += '0 ';
					sendcommand();
				}
				, oklabel      : ojson.oklabel ? ojson.oklabel : 'Yes'
				, okcolor      : ojson.checked == 0 ? '#34495e' : ''
				, ok           : function() {
					opt += '1 ';
					sendcommand();
				}
			} );
			break;
// -------------------------------------------------------------------------------------------------
		case 'skip':
			info( {
				  title        : title
				, message      : option[ oj ]
				, cancellabel  : 'No'
				, cancel       : function() {
					sendcommand();
				}
				, oklabel      : 'Yes'
				, ok           : function() {
					$( '#loader' )
						.html( '<i class="fa fa-addons blink"></i><br>Executing ...' )
						.removeClass( 'hide' );
					formtemp();
				}
			} );
			break;
// -------------------------------------------------------------------------------------------------
		case 'text':
			var ojson = option[ oj ];
			info( {
				  title        : title
				, message      : ojson.message
				, textlabel    : ojson.label
				, textvalue    : ojson.value
				, textlabel2    : ojson.label2
				, textvalue2    : ojson.value2
				, ok         : function() {
					var input = $( '#infoTextBox' ).val();
					if ( ojson.label2 ) input += ' '+ $( '#infoTextBox2' ).val();
					opt += input ? "'"+ input +"' " : 0;
					sendcommand();
				}
			} );
			break;
// -------------------------------------------------------------------------------------------------
		case 'password':
			ojson = option[ oj ];
			info( {
				  title        : title
				, message      : ojson.message
				, passwordlabel: ojson.label
				, ok:          function() {
					var pwd = $( '#infoPasswordBox' ).val();
					if ( pwd ) {
						verifyPassword( title, pwd, function() {
							opt += "'"+ pwd +"' ";
							sendcommand();
						} );
					} else {
						if ( !ojson.required ) {
							opt += '0 ';
							sendcommand();
						} else {
							blankPassword( title, ojson.message, ojson.label, function() {
								opt += "'"+ pwd +"' ";
								sendcommand();
							} );
						}
					}
				}
			} );
			break;
// -------------------------------------------------------------------------------------------------
		case 'radio':
			ojson = option[ oj ];
			info( {
				  title        : title
				, message      : ojson.message
				, radiohtml    : function() {
					var list = ojson.list;
					var radiohtml = '';
					for ( var key in list ) {
						var checked = ( key[ 0 ] === '*' || list[ key ] == ojson.checked ) ? ' checked' : '';
						radiohtml += '<label><input type="radio" name="inforadio" value="'+ list[ key ] +'"'+ checked +'>&ensp;'+ key.replace( /^\*/, '' ) +'</label><br>';
					}
					return radiohtml
				}
				, ok           : function() {
					var radiovalue = $( '#infoRadio input[type=radio]:checked').val();
					opt += "'"+ radiovalue +"' ";
					sendcommand();
				}
			} );
			$( '#infoRadio input' ).change( function() { // cutom value
				if ( $( this ).val() === '?' ) {
					info( {
						  title       : title
						, message     : ojson.message
						, textlabel   : 'Custom'
						, ok          : function() {
							opt += "'"+ $( '#infoTextBox' ).val() +"' ";
							sendcommand();
						}
					} );
				}
			} );
			break;
// -------------------------------------------------------------------------------------------------
		case 'checkbox':
			ojson = option[ oj ];
			info( {
				  title        : title
				, message      : ojson.message
				, checkboxhtml : function() {
					var list = ojson.list;
					var checkboxhtml = '';
					for ( var key in list ) {
						var checked = ( key[ 0 ] === '*' || list[ key ] == ojson.checked ) ? ' checked' : '';
						checkboxhtml += '<label><input type="checkbox" value="'+ list[ key ] +'"'+ checked +'>\
							&ensp;'+ key.replace( /^\*/, '' ) +'</label><br>';
					}
					return checkboxhtml
				}
				, ok:       function() {
					$( '#infoCheckbox input[type=checkbox]:checked').each( function() {
						opt += "'"+ $( this ).val() +"' ";
					} );
					sendcommand();
				}
			} );
			break;
// -------------------------------------------------------------------------------------------------
		case 'select':
			ojson = option[ oj ];
			info( {
				  title        : title
				, message      : ojson.message
				, selectlabel  : ojson.label
				, selecthtml   : function() {
					var list = ojson.list;
					var selecthtml = '';
					for ( var key in list ) {
						var selected = ( key[ 0 ] === '*' || list[ key ] == ojson.checked ) ? ' selected' : '';
						selecthtml += '<option value="'+ list[ key ] +'"'+ selected +'>'+ key.replace( /^\*/, '' ) +'</option>';
					}
					return selecthtml
				}
				, ok           : function() {
					opt += "'"+ $( '#infoSelectBox').val() +"' ";
					sendcommand();
				}
			} );
			$( '#infoSelectBox' ).change( function() { // cutom value
				if ( $( '#infoSelectBox :selected' ).val() === '?' ) {
					info( {
						  title        : title
						, message      : ojson.message
						, textlabel    : 'Custom'
						, ok           : function() {
							var input = $( '#infoTextBox' ).val();
							opt += input ? "'"+ input +"' " : 0;
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
		$( '#loader' )
			.html( '<i class="fa fa-addons blink"></i><br>Executing ...' )
			.removeClass( 'hide' );
		opt += branch;
		formtemp();
	}
}
// post submit with temporary form (separate option to hide password)
function formtemp() {
	var prewidth = document.getElementsByClassName( 'container' )[ 0 ].offsetWidth - 50; // width for title lines
	// pass cache busting assets to addonsbash which cannot bind in '/templates'
	$( 'body' ).append( '\
		<form id="formtemp" action="addonsbash.php" method="post">\
			<input type="hidden" name="alias" value="'+ alias +'">\
			<input type="hidden" name="type" value="'+ type +'">\
			<input type="hidden" name="opt" value="'+ opt +'">\
			<input type="hidden" name="prewidth" value="'+ prewidth +'">\
			<input type="hidden" name="addonswoff" value="'+ $( '#addonswoff' ).val() +'">\
			<input type="hidden" name="addonsttf" value="'+ $( '#addonsttf' ).val() +'">\
			<input type="hidden" name="addonsinfocss" value="'+ $( '#addonsinfocss' ).val() +'">\
			<input type="hidden" name="addonscss" value="'+ $( '#addonscss' ).val() +'">\
			<input type="hidden" name="addonsinfojs" value="'+ $( '#addonsinfojs' ).val() +'">\
		</form>\
	' );
	$( '#formtemp' ).submit();
}
