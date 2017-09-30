$( '#addons' ).click( function() {
	// fix path if click in other menu pages
	var path = /\/.*\//.test( location.pathname ) ? '../../' : '';
	
	$( '#loader' ).removeClass( 'hide' );
	
	$.get( 
		path +'addonssudo.php',
		function( data ) {
			if ( data === 'failed' ) {
				alert( "Addons server cannot be reached.\n"
					+"Please try again later." );
				$( '#loader' ).addClass( 'hide' );
				return
			}
			location.href = path +'addons.php';
		}
	);
});
