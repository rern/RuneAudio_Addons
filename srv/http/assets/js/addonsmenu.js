$( function() { //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

//$( '#badge' ).text( 5 ).show();

var hammeraddons = new Hammer( document.getElementById( 'addons' ) );
hammeraddons.on( 'tap', function () {
	$( '#loader' ).removeClass( 'hide' );
	
	$.get( '/addonsdl.php', function( exit ) {
			addonsdl( exit );
	} );
} );

// for branch testing
hammeraddons.on( 'press', function () {
	info( {
		  title    : 'Addons Menu Branch Test'
		, textlabel: 'Branch'
		, textvalue: 'UPDATE'
		, cancel   : 1
		, ok       : function() {
			var branch = $( '#infoTextbox' ).val();
			$( '#loader' ).removeClass( 'hide' );
			if ( branch ) {
				$.get(
					'/addonsdl.php?branch='+ branch,
					function( exit ) {
						addonsdl( exit );
					}
				);
			}
		}
	} );
} );

function addonsdl( exit ) {
	if ( exit == 1 ) {
		info( {
			  icon   : '<i class="fa fa-info-circle fa-2x">'
			, message: 'Download from Addons server failed.'
				+'<br>Please try again later.'
			, ok     : function() {
				$( '#loader' ).addClass( 'hide' );
			}
		} );
	} else if ( exit == 2 ) {
		info( {
			  icon   : '<i class="fa fa-info-circle fa-2x">'
			, message: 'Addons Menu cannot be updated.'
				+'<br>Root partition has <white>less than 1 MB free space</white>.'
			, ok     : function() {
				$( '#loader' ).addClass( 'hide' );
				location.href = '/addons.php';
			}
		} );
	} else {
		PNotify.removeAll();
		location.href = '/addons.php';
	}
}

// nginx pushstream websocket
var pushstreamAddons = new PushStream( {
	host: window.location.hostname,
	port: window.location.port,
	modes: GUI.mode
} );
pushstreamAddons.onmessage = function() {
	$( '#loadercontent' ).html( '<i class="fa fa-gear fa-spin"></i>Updating...' );
};
pushstreamAddons.addChannel('addons');
pushstreamAddons.connect();

// remove previous before new notify
var old_renderMSG = renderMSG;
renderMSG = function( text ) {
	PNotify.removeAll();
	old_renderMSG( text );
}

if ( $( '#bartop' ).length ) return;
var redis = { update: [ 'hGet', 'display', 'update' ] };
$.post( '/enhanceredis.php', { json: JSON.stringify( redis ) }, function( data ) {
	var update = JSON.parse( data ).update
	$( '#badge' ).text( update ).toggle( update != 0 );
} );

} ); //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
