$( '#addons' ).click( function() {
	$( '#loader' ).removeClass( 'hide' );
	$.get( 'addondl.php', function( data ) {
		if ( data == 1 ) {
			window.location.href = 'addons.php';
		} else {
			alert( "Addons server cannot be reached.\n"
				+"Try check and set date to current" );
			$( '#loader' ).addClass( 'hide' );
		}
	});
});
