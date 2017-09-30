$( '#addons' ).click( function() {
	// fix path if click in other menu pages
	var path = /\/.*\//.test( location.pathname ) ? '../../' : '';
	
	$( '#loader' ).removeClass( 'hide' );
	
	$.post( 
		path +'addonssudo.php',
		{file: 'addonsdl.php'},
		function( data ) {
			if ( data == 1 ) {
				location.href = path +'addons.php';
			} else {
				alert( "Addons server cannot be reached.\n"
					+"Please try again later." );
				$( '#loader' ).addClass( 'hide' );
			}
		}
	);
});
