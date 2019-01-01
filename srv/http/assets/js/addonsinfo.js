/*
simple usage: 
info( 'message' );

normal usage:
info( {
	width         : N              // 400 / N                (info width)
	icon          : 'NAME'         // question-circle / NAME (FontAwesome name for top icon)
	title         : 'TITLE'        // Information / TITLE    (top title)
	nox           : 1..            // 0 / 1                  (no top 'X' close button)
	boxwidth      : N              // 200 / N / 'max'        (input text/password width)
	message       : 'MESSAGE'      // (blank) / MESSAGE      (message under title)
	textlabel     : 'LABEL'        // (blank) / LABEL        (text input label)
	textvalue     : 'VALUE'        // (blank) / VALUE        (text input value)
	textalign     : 'CSS'          // left / CSS             (text input align)
	passwordlabel : 'LABEL'        // (blank) / LABEL        (password input label)
	filelabel     : 'LABEL'        // (blank) / LABEL        (upload button label)
	filetype      : '.TYPE'        // (none) / .TYPE         (filter and verify filetype)
	required      : 1              // 0 / 1                  (password required)
	radiohtml     : 'HTML'         // required HTML
	checked       : N              // (none) / N             (pre-select input)
	selectlabel   : 'LABEL'        // (blank) / LABEL        (select input label)
	selecthtml    : 'HTML'         // required HTML
	checkboxhtml  : 'HTML'         // required HTML
	checked       : [ N, N1, ... ] // (none) / [ array ]     (pre-select multiple)
	oklabel       : 'LABEL'        // OK / LABEL             (ok button label)
	okcolor       : 'COLOR'        // #0095d8 / COLOR        (ok button color)
	ok            : 'FUNCTION'     // (hide) / FUNCTION      (ok click function)
	cancellabel   : 'LABEL'        // Cancel / LABEL         (cancel button label)
	cancelcolor   : 'COLOR'        // #34495e / COLOR        (cancel button color)
	cancel        : 'FUNCTION'     // (hide) / FUNCTION      (cancel click function)
	buttonlabel   : 'LABEL'        // required LABEL         (button button label)
	buttoncolor   : 'COLOR'        // #34495e / COLOR        (button button color)
	button        : 'FUNCTION'     // required FUNCTION      (button click function)
	nobutton      : 1              // 0 / 1                  (no button)
	autoclose     : N              // ms                     (auto close in ms)
} );
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
				<div class="infotextlabel">
					<a id="infoTextLabel" class="infolabel"></a><br>
					<a id="infoTextLabel2" class="infolabel"></a>
				</div>
				<div class="infotextbox">
					<input type="text" class="infoinput" id="infoTextBox" spellcheck="false"><br>
					<input type="text" class="infoinput" id="infoTextBox2" spellcheck="false">
				</div>
				<div style="clear: both"></div>
			</div>
			<div id="infoPassword" class="infocontent">
				<a id="infoPasswordLabel" class="infolabel"></a><input type="password" class="infoinput" id="infoPasswordBox">
			</div>
			<div id="infoFile" class="infocontent">
				<a id="infoFileLabel" class="infobtn infobtn-primary">Browse</a>
				<span id="infoFilename"></span>
				<input type="file" class="infoinput" id="infoFileBox">
			</div>
			<div id="infoRadio" class="infocontent infohtml"></div>
			<div id="infoCheckBox" class="infocontent infohtml"></div>
			<div id="infoSelect" class="infocontent">
				<a id="infoSelectLabel" class="infolabel"></a><select class="infohtml" id="infoSelectBox"></select>
			</div>
		</div>
		<div id="infoButtons">
			<a id="infoCancel" class="infobtn infobtn-default"></a>
			<a id="infoButton" class="infobtn infobtn-default"></a>
			<a id="infoOk" class="infobtn infobtn-primary"></a>
		</div>
	</div>
</div>
*/ } );

$( 'body' ).prepend( html );

$( '#infoOverlay' ).keypress( function( e ) {
	if ( $( '#infoOverlay' ).is( ':visible' ) && e.which == 13 ) $( '#infoOk' ).click();
} );
// close: reset to default
$( '#infoX' ).click( function() {
	typeof O === 'object' ? $( '#infoCancel' ).click() : infoReset();
} );

function infoReset() {
	$( '#infoOverlay, .infocontent, .infolabel, .infoinput, .infohtml, .infobtn' ).hide();
	$( '.infolabel, .infohtml, #infoFilename' ).empty();
	$( '.infoinput' ).val( '' ).css( 'text-align', '' );
	$( '#infoBox, .infolabel, .infoinput' ).css( 'width', '' );
	$( '#infoFileLabel, #infoButtons a' ).css( 'background', '' );
	$( '#infoFileBox' ).removeAttr( 'accept' );
	$( '#infoFileLabel, #infoButtons a' ).off( 'click' );
	$( '#loader' ).addClass( 'hide' ); // for 'X' click
}

