$(function() { // $( document ).ready(function() {

// append style for addons icon
function heredoc( fn ) {
  return fn.toString().match( /\/\*\s*([\s\S]*?)\s*\*\//m )[ 1 ];
}
if ( $( '#bartop' ).length ) {
	var style = heredoc(function () {/*
<style>
	@font-face {
		font-family: enhance;
		src: url('../fonts/enhance.woff') format('woff'),
			url('../fonts/enhance.ttf') format('truetype');
		font-weight: normal;
		font-style: normal;
	}
	.container h1:before,
	#addo span:before { 
		font-family: enhance;
		content: "\00a0\f506\00a0";
		color: #7795b4;
	}
</style>
	*/});
	$( 'head' ).append( style );
}

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
	
$.post( '/addonsdl.php', { redis: 'update' }, function( data ) {
	if ( data != 0 ) {
		$( '#menu-settings').find( 'i' ).append( '<span id="badge">'+ data +'</span>' );
	} else {
		$( '#badge' ).remove();
	}
} );

} );
