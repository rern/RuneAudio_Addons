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
	window.scrollBy( 0, -10 );
} );
// sroll top
$( 'legend' ).click( function() {
	window.scrollTo( 0, 0 );
} );

// branch test
function branchtest( message, install ) {
	info( {
		  title     : title
		, message   : message
		, textlabel : 'Tree #/Branch'
		, textvalue : 'UPDATE'
		, boxwidth  : 'max'
		, ok        : function() {
			branch = $( '#infoTextBox' ).val() +' -b';
			option = addons[ alias ].option;
			j = 0;
			if ( install && option ) {
				getoptions();
			} else {
				opt = branch;
				formtemp();
			}
		}
	} );
}
$( '.boxed-group .btn' ).on( 'taphold', function () {
	$this = $( this );
	alias = $this.attr( 'alias' );
	title = addons[ alias ].title.replace( / *\**$/, '' );
	type = $this.text() === 'Install' ? 'Install' : 'Update';
	rollback = addons[ alias ].rollback ? addons[ alias ].rollback : '';
	opt = '';
	branch = '';
	if ( type === 'Install' ) {
		branchtest( 'Install version?', 'install' );
		return 1;
	} else if ( !rollback ) {
		branchtest( 'Install version?' );
		return 1;
	}
	info( {
		  title     : title
		, message   : 'Upgrade / Downgrade ?'
		, radiohtml : '<label><input type="radio" name="inforadio" value="1" checked>&ensp;Rollback to previous version</label><br>'
					 +'<label><input type="radio" name="inforadio" value="Branch">&ensp;Tree # / Branch ...</label>'
		, ok        : function() {
			if ( $( '#infoRadio input[type=radio]:checked').val() == 1 ) {
				opt = rollback +' -b';
				formtemp();
			} else {
				branchtest( 'Upgrade / Downgrade to ?' );
			}
		}
	} );
} ).on( 'click', function () {
	$this = $( this );
	alias = $this.attr( 'alias' );
	title = addons[ alias ].title.replace( / *\**$/, '' );
	type = $this.text();
	opt = '';
	branch = '';
	if ( $this.attr( 'space' ) ) {
		info( {
			  icon    : 'warning'
			, title   : title
			, message : '<white>Warning</white> - Disk space not enough:<br>'
					   +'Need: <white>'+ $( this ).attr( 'needmb' ) +' MB</white>'
					   +'<br>'+ $( this ).attr( 'space' )
					   +'<br>(Use <white>Expand Partition</white> addon to gain more space.)'
		} );
		return
	} else if ( $this.attr( 'conflict' ) ) {
		info( {
			  icon    : 'warning'
			, title   : title
			, message : 'Warning - Conflict Addon:<br>'
					   +'<white>'+ $this.attr( 'conflict' ) +'</white> must be uninstalled first.'
		} );
		return
	} else if ( $this.attr( 'depend' ) ) {
		info( {
			  icon    : 'warning'
			, title   : title
			, message : 'Warning - Depend Addon:<br>'
					   +'<white>'+ $this.attr( 'depend' ) +'</white> must be installed first.'
		} );
		return
	}
	
	if ( type === 'Link' ) {
		window.open( addons[ 'dual' ][ 'installurl' ], '_blank' );
	} else if ( type === 'Backup' ) {
		info( {
			  title   : title
			, message : 'Backup all RuneAudio <white>settings and databases</white>?'
			, ok      : function() {
				$.post( 'addonsdl.php', { backup: 1 }, function( data ) {
					data ? location.href = data : info( 'Process backup file failed.' );
				} );
			}
		} );
	} else {
		option = addons[ alias ].option;
		j = 0;
		if ( option && type !== 'Update' && type !== 'Uninstall' ) {
			$( '#loader' ).html( '<i class="fa fa-addons blink"></i>' ).removeClass( 'hide' );
			getoptions();
		} else {
			info( {
				  title   : title
				, message : type +'?'
				, ok      : function () {
					$( '#loader' ).html( '<i class="fa fa-addons blink"></i>' ).removeClass( 'hide' );
					( option && type !== 'Update' && type !== 'Uninstall' ) ? getoptions() : formtemp();
				}
			} );
		}
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
		case 'wait': // only 1 'Ok' = continue
			info( {
				  icon    : 'info-circle'
				, title   : title
				, message : option[ oj ]
				, oklabel : 'Continue'
				, ok      : sendcommand
			} );
			break;
// -------------------------------------------------------------------------------------------------
		case 'confirm': // 'Cancel' = close
			info( {
				  title   : title
				, message : option[ oj ]
				, oklabel : 'Continue'
				, ok      : sendcommand
			} );
			break;
// -------------------------------------------------------------------------------------------------
		case 'yesno': // 'Cancel' = 0
			var ojson = option[ oj ];
			info( {
				  title       : title
				, message     : ojson.message
				, buttonlabel : 'No'
				, button      : function() {
					opt += '0 ';
					sendcommand();
				}
				, ok          : function() {
					opt += '1 ';
					sendcommand();
				}
			} );
			break;
// -------------------------------------------------------------------------------------------------
		case 'skip': // 'Cancel' = continue, 'Ok' = skip options
			info( {
				  title       : title
				, message     : option[ oj ]
				, cancellabel : 'No'
				, cancel      : sendcommand
				, oklabel     : 'Yes'
				, ok          : function() {
					$( '#loader' )
						.html( '<i class="fa fa-addons blink"></i>' )
						.removeClass( 'hide' );
					formtemp();
				}
			} );
			break;
// -------------------------------------------------------------------------------------------------
		case 'text':
			var ojson = option[ oj ];
			info( {
				  title     : title
				, message   : ojson.message
				, textlabel : ojson.label
				, textvalue : ojson.value
				, boxwidth  : ojson.width
				, ok        : function() {
					var input = '';
					$( '.infotextbox .infoinput' ).each( function() {
						var input = this.value;
						opt += input ? "'"+ input +"' " : '0 ';
					} );
					sendcommand();
				}
			} );
			break;
// -------------------------------------------------------------------------------------------------
		case 'password':
			ojson = option[ oj ];
			info( {
				  title         : title
				, message       : ojson.message
				, passwordlabel : ojson.label
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
		case 'file':
			var ojson = option[ oj ];
			info( {
				  title       : title
				, message     : ojson.message
				, fileoklabel : ojson.label
				, filetype    : ojson.type
				, ok          : function() {
					var file = $( '#infoFileBox' )[ 0 ].files[ 0 ];
					var fd = new FormData();
					fd.append( 'file', file );
					var xhr = new XMLHttpRequest();
					xhr.open( 'POST', 'addonsdl.php', true );
					xhr.send( fd );
					xhr.onreadystatechange = function() {
						if ( xhr.readyState == 4 && xhr.status == 200 ) {
							if ( xhr.responseText ) {
								opt += "'"+ file.name +"' ";
								sendcommand();
							} else {
								info( 'Upload file failed.' );
							}
						}
					}
				}
			} );
			break;
// -------------------------------------------------------------------------------------------------
		case 'radio': // single value
			ojson = option[ oj ];
			info( {
				  title   : title
				, message : ojson.message
				, radio   : ojson.list
				, checked : ojson.checked
				, ok      : function() {
					var radiovalue = $( '#infoRadio input[ type=radio ]:checked' ).val();
					opt += "'"+ radiovalue +"' ";
					sendcommand();
				}
			} );
			$( '#infoRadio input' ).change( function() { // cutom value
				if ( $( this ).val() === '?' ) {
					info( {
						  title     : title
						, message   : ojson.message
						, textlabel : 'Custom'
						, ok        : function() {
							opt += "'"+ $( '#infoTextBox' ).val() +"' ";
							sendcommand();
						}
					} );
				}
			} );
			break;
// -------------------------------------------------------------------------------------------------
		case 'select': // long single value
			ojson = option[ oj ];
			info( {
				  title       : title
				, message     : ojson.message
				, selectlabel : ojson.label
				, select      : ojson.list
				, checked     : ojson.checked
				, ok          : function() {
					opt += "'"+ $( '#infoSelectBox').val() +"' ";
					sendcommand();
				}
			} );
			$( '#infoSelectBox' ).change( function() { // cutom value
				if ( $( '#infoSelectBox :selected' ).val() === '?' ) {
					info( {
						  title     : title
						, message   : ojson.message
						, textlabel : 'Custom'
						, ok        : function() {
							var input = $( '#infoTextBox' ).val();
							opt += input ? "'"+ input +"' " : 0;
							sendcommand();
						}
					} );
				}
			} );
			break;
// -------------------------------------------------------------------------------------------------
		case 'checkbox': // multiple values
			ojson = option[ oj ];
			info( {
				  title    : title
				, message  : ojson.message
				, checkbox : ojson.list
				, checked  : ojson.checked
				, ok       : function() {
					$( '#infoCheckBox input' ).each( function() {
						opt += "'"+ ( $( this ).prop( 'checked' ) ? 1 : 0 ) +"' ";
					} );
					sendcommand();
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
			.html( '<i class="fa fa-addons blink"></i>' )
			.removeClass( 'hide' );
		opt += branch;
		formtemp();
	}
}
// post submit with temporary form (separate option to hide password)
function formtemp() {
	$( 'body' ).append(
		'<form id="formtemp" action="addonsbash.php" method="post">'
			+'<input type="hidden" name="alias" value="'+ alias +'">'
			+'<input type="hidden" name="type" value="'+ type +'">'
			+'<input type="hidden" name="opt" value="'+ opt +'">'
		+'</form>' );
	$( '#formtemp' ).submit();
}
