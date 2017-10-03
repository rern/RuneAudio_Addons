$( '#addons' ).click( function() {
	// fix path if click in other menu pages
	var path = /\/.*\//.test( location.pathname ) ? '../../' : '';
	
	$( '#loader' ).removeClass( 'hide' );
	
	$.get(
		path +'addonsdl.php',
		function( data ) {
			addonsdl( data );
		}
	);
} );

var hammeraddons = new Hammer( $( '#addons' ) );
hammeraddons.on( 'press', function () {
	info( {
		title : 'Addons Menu Branch Test',
		textlabel: 'Branch',
		cancel: 1,
		ok: function() {
			$.post(
				path +'addonsdl.php',
				{ branch: $( '#infoTextbox' ).val() },
				function( data ) {
					addonsdl( data );
				}
			);
		}
	} );
} );

function addonsdl( data ) {
	if ( data === 'failed' ) {
		info( 'Addons server cannot be reached.'
			+'<br>Please try again later.' );
		$( '#loader' ).addClass( 'hide' );
	} else {
		location.href = path +'addons.php';
	}
}
