// append style for addons icon
function heredoc( fn ) {
  return fn.toString().match( /\/\*\s*([\s\S]*?)\s*\*\//m )[ 1 ];
}
if ( $( '#list li[alias=enha] i' ).length ) {
	var style = heredoc(function () {/*
<style>
@font-face {
	font-family: enhance;
	src: url('../fonts/enhance.woff') format('woff'),
		url('../fonts/enhance.ttf') format('truetype');
	font-weight: normal;
	font-style: normal;
}
.fa {
	font-family: FontAwesome, enhance;
}
.container h1:before,
#addo span:before { 
	font-family: enhance;
	content: "\00a0\f520\00a0";
	color: #7795b4;
}
.fa-plus:before { content: "\f518" }
.fa-minus:before { content: "\f519" }
.fa-times:before { content: "\f51A" }

.fa-plus-circle:before { content: "\f51D" }
.fa-minus-circle:before { content: "\f51C" }
.fa-times-circle:before { content: "\f51E" }

.fa-check:before { content: "\f51B" }
.fa-refresh:before { content: "\f563" }
.fa-arrow-up:before { content: "\f566" }
.fa-arrow-down:before { content: "\f567" }
.fa-chevron-down:before { content: "\f568" }
.fa-external-link:before { content: "\f569" }

.fa-info-circle:before { content: "\f560" }
.fa-question-circle:before { content: "\f561" }

</style>
	*/});
	$( 'head' ).append( style );
}

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
			branch = $( '#infoTextbox' ).val();
			opt += branch +' -b';
			formtemp();
		}
	} );
}
$( '.boxed-group .btn' ).each( function() {
	var hammerbtn = new Hammer( this );
	hammerbtn.on( 'press', function ( e ) {
		$this = $( e.target );
		opt = '';
		branch = '';
		alias = $this.parent().attr( 'alias' );
		rollback = $this.attr( 'rollback' );
		type = $this.text().trim() === 'Install' ? 'Install' : 'Update';
		title = $this.parent().prev().prev().find( 'span' ).text();
		
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
				var radiovalue = $( '#infoRadio input[type=radio]:checked').val();
				if ( radiovalue == 1 ) {
					opt += rollback +' -b';
					formtemp();
				} else {
					branchtest( title, 'Upgrade / Downgrade to ?' );
				}
			}
		} );
	} ).on( 'tap', function ( e ) {
		$this = $( e.target );
		if ( $this.hasClass( 'btnneedspace' ) ) {
			info( {
				  icon   : 'info-circle'
				, title  : 'Warning'
				, message: 'Disk space not enough.<br>'
						+ $this.attr( 'needspace' )
			} );
			return
		}
		opt = '';
		branch = '';
		alias = $this.parent().attr( 'alias' );
		type = $this.text().trim();
		title = $this.parent().prev().prev().find( 'span' ).text();
		
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
} );
$( '.thumbnail' ).click( function() {
	$sourcecode = $( this ).prev().find('form a').attr( 'href');
	if ( $sourcecode ) window.open( $sourcecode, '_blank' );
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
			info( {
				  title        : title
				, message      : option[ oj ]
				, cancellabel  : 'No'
				, cancel       : function() {
					opt += '0 ';
					sendcommand();
				}
				, oklabel      : 'Yes'
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
				, ok         : function() {
					var input = $( '#infoTextbox' ).val();
					if ( alias !== 'bash' ) {
						opt += input ? "'"+ input +"' " : 0;
					} else {
						if ( input == '' ) return;
						opt += input;
					}
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
						verifypassword( title, pwd, function() {
							opt += "'"+ pwd +"' ";
							sendcommand();
						} );
					} else {
						if ( !ojson.required ) {
							opt += '0 ';
							sendcommand();
						} else {
							blankpassword( title, ojson.message, ojson.label, function() {
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
						var checked = ( key[ 0 ] === '*' || key == ojson.checked ) ? ' checked' : '';
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
							opt += "'"+ $( '#infoTextbox' ).val() +"' ";
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
						var checked = ( key[ 0 ] === '*' || key == ojson.checked ) ? ' checked' : '';
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
						var selected = ( key[ 0 ] === '*' || key == ojson.checked ) ? ' selected' : '';
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
							var input = $( '#infoTextbox' ).val();
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
