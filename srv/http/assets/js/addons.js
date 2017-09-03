$( '#addons' ).click( function() {
  $( '#loader' ).removeClass( 'hide' );
  $.get( 'addondl.php', function( data ) {
    $( '#loader' ).addClass( 'hide' );
		if ( data ) {
			window.location.href = 'addons.php';
		} else {
			alert( 'Addons server not reachable.' );
		}
	});
});
