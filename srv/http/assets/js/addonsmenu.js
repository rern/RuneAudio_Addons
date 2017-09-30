$( '#addons' ).click( function() {
	var version = $( this ).attr( 'version' );
	// fix path if click in other menu pages
	var path = /\/.*\//.test( location.pathname ) ? '../../' : '';
	
	$( '#loader' ).removeClass( 'hide' );
	
	$.post( 
		path +'addonssudo.php',
		{file: 'addonsdl.sh'},
		function( data ) {
			if ( !data ) {
				alert( "Addons server cannot be reached.\n"
					+"Please try again later." );
				$( '#loader' ).addClass( 'hide' );
				return
			}
			if ( data == version ) {
				location.href = path +'addons.php';
			} else {
				$.post( 
					'addonssudo.php',
					{file: 'addonsupdate.sh'},
					function(data) {
						location.href = path +'addons.php';
					}
				);
			}
		}
	);
});
