$( '#addons' ).click( function () {
	$.get( 'addonsdl.php', function( exit ) {
		addonsdl( exit );
	} );
	temploader();
} ).on( 'taphold', function () {
	info( {
		  title     : 'Addons Branch Test'
		, textlabel : 'Branch'
		, textvalue : 'UPDATE'
		, boxwidth  : 'max'
		, ok        : function() {
			var branch = $( '#infoTextBox' ).val();
			if ( branch ) {
				$.get( 'addonsdl.php?branch='+ branch, function( exit ) {
					addonsdl( exit );
				} );
			}
			setTimeout( temploader, 0 ); // info() hides #loader on close
		}
	} );
} );

function temploader() {
	$( '#loader' )
		.html( '<i class="fa fa-addons blink"></i>' )
		.removeClass( 'hide' );
	$( '#settings' ).addClass( 'hide' );
}
function addonsdl( exit ) {
	if ( exit == 1 ) {
		info( {
			  icon    : 'info-circle'
			, message : 'Download from Addons server failed.'
					   +'<br>Please try again later.'
			, ok      : function() {
				$( '#loader' ).addClass( 'hide' );
			}
		} );
	} else if ( exit == 2 ) {
		info( {
			  icon    : 'info-circle'
			, message : 'Addons Menu cannot be updated.'
					   +'<br>Root partition has <white>less than 1 MB free space</white>.'
			, ok      : function() {
				location.href = 'addons.php';
			}
		} );
	} else {
		location.href = 'addons.php';
	}
}
var pushstreamAddons = new PushStream( {
	  host  : window.location.hostname
	, port  : window.location.port
	, modes : 'websocket'
} );
pushstreamAddons.onmessage = function() {
	$( '#loader' ).html( '<i class="fa fa-gear fa-spin"></i><br><br>Updating ...' );
};
pushstreamAddons.addChannel( 'addons' );
pushstreamAddons.connect();

document.addEventListener( 'visibilitychange', function() {
	document.hidden ? pushstreamAddons.disconnect() : pushstreamAddons.connect();
} );
