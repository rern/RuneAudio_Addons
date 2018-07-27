/*
simple usage: info( 'message' );
normal usage: info( {
	icon          : 'NAME'         // question-circle / NAME (FontAwesome name for top icon)
	title         : 'TITLE'        // Information / TITLE    (top title)
	nox           : 1..            // 0 / 1                  (no top 'X' close button)
	boxwidth      : N              // 400 / N                (pixel)
	message       : 'MESSAGE'      // (blank) / MESSAGE      (message under title)
	textlabel     : 'LABEL'        // (blank) / LABEL        (text input label)
	passwordlabel : 'LABEL'        // (blank) / LABEL        (password input label)
	required      : 1              // 0 / 1                  (password required)
	radiohtml     : 'HTML'         // required HTML
	checked       : N              // (none) / N             (pre-select input)
	checkboxhtml  : 'HTML'         // required HTML
	checked       : [ N, N1, ... ] // (none) / [ array ]     (pre-select multiple)
	selecthtml    : 'HTML'         // required HTML
	oklabel       : 'LABEL'        // OK / LABEL             (ok button label)
	okcolor       : 'COLOR'        // #0095d8 / COLOR        (ok button color)
	ok            : 'FUNCTION'     // (hide) / FUNCTION      (ok click function)
	cancellabel   : 'LABEL'        // Cancel / LABEL         (cancel button label)
	cancelcolor   : 'COLOR'        // #34495e / COLOR        (cancel button color)
	cancel        : 'FUNCTION'     // (hide) / FUNCTION      (cancel click function)
	buttonlabel   : 'LABEL'        // required LABEL         (button button label)
	buttoncolor   : 'COLOR'        // #34495e / COLOR        (button button color)
	button        : 'FUNCTION'     // required FUNCTION      (button click function)
} );
*/
$( 'body' ).prepend( '\
<div id="infoOverlay" tabindex="1">\
	<div id="infoBox">\
		<div id="infoTopBg">\
			<div id="infoTop">\
				<a id="infoIcon"></a>&emsp;<a id="infoTitle"></a>\
			</div>\
			<div id="infoX"><i class="fa fa-times fa-2x"></i></div>\
			<div style="clear: both"></div>\
		</div>\
		<div id="infoContent">\
			<p id="infoMessage" class="infocontent"></p>\
			<div id="infoText" class="infocontent">\
				<a id="infoTextLabel" class="infolabel"></a>&emsp;<input type="text" class="inputbox" id="infoTextbox" spellcheck="false">\
			</div>\
			<div id="infoPassword" class="infocontent">\
				<a id="infoPasswordLabel" class="infolabel"></a>&emsp;<input type="password" class="inputbox" id="infoPasswordbox">\
			</div>\
			<div id="infoRadio" class="infocontent infohtml"></div>\
			<div id="infoCheckbox" class="infocontent infohtml"></div>\
			<div id="infoSelect" class="infocontent">\
				<a id="infoSelectLabel" class="infolabel"></a><select class="infohtml" id="infoSelectbox"></select>\
			</div>\
		</div>\
		<div id="infoButtons">\
			<a id="infoCancel" class="infobtn infobtn-default"></a>\
			<a id="infoBtn" class="infobtn infobtn-default"></a>\
			<a id="infoOk" class="infobtn infobtn-primary"></a>\
		</div>\
	</div>\
</div>\
' );

$( '#infoOverlay' ).keypress( function( e ) {
	if ( $( '#infoOverlay' ).is( ':visible' ) && e.which == 13 ) $( '#infoOk' ).click();
//} ).click( function( e ) {
//	if ( e.target.id === this.id && $( this ).is( ':visible' ) ) $( '#infoX' ).click();
} );
// close: reset to default
$( '#infoX' ).click( function() {
	inforeset();
} );

function inforeset() {
	$( '#infoOverlay' ).hide();
	$( '.infolabel, .infohtml' ).empty();
	$( '.inputbox' ).css( 'width', '' ).val( '' );
	$( '#infoButtons a' ).css( 'background', '' ).off( 'click' );
}

