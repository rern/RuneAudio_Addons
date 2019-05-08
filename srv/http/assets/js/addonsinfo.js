/*
simple usage: 
info( 'message' );

normal usage:
info( {                                     // default / custom
	width         : N                       // 400 / N             (info width)
	icon          : 'NAME'                  // question / NAME     (FontAwesome name for top icon)
	title         : 'TITLE'                 // Information / TITLE (top title)
	nox           : 1..                     // 0 / 1               (no top 'X' close button)
	nobutton      : 1                       // 0 / 1               (no button)
	boxwidth      : N                       // 200 / N / 'max'     (input text/password width)
	autoclose     : N                       // ms                  (auto close in ms)
	
	message       : 'MESSAGE'               // (blank) / MESSAGE   (message under title)
	msgalign      : 'CSS'                   // left / CSS          (message under title)
	
	textlabel     : [ 'LABEL', ... ]        // (blank) / LABEL     (label array input label)
	textvalue     : [ 'VALUE', ... ]        // (blank) / VALUE     (pre-filled array input value)
	textrequired  : [ N, ... ]              // (none) / N          (required fields disable ok button if blank)
	textalign     : 'CSS'                   // left / CSS          (input text alignment)
	
	passwordlabel : 'LABEL'                 // (blank) / LABEL     (password input label)
	pwdrequired   : 1                       // 0 / 1               (password required)
	
	fileoklabel   : 'LABEL'                 // (blank) / LABEL     (upload button label)
	filetype      : 'TYPE'      .           // (none) / .TYPE      (filter and verify filetype)
	
	radio         : { LABEL: 'VALUE', ... } // ............        ( var value = $( '#infoRadio input[ type=radio ]:checked' ).val(); )
	checked       : N                       // (none) / N          (pre-select input index)
	
	select        : { LABEL: 'VALUE', ... } //                     ( var value = $( '#infoSelectBox').val(); )
	selectlabel   : 'LABEL'                 // (blank) / LABEL     (select input label)
	checked       : N                       // (none) / N          (pre-select option index)
	
	checkbox      : { LABEL: 'VALUE', ... }// ............         ( $( '#infoCheckBox input[ type=checkbox ]:checked' ).each( function() {
	                                                                   var value = this.value;
	                                                                 } ); )
	checked       : [ N, ... ]              // (none) / N          (pre-select array input indexes)
	
	oklabel       : 'LABEL'                 // OK / LABEL          (ok button label)
	okcolor       : 'COLOR'                 // #0095d8 / COLOR     (ok button color)
	ok            : 'FUNCT'                 // (hide) / FUNCTION   (ok click function)
	cancellabel   : 'LABEL'                 // Cancel / LABEL      (cancel button label)
	cancelcolor   : 'COLOR'                 // #34495e / COLOR     (cancel button color)
	cancel        : 'FUNCT'                 // (hide) / FUNCTION   (cancel click function)
	
	buttonlabel   : [ 'LABEL', ... ]        // LABEL               (label array)
	button        : [ 'FUNCT', ... ]        // FUNCTION            (function array)
	buttoncolor   : [ 'COLOR', ... ]        // #34495e / COLOR     (color array)
	buttonwidth   : 0                       // 0 / 1               (equal buttons width)
} );
Note:
- No default - must be specified.
- Single value/function - no need to be array
*/
function heredoc( fn ) {
	return fn.toString().match( /\/\*\s*([\s\S]*?)\s*\*\//m )[ 1 ];
};
var html = heredoc( function() { /*
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

$( 'body' ).prepend( html );

emptyinput = 0; // for 'textrequired'
$( '#infoOverlay' ).keypress( function( e ) {
	if ( $( '#infoOverlay' ).is( ':visible' ) && e.which == 13 && !emptyinput ) $( '#infoOk' ).click();
} );
// close: reset to default
$( '#infoX' ).click( function() {
	$( '#infoCancel' ).click();
	infoReset();
} );

function infoReset() {
	$( '#infoOverlay, .infocontent, .infolabel, .infoinput, .infohtml, .filebtn, .infobtn' ).hide();
	$( '#infoMessage, .infotextlabel, .infotextbox, .infohtml, #infoFilename' ).empty();
	$( '.infoinput' ).val( '' ).css( 'text-align', '' );
	$( '#infoBox, .infolabel, .infoinput' ).css( 'width', '' );
	$( '.filebtn, .infobtn' ).css( 'background', '' ).off( 'click' );
	$( '#infoFileBox' ).removeAttr( 'accept' );
	$( '#infoOk' ).removeClass( 'disabled' );
	$( '.extrabtn' ).remove();
	$( '#loader' ).addClass( 'hide' ); // for 'X' click
}
infoReset();

function info( O ) {
	setTimeout( function() { // force wait for infoReset()
	///////////////////////////////////////////////////////////////////
	// title
	$( '#infoBox' ).css( 'width', ( O.width || 400 ) +'px' );
	if ( 'icon' in O ) {
		if ( O.icon.charAt( 0 ) !== '<' ) {
			var iconhtml = '<i class="fa fa-'+ O.icon +'">';
		} else {
			var iconhtml = O.icon;
		}
	} else {
		var iconhtml = '<i class="fa fa-question-circle">';
	}
	$( '#infoIcon' ).html( iconhtml );
	$( '#infoTitle' ).html( O.title || 'Information' );
	if ( 'nox' in O ) $( '#infoX' ).hide();
	if ( 'autoclose' in O ) {
		setTimeout( function() {
			$( '#infoX' ).click();
		}, O.autoclose );
	}
	// simple use as info( 'message' )
	if ( typeof O !== 'object' ) {
		$( '#infoMessage' ).html( O );
		$( '#infoIcon' ).html( '<i class="fa fa-info-circle">' );
		$( '#infoOverlay, #infoMessage, #infoOk' ).show();
		alignVertical();
		$( '#infoOk' ).html( 'OK' ).click( function() {
			infoReset();
		});
		return;
	}
	
	// message
	if ( 'message' in O ) {
		$( '#infoMessage' )
			.html( O.message )
			.css( 'text-align', O.msgalign || '' )
			.show();
	}
	// buttons
	if ( 'nobutton' in O === false ) {
		$( '#infoOk' )
			.html( O.oklabel ? O.oklabel : 'OK' )
			.css( 'background', O.okcolor || '' )
			.show();
			if ( typeof O.ok === 'function' ) $( '#infoOk' ).click( O.ok );
		if ( 'cancel' in O ) {
			$( '#infoCancel' )
				.html( O.cancellabel || 'Cancel' )
				.css( 'background', O.cancelcolor || '' );
			if ( 'cancelbtn' in O ) $( '#infoCancel' ).show();
			if ( typeof O.cancel === 'function' ) $( '#infoCancel' ).click( O.cancel );
		}
		if ( 'button' in O ) {
			if ( !O.button.length ) O.button = [ O.button ];
			if ( typeof O.buttonlabel === 'string' ) O.buttonlabel = [ O.buttonlabel ];
			O.buttoncolor = O.buttoncolor || '';
			if ( typeof O.buttoncolor === 'string' ) O.buttoncolor = [ O.buttoncolor ];
			var buttonhtml = '';
			var iL = O.button.length;
			for ( i = 0; i < iL; i++ ) {
				var iid = i || '';
				$( '#infoOk' ).before(  '<a id="infoButton'+ iid +'" class="infobtn extrabtn infobtn-default">'+ O.buttonlabel[ i ] +'</a>' );
				$( '#infoButton'+ iid )
									.css( 'background', O.buttoncolor[ i ] || '' )
									.click( O.button[ i ] );
			}
		}
		$( '.infobtn' ).click( infoReset );
	}
	// inputs
	if ( 'textlabel' in O || 'textvalue' in O ) {
		O.textlabel = O.textlabel || '';
		O.textvalue = O.textvalue || '';
		if ( typeof O.textlabel === 'string' ) O.textlabel = [ O.textlabel ];
		if ( typeof O.textvalue === 'string' ) O.textvalue = [ O.textvalue ];
		var labelhtml = '';
		var boxhtml = '';
		var iL = O.textlabel.length > 1 ? O.textlabel.length : O.textvalue.length;
		for ( i = 0; i < iL; i++ ) {
			var iid = i || '';
			labelhtml += i ? '<br>' : '';
			var labeltext = O.textlabel[ i ] || '';
			labelhtml += '<a id="infoTextLabel'+ iid +'" class="infolabel">'+ labeltext +'</a>';
			boxhtml += i ? '<br>' : '';
			var valuehtml = O.textvalue[ i ] ? 'value="'+ O.textvalue[ i ].toString().replace( /"/g, '&quot;' ) : '';
			boxhtml += '<input type="text" class="infoinput" id="infoTextBox'+ iid +'"'+ valuehtml +'" spellcheck="false">';
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
			$( '.infoinput' ).on( 'keyup', function() {
				emptyinput = 0;
				O.textrequired.forEach( function( e ) {
					if ( !$( '.infotextbox input' ).eq( e ).val() ) emptyinput++;
				} );
				$( '#infoOk' ).toggleClass( 'disabled', emptyinput !== 0 );
			} );
		}
	} else if ( 'passwordlabel' in O ) {
		$( '#infoPasswordLabel' ).html( O.passwordlabel );
		$( '#infoPassword, #infoPasswordLabel, #infoPasswordBox' ).show();
		var $infofocus = $( '#infoPasswordBox' );
	} else if ( 'fileoklabel' in O ) {
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
	} else if ( 'radio' in O ) {
		if ( typeof O.radio === 'string' ) {
			var html = O.radio;
		} else {
			var html = '';
			$.each( O.radio, function( key, val ) {
				// <label> for clickable label
				html += '<label><input type="radio" name="inforadio" value="'+ val.toString().replace( /"/g, '&quot;' ) +'">&ensp;'+ key +'</label><br>';
			} );
		}
		renderOption( $( '#infoRadio' ), html, O.checked );
	} else if ( 'select' in O ) {
		$( '#infoSelectLabel' ).html( O.selectlabel );
		if ( typeof O.select === 'string' ) {
			var html = O.select;
		} else {
			var html = '';
			$.each( O.select, function( key, val ) {
				html += '<option value="'+ val.toString().replace( /"/g, '&quot;' ) +'">'+ key +'</option>';
			} );
		}
		renderOption( $( '#infoSelectBox' ), html, O.checked );
		$( '#infoSelect, #infoSelectLabel, #infoSelectBox' ).show();
	} else if ( 'checkbox' in O ) {
		if ( typeof O.checkbox === 'string' ) {
			var html = O.checkbox;
		} else {
			var html = '';
			$.each( O.checkbox, function( key, val ) {
				html += '<label><input type="checkbox" value="'+ val.toString().replace( /"/g, '&quot;' ) +'">&ensp;'+ key +'</label><br>';
			} );
		}
		renderOption( $( '#infoCheckBox' ), html, O.checked );
	}
	
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
	}, 0 );
}

function alignVertical() {
	var boxH = $( '#infoBox' ).height();
	var wH = window.innerHeight;
	var top = boxH < wH ? ( wH - boxH ) / 2 : 20;
	$( '#infoBox' ).css( 'margin', top +'px auto' );
}
function renderOption( $el, htm, chk ) {
	$el.html( htm ).show();
	if ( chk == 'undefined' ) return;
	
	var $opt = $el.prop( 'id' ) === 'infoSelectBox' ? $el.find( 'option' ) : $el.find( 'input' );
	var checked = typeof chk === 'object' ? chk : [ chk ];
	checked.forEach( function( val ) {
		$opt.eq( val ).prop( 'checked', true );
	} );
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
