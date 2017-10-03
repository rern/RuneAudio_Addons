// fix path if click in other menu pages
var path = /\/.*\//.test( location.pathname ) ? '../../' : '';

$( '#addons' ).click( function() {
	$( '#loader' ).removeClass( 'hide' );
	
	$.get(
		path +'addonsdl.php',
		function( data ) {
			addonsdl( data );
		}
	);
} );

// for branch testing
var hammeraddons = new Hammer( $( '#addons' )[0] );
hammeraddons.on( 'press', function () {
	info( {
		title : 'Addons Menu Branch Test',
		textlabel: 'Branch',
		cancel: 1,
		ok: function() {
			var branch = $( '#infoTextbox' ).val();
			if ( !branch ) return;
			$.get(
				path +'addonsdl.php?branch='+ branch,
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
