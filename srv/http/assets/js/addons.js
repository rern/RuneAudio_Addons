$( '#addons' ).click( function() {
	$( '#loader' ).removeClass( 'hide' );
	$.get( 'addondl.php', function( data ) {
		if ( data ) {
			window.location.href = 'addons.php';
		} else {
			$( '#loader' ).addClass( 'hide' );
			alert( 'Addons server not reachable.' );
		}
	});
});
