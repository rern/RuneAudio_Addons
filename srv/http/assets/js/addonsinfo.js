/*
simple usage: 
info( 'message' );

normal usage:
info( {                                     // default
	width         : N                       // 400            (info width)
	icon          : 'NAME'                  // 'question'     (FontAwesome name for top icon)
	title         : 'TITLE'                 // 'Information'  (top title)
	nox           : 1                       // (show)         (no top 'X' close button)
	nobutton      : 1                       // (show)         (no button)
	boxwidth      : N                       // 200            (input text/password width - 'max' to fit)
	autoclose     : N                       // (disabled)     (auto close in ms)
	preshow       : FUNCTION                // (none)         (function before show)
	
	content       : 'HTML'                  //                (replace whole '#infoContent' html)
	message       : 'MESSAGE'               // (blank)        (message under title)
	messagealign  : 'CSS'                   // 'center'       (message under title)
	
	textlabel     : [ 'LABEL', ... ]        // (blank)        (label array input label)
	textvalue     : [ 'VALUE', ... ]        // (blank)        (pre-filled array input value)
	textrequired  : [ N, ... ]              // (none)         (required fields disable ok button if blank)
	textalign     : 'CSS'                   // 'left'         (input text alignment)
	
	passwordlabel : 'LABEL'                 // (blank)        (password input label)
	pwdrequired   : 1                       // (none)         (password required)
	
	fileoklabel   : 'LABEL'                 // 'OK'           (upload button label)
	filetype      : 'TYPE'                  // (none)         (filter and verify filetype)
	
	radio         : { LABEL: 'VALUE', ... } //                ( var value = $( '#infoRadio input[ type=radio ]:checked' ).val(); )
	checked       : N                       // 0              (pre-select input index)
	
	select        : { LABEL: 'VALUE', ... } //                ( var value = $( '#infoSelectBox').val(); )
	selectlabel   : 'LABEL'                 // (blank)        (select input label)
	checked       : N                       // 0              (pre-select option index)
	
	checkbox      : { LABEL: 'VALUE', ... } //                ( $( '#infoCheckBox input[ type=checkbox ]:checked' ).each( function() {
	                                                                var value = this.value;
	                                                            } ); )
	checked       : [ N, ... ]              // (none)         (pre-select array input indexes)
	
	oklabel       : 'LABEL'                 // 'OK'           (ok button label)
	okcolor       : 'COLOR'                 // '#0095d8'      (ok button color)
	ok            : FUNCTION                // (reset)        (ok click function)
	cancellabel   : 'LABEL'                 // 'Cancel'       (cancel button label)
	cancelcolor   : 'COLOR'                 // '#34495e'      (cancel button color)
	cancelbutton  : 1                       // (hide)         (cancel button color)
	cancel        : FUNCTION                // (reset)        (cancel click function)
	
	buttonlabel   : [ 'LABEL', ... ]        //                (label array)
	button        : [ FUNCTION, ... ]       //                (function array)
	buttoncolor   : [ 'COLOR', ... ]        // '#34495e'      (color array)
	buttonwidth   : 1                       // (none)         (equal buttons width)
} );
Note:
- No default - must be specified.
- Single value/function - no need to be array
*/
function heredoc( fn ) {
	return fn.toString().match( /\/\*\s*([\s\S]*?)\s*\*\//m )[ 1 ];
};
var containerhtml = heredoc( function() { /*
<div id="infoOverlay" tabindex="1">
	<div id="infoBox">
		<div id="infoTopBg">
			<div id="infoTop">
				<i id="infoIcon"></i>&emsp;<a id="infoTitle"></a>
			</div>
			<i id="infoX" class="fa fa-times"></i>
			<div style="clear: both"></div>
		</div>
		<div id="infoContent">
		</div>
		<div id="infoButtons">
			<div id="infoFile">
				<a id="infoFileLabel" class="filebtn infobtn-primary">Browse</a>
				<span id="infoFilename"></span>
				<input type="file" class="infoinput" id="infoFileBox">
			</div>
			<a id="infoCancel" class="infobtn infobtn-default"></a>
			<a id="infoOk" class="infobtn infobtn-primary"></a>
		</div>
	</div>
</div>
*/ } );
infocontenthtml = heredoc( function() { /*
			<p id="infoMessage" class="infocontent"></p>
			<div id="infoText" class="infocontent">
				<div class="infotextlabel"></div>
				<div class="infotextbox"></div>
			</div>
			<div id="infoPassword" class="infocontent">
				<a id="infoPasswordLabel" class="infolabel"></a><input type="password" class="infoinput" id="infoPasswordBox">
			</div>
			<div id="infoRadio" class="infocontent infohtml"></div>
			<div id="infoCheckBox" class="infocontent infohtml"></div>
			<div id="infoSelect" class="infocontent">
				<a id="infoSelectLabel" class="infolabel"></a><select class="infohtml" id="infoSelectBox"></select>
			</div>
*/ } );

$( 'body' ).prepend( containerhtml );

emptyinput = 0; // for 'textrequired'

$( '#infoOverlay' ).keydown( function( e ) {
	if ( $( '#infoOverlay' ).is( ':visible' ) ) {
		if ( e.key == 'Enter' && !$( '#infoOk' ).hasClass( 'disabled' ) ) {
			$( '#infoOk' ).click();
		} else if ( e.key === 'Escape' ) {
			infoReset();
		}
	}
} );
// close: reset to default
$( '#infoX' ).click( function() {
	$( '#infoCancel' ).click();
	$( '#infoContent' ).empty();
} );

function infoReset() {
	$( '#infoContent' ).html( infocontenthtml );
	$( '#infoOverlay, .infocontent, .infolabel, .infoinput, .infohtml, .filebtn, .infobtn' ).hide();
	$( '.infoinput' ).css( 'text-align', '' );
	$( '#infoBox, .infolabel, .infoinput' ).css( 'width', '' );
	$( '.filebtn, .infobtn' ).css( 'background', '' ).off( 'click' );
	$( '#infoIcon' ).removeAttr( 'class' );
	$( '#infoFileBox' ).removeAttr( 'accept' );
	$( '#infoOk' ).removeClass( 'disabled' );
	$( '.extrabtn' ).remove();
	$( '#loader' ).addClass( 'hide' ); // for 'X' click
}

function info( O ) {
	infoReset();
//	setTimeout( function() { // force wait for infoReset()
	///////////////////////////////////////////////////////////////////
	// simple use as info( 'message' )
	if ( typeof O !== 'object' ) {
		$( '#infoMessage' ).html( O );
		$( '#infoIcon' ).addClass( 'fa fa-info-circle' );
		$( '#infoOverlay, #infoMessage, #infoOk' ).show();
		alignVertical();
		$( '#infoOk' ).html( 'OK' ).click( function() {
			infoReset();
		});
		return;
	}
	
	// title
	$( '#infoBox' ).css( 'width', ( O.width || 400 ) +'px' );
	if ( 'icon' in O ) {
		if ( O.icon.charAt( 0 ) !== '<' ) {
			$( '#infoIcon' ).addClass( 'fa fa-'+ O.icon );
		} else {
			$( '#infoIcon' ).html( O.icon );
		}
	} else {
		$( '#infoIcon' ).addClass( 'fa fa-question-circle' );
	}
	$( '#infoTitle' ).html( O.title || 'Information' );
	if ( 'nox' in O ) $( '#infoX' ).hide();
	if ( 'autoclose' in O ) {
		setTimeout( function() {
			$( '#infoX' ).click();
		}, O.autoclose );
	}
	
	// buttons
	if ( 'nobutton' in O === false ) {
		$( '#infoOk' )
			.html( O.oklabel ? O.oklabel : 'OK' )
			.css( 'background-color', O.okcolor || '' )
			.show();
			if ( typeof O.ok === 'function' ) $( '#infoOk' ).click( O.ok );
		if ( 'cancel' in O ) {
			$( '#infoCancel' )
				.html( O.cancellabel || 'Cancel' )
				.css( 'background', O.cancelcolor || '' );
			if ( 'cancelbutton' in O ) $( '#infoCancel' ).show();
			if ( typeof O.cancel === 'function' ) $( '#infoCancel' ).click( O.cancel );
		}
		if ( 'button' in O ) {
			if ( !O.button.length ) O.button = [ O.button ];
			if ( typeof O.buttonlabel !== 'object' ) O.buttonlabel = [ O.buttonlabel ];
			O.buttoncolor = O.buttoncolor || '';
			if ( typeof O.buttoncolor !== 'object' ) O.buttoncolor = [ O.buttoncolor ];
			var buttonhtml = '';
			var iL = O.button.length;
			for ( i = 0; i < iL; i++ ) {
				var iid = i || '';
				$( '#infoOk' ).before(  '<a id="infoButton'+ iid +'" class="infobtn extrabtn infobtn-default">'+ O.buttonlabel[ i ] +'</a>' );
				$( '#infoButton'+ iid )
									.css( 'background-color', O.buttoncolor[ i ] || '' )
									.click( O.button[ i ] );
			}
		}
		$( '.infobtn' ).click( infoReset );
	}
	
	if ( O.content ) {
		// custom html content
		$( '#infoContent' ).html( O.content );
	} else {
		// message
		if ( 'message' in O ) {
			$( '#infoMessage' )
				.html( O.message )
				.css( 'text-align', O.messagealign || 'center' )
				.show();
		}
		// inputs
		if ( 'textlabel' in O || 'textvalue' in O ) {
			O.textlabel = O.textlabel || '';
			O.textvalue = O.textvalue || '';
			if ( typeof O.textlabel !== 'object' ) O.textlabel = [ O.textlabel ];
			if ( typeof O.textvalue !== 'object' ) O.textvalue = [ O.textvalue ];
			var labelhtml = '';
			var boxhtml = '';
			var iL = O.textlabel.length > 1 ? O.textlabel.length : O.textvalue.length;
			for ( i = 0; i < iL; i++ ) {
				var iid = i || '';
				var labeltext = O.textlabel[ i ] || '';
				labelhtml += '<a id="infoTextLabel'+ iid +'" class="infolabel">'+ labeltext +'</a>';
				var valuehtml = O.textvalue[ i ] ? ' value="'+ O.textvalue[ i ].toString().replace( /"/g, '&quot;' ) +'"' : '';
				boxhtml += '<input type="text" class="infoinput" id="infoTextBox'+ iid +'"'+ valuehtml +' spellcheck="false">';
			}
			$( '.infotextlabel' ).html( labelhtml );
			$( '.infotextbox' ).html( boxhtml );
			var $infofocus = $( '#infoTextBox' );
			$( '#infoText' ).show();
			if ( 'textalign' in O ) $( '.infoinput' ).css( 'text-align', O.textalign );
			if ( 'textrequired' in O ) {
				if ( typeof O.textrequired !== 'object' ) O.textrequired = [ O.textrequired ];
				var blank = 0;
				O.textrequired.forEach( function( e ) {
					if ( !$( '.infotextbox input' ).eq( e ).val() ) blank++;
				} );
				if ( blank ) $( '#infoOk' ).addClass( 'disabled' );
				$( '.infoinput' ).on( 'input', function() {
					emptyinput = 0;
					O.textrequired.forEach( function( e ) {
						if ( !$( '.infotextbox input' ).eq( e ).val() ) emptyinput++;
					} );
					$( '#infoOk' ).toggleClass( 'disabled', emptyinput !== 0 );
				} );
			}
		}
		if ( 'passwordlabel' in O ) {
			$( '#infoPasswordLabel' ).html( O.passwordlabel );
			$( '#infoPassword, #infoPasswordLabel, #infoPasswordBox' ).show();
			var $infofocus = $( '#infoPasswordBox' );
		}
		if ( 'fileoklabel' in O ) {
			$( '#infoOk' )
				.html( O.fileoklabel )
				.hide();
			$( '#infoFileLabel' ).click( function() {
				$( '#infoFileBox' ).click();
			} );
			$( '#infoFile, #infoFileLabel' ).show();
			if ( 'filetype' in O ) $( '#infoFileBox' ).attr( 'accept', O.filetype );
			$( '#infoFileBox' ).change( function() {
				var file = this.files[ 0 ];
				if ( !file ) return
				
				var filename = file.name;
				var ext = filename.split( '.' ).pop();
				if ( 'filetype' in O && O.filetype.indexOf( ext ) === -1 ) {
					info( {
						  icon    : 'warning'
						, title   : O.title
						, message : 'File extension must be: <code>'+ O.filetype +'</code>'
						, ok      : function() {
							info( {
								  title       : O.title
								, message     : O.message
								, fileoklabel : O.fileoklabel
								, filetype    : O.filetype
								, ok          : function() {
									info( O );
								}
							} );
						}
					} );
					return;
				}
				
				$( '#infoOk' ).show();
				$( '#infoFileLabel' ).css( 'background', '#34495e' );
				$( '#infoFilename' ).html( '&ensp;'+ filename );
			} );
		}
		if ( 'radio' in O ) {
			if ( typeof O.radio !== 'object' ) {
				var html = O.radio;
			} else {
				var html = '';
				$.each( O.radio, function( key, val ) {
					// <label> for clickable label
					html += '<label><input type="radio" name="inforadio" value="'+ val.toString().replace( /"/g, '&quot;' ) +'">&ensp;'+ key +'</label><br>';
				} );
			}
			renderOption( $( '#infoRadio' ), html, O.checked || '' );
		}
		if ( 'select' in O ) {
			$( '#infoSelectLabel' ).html( O.selectlabel );
			if ( typeof O.select !== 'object' ) {
				var html = O.select;
			} else {
				var html = '';
				$.each( O.select, function( key, val ) {
					html += '<option value="'+ val.toString().replace( /"/g, '&quot;' ) +'">'+ key +'</option>';
				} );
			}
			renderOption( $( '#infoSelectBox' ), html, O.checked || '' );
			$( '#infoSelect, #infoSelectLabel, #infoSelectBox' ).show();
		}
		if ( 'checkbox' in O ) {
			if ( typeof O.checkbox !== 'object' ) {
				var html = O.checkbox;
			} else {
				var html = '';
				$.each( O.checkbox, function( key, val ) {
					html += '<label><input type="checkbox" value="'+ val.toString().replace( /"/g, '&quot;' ) +'">&ensp;'+ key +'</label><br>';
				} );
			}
			renderOption( $( '#infoCheckBox' ), html, 'checked' in O ? O.checked : '' );
		}
	}

	if ( O.preshow ) O.preshow();
	$( '#infoOverlay' )
		.show()
		.focus(); // enable e.which keypress (#infoOverlay needs tabindex="1")
	alignVertical();
	if ( $infofocus ) $infofocus.focus();
	if ( 'boxwidth' in O ) {
		var maxW = window.innerWidth * 0.98;
		var infoW = O.width ? O.width : parseInt( $( '#infoBox' ).css( 'width' ) );
		var calcW = maxW < infoW ? maxW : infoW;
		var labelW = 0;
		$( '.infolabel' ).each( function() {
			var thisW = $( this ).width();
			if ( thisW > labelW ) labelW = thisW;
		} );
		var boxW = O.boxwidth !== 'max' ? O.boxwidth : calcW - 40 - labelW;
		$( '.infoinput' ).css( 'width', boxW +'px' );
	}
	if ( 'buttonwidth' in O ) {
		var widest = 0;
		var w;
		$.each( $( '.infobtn' ), function() {
			w = $( this ).outerWidth();
			if ( w > widest ) widest = w;
		} );
		$( '.infobtn' ).css( 'min-width', widest +'px' );
	}
	/////////////////////////////////////////////////////////////////////////////
//	}, 0 );
}

function alignVertical() {
	var boxH = $( '#infoBox' ).height();
	var wH = window.innerHeight;
	var top = boxH < wH ? ( wH - boxH ) / 2 : 20;
	$( '#infoBox' ).css( 'margin', top +'px auto' );
}
function renderOption( $el, htm, chk ) {
	$el.html( htm ).show();
	if ( $el.prop( 'id' ) === 'infoCheckBox' ) { // by index
		if ( !chk ) return;
		
		var checked = typeof chk === 'object' ? chk : [ chk ];
		checked.forEach( function( val ) {
			$el.find( 'input' ).eq( val ).prop( 'checked', true );
		} );
	} else {                                    // by value
		var opt = $el.prop( 'id' ) === 'infoSelectBox' ? 'option' : 'input';
		if ( chk === '' ) { // undefined
			$el.find( opt ).eq( 0 ).prop( 'checked', true );
		} else {
			$el.find( opt +'[value="'+ chk +'"]' ).prop( 'checked', true );
		}
	}
}
function verifyPassword( title, pwd, fn ) {
	info( {
		  title         : title
		, message       : 'Please retype'
		, passwordlabel : 'Password'
		, ok            : function() {
			if ( $( '#infoPasswordBox' ).val() === pwd ) {
				fn();
				return;
			}
			
			info( {
				  title   : title
				, message : 'Passwords not matched. Please try again.'
				, ok      : function() {
					verifyPassword( title, pwd, fn )
				}
			} );
		}
	} );
}
function blankPassword( title, message, label, fn ) {
	info( {
		  title   : title
		, message : 'Blank password not allowed.'
		, ok      : function() {
			info( {
				  title         : title
				, message       : message
				, passwordlabel : 'Password'
				, ok            : function() {
					var pwd = $( '#infoPasswordBox' ).val();
					if ( !pwd ) {
						blankPassword( title, message, label, fn );
					} else {
						verifyPassword( title, pwd, fn )
					}
				}
			} );
		}
	} );
}
