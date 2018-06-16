$( 'body' ).prepend( '\
<div id="infoOverlay">\
	<div id="infoBox">\
		<div id="infoTopBg">\
			<div id="infoTop">\
				<a id="infoIcon"></a><a id="infoTitle"></a>\
			</div>\
			<div id="infoX"><i class="fa fa-times fa-2x"></i></div>\
			<div style="clear: both"></div>\
		</div>\
		<div id="infoContent">\
			<p id="infoMessage" class="info"></p>\
			<div id="infoText" class="info">\
				<a id="infoTextLabel"></a><input type="text" class="infoBox" id="infoTextbox" spellcheck="false">\
			</div>\
			<div id="infoPassword" class="info">\
				<a id="infoPasswordLabel"></a> <input type="password" class="infoBox" id="infoPasswordbox">\
			</div>\
			<div id="infoRadio" class="info"></div>\
			<div id="infoCheckbox" class="info"></div>\
			<div id="infoSelect" class="info">\
				<a id="infoSelectLabel"></a><select class="infoBox" id="infoSelectbox"></select>\
			</div>\
		</div>\
		<div id="infoButtons">\
			<a id="infoCancel" class="btn btn-default"></a>\
			<a id="infoOk" class="btn btn-primary"></a>\
		</div>\
	</div>\
</div>\
' );

function info( O ) {
	// reset to default
	$( '#infoTextLabel, #infoPasswordLabel, #infoSelectLabel' ).empty();
	$( '#infoRadio, #infoCheckbox, #infoSelectbox' ).empty();
	$( '.infoBox' ).width( 200 ).val('');
	$( '.info, #infoCancel' ).hide();
	$( '#infoOk' ).html( 'Ok' );
	$( '#infoCancel' ).html( 'Cancel' );
	$( 'body' ).unbind( 'keypress' );

	// simple use as info('message')
	if ( typeof O != 'object' ) {
		$( '#infoOk' ).off( 'click' ).on( 'click', function () {
			$( '#infoOverlay' ).hide();
		});
		$( '#infoMessage' ).html( O ).show();
	} else {
		// O use as info({x: 'x', y: 'y'})
		$( '#infoIcon' ).html( O.icon ? O.icon : '<i class="fa fa-question-circle fa-2x">' );
		$( '#infoTitle' ).html( O.title ? O.title : 'Information' );
		if ( O.message ) $( '#infoMessage' ).html( O.message ).show();
		if ( O.textlabel ) {
			$( '#infoTextLabel' ).html( O.textlabel +' ' );
			$( '#infoTextbox' ).val( O.textvalue );
			$( '#infoText' ).show();
			var $infofocus = $( '#infoTextbox' );
			if ( O.textvalue ) $( '#infoTextbox' ).select();
		}
		if ( O.passwordlabel ) {
			$( '#infoPasswordLabel' ).html( O.passwordlabel +' ' );
			$( '#infoPassword' ).show().focus();
			var $infofocus = $( '#infoPasswordbox' );
		}
		if ( O.radiohtml ) setboxwidth( $( '#infoRadio' ), O.radiohtml );
		if ( O.checkboxhtml ) setboxwidth( $( '#infoCheckbox' ), O.checkboxhtml );
		if ( O.selecthtml ) {
			$( '#infoSelectLabel' ).html( O.selectlabel +' ' );
			$( '#infoSelectbox' ).html( O.selecthtml );
			$( '#infoSelect' ).show();
		}
		if ( O.boxwidth ) $( '.infoBox' ).width( O.boxwidth );
		
		if ( O.oklabel ) $( '#infoOk' ).html( O.oklabel );
		if ( O.okcolor ) $( '#infoOk' ).css( 'background', O.okcolor );
		if ( O.cancel ) {
			$( '#infoCancel' ).show();
			$( '#infoCancel' ).off( 'click' ).on( 'click', function() {
				$( '#infoOverlay' ).hide();
				if ( typeof O.cancel === 'function' ) O.cancel();
			});
			if ( O.cancellabel ) $( '#infoCancel' ).html( O.cancellabel );
		}
		if ( O.nox ) $( '#infoX' ).hide();
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
	$( '#infoX' ).click( function() {
		$( '#infoCancel' ).click();
		$( '#infoOverlay' ).hide();
		$( '#infoTextbox, #infoPasswordbox' ).val( '' );
	} );
	$( '#infoOverlay' ).click( function( e ) {
		if ( e.target === this ) $( '#infoX' ).click();
	} );
}
window.addEventListener( 'orientationchange', function() {
	$( '#infoBox' ).css( 'top', ( window.innerWidth - $( '#infoBox' ).height() ) / 2 +'px' );
} );

function setboxwidth( $box, html ) {
	var windowW = window.innerWidth;
	var windowH = window.innerHeight;
	var contentW = windowW >= 400 ? $( '#infoBox' ).width() : windowW;
	var maxW = 0;
	var spanW = 0;
	$( '#infoBox' ).css('left', '-100%' );      // move out of screen
	$box.html( html ).show();                   // show to get width
	setTimeout( function() {                    // wait for O.radiohtml ready
		$box.find( 'label' ).each( function() { // get max width
				spanW = $( this ).width();
				maxW = ( spanW > maxW ) ? spanW : maxW;
		} );
		var pad = ( contentW - 20 - maxW ) / 2; // 15 = button width
		$box.css('padding-left', pad +'px');    // set padding-left
		$( '#infoBox' ).css( { 'left': '50%', 'top': ( windowH - $( '#infoBox' ).height() ) / 2 +'px' } );    // move back
	}, 100);
}
function verifypassword( msg, pwd, fn ) {
	$( '#infoPasswordbox' ).val( '' );
	info( {
		message      : msg,
		passwordlabel: 'Retype password',
		ok           : function() {
			if ( $( '#infoPasswordbox' ).val() !== pwd ) {
				info( {
					message : 'Passwords not matched. Please try again.',
					ok      : function() {
						verifypassword( msg, pwd, fn )
					}
				} );
			} else {
				fn()
			}
		}
	} );
}
