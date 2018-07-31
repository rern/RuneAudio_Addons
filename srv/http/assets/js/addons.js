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
	$target = $( e.target );
	$this = $target.hasClass( 'fa' ) ? $target.parent() : $target;
	alias = $this.parent().attr( 'alias' );
	rollback = $this.attr( 'rollback' );
	type = $this.text().trim() === 'Install' ? 'Install' : 'Update';
	title = $this.parent().prev().prev().find( 'span' ).text();
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
	$target = $( e.target );
	$this = $target.hasClass( 'fa' ) ? $target.parent() : $target;
	if ( $this.attr( 'needspace' ) ) {
		info( {
			  icon   : 'info-circle'
			, title  : 'Warning'
			, message: 'Disk space not enough:<br>'
					+ $this.attr( 'needspace' )
		} );
		return
	} else if ( $this.attr( 'conflict' ) ) {
		info( {
			  icon   : 'info-circle'
			, title  : 'Warning'
			, message: 'Conflict Addon:<br>'
					+ $this.attr( 'conflict' )
		} );
		return
	}
	alias = $this.parent().attr( 'alias' );
	type = $this.text().trim();
	title = $this.parent().prev().prev().find( 'span' ).text();
	opt = '';
	branch = '';
	
	if ( type === 'Link' ) {
		window.open( $this.prev().find( 'a' ).attr( 'href' ), '_blank' );
	} else {
		info( {
			  title  : title
			, message: type +'?'
			, cancel : 1
			, ok     : function () {
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
					$( '#loader' ).show();
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
					var pwd = $( '#infoPasswordbox' ).val();
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
					opt += "'"+ $( '#infoSelectbox').val() +"' ";
					sendcommand();
				}
			} );
			$( '#infoSelectbox' ).change( function() { // cutom value
				if ( $( '#infoSelectbox :selected' ).val() === '?' ) {
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
		$( '#loader' ).show();
		opt += branch;
		formtemp();
	}
}
// post submit with temporary form (separate option to hide password)
function formtemp() {
	var prewidth = document.getElementsByClassName( 'container' )[ 0 ].offsetWidth - 50; // width for title lines
	
	$( 'body' ).append( '\
		<form id="formtemp" action="addonsbash.php" method="post">\
			<input type="hidden" name="alias" value="'+ alias +'">\
			<input type="hidden" name="type" value="'+ type +'">\
			<input type="hidden" name="opt" value="'+ opt +'">\
			<input type="hidden" name="prewidth" value="'+ prewidth +'">\
		</form>\
	' );
	$( '#formtemp' ).submit();
}
