/*
simple usage: info( 'message' );
normal usage: info( {
	icon          : 'NAME'         // question-circle / NAME (FontAwesome name for top icon)
	title         : 'TITLE'        // Information / TITLE    (top title)
	nox           : 1..            // 0 / 1                  (no top 'X' close button)
	boxwidth      : N              // 200 / N / 'max'        (input text/password width)
	message       : 'MESSAGE'      // (blank) / MESSAGE      (message under title)
	textlabel     : 'LABEL'        // (blank) / LABEL        (text input label)
	passwordlabel : 'LABEL'        // (blank) / LABEL        (password input label)
	required      : 1              // 0 / 1                  (password required)
	radiohtml     : 'HTML'         // required HTML
	checked       : N              // (none) / N             (pre-select input)
	checkboxhtml  : 'HTML'         // required HTML
	checked       : [ N, N1, ... ] // (none) / [ array ]     (pre-select multiple)
	selectlabel   : 'LABEL'        // (blank) / LABEL        (select input label)
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
function heredoc( fn ) {
	return fn.toString().match( /\/\*\s*([\s\S]*?)\s*\*\//m )[ 1 ];
};
var html = heredoc( function() { /*
<div id="infoOverlay" tabindex="1">
	<div id="infoBox">
		<div id="infoTopBg">
			<div id="infoTop">
				<a id="infoIcon"></a>&emsp;<a id="infoTitle"></a>
			</div>
			<div id="infoX"><i class="fa fa-times fa-2x"></i></div>
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
	infoReset();
} );

function infoReset() {
	$( '#infoOverlay, .infocontent, .infolabel, .infoinput, .infohtml, .infobtn' ).hide();
	$( '.infolabel, .infohtml, #infoFilename' ).empty();
	$( '.infoinput' ).val( '' );
	$( '.infolabel, .infoinput' ).css( 'width', '' );
	$( '#infoFileLabel, #infoButtons a' ).css( 'background', '' );
	$( '#infoFileBox' ).removeAttr( 'accept' );
	$( '#infoFileLabel, #infoButtons a' ).off( 'click' );
	$( '#loader' ).addClass( 'hide' ); // temp fix
}

function info( O ) {
	// title
	infoReset();
	
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
	if ( O.nox ) $( '#infoX' ).hide();
	
	// simple use as info( 'message' )
	if ( typeof O !== 'object' ) {
		$( '#infoMessage' ).html( O );
		$( '#infoIcon' ).html( '<i class="fa fa-info-circle fa-2x">' );
		$( '#infoOverlay, #infoMessage, #infoOk' ).show();
		$( '#infoOk' ).html( 'OK' ).click( function() {
			infoReset();
		});
		return;
	}
	
	// message
	if ( O.message ) $( '#infoMessage' ).html( O.message ).show();
	
	// buttons
	if ( O.cancel ) {
		$( '#infoCancel' )
			.html( O.cancellabel ? O.cancellabel : 'Cancel' )
			.css( 'background', O.cancelcolor ? O.cancelcolor : '' )
			.show()
			.on( 'click', function() {
				$( '#infoOverlay' ).hide();
				if ( typeof O.cancel === 'function' ) {
					O.cancel();
					O.cancel = ''; // suppress multiple runs
				}
			} );
	}
	if ( O.button ) {
		$( '#infoButton' )
			.html( O.buttonlabel )
			.css( 'background', O.buttoncolor ? O.buttoncolor : '' )
			.show()
			.on( 'click', function() {
				$( '#infoOverlay' ).hide();
				O.button();
				O.button = '';
			} );
	}
	$( '#infoOk' )
		.html( O.oklabel ? O.oklabel : 'OK' )
		.css( 'background', O.okcolor ? O.okcolor : '' )
		.show()
		.on( 'click', function() {
			$( '#infoOverlay' ).hide();
			if ( typeof O.ok === 'function' ) {
				O.ok();
				O.ok = ''; // suppress multiple runs
			} else {
				infoReset();
			}
		} );

		// inputs
	if ( O.textlabel ) {
		$( '#infoTextLabel' ).html( O.textlabel );
		$( '#infoTextBox' ).val( O.textvalue );
		$( '#infoText, #infoTextLabel, #infoTextBox' ).show();
		var $infofocus =  $( '#infoTextBox' );
		if ( O.textlabel2 ) {
			$( '#infoTextLabel2' ).html( O.textlabel2 );
			$( '#infoTextBox2' ).val( O.textvalue2 );
			$( '#infoTextLabel2, #infoTextBox2' ).show();
		}
		if ( O.boxwidth ) {
			var calcW = window.innerWidth * 0.98;
			var infoW = calcW > 400 ? 290 : calcW - 110;
			var boxW = O.boxwidth !== 'max' ? O.boxwidth +'px' : infoW - $( '.infoinput' ).width() +'px'
			$( '.infoinput' ).css( 'width', boxW );
		}
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
		$( '#infoFileLabel' ).on( 'click', function() {
			$( '#infoFileBox' ).click();
		} );
		$( '#infoFileBox' ).on( 'change', function() {
			var filename = this.files[ 0 ].name;
			if ( O.filetype && filename.indexOf( O.filetype ) === -1 ) {
				O.ok = '';
				info( {
					  icon    : 'warning'
					, title   : title
					, message : 'The file must has extension: <white>'+ O.filetype +'</white>'
				} );
				return;
			}
			$( '#infoFilename' ).html( '&ensp;'+ filename );
			$( '#infoFileLabel' ).css( 'background', '#34495e' );
			$( '#infoOk' )
				.css( 'background', '' )
				.on( 'click', function() {
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
	if ( $infofocus ) $infofocus.select();
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
