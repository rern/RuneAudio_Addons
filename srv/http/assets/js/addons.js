$( '#addons' ).click( function() {
	$( '#loader' ).removeClass( 'hide' );
	$.get( 'addondl.php', function( data ) {
		if ( data ) {
			window.location.href = 'addons.php';
		} else {
			alert( 'Addons server not reachable.' );
			$( '#loader' ).addClass( 'hide' );
		}
	});
});
