$( '#addons' ).click( function() {
	// fix path if click in other menu pages
	var path = /\/.*\//.test( location.pathname ) ? '../../' : '';
	
	$( '#loader' ).removeClass( 'hide' );
	
	$.get(
		path +'addonsdl.php',
		function( data ) {
			addonsdl( data, path );
		}
	);
} );
$( '#infoX' ).click( function() {
	$( '#infoCancel' ).click();
} );

// for branch testing
var hammeraddons = new Hammer( $( '#addons' )[0] );
hammeraddons.on( 'press', function () {
	var path = /\/.*\//.test( location.pathname ) ? '../../' : '';
	
	$( '#loader' ).removeClass( 'hide' );
	
	info( {
		title    : 'Addons Menu Branch Test',
		textlabel: 'Branch',
		textvalue: 'UPDATE',
		cancel   : function() {
			$( '#loader' ).addClass( 'hide' );
		},
		ok       : function() {
			var branch = $( '#infoTextbox' ).val();
			if ( !branch ) return;
			$.get(
				path +'addonsdl.php?branch='+ branch,
				function( data ) {
					addonsdl( data, path );
				}
			);
		}
	} );
} );

function addonsdl( data, path ) {
	if ( data === 'failed' ) {
		info( {
			icon   : '<i class="fa fa-info-circle fa-2x">',
			message: 'Addons server cannot be reached.'
				+'<br>Please try again later.' 
		} );
		$( '#loader' ).addClass( 'hide' );
	} else {
		location.href = path +'addons.php';
	}
}
