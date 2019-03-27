/*
simple usage: 
info( 'message' );

normal usage:
info( {                            // default / custom
	width         : N              // 400 / N                (info width)
	icon          : 'NAME'         // question-circle / NAME (FontAwesome name for top icon)
	title         : 'TITLE'        // Information / TITLE    (top title)
	nox           : 1..            // 0 / 1                  (no top 'X' close button)
	boxwidth      : N              // 200 / N / 'max'        (input text/password width)
	message       : 'MESSAGE'      // (blank) / MESSAGE      (message under title)
	msgalign      : 'CSS'          // left / CSS             (message under title)
	textlabel     : 'LABEL'        // (blank) / LABEL        (text input label)
	textrequired  : 1              // 0 / 1                  (text input - ! = disable ok if blank)
	textvalue     : 'VALUE'        // (blank) / VALUE        (text input value)
	textalign     : 'CSS'          // left / CSS             (text input alignment)
	passwordlabel : 'LABEL'        // (blank) / LABEL        (password input label)
	pwdrequired   : 1              // 0 / 1                  (password required)
	fileoklabel   : 'LABEL'        // (blank) / LABEL        (upload button label)
	filetype      : '.TYPE'        // (none) / .TYPE         (filter and verify filetype)
	radio         : JSON           // required               ( var value = $( '#infoRadio input[ type=radio ]:checked' ).val(); )
	checked       : N              // (none) / N             (pre-select input index)
	selectlabel   : 'LABEL'        // (blank) / LABEL        (select input label)
	select        : JSON           // required               ( var value = $( '#infoSelectBox').val(); )
	checked       : N              // (none) / N             (pre-select option index)
	checkbox      : JSON           // required               ( $( '#infoCheckBox input[ type=checkbox ]:checked' ).each( function() {
	                                                               var value = $( this ).val();
	                                                           } ); )
	checked       : [ N, N1, ... ] // (none) / [ array ]     (pre-select input indexes)
	oklabel       : 'LABEL'        // OK / LABEL             (ok button label)
	okcolor       : 'COLOR'        // #0095d8 / COLOR        (ok button color)
	ok            : 'FUNCTION'     // (hide) / FUNCTION      (ok click function)
	cancellabel   : 'LABEL'        // Cancel / LABEL         (cancel button label)
	cancelcolor   : 'COLOR'        // #34495e / COLOR        (cancel button color)
	cancel        : 'FUNCTION'     // (hide) / FUNCTION      (cancel click function)
	buttonwidth   : 0              // 0 / 1                  (keep same button witdth)
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
			<div id="infoRadio" class="infocontent infohtml"></div>
			<div id="infoCheckBox" class="infocontent infohtml"></div>
			<div id="infoSelect" class="infocontent">
				<a id="infoSelectLabel" class="infolabel"></a><select class="infohtml" id="infoSelectBox"></select>
			</div>
		</div>
		<div id="infoButtons">
			<div id="infoFile" class="infobtn">
				<a id="infoFileLabel" class="infobtn infobtn-primary">Browse</a>
				<span id="infoFilename"></span>
				<input type="file" class="infoinput" id="infoFileBox">
			</div>
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
	$( '#infoCancel' ).click();
	infoReset();
} );

function infoReset() {
	$( '#infoOverlay, .infocontent, .infolabel, .infoinput, .infohtml, .infobtn' ).hide();
	$( '.infolabel, .infohtml, #infoFilename' ).empty();
	$( '.infoinput' ).val( '' ).css( 'text-align', '' );
	$( '#infoBox, .infolabel, .infoinput' ).css( 'width', '' );
	$( '#infoFileLabel, #infoButtons a' ).css( 'background', '' );
	$( '#infoFileBox' ).removeAttr( 'accept' );
	$( '#infoFileLabel, #infoButtons a' ).off( 'click' );
	$( '#infoOk' ).removeClass( 'disabled' );
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
	$( '#infoTitle' ).html( O.title || 'Information' );
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
	if ( O.message ) {
		$( '#infoMessage' )
			.html( O.message )
			.css( 'text-align', O.msgalign || '' )
			.show();
	}
	// buttons
	if ( !O.nobutton ) {
		$( '#infoOk' )
			.html( O.oklabel ? O.oklabel : 'OK' )
			.css( 'background', O.okcolor || '' )
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
				.html( O.cancellabel || 'Cancel' )
				.css( 'background', O.cancelcolor || '' )
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
				.css( 'background', O.buttoncolor || '' )
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
		if ( O.textrequired ) {
			if ( !$( '#infoTextBox' ).val() ) $( '#infoOk' ).addClass( 'disabled' );
			$( '#infoTextBox, #infoTextBox2' ).on( 'keyup', function() {
				if ( O.textlabel2 ) {
					var emptytext = !$( '#infoTextBox' ).val() || !$( '#infoTextBox2' ).val();
				} else {
					var emptytext = !$( '#infoTextBox' ).val();
				}
				$( '#infoOk' ).toggleClass( 'disabled', emptytext );
			} );
		}
	} else if ( O.passwordlabel ) {
		$( '#infoPasswordLabel' ).html( O.passwordlabel );
		$( '#infoPassword, #infoPasswordLabel, #infoPasswordBox' ).show();
		var $infofocus = $( '#infoPasswordBox' );
	} else if ( O.fileoklabel ) {
		$( '#infoOk' )
			.html( O.fileoklabel )
			.hide();
		$( '#infoFileLabel' ).click( function() {
			$( '#infoFileBox' ).click();
		} );
		$( '#infoFile, #infoFileLabel' ).show();
		if ( O.filetype ) $( '#infoFileBox' ).attr( 'accept', O.filetype );
		$( '#infoFileBox' ).change( function() {
			var filename = this.files[ 0 ].name;
			var ext = filename.split( '.' ).pop();
			if ( O.filetype && O.filetype.indexOf( ext ) === -1 ) {
				O.ok = '';
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
							, ok          : O.ok
						} );
					}
				} );
				return;
			}
			$( '#infoOk' ).show();
			$( '#infoFileLabel' ).css( 'background', '#34495e' );
			$( '#infoFilename' ).html( '&ensp;'+ filename );
		} );
	} else if ( O.radio ) {
		if ( typeof O.radio === 'string' ) {
			var html = O.radio;
		} else {
			var html = '';
			$.each( O.radio, function( key, val ) {
				// <label> for clickable label
				html += '<label><input type="radio" name="inforadio" value="'+ val +'">&ensp;'+ key +'</label><br>';
			} );
		}
		renderOption( $( '#infoRadio' ), html, O.checked );
	} else if ( O.select ) {
		$( '#infoSelectLabel' ).html( O.selectlabel );
		if ( typeof O.select === 'string' ) {
			var html = O.select;
		} else {
			var html = '';
			$.each( O.select, function( key, val ) {
				html += '<option value="'+ val +'">'+ key +'</option>';
			} );
		}
		renderOption( $( '#infoSelectBox' ), html, O.checked );
		$( '#infoSelect, #infoSelectLabel, #infoSelectBox' ).show();
	} else if ( O.checkbox ) {
		if ( typeof O.checkbox === 'string' ) {
			var html = O.checkbox;
		} else {
			var html = '';
			$.each( O.checkbox, function( key, val ) {
				html += '<label><input type="checkbox" value="'+ val +'">&ensp;'+ key +'</label><br>';
			} );
		}
		renderOption( $( '#infoCheckBox' ), html, O.checked );
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
	if ( O.buttonwidth ) {
		var widest = 0;
		var w;
		$.each( $( '.infobtn' ), function() {
			w = $( this ).outerWidth();
			if ( w > widest ) widest = w;
		} );
		$( '.infobtn' ).css( 'min-width', widest +'px' );
	}
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
	
	var checked = typeof chk === 'array' ? chk : [ chk ];
	checked.forEach( function( i ) {
		$opt = $el.prop( 'id' ) === 'infoSelectBox' ? $el.find( 'option' ) : $el.find( 'input' )
		$opt.eq( i ).prop( 'checked', true );
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