function info( O ) {
	// title
	infoReset();
	if ( O.width ) $( '#infoBox' ).css( 'width', O.width +'px' );
	if ( !O.icon ) {
		var iconhtml = '<i class="fa fa-question-circle">';
	} else {
		if ( O.icon.charAt( 0 ) !== '<' ) {
			var iconhtml = '<i class="fa fa-'+ O.icon +'">';
		} else {
			var iconhtml = O.icon;
		}
	}
	$( '#infoIcon' ).html( iconhtml );
	$( '#infoTitle' ).html( O.title ? O.title : 'Information' );
	if ( O.nox ) $( '#infoX' ).hide();
	if ( O.autoclose ) {
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
	if ( O.message ) $( '#infoMessage' ).html( O.message ).show();
	
	// buttons
	if ( !O.nobutton ) {
		$( '#infoOk' )
			.html( O.oklabel ? O.oklabel : 'OK' )
			.css( 'background', O.okcolor ? O.okcolor : '' )
			.show()
			.click( function() {
				$( '#infoOverlay' ).hide();
				if ( typeof O.ok === 'function' ) {
					O.ok();
					O.ok = ''; // reset
				} else {
					infoReset();
				}
			} );
		if ( O.cancel || O.cancellabel ) {
			$( '#infoCancel' )
				.html( O.cancellabel ? O.cancellabel : 'Cancel' )
				.css( 'background', O.cancelcolor ? O.cancelcolor : '' )
				.show()
				.click( function() {
					$( '#infoOverlay' ).hide();
					if ( typeof O.cancel === 'function' ) {
						O.cancel();
						O.cancel = ''; // reset
					} 
					infoReset();
				} );
		} else {
			$( '#infoCancel' ).click( infoReset );
		}
		if ( O.button ) {
			$( '#infoButton' )
				.html( O.buttonlabel )
				.css( 'background', O.buttoncolor ? O.buttoncolor : '' )
				.show()
				.click( function() {
					$( '#infoOverlay' ).hide();
					O.button();
					O.button = '';
				} );
		}
	}
		// inputs
	if ( O.textlabel || O.textvalue ) {
		$( '#infoTextLabel' ).html( O.textlabel );
		$( '#infoTextBox' ).val( O.textvalue );
		$( '#infoText, #infoTextLabel, #infoTextBox' ).show();
		var $infofocus =  $( '#infoTextBox' );
		if ( O.textlabel2 ) {
			$( '#infoTextLabel2' ).html( O.textlabel2 );
			$( '#infoTextBox2' ).val( O.textvalue2 );
			$( '#infoTextLabel2, #infoTextBox2' ).show();
		}
		if ( O.textalign ) $( '.infoinput' ).css( 'text-align', O.textalign );
	} else if ( O.passwordlabel ) {
		$( '#infoPasswordLabel' ).html( O.passwordlabel );
		$( '#infoPassword, #infoPasswordLabel, #infoPasswordBox' ).show();
		var $infofocus = $( '#infoPasswordBox' );
	} else if ( O.filelabel ) {
		if ( O.filetype ) $( '#infoFileBox' ).attr( 'accept', O.filetype );
		$( '#infoOk' )
			.html( O.filelabel )
			.css( 'background', '#34495e' )
			.off( 'click' );
		$( '#infoFileLabel' ).click( function() {
			$( '#infoFileBox' ).click();
		} );
		$( '#infoFileBox' ).on( 'change', function() {
			var filename = this.files[ 0 ].name;
			if ( O.filetype && filename.indexOf( O.filetype ) === -1 ) {
				O.ok = '';
				info( {
					  icon    : 'warning'
					, title   : O.title
					, message : 'File extension must be: <code>'+ O.filetype +'</code>'
					, ok      : function() {
						info( {
							  title     : O.title
							, message   : O.message
							, filelabel : O.filelabel
							, filetype  : O.filetype
							, ok        : O.ok
						} );
					}
				} );
				return;
			}
			$( '#infoFilename' ).html( '&ensp;'+ filename );
			$( '#infoFileLabel' ).css( 'background', '#34495e' );
			$( '#infoOk' )
				.css( 'background', '' )
				.click( function() {
					O.ok();
					O.ok = '';
				} );
		} );
		$( '#infoFile, #infoFileLabel' ).show();
	} else if ( O.radiohtml ) {
		radioCheckbox( $( '#infoRadio' ), O.radiohtml, O.checked );
	} else if ( O.selecthtml ) {
		$( '#infoSelectLabel' ).html( O.selectlabel );
		$( '#infoSelectBox' ).html( O.selecthtml );
		$( '#infoSelect, #infoSelectLabel, #infoSelectBox' ).show();
	} else if ( O.checkboxhtml ) {
		radioCheckbox( $( '#infoCheckBox' ), O.checkboxhtml, O.checked );
	}
	
	$( '#infoOverlay' )
		.show()
		.focus(); // enable e.which keypress (#infoOverlay needs tabindex="1")
	alignVertical();
	if ( $infofocus ) $infofocus.focus();
	if ( O.boxwidth ) {
		var maxW = window.innerWidth * 0.98;
		var infoW = O.width ? O.width : parseInt( $( '#infoBox' ).css( 'width' ) );
		var calcW = maxW < infoW ? maxW : infoW;
		var boxW = O.boxwidth !== 'max' ? O.boxwidth : calcW - 40 - $( '#infoTextLabel' ).width();
		$( '.infoinput' ).css( 'width', boxW +'px' );
	}
}

function alignVertical() {
	var boxH = $( '#infoBox' ).height();
	var wH = window.innerHeight;
	var translate = boxH < wH ? boxH : wH / 2
	$( '#infoBox' ).css( 'transform', 'translateY( -'+ translate +'px )' )
}
function radioCheckbox( el, htm, chk ) {
	el.html( htm ).show();
	if ( !chk ) return;
	
	var checked = typeof chk === 'array' ? chk : [ chk ];
	el.find( 'label' ).each( function( i ) {
		if ( checked.indexOf( i ) !== -1 ) el.find( 'input' ).prop( 'checked', true );
	} );
}
function verifyPassword( title, pwd, fn ) {
	$( '#infoX' ).click();
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
			$( '#infoX' ).click();
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
