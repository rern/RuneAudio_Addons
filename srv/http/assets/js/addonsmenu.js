$(function() { // $( document ).ready(function() {

$( '#addons' ).click( function () {
	$( '#loader' )
		.html( '<i class="fa fa-addons blink"></i><br>Connecting ...' )
		.removeClass( 'hide' );
	$.get( '/addonsdl.php', function( exit ) {
			addonsdl( exit );
	} );
} ).on( 'taphold', function () {
	info( {
		  title    : 'Addons Menu Branch Test'
		, textlabel: 'Branch'
		, textvalue: 'UPDATE'
		, cancel   : 1
		, ok       : function() {
			var branch = $( '#infoTextBox' ).val();
			$( '#loader' )
				.html( '<i class="fa fa-addons blink"></i><br>Connecting ...' )
				.removeClass( 'hide' );
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
			  icon   : 'info-circle'
			, message: 'Download from Addons server failed.'
				+'<br>Please try again later.'
			, ok     : function() {
				$( '#loader' ).addClass( 'hide' );
			}
		} );
	} else if ( exit == 2 ) {
		info( {
			  icon   : 'info-circle'
			, message: 'Addons Menu cannot be updated.'
				+'<br>Root partition has <white>less than 1 MB free space</white>.'
			, ok     : function() {
				location.href = '/addons';
			}
		} );
	} else {
		location.href = '/addons';
	}
}

// nginx pushstream websocket
var pushstreamAddons = new PushStream( {
	host: window.location.hostname,
	port: window.location.port,
	modes: GUI.mode
} );
pushstreamAddons.onmessage = function() {
	$( '#loader' ).html( '<i class="fa fa-gear fa-spin"></i><br>Updating ...' );
};
pushstreamAddons.addChannel('addons');
pushstreamAddons.connect();

// remove previous before new notify
var old_renderMSG = renderMSG;
renderMSG = function( text ) {
	PNotify.removeAll();
	old_renderMSG( text );
}

} );
