$( '#addons' ).click( function() {
	// fix path if click in other menu pages
	var path = /\/.*\//.test( window.location.pathname ) ? '../../' : '';
	$( '#loader' ).removeClass( 'hide' );
	$.get( path +'addondl.php', function( data ) {
		if ( data == 1 ) {
			window.location.href = path +'addons.php';
		} else {
			alert( "Addons server cannot be reached.\n"
				+"Try check and set date to current." );
			$( '#loader' ).addClass( 'hide' );
		}
	});
});
