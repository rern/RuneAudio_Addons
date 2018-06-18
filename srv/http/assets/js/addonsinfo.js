function info( O ) {
	$( 'body' ).prepend( 
		'<div id="infoOverlay">'
			+'<div id="infoBox">'
				+'<div id="infoTopBg">'
					+'<div id="infoTop">'
						+'<a id="infoIcon"></a><a id="infoTitle"></a>'
					+'</div>'
					+'<div id="infoX"><i class="fa fa-times fa-2x"></i></div>'
					+'<div style="clear: both"></div>'
				+'</div>'
				+'<div id="infoContent"></div>'
				+'<div id="infoButtons">'
					+'<a id="infoOk" class="btn btn-primary"></a>'
				+'</div>'
			+'</div>'
		+'</div>'
	);

// title
	$( '#infoIcon' ).html( O.icon ? '<i class="fa fa-'+ O.icon +' fa-2x">' : '<i class="fa fa-question-circle fa-2x">' );
	$( '#infoTitle' ).html( O.title ? O.title : 'Information' );
	if ( O.nox ) $( '#infoX' ).hide();
	if ( O.boxwidth ) $( '.infoBox' ).width( O.boxwidth );
	
	$( '#infoX' ).click( function() {
		if ( O.cancel ) $( '#infoCancel' ).click();
		$( '#infoOverlay' ).remove();
	} );
	$( '#infoOverlay' ).click( function( e ) {
		if ( e.target === this ) $( '#infoX' ).click();
	} );
	
// simple use: info( 'message' );
	if ( typeof O !== 'object' ) {
		$( '#infoIcon' ).html( '<i class="fa fa-info-circle fa-2x">' );
		$( '#infoContent' ).html( '<p id="infoMessage" class="info">'+ O +'</p>' );
		$( '#infoOk' ).on( 'click', function () {
			$( '#infoOverlay' ).remove();
		});
	} else {
// normal use: info( { x: 'x', y: 'y' } );
// content
		var message = O.message ? '<p id="infoMessage" class="info">'+ O.message +'</p>' : '';
		if ( O.message ) {
			$( '#infoContent' ).html( message );
		}
		
		if ( O.textlabel ) {
			$( '#infoContent' ).html(
				message
				+'<div id="infoText" class="info">'
				+'<a id="infoTextLabel">'+ O.textlabel +'</a> <input type="text" class="infoBox" id="infoTextbox" spellcheck="false" value="'+ O.textvalue +'">'
				+'</div>'
			);
			var $infofocus = $( '#infoTextbox' );
		} else if ( O.passwordlabel ) {
			$( '#infoContent' ).html(
				message
				+'<div id="infoPassword" class="info">'
					+'<a id="infoPasswordLabel">'+ O.passwordlabel +'</a> <input type="password" class="infoBox" id="infoPasswordbox">'
				+'</div>'
			);
			var $infofocus = $( '#infoPasswordbox' );
		} else if ( O.radiohtml ) {
			$( '#infoContent' ).html(
				message
				+'<div id="infoRadio" class="info">'+ O.radiohtml() +'</div>'
			).promise().done( function() {
				setboxwidth( $( '#infoRadio' ) );
			} );
		} else if ( O.checkboxhtml ) {
			$( '#infoContent' ).html(
				message
				+'<div id="infoCheckbox" class="info">'+ O.checkboxhtml() +'</div>'
			).promise().done( function() {
				setboxwidth( $( '#infoCheckbox' ) );
			} );
		} else if ( O.selecthtml ) {
			$( '#infoContent' ).html(
				message
				+'<div id="infoSelect" class="info">'+
					+'<a id="infoSelectLabel">'+ O.selectlabel +'</a>'
					+'<select class="infoBox" id="infoSelectbox">'+ O.selecthtml() +'</select>'
				+'</div>'
			);
		}
// buttons
		$( '#infoOk' ).html( O.oklabel ? O.oklabel : 'Ok' );
		if ( O.okcolor ) $( '#infoOk' ).css( 'background', O.okcolor );
		if ( O.cancel ) {
			$( '#infoButtons' ).prepend( '<a id="infoCancel" class="btn btn-default">Cancel</a>' );
			$( '#infoCancel' ).on( 'click', function() {
				$( '#infoOverlay' ).hide();
				if ( typeof O.cancel === 'function' ) O.cancel();
			});
			if ( O.cancellabel ) $( '#infoCancel' ).html( O.cancellabel );
		}
		if ( O.button ) {
			$( '#infoButtons' ).append( '<a id="infoBtn" class="btn btn-default">'+ O.buttonlabel +'</a>' );
			$( '#infoBtn' ).click( function() {
				O.button();
			} );
		}
	}
	
	$( '#infoOverlay' ).show();
	$( '#infoBox' ).css( 'top', ( window.innerHeight - $( '#infoBox' ).height() ) / 2 +'px' );
	if ( $infofocus ) $infofocus.focus();
	
	$( '#infoOk' ).off( 'click' ).on( 'click', function() {
		$( '#infoOverlay' ).hide();
		if ( O.ok && typeof O.ok === 'function' ) O.ok();
	} );
	$( 'body' ).keypress( function( e ) {
		if ( $( '#infoOverlay' ).is( ':visible' ) && e.which == 13 ) $( '#infoOk' ).click();
	} );
}
window.addEventListener( 'orientationchange', function() {
	$( '#infoBox' ).css( 'top', ( window.innerWidth - $( '#infoBox' ).height() ) / 2 +'px' );
} );

function setboxwidth( $box ) {
	var windowW = window.innerWidth;
	var windowH = window.innerHeight;
	var contentW = windowW >= 400 ? $( '#infoBox' ).width() : windowW;
	var maxW = 0;
	var spanW = 0;
	$( '#infoBox' ).css('left', '-100%' );       // move out of screen
	setTimeout( function() {                     // delay for new html ready
		$box.find( 'label' ).each( function() {  // get max width
				spanW = $( this ).width();
				maxW = ( spanW > maxW ) ? spanW : maxW;
		} );
		var pad = ( contentW - 20 - maxW ) / 2; // 15 = button width
		$box.css('padding-left', pad +'px');    // set padding-left
		$( '#infoBox' ).css( { 'left': '50%', 'top': ( windowH - $( '#infoBox' ).height() ) / 2 +'px' } );    // move back
	}, 100 );
}
// for loop multiple verifications
function verifypassword( message, pwd, fn ) {
	info( {
		  message      : message
		, passwordlabel: 'Retype password'
		, ok           : function() {
			if ( $( '#infoPasswordbox' ).val() !== pwd ) {
				info( {
					  message : 'Passwords not matched. Please try again.'
					, ok      : function() {
						verifypassword( message, pwd, fn );
					}
				} );
			} else {
				fn()
			}
		}
	} );
}
// for loop multiple blank passwords
function infopassword( title, message, label, fn, required ) {
	info( {
		  title        : title
		, message      : message
		, passwordlabel: label
		, ok:          function() {
			var pwd = $( '#infoPasswordbox' ).val();
			$( '#infoPasswordbox' ).val( '' );

			if ( pwd ) {
				verifypassword( message, pwd, fn );
			} else {
				if ( !required ) {
					opt += '0 ';
					sendcommand();
				} else {
					info( {
						  message : 'Blank password not allowed.'
						, ok      : function() {
							infopassword( title, message, label, fn, required );
						}
					} );
				}
			}
		}
	} );
}