function info( O ) {
	// common
	inforeset();
	if ( !O.icon ) {
		var iconhtml = '<i class="fa fa-question-circle fa-2x">';
	} else {
		if ( O.icon.charAt( 0 ) !== '<' ) {
			var iconhtml = '<i class="fa fa-'+ O.icon +' fa-2x">';
		} else {
			var iconhtml = O.icon;
		}
	}
	$( '#infoIcon' ).html( iconhtml );
	$( '#infoTitle' ).html( O.title ? O.title : 'Information' );
	$( '.infocontent, #infoButtons a' ).hide();
	// simple use as info('message')
	if ( typeof O != 'object' ) {
		$( '#infoOk' ).on( 'click', function () {
			$( '#infoOverlay' ).hide();
		});
		$( '#infoMessage' ).html( O ).show();
		$( '#infoOverlay' ).show();
		return;
	}
	
	if ( O.nox ) {
		$( '#infoX' ).hide();
	} else {
		$( '#infoX' ).click( function() {
			inforeset();
		} );
	}
	if ( O.message )$( '#infoMessage' ).html( O.message ).show();
	if ( O.boxwidth ) $( '.inputbox' ).width( O.boxwidth );
	if ( O.textlabel ) {
		$( '#infoTextLabel' ).html( O.textlabel );
		$( '#infoTextbox' ).val( O.textvalue );
		$( '#infoText' ).show();
		if ( O.textvalue ) $( '#infoTextbox' ).select();
		var $infofocus = $( '#infoTextbox' );
	} else if ( O.passwordlabel ) {
		$( '#infoPasswordLabel' ).html( O.passwordlabel );
		$( '#infoPassword' ).show().focus();
		var $infofocus = $( '#infoPasswordbox' );
	} else if ( O.radiohtml ) {
		var checked = [ O.checked ];
		setboxwidth( $( '#infoRadio' ), O.radiohtml, checked );
	} else if ( O.checkboxhtml ) {
		var checked = typeof O.checked === 'array' ? O.checked : [ O.checked ];
		setboxwidth( $( '#infoCheckbox' ), O.checkboxhtml, checked );
	} else if ( O.selecthtml ) {
		$( '#infoSelectLabel' ).html( O.selectlabel );
		$( '#infoSelectbox' ).html( O.selecthtml );
		$( '#infoSelect' ).show();
	}
	if ( O.cancel || O.cancellabel ) {
		$( '#infoCancel' )
			.html( O.cancellabel ? O.cancellabel : 'Cancel' )
			.css( 'background', O.cancelcolor ? O.cancelcolor : '' )
			.show()
			.on( 'click', function() {
				O.cancel();
			} );
	}
	if ( O.button ) {
		$( '#infoBtn' )
			.html( O.buttonlabel )
			.css( 'background', O.buttoncolor ? O.buttoncolor : '' )
			.show()
			.on( 'click', function() {
				$('#infoOverlay').hide();
				O.button();
				O.button = '';
			} );
	}
	$( '#infoOk' )
		.html( O.oklabel ? O.oklabel : 'OK' )
		.css( 'background', O.okcolor ? O.okcolor : '' )
		.show()
		.on( 'click', function() {
			$('#infoOverlay').hide();
			if ( O.ok && typeof O.ok === 'function' ) {
				O.ok();
				O.ok = ''; // fix: multiple runs
			}
		} );
	
	$( '#infoOverlay' )
		.show()
		.focus(); // enable e.which keypress (#infoOverlay needs tabindex="1")
	$( '#infoBox' ).css( 'top', ( window.innerHeight - $( '#infoBox' ).height() ) / 2 +'px' );
	if ( $infofocus ) $infofocus.select();
}
window.addEventListener( 'orientationchange', function() {
	$( '#infoBox' ).css( 'top', ( window.innerWidth - $( '#infoBox' ).height() ) / 2 +'px' );
} );

function setboxwidth( $box, html, checked ) {
	var windowW = window.innerWidth;
	var windowH = window.innerHeight;
	var contentW = windowW >= 400 ? $( '#infoBox' ).width() : windowW;
	var maxW = 0;
	var spanW = 0;
	$( '#infoBox' ).css('left', '-100%' );           // move out of screen
	$box.html( html ).show();                        // show to get width
	setTimeout( function() {                         // wait for radiohtml ready
		$box.find( 'label' ).each( function( i ) {   // get max width
			spanW = $( this ).width();
			maxW = ( spanW > maxW ) ? spanW : maxW;
			if ( checked && checked.indexOf( i ) !== -1 ) $( this ).find( 'input' ).prop( 'checked', true );
		} );
		var pad = ( contentW - 20 - maxW ) / 2;      // 15 = button width
		$box.css('padding-left', pad +'px');         // set padding-left
		$( '#infoBox' ).css( { 'left': '50%', 'top': ( windowH - $( '#infoBox' ).height() ) / 2 +'px' } ); // move back
	}, 100 );
}
function verifypassword( title, pwd, fn ) {
	$( '#infoX' ).click();
	info( {
		  title         : title
		, message       : 'Please retype'
		, passwordlabel : 'Password'
		, ok            : function() {
			if ( $( '#infoPasswordbox' ).val() === pwd ) {
				fn();
				return;
			}
			info( {
				  title   : title
				, message : 'Passwords not matched. Please try again.'
				, ok      : function() {
					verifypassword( title, pwd, fn )
				}
			} );
		}
	} );
}
function blankpassword( title, message, label, fn ) {
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
					var pwd = $( '#infoPasswordbox' ).val();
					if ( !pwd ) {
						blankpassword( title, message, label, fn );
					} else {
						verifypassword( title, pwd, fn )
					}
				}
			} );
		}
	} );
}
