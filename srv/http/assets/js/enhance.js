var GUI = {
	  activePlayer : ''
	, airplay      : {}
	, artistalbum  : ''
	, bookmarkedit : 0
	, browsemode   : ''
	, cvscrolltop  : 0
	, currentpage  : 'playback'
	, currentpath  : ''
	, dbback       : 0
	, dbbackdata   : []
	, dbbrowsemode : ''
	, dblist       : 0
	, dbscrolltop  : {}
	, debounce     : ''
	, display      : {}
	, imodedelay   : 0
	, list         : {}
	, library      : 0
	, local        : 0
	, localhost    : ( location.hostname === 'localhost' || location.hostname === '127.0.0.1' )
	, lsplaylists  : []
	, playback     : 1
	, playlist     : 0
	, pleditor     : 0
	, pllist     : {}
	, plscrolltop  : 0
	, plugin       : ''
	, scale        : 1
	, screenS      : ( window.innerHeight < 590 || window.innerWidth < 500 )
	, scrollspeed  : 80 // pixel/s
	, status       : {}
	, debounce      : ''
	, debouncems    : 300
};
PNotify.prototype.options.delay = 3000;
PNotify.prototype.options.styling = 'fontawesome';
PNotify.prototype.options.icon = '';
PNotify.prototype.options.stack = {
	  dir1      : 'up'    // stack up
	, dir2      : 'right' // shift right when fill height
	, firstpos1 : 60      // top offset from border H
	, firstpos2 : 0       // left offset from border V
	, spacing1  : 10      // space between each
	, spacing2  : 10      // space between column( or row if dir1: right/left )
}
var picaOption = { // pica.js scaling: img to canvas
	  unsharpAmount: 100  // 0...500 Default = 0 (try 50-100)
	, unsharpThreshold: 5 // 0...100 Default = 0 (try 10)
	, unsharpRadius: 0.6
//	, quality: 3          // 0...3 Default = 3 (Lanczos win=3)
//	, alpha: true         // Default = false (black crop background)
};
var A2Z = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split( '' );
var cssnotify = 
	 '<style id="cssnotify">'
		+'.ui-pnotify { bottom: 20px; }'
		+'.pnotify_custom { top: 20px !important; }'
	+'</style>';
var hash = Date.now();
var coverrune = '/assets/img/cover.'+ hash +'.svg';
var vustop = '/assets/img/vustop.'+ hash +'.gif';
if ( GUI.localhost ) {
	var vu = '/assets/img/vustop.'+ hash +'.gif';
	var blinkdot = '<a>·</a>&ensp;<a>·</a>&ensp;<a>·</a>';
} else {
	var vu = '/assets/img/vu.'+ hash +'.gif';
	var blinkdot = '<a class="dot">·</a>&ensp;<a class="dot dot2">·</a>&ensp;<a class="dot dot3">·</a>';
}

// get playlist display, status, library
$.post( 'enhance.php', { getplaylist: 1 }, function( data ) {
	GUI.pllist = data.playlist; // for dirble coverart
}, 'json' );
$.post( 'enhance.php', { getdisplay: 1, data: 1 }, function( data ) {
	GUI.display = data;
	$.event.special.tap.emitTapOnTaphold = false; // suppress tap on taphold
	$.event.special.swipe.horizontalDistanceThreshold = 80; // pixel to swipe
	$.event.special.tap.tapholdThreshold = 1000;
	setSwipe();
	$.post( 'enhancestatus.php', function( status ) {
		GUI.status = status;
		renderPlayback();
		displayPlayback();
		setButton();
	}, 'json' );
}, 'json' );

$( function() { // document ready start >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

$( '#cover-art' ).on( 'error', function() {
	var $this = $( this );
	$this.unbind( 'error' );
	if ( GUI.status.ext === 'radio' ) {
		$this
			.attr( 'src', status.state === 'play' ? vu : vustop )
			.css( 'border-radius', '18px' )
		$( '#coverartoverlay' ).removeClass( 'hide' );
	} else {
		$this
			.attr( 'src', url )
			.css( 'border-radius', '' );
		$( '#coverartoverlay' ).addClass( 'hide' );
	}
} ).one( 'load', function() {
	removeSplash();
} );
// COMMON /////////////////////////////////////////////////////////////////////////////////////
$( '#menu-settings, #badge' ).click( function() {
	$( '#settings' )
		.toggleClass( 'hide' )
		.css( 'top', ( GUI.bars ? '40px' : 0 ) );
} );
var chklibrary = {
	  coverart       : '_<i class="fa fa-coverart"></i>CoverArt'
	, sd             : '<i class="fa fa-microsd"></i>SD'
	, usb            : '_<i class="fa fa-usbdrive"></i>USB'
	, nas            : '<i class="fa fa-network"></i>Network'
	, webradio       : '_<i class="fa fa-webradio"></i>Webradio'
	, album          : '<i class="fa fa-album"></i>Album'
	, artist         : '_<i class="fa fa-artist"></i>Artist'
	, composer       : '<i class="fa fa-composer"></i>Composer'
	, albumartist    : '_<i class="fa fa-albumartist"></i>Album artist'
	, genre          : '<i class="fa fa-genre"></i>Genre'
	, dirble         : '_<i class="fa fa-dirble"></i>Dirble'
	, jamendo        : '<i class="fa fa-jamendo"></i>Jamendo'
	, count          : '_<gr>text</gr> Count'
	, label          : '<gr>text</gr> Label'
	, plclear        : 'Confirm <gr>on clear Playlist</gr>'
	, playbackswitch : 'Open Playback <gr>on</gr> <i class="fa fa-play-plus"></i>Add <gr>►</gr> Play'
	, tapaddplay     : 'Single tap song <gr>=</gr> <i class="fa fa-play-plus"></i>Add <gr>►</gr> Play'
	, coverfile      : '<i class="fa fa-coverart"></i>Cover art <gr>in album/folder</gr>'
	, thumbbyartist  : '<i class="fa fa-coverart"></i>Sort CoverArts by artist'
}
$( '#displaylibrary' ).click( function() {
	var coverfile = GUI.display.coverfile;
	var thumbbyartist = GUI.display.thumbbyartist;
	info( {
		  icon     : 'library'
		, title    : 'Library Tools'
		, message  : 'Select items to show / options:'
		, checkbox : '<form id="displaysavelibrary">'+ displayCheckbox( chklibrary ) +'</form>'
		, cancel   : 1
		, ok       : function () {
			var data = {};
			$( '#displaysavelibrary input' ).each( function() {
				var checked = this.checked ? 'checked' : '';
				GUI.display[ this.name ] = checked;
				data[ this.name ] = checked;
			} );
			$.post( 'enhance.php', { setdisplay: data }, function() {
				if ( GUI.display.thumbbyartist !== thumbbyartist ) location.reload();
				
				if ( !GUI.library ) $( '#tab-library' ).click();
			} );
		}
	} );
} );
var chkplayback = {
	  bars         : 'Top-Bottom bars'
	, barsauto     : 'Bars on small screen'
	, time         : 'Time'
	, radioelapsed : 'Webradio elapsed'
	, cover        : 'Cover art'
	, coverlarge   : 'Large Cover art'
	, volume       : 'Volume'
	, buttons      : 'Buttons'
}
$( '#displayplayback' ).click( function() {
	info( {
		  icon     : 'play-circle'
		, title    : 'Playback Tools'
		, message  : 'Select items to show / options:'
		, checkbox : '<form id="displaysaveplayback">'+ displayCheckbox( chkplayback ) +'</form>'
		, cancel   : 1
		, ok       : function () {
			// no: serializeArray() omit unchecked fields
			var data = {};
			$( '#displaysaveplayback input' ).each( function() {
				var checked = this.checked ? 'checked' : '';
				GUI.display[ this.name ] = checked;
				data[ this.name ] = checked;
			} );
			$.post( 'enhance.php', { setdisplay: GUI.display }, function() {
				displayPlayback();
				$( '#swipebar, .page' ).off( 'swipeleft swiperight' );
				setSwipe();
				cssNotify();
			} );
			if ( !GUI.playback ) $( '#tab-playback' ).click();
		}
	} );
	// disable by bars hide
	if ( !GUI.display.bars ) disableCheckbox( 'barsauto' );
	// disable by mpd volume
	if ( !GUI.display.volumempd ) disableCheckbox( 'volume' );
	// disable by autohide
	if ( !GUI.display.time && !GUI.display.volume ) {
		disableCheckbox( 'coverart' );
		disableCheckbox( 'buttons' );
	}
} );
$( '#turnoff' ).click( function() {
	var localhost = document.location.hostname === 'localhost';
	info( {
		  icon        : 'power'
		, title       : 'Power'
		, message     : 'Select mode:'
		, oklabel     : 'Off'
		, okcolor     : '#bb2828'
		, ok          : function() {
			$.post( 'enhance.php', { power: 'shutdown' } );
			$( '#loader' ).removeClass( 'hide' );
		}
		, buttonlabel : 'Reboot'
		, buttoncolor : '#de810e'
		, button      : function() {
			$.post( 'enhance.php', { power: 'reboot' } );
			$( '#loader' ).removeClass( 'hide' );
		}
		, cancellabel : !localhost ? '' : 'Screen off'
		, cancel      : !localhost ? '' : function() {
			$.post( 'enhance.php', { power: 'screenoff' } );
		}	} );
} );
$( '#tab-library' ).click( function() {
	$( '#db-search-close span' ).empty();
	if ( GUI.library ) {
		$( '#divcoverarts' ).addClass( 'hide' );
		$( '#home-blocks' ).removeClass( 'hide' );
		$( '.home-bookmark' ).children()
			.add( '.coverart img' ).css( 'opacity', '' );
		$( '.edit' ).remove();
	}
	if ( GUI.library && GUI.bookmarkedit ) {
		GUI.bookmarkedit = 0;
		renderLibrary();
	} else if ( GUI.library && GUI.dblist ) {
		GUI.dblist = GUI.dbback = 0;
		GUI.currentpath = GUI.browsemode = GUI.dbbrowsemode = ''
		GUI.dbbackdata = [];
		renderLibrary();
	} else if ( GUI.status.activePlayer === 'Airplay' ) {
		$( '#timeTL' ).click();
	} else {
		switchPage( 'library' );
	}
} );
$( '#tab-playback' ).click( function() {
	getPlaybackStatus();
	switchPage( 'playback' );
} )
$( '#tab-playlist' ).click( function() {
	GUI.plappend = '';
	if ( GUI.playlist && GUI.pleditor ) GUI.pleditor = 0;
	if ( GUI.status.activePlayer === 'Airplay' ) {
		$( '#timeTL' ).click();
		return
	}
	
	switchPage( 'playlist' );
	if ( GUI.pleditor ) return
	
	$.post( 'enhance.php', { getplaylist: 1 }, function( data ) {
		GUI.lsplaylists = data.lsplaylists || [];
		GUI.pllist = data.playlist;
		renderPlaylist();
	}, 'json' );
} );
$( '#swipebar' ).tap( function( e ) {
	if ( !GUI.swipe && e.target.id !== 'swipeL' && e.target.id !== 'swipeR' ) $( '#menu-settings' ).click();
} ).taphold( function() {
	if ( GUI.swipe ) return
	
	location.reload();
} );
$( '#swipeL' ).click( function() {
	var page = GUI.playback ? 'library' : ( GUI.library ? 'playlist' : 'playback' );
	$( '#tab-'+ page ).click();
} );
$( '#swipeR' ).click( function() {
	var page = GUI.playback ? 'playlist' : ( GUI.library ? 'playback' : 'library' );
	$( '#tab-'+ page ).click();
} );
$( '#page-playback' ).tap( function( e ) {
	if ( $( '.edit' ).length ) {
		if ( $( e.target ).hasClass( 'edit' ) ) return
		
		$( '.edit' ).remove();
		$( '#cover-art' ).css( 'opacity', '' );
		return
	}
	
	if ( $( e.target ).is( '.controls, .timemap, .covermap, .volmap' ) ) return
	
	$( '.controls' ).addClass( 'hide' );
	$( '.controls1, .rs-tooltip, #imode' ).removeClass( 'hide' );
	$( '#swipebar' ).addClass( 'transparent' );
} );
$( '#page-library' ).tap( function( e ) {
	var $target = $( e.target );
	if ( GUI.bookmarkedit
		&& !$target.closest( '.home-bookmark' ).length
		&& !$target.closest( '.coverart' ).length
	) {
		GUI.bookmarkedit = 0;
		$( '.edit' ).remove();
		$( '.home-bookmark' ).find( '.fa-bookmark, .bklabel, img' ).css( 'opacity', '' );
		$( '.coverart img' ).css( 'opacity', '' );
	}
} );
$( '#page-library, #page-playback, #page-playlist' ).click( function( e ) {
	if ( [ 'coverTR', 'timeTR' ].indexOf( e.target.id ) === -1 ) $( '#settings' ).addClass( 'hide' );
} );
$( '#menu-top, #menu-bottom, #settings' ).click( function( e ) {
	if ( e.target.id !== 'menu-settings' && e.target.id !== 'badge' ) $( '#settings' ).addClass( 'hide' );
	$( '.controls' ).addClass( 'hide' );
	$( '.controls1, .rs-tooltip, #imode' ).removeClass( 'hide' );
	$( '#swipebar' ).addClass( 'transparent' );
} );
$( '#menu-bottom' ).taphold( function() {
	location.reload();
} );
$( '#db-entries, #pl-entries, #pl-editor' ).on( 'click', 'p', function() {
	$( '.menu' ).addClass( 'hide' );
	$( '#db-entries li, #pl-editor li' ).removeClass( 'active' );
	$( '#pl-entries li' ).removeClass( 'lifocus' );
	$( '.pl-remove' ).remove();
} );
$( '.home-block' ).click( function() {
	$( '#db-search-close' ).click();
} );
$( '#infoCheckBox' ).on( 'click', 'label', function() { // playback tools
	var $time = $( '#infoCheckBox input[name=time]' );
	var $volume = $( '#infoCheckBox input[name=volume]' );
	var $coverlarge = $( '#infoCheckBox input[name=coverlarge]' );
	var name = $( this ).find( 'input' ).prop( 'name' );
	if ( name === 'time' || name === 'volume' ) {
		if ( !$time.is( ':checked' ) && !$volume.is( ':checked' ) ) {
			$coverlarge.prop( 'checked', true );
			disableCheckbox( 'buttons', 0, 0 );
		} else if ( $time.is( ':checked' ) && $volume.is( ':checked' ) ) {
			$coverlarge.prop( 'checked', false );
		} else if ( $time.is( ':checked' ) || $volume.is( ':checked' ) ) {
			disableCheckbox( 'buttons', 1 );
		}
	} else if ( name === 'bars' ) {
		if ( $( '#infoCheckBox input[name=bars]' ).prop( 'checked' ) === true ) {
			disableCheckbox( 'barsauto', 1 );
		} else {
			disableCheckbox( 'barsauto', 0, 0 );
		}
	}
} );
// PLAYBACK /////////////////////////////////////////////////////////////////////////////////////
$( '#song, #playlist-warning' ).on( 'click', 'i', function() {
	$( '#tab-library' ).click();
} );
$( '#artist, #bio-open' ).click( function() {
	if ( GUI.status.ext === 'radio' ) return
	
	if ( $( '#bio legend' ).text() != GUI.status.Artist ) {
		getBio( GUI.status.Artist );
	} else {
		$( '#menu-top, #menu-bottom, #loader' ).addClass( 'hide' );
		$( '#bio' ).removeClass( 'hide' );
	}
} );
$( '#album' ).click( function() {
	if ( GUI.status.ext !== 'radio'&& location.hostname !== 'localhost' ) window.open( 'https://www.last.fm/music/'+ GUI.status.Artist +'/'+ GUI.status.Album, '_blank' );
} );
$( '#time' ).roundSlider( {
	  sliderType  : 'min-range'
	, max         : 1000
	, radius      : 115
	, width       : 20
	, startAngle  : 90
	, endAngle    : 450
	, showTooltip : false
	, create      : function ( e ) {
		$timeRS = this;
	}
	, change      : function( e ) { // not fire on 'setValue'
		if ( GUI.status.ext === 'radio' ) {
			$timeRS.setValue( 0 );
		} else {
			mpdSeek( Math.floor( e.value / 1000 * GUI.status.Time ) );
		}
	}
	, start       : function () {
		if ( GUI.status.ext === 'radio' ) return
		
		clearInterval( GUI.intKnob );
		clearInterval( GUI.intElapsed );
	}
	, drag        : function ( e ) { // drag with no transition by default
		if ( GUI.status.ext === 'radio' ) return
		
		$( '#elapsed' ).text( second2HMS( Math.round( e.value / 1000 * GUI.status.Time ) ) );
	}
	, stop        : function( e ) { // on 'stop drag'
		if ( GUI.status.ext === 'radio' ) return
		
		mpdSeek( Math.round( e.value / 1000 * GUI.status.Time ) );
	}
} );
$( '#volume' ).roundSlider( {
	  sliderType: 'default'
	, radius          : 115
	, width           : 50
	, handleSize      : '-25'
	, startAngle      : -50
	, endAngle        : 230
	, editableTooltip : false
	, create          : function () { // maintain shadow angle of handle
		$volumeRS = this;
		$volumetransition = $( '#volume' ).find( '.rs-animation, .rs-transition' );
		$volumetooltip = $( '#volume' ).find( '.rs-tooltip' );
		$volumehandle = $( '#volume' ).find( '.rs-handle' );
		$volumehandle.addClass( 'rs-transition' ).eq( 0 )           // make it rotate with 'rs-transition'
			.rsRotate( - this._handle1.angle );                     // initial rotate
		$( '.rs-transition' ).css( 'transition-property', 'none' ); // disable animation on load
	}
	, change          : function( e ) { // (not fire on 'setValue' ) value after click or 'stop drag'
		$( e.handle.element ).rsRotate( - e.handle.angle );
		// value before 'change'
		if ( e.preValue === 0 ) unmuteColor();
		if ( GUI.local ) return
		
		GUI.local = 1;
		setTimeout( function() { GUI.local = 0 }, 500 );
		$.post( 'enhance.php', { volume: e.value } );
	}
	, start           : function( e ) { // on 'start drag'
		// restore handle color immediately on start drag
		if ( e.value === 0 ) unmuteColor(); // value before 'start drag'
	}
	, drag            : function ( e ) { // drag with no transition by default
		if ( e.value % 2 === 0 ) {
			GUI.local = 1; // cleared by 'change'
			$.post( 'enhance.php', { mpc: 'mpc volume '+ e.value } );
			$( e.handle.element ).rsRotate( - e.handle.angle );
		}
	}
} );
$( '#volmute, #volM' ).click( function() {
	var vol = $volumeRS.getValue();
	if ( vol ) {
		$volumeRS.setValue( 0 );
		$volumehandle.rsRotate( - $volumeRS._handle1.angle );
		muteColor( vol );
		GUI.display.volumemute = vol;
	} else {
		$volumeRS.setValue( GUI.display.volumemute );
		$volumehandle.rsRotate( - $volumeRS._handle1.angle );
		unmuteColor();
		GUI.display.volumemute = 0;
	}
	$.post( 'enhance.php', { volume: 'setmute' } );
} );
$( '#volup, #voldn' ).click( function() {
	var thisid = this.id;
	var vol = $volumeRS.getValue();
	if ( ( vol === 0 && ( thisid === 'voldn' ) ) || ( vol === 100 && ( thisid === 'volup' ) ) ) return

	vol = ( thisid === 'volup' ) ? vol + 1 : vol - 1;
	$volumeRS.setValue( vol );
	$.post( 'enhance.php', { volume: vol } );
} );
$( '#coverTL' ).click( function() {
	if ( $( '#cover-art' ).hasClass( 'vu' ) ) return
	
	if ( !$( '#controls-cover' ).hasClass( 'hide' ) ) {
		$( '.controls' ).addClass( 'hide' );
		$( '.controls1, .rs-tooltip, #imode' ).removeClass( 'hide' );
		$( '#swipebar' ).toggleClass( 'transparent' );
	}
	$.post( 'enhancestatus.php', { statusonly: 1 }, function( status ) {
		$.each( status, function( key, value ) {
			GUI.status[ key ] = value;
		} );
		if ( GUI.status.ext === 'radio' && GUI.status.state === 'stop' ) GUI.status.Title = '';
		var coverlarge = GUI.display.coverlarge;
		var time = GUI.display.time;
		var volume = GUI.display.volume;
		var buttons = GUI.display.buttons;
		GUI.display.coverlarge = $( '#divcover' ).hasClass( 'coversmall' ) ? 'checked' : '';
		var radio = $( '#album' ).text().slice( 0, 4 ) === 'http';
		if ( GUI.display.volumempd ) {
			if ( !$( '#time-knob' ).hasClass( 'hide' ) && !$( '#volume-knob' ).hasClass( 'hide' ) ) {
				if ( GUI.display.volume && GUI.display.time ) {
					if ( !radio ) GUI.display.coverlarge = 'checked';
					GUI.display.time = '';
					GUI.display.volume = '';
					GUI.display.buttons = '';
				} else {
					if ( !radio ) GUI.display.coverlarge = coverlarge;
					GUI.display.time = time;
					GUI.display.volume = volume;
				}
			} else if ( $( '#time-knob' ).hasClass( 'hide' ) && $( '#volume-knob' ).hasClass( 'hide' ) ) {
				if ( GUI.display.time || GUI.display.volume ) {
					if ( !radio ) GUI.display.coverlarge = coverlarge;
					GUI.display.time = time;
					GUI.display.volume = volume;
				} else {
					GUI.display.coverlarge = '';
					GUI.display.time = 'checked';
					GUI.display.volume = 'checked';
				}
			} else {
				if ( GUI.display.volume && GUI.display.time ) {
					GUI.display.time = 'checked';
					GUI.display.volume = 'checked';
				} else {
					if ( !radio ) GUI.display.coverlarge = 'checked';
					GUI.display.time = '';
					GUI.display.volume = '';
					GUI.display.buttons = '';
				}
			}
		} else {
			if ( !$( '#time-knob' ).hasClass( 'hide' ) ) {
				if ( !radio ) GUI.display.coverlarge = 'checked';
				GUI.display.time = '';
				GUI.display.buttons = '';
			} else {
				if ( !radio ) GUI.display.coverlarge = coverlarge;
				GUI.display.time = 'checked';
			}
		}
		renderPlayback();
		displayPlayback();
		setButton();
		if ( window.innerWidth < 500 ) $( '#format-bitrate' ).css( 'display', GUI.display.time ? 'inline' : 'block' );
		GUI.display.coverlarge = coverlarge;
		GUI.display.time = time;
		GUI.display.volume = volume;
		GUI.display.buttons = buttons;
	}, 'json' );
} );
var btnctrl = {
//	  timeTL  : ''
	  timeT   : 'guide'
	, timeTR  : 'menu'
	, timeL   : 'previous'
	, timeM   : 'play'
	, timeR   : 'next'
	, timeBL  : 'random'
	, timeB   : 'stop'
	, timeBR  : 'repeat'
//	, coverTL : ''
	, coverT  : 'guide'
	, coverTR : 'menu'
	, coverL  : 'previous'
	, coverM  : 'play'
	, coverR  : 'next'
	, coverBL : 'random'
	, coverB  : 'stop'
	, coverBR : 'repeat'
	, volT    : 'volup'
	, volL    : 'voldn'
	, volM    : 'volumemute'
	, volR    : 'volup'
	, volB    : 'voldn'
}
$( '.covermap' ).taphold( function( e ) {
	$( '#cover-art' )
		.css( 'opacity', 0.33 )
		.after(
			 '<i class="edit licover-remove fa fa-minus-circle"></i>'
			+'<i class="edit licover-cover fa fa-coverart"></i>'
		);
} );
$( '#divcover' ).on( 'click', '.edit', function() {
	var $img = $( '#cover-art' );
	var album = GUI.status.Album;
	var artist = GUI.status.Artist;
	var path = '/mnt/MPD/'+ GUI.status.file.substr( 0, GUI.status.file.lastIndexOf( '/' ) );
	var fn = $( this ).hasClass( 'licover-remove' ) ? removeCoverart : replaceCoverart;
	fn( $img, album, artist, path );
} );
$( '.timemap, .covermap, .volmap' ).tap( function() {
	var cmd = btnctrl[ this.id ];
	if ( GUI.display.cover
		&& $( this ).hasClass( 'timemap' )
		|| !cmd
		|| $( '.edit' ).length
	) return
	
	if ( cmd === 'guide' ) {
		$( '#controls-cover, #controls-vol, .rs-tooltip, #imode' ).toggleClass( 'hide' );
		if ( !GUI.display.cover ) $( '#controls-time, .controls1' ).toggleClass( 'hide' );
		if ( !GUI.bars ) $( '#swipebar' ).toggleClass( 'transparent' );
		return
	} else if ( cmd === 'menu' ) {
		$( '#menu-settings' ).click();
	} else if ( cmd === 'random' ) {
		$( '#random' ).click();
	} else if ( cmd === 'repeat' ) {
		if ( GUI.status.repeat ) {
			if ( GUI.status.single ) {
				$.post( 'enhance.php', { mpc: [ 'mpc repeat 0', 'mpc single 0' ] } );
			} else {
				$( '#single' ).click();
			}
		} else {
			$( '#repeat' ).click();
		}
	} else if ( cmd === 'play' ) {
		if ( GUI.status.state === 'play' ) {
			GUI.status.ext === 'radio' ? $( '#stop' ).click() : $( '#pause' ).click();
		} else {
			$( '#play' ).click();
		}
	} else {
		$( '#'+ cmd ).click();
	}
} );
$( '.btn-cmd' ).click( function() {
	var $this = $( this );
	var cmd = this.id;
	if ( $this.hasClass( 'btn-toggle' ) ) {
		if ( cmd === 'random' && $this.hasClass( 'ashuffle' ) ) {
			$.post( 'enhance.php', { bash: '/usr/bin/killall ashuffle &' } );
			$this.removeClass( 'btn-primary ashuffle' );
			return
		}
		
		var onoff = GUI.status[ cmd ] ? 0 : 1;
		GUI.status[ cmd ] = onoff;
		command = 'mpc '+ cmd +' '+ onoff;
	} else {
		if ( GUI.status.ext === 'radio' && cmd === 'pause' ) cmd = 'stop';
		if ( GUI.bars ) {
			$( '#playback-controls .btn-cmd' ).removeClass( 'btn-primary' );
			$this.addClass( 'btn-primary' );
		}
		if ( cmd === 'stop' ) {
			command = 'mpc stop';
			$( '#pl-entries .elapsed' ).empty();
		} else if ( cmd === 'previous' || cmd === 'next' ) {
			// enable previous / next while stop
			var current = GUI.status.song + 1;
			var last = GUI.status.playlistlength;
			if ( GUI.status.random === 1 ) {
				// improve: repeat pattern of mpd random
				var pos = Math.floor( Math.random() * last ); // Math.floor( Math.random() * ( max - min + 1 ) ) + min;
				if ( pos === current ) pos = ( pos === last ) ? pos - 1 : pos + 1; // avoid same pos ( no pos-- or pos++ in ternary )
			} else {
				if ( cmd === 'previous' ) {
					var pos = current !== 1 ? current - 1 : last;
				} else {
					var pos = current !== last ? current + 1 : 1;
				}
			}
			pos = pos || 1;
			command = GUI.status.state === 'play' ? 'mpc play '+ pos : [ 'mpc play '+ pos, 'mpc stop' ];
		} else {
			command = ( GUI.status.ext === 'radio' && GUI.status.state === 'play' ) ? 'mpc stop' : 'mpc toggle';
		}
	}
	$.post( 'enhance.php', { mpc: command } );
} );
$( '#timeTL' ).click( function() {
	var active = GUI.status.activePlayer;
	info( {
		  icon    : 'source'
		, title   : 'Playback Source'
		, radio   : {
			  '<i class="fa fa-mpd"></i>MPD'         : 'mpd'
			, '<i class="fa fa-spotify"></i>Spotify' : 'spotify'
			, '<i class="fa fa-airplay"></i>Airplay' : 'airplay'
			, '<i class="fa fa-dlna"></i>DLNA'       : 'dlna'
		}
		, checked : active === 'MPD' ? 0 : ( active === 'spotify' ? 1 : ( active === 'airplay' ? 2 : 3 ) )
		, cancel  : 1
		, ok      : function() {
			var source = $( '#infoRadio input[ type=radio ]:checked' ).val();
			if ( source === 'mpd' ) {
				$.post( 'enhance.php', { bash: '/usr/bin/systemctl restart shairport' } );
			} else if ( source === 'spotify' ) {
				$.post( 'enhance.php', { bash: '/usr/bin/redis-cli hget spotify enable' }, function( data ) {
					if ( !data ) notify( 'Spotify not enabled', 'Enable in Settings menu', 'fa fa-warning' );
				} );
			}
		}
	} );
} );
$( '#share' ).click( function() {
	info( {
		  icon        : 'share'
		, title       : 'Sharing'
		, message     : 'Share this track:'
		, buttonwidth : 1
		, cancellabel : '<i class="fa fa-facebook"></i>Facebook'
		, cancelcolor : '#4267b2'
		, cancel  : function() {
			windowopen( 'https://www.facebook.com/sharer.php?u=http%3A%2F%2Fwww.runeaudio.com%2F&display=popup' );
		}
		, oklabel     : '<i class="fa fa-twitter"></i>Twitter'
		, ok          : function() {
			windowopen( 'https://twitter.com/home?status=Listening+to+' + GUI.status.Title.replace( /\s+/g, '+' ) +'+by+'+ GUI.status.Artist.replace( /\s+/g, '+' ) +'+on+%40RuneAudio+http%3A%2F%2Fwww.runeaudio.com%2F+%23nowplaying' );
		}
	} );
} );
$( '#biocontent' ).delegate( '.biosimilar', 'click', function() {
	getBio( $( this ).find( 'p' ).text() )
} );
$( '#closebio' ).click( function() {
	$( '#bio' ).addClass( 'hide' );
	displayTopBottom();
} );
// LIBRARY /////////////////////////////////////////////////////////////////////////////////////
$( '#db-home' ).click( function() {
	$( '#tab-library' ).click();
	$( '.menu' ).addClass( 'hide' );
} );
$( '#db-currentpath' ).on( 'click', 'a', function() {
	if ( $( '#db-currentpath span a' ).length === 1 || GUI.plugin ) return
	
	var rootpath = this.id === 'rootpath';
	if ( [ 'album', 'artist', 'albumartist', 'composer', 'genre' ].indexOf( GUI.browsemode ) !== -1 && !rootpath ) return
	
	if ( rootpath ) {
		GUI.dbbackdata = [];
		var path = $( this ).data( 'path' );
	} else {
		var path = $( this ).find( '.lipath' ).text();
	}
	// get scroll position for back navigation
	var currentpath =  $( '#db-currentpath' ).find( '.lipath' ).text();
	GUI.dbscrolltop[ currentpath ] = $( window ).scrollTop();
	mutationLibrary.observe( observerLibrary, observerOption );
	
	var path2mode = {
		  Album       : 'album'
		, Artist      : 'artist'
		, AlbumArtist : 'albumartist'
		, Composer    : 'composer'
		, Genre       : 'genre'
		, Dirble      : 'Dirble'
	}
	getData( { browsemode: path2mode[ path ], path: path } );
} );
$( '#db-webradio-new' ).click( function() {
	webRadioNew();
} );
$( '#db-searchbtn' ).click( function() { // icon
	$( '#db-currentpath span, #db-back, #db-searchbtn' ).addClass( 'hide' );
	$( '#db-search-close, #db-search, #dbsearchbtn' ).removeClass( 'hide' );
	$( '#db-currentpath' ).css( 'max-width', '40px' );
	$( '#db-search-keyword' ).focus();
} );
$( '#dbsearchbtn' ).click( function() { // search
	var keyword = $( '#db-search-keyword' ).val();
	if ( !keyword ) {
		$( '#db-search-close' ).click();
	} else {
		GUI.dblist = 1;
		getData( {
			  cmd : 'search'
			, arg : keyword
		} );
	}
} );
$( '#db-search-close' ).click( function() {
	GUI.keyword = '';
	$( '#db-search-close, #db-search, #dbsearchbtn' ).addClass( 'hide' );
	$( '#db-currentpath span, #db-searchbtn' ).removeClass( 'hide' );
	$( '#db-currentpath' ).css( 'max-width', '' );
	$( '#db-search-close span' ).empty();
	if ( $( '#db-currentpath .lipath').text() ) $( '#db-back' ).removeClass( 'hide' );
	if ( !$( '#db-search-keyword' ).val() ) return
	
	$( '#db-search-keyword' ).val( '' );
	var path = $( '#db-currentpath .lipath:last').text();
	if ( !path ) {
		$( '#db-entries' ).empty();
		$( '#home-blocks' ).removeClass( 'hide' );
		return
	}
	
	if ( GUI.dbbackdata.length ) {
		var data = GUI.dbbackdata.pop();
		GUI.dbbackdata.pop();
	} else {
		var data = { path: path };
	}
	getData( data );
	mutationLibrary.observe( observerLibrary, observerOption );
} );
$( '#db-search-keyword' ).keydown( function( e ) {
	if ( e.key === 'Enter' ) $( '#dbsearchbtn' ).click();
} );
// MutationObserver - watch for '#db-entries' content changed then scroll to previous position
var MutationObserver = window.MutationObserver || window.WebKitMutationObserver;
var observerOption = { childList: true };
var observerLibrary = document.getElementById( 'db-entries' );
var mutationLibrary = new MutationObserver( function() { // on observed target changed
	var lipath = $( '#db-currentpath' ).find( '.lipath' ).text();
	if ( !$( '#divcoverarts' ).hasClass( 'hide' ) ) {
		$( 'html, body' ).scrollTop( GUI.cvscrolltop );
	} else {
		var scrollpos = GUI.dbscrolltop[ lipath ];
		$( 'html, body' ).scrollTop( scrollpos ? scrollpos : 0 );
	}
	mutationLibrary.disconnect();
} );
$( '#db-back' ).click( function() {
	$( '.menu' ).addClass( 'hide' );
	mutationLibrary.observe( observerLibrary, observerOption ); // standard js - must be one on one element
	// topmost of path
	if ( GUI.dbbrowsemode === 'file' || !GUI.dbbackdata.length ) {
		if ( $( '#db-currentpath span a' ).length === 1 ) {
			renderLibrary();
		} else {
			$( '#db-currentpath a:nth-last-child( 2 )' ).click();
		}
		return
		
	} else if ( GUI.dbbrowsemode === 'coverart' ) {
		var currentpath =  $( '#db-currentpath' ).find( '.lipath' ).text();
		GUI.dbscrolltop[ currentpath ] = $( window ).scrollTop();
		GUI.dbbackdata = [];
		var index = $( '#indexcover' ).data().index;
		index.forEach( function( index ) {
			$( '#db-index .index-'+ index ).removeClass( 'gr' );
		} );
		$( '#divcoverarts, #db-index' ).removeClass( 'hide' );
		$( '#db-entries' ).empty();
		return
	}
	
	GUI.artistalbum = '';
	GUI.dbbackdata.pop();
	if ( !GUI.dbbackdata.length ) {
		renderLibrary();
		return
	}
	
	getData( GUI.dbbackdata.pop() );
} );
$( '#home-blocks' ).contextmenu( function( e ) { // disable default image context menu
	e.preventDefault();
} );
$( '.home-block' ).click( function() {
	var id = this.id;
	if ( id === 'home-coverart' || $( this ).hasClass( 'home-bookmark' ) ) return
	
	if ( id === 'home-usb' && !$( '#home-usb gr' ).text() ) {
		location.href = '/sources';
		return
	} else if ( id === 'home-nas' && !$( '#home-nas gr' ).text() ) {
		location.href = '/sources/add';
		return
	} else if ( id === 'home-webradio' && !$( '#home-webradio gr' ).text() ) {
		webRadioNew();
		return
	}

	var $this = $( this );
	var path = $this.find( '.lipath' ).text();
	var name = $this.find( '.bklabel' ).text();
	GUI.plugin = $this.data( 'plugin' );
	if ( id === 'home-spotify' && GUI.status.activePlayer !== 'Spotify' ) {
		$( '#timeTL' ).click();
	} else {
		GUI.dblist = 1;
		mutationLibrary.observe( observerLibrary, observerOption );
		var browsemode = $this.data( 'browsemode' );
		GUI.dbbrowsemode = browsemode ? browsemode : GUI.plugin ? GUI.plugin : 'file';
		getData( {
			  browsemode : browsemode
			, path       : path
			, plugin     : GUI.plugin
		} );
	}
} );

$( '#infoFileBox' ).change( function() {
	var filename = this.files[ 0 ].name;
	var reader = new FileReader();    // create filereader
	reader.onload = function ( e ) {  // prepare onload callback
		var base64img = e.target.result;
		var img = new Image();
		img.src = base64img;
		img.onload = function () {
			var imgW = img.width;
			var imgH = img.height;
			var coverart = GUI.playback || $( '#db-entries li' ).length;
			var imgWHhtml = '<div class="imagewh"><span>Current</span><span>'+ imgW +' x '+ imgH +'</span>';
			$( '#infoFilename' ).empty();
			$( '.newimg, .imagewh, .bkname' ).remove();
			if ( !coverart ) {
				var px = 200;
			} else {
				if ( imgW > 1000 || imgH > 1000 ) {
					var px = 1000;
				} else {
					var px = imgW < imgH ? imgW : imgH;
				}
			}
			if ( imgW === px && imgH === px ) {
				$( '#infoMessage' ).append( '<img class="newimg" src="'+ base64img +'">'+ imgWHhtml +'</div>' );
			} else {
				imgWHhtml += '<div>(Resized to '+ px +' x '+ px +' px)</div></div>';
				var picacanvas = document.createElement( 'canvas' ); // create canvas object
				picacanvas.width = picacanvas.height = px; // size of resized image
				window.pica.resizeCanvas( img, picacanvas, picaOption, function() {
					var resizedimg = picacanvas.toDataURL( 'image/jpeg', 0.9 ); // canvas -> base64 (jpg, qualtity)
					$( '#infoMessage' ).append( '<img class="newimg" src="'+ resizedimg +'">'+ imgWHhtml );
				} );
			}
		}
	}
	reader.readAsDataURL( this.files[ 0 ] ); // load filereader
} );
$( '#home-blocks' ).on( 'tap', '.home-bookmark', function( e ) { // delegate - id changed on renamed
	if ( $( '.edit' ).length && !$( e.target ).hasClass( 'edit' )  ) {
		$( '.edit' ).remove();
		$( '.home-bookmark' ).find( '.fa-bookmark, .bklabel, img' ).css( 'opacity', '' );
		return
	}
	
	var $target = $( e.target );
	var $this = $( this );
	var path = $this.find( '.lipath' ).text();
	var name = $this.find( '.bklabel' ).text() || '';
	if ( $target.is( '.home-block-edit' ) ) {
		bookmarkRename( name, path, $this );
	} else if ( $target.is( '.home-block-cover' ) ) {
		if ( $this.find( 'img' ).length ) {
			var icon = '<img src="'+ $this.find( 'img' ).prop( 'src' ) +'">'
					  +'<span class="bkname"><br>'+ name +'<span>';
		} else {
			var icon = '<div class="infobookmark"><i class="fa fa-bookmark"></i><br><span class="bklabel">'+ $this.find( '.bklabel' ).text() +'</span></div>';
		}
		info( {
			  icon        : 'bookmark'
			, title       : 'Change Bookmark Thumbnail'
			, message     : icon
			, msgalign    : 'center'
			, fileoklabel : 'Replace'
			, cancel      : 1
			, ok          : function() {
				var bookmarkname = path.replace( /\//g, '|' );
				var newimg = $( '#infoMessage .newimg' ).attr( 'src' );
				$.post( 'enhance.php', { imagefile: bookmarkname, base64bookmark: newimg }, function() {
					var $img = $this.find( 'img' );
					if ( $img.length ) {
						$img.attr( 'src', newimg  );
					} else {
						$this.find( '.fa-bookmark' ).remove();
						$this.find( '.divbklabel' ).remove();
						$this.find( '.lipath' ).after( '<img class="bkcoverart" src="'+ newimg +'">' );
						$( '.home-bookmark img' ).css( 'opacity', 0.33 );
					}
				} );
			}
		} );
	} else if ( $target.is( '.home-block-remove' ) ) {
		bookmarkDelete( path, name, $this );
	} else {
		GUI.dblist = 1;
		GUI.dbbrowsemode = 'file';
		getData( {
			  browsemode : 'file'
			, path       : path
		} );
	}
} ).on( 'taphold', '.home-bookmark', function() {
	if ( GUI.drag ) return
	
	GUI.bookmarkedit = 1;
	GUI.bklabel = $( this ).find( '.bklabel' );
	$( '.home-bookmark' ).each( function() {
		$this = $( this );
		var buttonhtml = '<i class="edit home-block-remove fa fa-minus-circle"></i>'
						+'<i class="edit home-block-cover fa fa-coverart"></i>';
		if ( !$this.find( 'img' ).length ) buttonhtml += '<i class="edit home-block-edit fa fa-edit-circle"></i>'
		$this.append( buttonhtml )
	} );
	$( '.home-bookmark' ).find( '.fa-bookmark, .bklabel, img' ).css( 'opacity', 0.33 );
} );
var sortablelibrary = new Sortable( document.getElementById( 'divhomeblocks' ), {
	  ghostClass : 'db-sortable-ghost'
	, delay      : 400
	, onStart    : function( e ) {
		GUI.drag = 1;
		var pos = $( e.item ).offset();
		posX = pos.left;
		posY = pos.top;
	  }
	, onMove    : function( e, oe ) {
		if ( GUI.bookmarkedit ) {
			if ( Math.abs( oe.clientX - posX ) > 5 || Math.abs( oe.clientY - posY ) > 5 ) {
				GUI.bookmarkedit = 0;
				$( '.edit' ).remove();
				$( '.home-bookmark' ).find( '.fa-bookmark, .bklabel, img' ).css( 'opacity', '' );
			}
		}
	  }
	, onEnd      : function() {
		GUI.drag = 0;
	  }
	, onUpdate   : function () {
		var $blocks = $( '.home-block' );
		var order = '';
		$blocks.each( function() {
			order += $( this ).find( '.lipath' ).text() +'^^';
		} );
		order = order.slice( 0, -2 );
		GUI.display.order = order.split( '^^' );
		$.post( 'enhance.php', { setorder: order } );
	}
} );
$( '#home-coverart' ).click( function() { // fix - 'tap' also fire .coverart click here
	if ( !$( '#divcoverarts' ).html() ) {
		info( {
			  icon     : 'coverart'
			, title    : 'Browse By CoverArt'
			, message  : 'Create thumbnails before use:<br>'
						+'<br><i class="fa fa-usbdrive"></i>or <i class="fa fa-network"></i> <w>></w>'
						+'&ensp;<i class="fa fa-folder"></i> <w>></w>'
						+'&ensp;<i class="fa fa-coverart"></i><w>Update thumbnails</w>'
		} );
		return
	}
	
	GUI.dbbrowsemode = 'coverart';
	$( '#db-currentpath span' ).html( '<i class="fa fa-coverart"></i> <a>COVERART</a>' );
	$( '#db-currentpath .lipath' ).text( 'coverart' );
	$( '#home-blocks' ).addClass( 'hide' );
	$( '#divcoverarts, #db-back, #db-index' ).removeClass( 'hide' );
	$( '#db-index li' ).not( ':eq( 0 )' ).addClass( 'gr' );
	var index = $( '#indexcover' ).data().index;
	index.forEach( function( index ) {
		$( '#db-index .index-'+ index ).removeClass( 'gr' );
	} );
	displayIndexBar();
	setTimeout( function() {
		var cH = window.innerHeight - $( '.coverart' ).height() - 94;
		$( '#divcoverarts p' ).css( 'height', cH +'px' );
	}, 50 );
} );
$( '.coverart' ).tap( function( e ) {
	if ( $( e.target ).hasClass( 'edit' ) ) return
	
	if ( $( '.edit' ).length ) {
		$( '.coverart img' ).css( 'opacity', '' );
		$( '.edit' ).remove();
		return
	}
	
	mutationLibrary.observe( observerLibrary, observerOption ); // standard js - must be one on one element
	GUI.cvscrolltop = $( window ).scrollTop();
	$this = $( this );
	$lipath = $this.find( '.lipath' );
	if ( $lipath.length ) {
		GUI.dbbrowsemode = 'file';
		getData( {
			  browsemode : 'file'
			, path       : $lipath.text()
		} );
	} else {
		if ( GUI.display.thumbbyartist ) {
			var album = $this.find( '.coverart2' ).text()
			var artist = $this.find( '.coverart1' ).text()
		} else {
			var album = $this.find( '.coverart1' ).text()
			var artist = $this.find( '.coverart2' ).text()
		}
		getData( {
			  path       : album
			, artist     : artist
			, browsemode : 'coverart'
		} );
	}
} ).taphold( function() {
	GUI.bookmarkedit = 1;
	$( '.coverart img' ).css( 'opacity', '' );
	$( '.edit' ).remove();
	$( '.coverart div' ).append(
		 '<i class="edit coverart-remove fa fa-minus-circle"></i>'
		+'<i class="edit coverart-cover fa fa-coverart"></i>'
	);
	$( '.coverart img' ).css( 'opacity', 0.33 );
} );
$( '#divcoverarts' ).on( 'tap', '.coverart-remove', function() {
	var $this = $( this );
	var imgsrc = $this.parent().find( 'img' ).prop( 'src' );
	var $album = $this.parent().next();
	var album = $album.text();
	var artist = $album.next().text();
	var thumbfile = '/srv/http/img/coverarts/'+ album +'^^'+ artist +'.jpg'
	info( {
		  icon     : 'coverart'
		, title    : 'Remove Thumbnail'
		, message  : '<img src="'+ imgsrc +'">'
					+'<br><wh>'+ album +'</wh>'
					+'<br>'+ artist
		, msgalign : 'center'
		, cancel   : 1
		, oklabel  : 'Remove'
		, ok       : function() {
			$this.parent().parent().remove();
			$.post( 'enhance.php', { imagefile: thumbfile }, function( std ) {
				if ( std == 13 ) {
					info( {
						  icon    : 'coverart'
						, message : '<i class="fa fa-warning"></i>Delete file denied.'
								   +'Set directory+file <w>permission</w> and try again.'
					} );
				}
			} );
		}
	} );
} );
$( '#divcoverarts' ).on( 'tap', '.coverart-cover', function() {
	var $this = $( this );
	var $img = $this.parent().find( 'img' );
	var imgsrc = $img.data( 'src' );
	var thumbfile = imgsrc.slice( 0, -14 ) + imgsrc.slice( -3 ); // remove cache busting timestamp
	info( {
		  icon        : 'coverart'
		, title       : 'Change Thumbnail'
		, message     : '<img src="'+ imgsrc +'">'
		, msgalign    : 'center'
		, fileoklabel : 'Replace'
		, cancel      : 1
		, ok          : function() {
			var newimg = $( '#infoMessage .newimg' ).attr( 'src' );
			$.post( 'enhance.php', { imagefile: thumbfile, base64: newimg }, function( std ) {
				if ( std == 0 ) {
					$img
						.removeAttr( 'data-src' ) // lazyload 'data-src'
						.attr( 'src', newimg );
				} else if ( std == 13 ) {
					info( {
						  icon    : 'coverart'
						, message : '<i class="fa fa-warning"></i>Replace file denied.'
								   +'Set directory+file <w>permission</w> and try again.'
					} );
				}
			} );
		}
	} );
} );
$( '#db-entries' ).on( 'tap', '.edit',  function() {
	var $this = $( this );
	var $img = $this.siblings( 'img' );
	var $thisli = $this.parent().parent();
	var album = $thisli.find( '.lialbum' ).text();
	var artist = $thisli.find( '.bioartist' ).text();
	var lipath = $thisli.next().find( '.lipath' ).text();
	var path = '/mnt/MPD/'+ lipath.substr( 0, lipath.lastIndexOf( '/' ) );
	var fn = $this.hasClass( 'licover-remove' ) ? removeCoverart : replaceCoverart;
	fn( $img, album, artist, path );
} );
$( '#db-entries' ).on( 'taphold', '.licoverimg',  function() {
	$( this ).parent().removeClass( 'active' );
	$( '#context-menu-album' ).addClass( 'hide' );
	$this = $( this );
	var btnhtml = '<i class="edit licover-cover fa fa-coverart"></i>';
	if ( !$this.hasClass( 'nocover' ) ) btnhtml += '<i class="edit licover-remove fa fa-minus-circle"></i>';
	$this.append( btnhtml );
	$this.find( 'img' ).css( 'opacity', '0.33' );
} ).on( 'tap', 'li', function( e ) {
	var $this = $( this );
	var $target = $( e.target )
	if ( $target.hasClass( 'edit' ) ) return
	
	if ( $( '.edit' ).length ) {
		$( '.edit' ).remove();
		$( '.licoverimg img' ).css( 'opacity', '' );
		if ( $( this ).is( '.licover' ) ) return
	}
	
	var islast = $this.find( '.fa-music' ).length + $this.find( '.fa-webradio' ).length + $this.find( '.radiothumb' ).length;
	if ( $this.index() === 0 && $target.is( '.bioartist, .fa-artist, .fa-albumartist, .biocomposer, .fa-composer' ) ) {
		var name = ( $target.is( '.biocomposer, .fa-composer' ) ) ? $this.find( '.biocomposer' ).text() : $this.find( '.bioartist' ).text();
		getBio( name );
		return
	} else if ( $target.hasClass( 'lialbum' ) ) {
		window.open( 'https://www.last.fm/music/'+ $this.find( '.bioartist' ).text() +'/'+ $this.find( '.lialbum' ).text(), '_blank' );
		return
	} else if ( islast || $target.data( 'target' ) ) {
		dbContextmenu( $this, $target );
		return
	}
	
	$( '.menu' ).addClass( 'hide' );
	// get file list in 'artist', 'composer', 'genre' mode (non-album)
	if ( $this.hasClass( 'licover' ) && GUI.dbbackdata.length ) {
		if ( [ 'artist', 'composer', 'genre' ].indexOf( GUI.dbbackdata[ 0 ].browsemode ) !== -1 ) {
			GUI.filelist = '';
			$( '#db-entries li .lipath' ).slice( 1 ).each( function() {
				var path = $( this ).text();
				GUI.filelist += '"'+ path.replace( /"/g, '\"' ) +'" ';
			} );
		}
	}
	var path = $this.find( '.lipath' ).text();
	// get scroll position for back navigation
	var currentpath = $( '#db-currentpath' ).find( '.lipath' ).text();
	GUI.dbscrolltop[ currentpath ] = $( window ).scrollTop();
	mutationLibrary.observe( observerLibrary, observerOption );
	$( '#db-entries li' ).removeClass( 'active' );
	$this.addClass( 'active' );
	if ( ( GUI.browsemode === 'artist' && currentpath !== 'Artist' )
		|| ( GUI.browsemode === 'albumartist' && currentpath !== 'AlbumArtist' )
	) {
		var artist = currentpath;
	} else if ( GUI.browsemode === 'album' || GUI.browsemode === 'genre' ) {
		var artist = $this.find( '.liartist' ).text() || '';
	} else {
		var artist = '';
	}
	var mode = $this.attr( 'mode' );
	if ( [ 'dirble', 'jamendo', 'spotify' ].indexOf( mode ) === -1 ) {
		getData( {
			  path       : path
			, artist     : artist
			, browsemode : mode ? mode : 'file'
		} );
		return
	}
	
	if ( $this.attr( 'mode' ) === 'spotify' ) {
		getData( {
			  querytype : 'tracks'
			, path      : GUI.currentpath +'/'+ $this.find( 'span' ).text()
			, args      : path.toString()
		} );
		GUI.plugin = 'Spotify';
	} else if ( $this.attr( 'mode' ) === 'dirble' ) {
		getData( {
			  querytype : $this.hasClass( 'db-dirble-child' ) ? 'stations' : 'childs'
			, path      : GUI.currentpath +'/'+ $this.find( 'span' ).text() // for d--currentpath
			, args      : path
		} );
		GUI.plugin = 'Dirble';
	} else if ( $this.attr( 'mode' ) === 'jamendo' ) {
/*		getData( {
			  querytype : 'radio'
			, path      : GUI.currentpath +'/'+ $this.find( 'span' ).text()
			, args      : path
		} );
		GUI.plugin = 'Jamendo';*/
	}
} );
$( '#db-index li' ).click( function() {
	var $this = $( this );
	if ( $this.hasClass( 'gr' ) ) return
	
	var index = $this.text();
	if ( index === '#' ) {
		$( 'html, body' ).scrollTop( 0 );
		return
	}
	
	var $el = $( '#divcoverarts' ).hasClass( 'hide' ) ? $( '#db-entries li' ) : $( '.coverart' );
	$el.each( function() {
		if ( $( this ).find( '.lisort' ).text().charAt( 0 ) === index ) {
			$( 'html, body' ).scrollTop( this.offsetTop - ( GUI.bars ? 80 : 40 ) );
			return false
		}
	} );
} );
// PLAYLIST /////////////////////////////////////////////////////////////////////////////////////
$( '#pl-home' ).click( function() {
	$( '#tab-playlist' ).click();
} );
$( '#pl-currentpath' ).on( 'click', '.plsback', function() {
	$( '.menu' ).addClass( 'hide' );
	$( '#plopen' ).click();
} );
$( '#pl-currentpath' ).on( 'click', '.plsbackroot', function() {
	$( '#tab-playlist' ).click();
} );
$( '#plopen' ).click( function() {
	if ( !GUI.lsplaylists.length ) return
	
	$( '.playlist, #pl-searchbtn, #context-menu-plaction' ).addClass( 'hide' );
	$( '#context-menu-plaction' ).addClass( 'hide' );
	$( '#loader' ).removeClass( 'hide' );
	
	var plL = GUI.lsplaylists.length - 1; // less index
	var plcounthtml = '<wh><i class="fa fa-microsd"></i></wh><bl>PLAYLIST</bl>';
	plcounthtml += plL ? '<gr>&ensp;·&emsp;</gr> <wh id="pls-count">'+ numFormat( plL ) +'</wh>&ensp;<i class="fa fa-list-ul"></i>' : '';
	$( '#pl-currentpath' ).html( plcounthtml +'<i class="fa fa-arrow-left plsbackroot"></i>' );
	$( '#pl-currentpath, #pl-editor, #pl-index' ).removeClass( 'hide' );
	renderLsPlaylists( GUI.lsplaylists );
} );
$( '#plsave' ).click( function() {
	if ( !GUI.status.playlistlength ) return
	
	playlistNew();
} );
$( '#plcrop' ).click( function() {
	info( {
		   title   : 'Crop Playlist'
		 , message : 'Clear this playlist except current song?'
		, cancel   : 1
		, ok       : function() {
			$.post( 'enhance.php', { mpc: GUI.status.state === 'stop' ? 'mpc play; mpc crop; mpc stop' : 'mpc crop' } );
		}
	} );
} );
$( '#plclear' ).click( function() {
	if ( $( '#pl-entries .pl-remove' ).length ) {
		$( '#pl-entries .pl-remove' ).remove();
		return
	}
	
	info( {
		  title       : 'Remove From Playlist'
		, message     : 'Selective remove / Clear all :'
		, cancellabel : 'Select'
		, cancel  : function() {
			$( '#pl-entries .li1' ).before( '<i class="fa fa-minus-circle pl-remove"></i>' );
		}
		, oklabel    : 'All'
		, ok         : function() {
			clearPlaylist();
		}
	} );
} );
$( '#pl-filter' ).keyup( function() {
	var keyword = $( this ).val();
	var count = 0;
	$( '#pl-entries li' ).each( function() {
		var $this = $( this );
		var match = ( $this.text().search( new RegExp( keyword, 'i' ) ) !== -1 ) ? true : false;
		count = match ? ( count + 1 ) : count;
		$this.toggleClass( 'hide', !match );
	} );
	if ( keyword ) {
		$( '#pl-search-close span' ).html( count +' <a>of</a> ' );
	} else {
		$( '#pl-search-close span' ).empty();
	}
} );
$( '#pl-search-close, #plsearchbtn' ).click( function() {
	$( '#pl-search-close, #pl-search, #plsearchbtn' ).addClass( 'hide' );
	$( '#pl-count, #pl-manage, #pl-searchbtn, #pl-entries li' ).removeClass( 'hide' );
	$( '#pl-filter' ).val( '' );
} );
$( '#pl-searchbtn' ).click( function() {
	$( '#pl-search-close, #pl-search, #plsearchbtn' ).removeClass( 'hide' );
	$( '#pl-count, #pl-manage, #pl-searchbtn' ).addClass( 'hide' );
	$( '#pl-filter' ).focus();
} );
var sortableplaylist = new Sortable( document.getElementById( 'pl-entries' ), {
	  ghostClass : 'pl-sortable-ghost'
	, delay      : 400
	, onUpdate   : function ( e ) {
		if ( $( e.from ).hasClass( 'active' ) ) {
			$( e.to ).removeClass( 'active' );
			$( e.item ).addClass( 'active' )
			GUI.status.Pos = $( e.item ).index();
			GUI.status.song = GUI.status.Pos;
		}
		GUI.sortable = 1;
		setTimeout( function() { GUI.sortable = 0 }, 500 );
		
		$.post( 'enhance.php', { mpc: 'mpc move '+ ( e.oldIndex + 1 ) +' '+ ( e.newIndex + 1 ) } );
	}
} );
var sortableplaylist = new Sortable( document.getElementById( 'pl-editor' ), {
	  ghostClass : 'pl-sortable-ghost'
	, delay      : 400
	, onUpdate   : function ( e ) {
		if ( !$( '#pl-currentpath .lipath' ).length ) return
		GUI.sortable = 1;
		setTimeout( function() { GUI.sortable = 0 }, 500 );
		
		var plname = $( '#pl-currentpath .lipath' ).text();
		var newindex = e.oldIndex > e.newIndex ? e.newIndex : e.newIndex + 1;
		$.post( 'enhance.php', {
			bash: '/usr/bin/printf "%s\n" "'+ ( e.oldIndex + 1 ) +'m'+ newindex +'" "wq" '
				 +'| /usr/bin/sudo /usr/bin/ex -s "/var/lib/mpd/playlists/'+ plname +'.m3u"'
		} );
	}
} );
$( '#pl-entries, #pl-editor' ).on( 'swipeleft', 'li', function() {
	GUI.swipe = 1;
	GUI.swipepl = 1; // suppress .page swipe
	setTimeout( function() {
		GUI.swipe = 0;
		GUI.swipepl = 0;
	}, 500 );
	$( '#tab-library' ).click();
} ).on( 'swiperight', 'li', function() {
	GUI.swipe = 1;
	GUI.swipepl = 1;
	setTimeout( function() {
		GUI.swipe = 0;
		GUI.swipepl = 0;
	}, 500 );
	$( '#tab-playback' ).click();
} );
$( '#pl-entries' ).on( 'click', 'li', function( e ) {
	$target = $( e.target );
	$plremove = $target.hasClass( 'pl-remove' );
	if ( !$plremove && $( '.pl-remove' ).length ) {
		$( '.pl-remove' ).remove();
		return
	}
	
	if ( GUI.swipe || $target.hasClass( 'pl-icon' ) || $plremove ) return
	
	var $this = $( this );
	var songpos = $this.index() + 1;
	$( '#context-menu-plaction' ).addClass( 'hide' );
	var state = GUI.status.state;
	if ( state == 'stop' ) {
		$.post( 'enhance.php', { mpc: 'mpc play '+ songpos } );
	} else {
		if ( $this.hasClass( 'active' ) ) {
			if ( state == 'play' ) {
				$this.hasClass( 'webradio' ) ? $( '#stop' ).click() : $( '#play' ).click();
			} else {
				$( '#play' ).click();
			}
		} else {
			$.post( 'enhance.php', { mpc: 'mpc play '+ songpos } );
		}
	}
} );
$( '#pl-entries' ).on( 'click', '.pl-icon', function( e ) {
	var $this = $( this );
	var $thisli = $this.parent();
	GUI.list = {};
	GUI.list.li = $thisli;
	GUI.list.path = $thisli.find( '.lipath' ).text().trim();
	GUI.list.name = $thisli.find( '.name' ).html().trim();
	GUI.list.thumb = $thisli.find( '.lithumb' ).text() || '';  // dirble save in contextmenu
	GUI.list.img = $thisli.find( '.liimg' ).text() || '';      // dirble save in contextmenu
	var menutop = ( $thisli.position().top + 49 ) +'px';
	var $contextmenu = $( '#context-menu-plaction' );
	var $contextlist = $( '#context-menu-plaction a' );
	$( '#pl-entries li' ).removeClass( 'lifocus' );
	if ( !$contextmenu.hasClass( 'hide' ) 
		&& $contextmenu.css( 'top' ) === menutop
	) {
		$contextmenu.addClass( 'hide' );
		return
	}
	
	var state = GUI.status.state;
	$thisli.addClass( 'lifocus' );
	$contextlist.removeClass( 'hide' );
	if ( $thisli.hasClass( 'active' ) ) {
		$contextlist.eq( 0 ).toggleClass( 'hide', state === 'play' );
		$contextlist.eq( 1 ).toggleClass( 'hide', state !== 'play' || $( e.target ).hasClass( 'fa-webradio' ) );
		$contextlist.eq( 2 ).toggleClass( 'hide', state === 'stop' );
	} else {
		$contextlist.eq( 1 ).add( $contextlist.eq( 2 ) ).addClass( 'hide' );
	}
	if ( $this.hasClass( 'fa-webradio' ) && $thisli.find( '.unsaved' ).length ) {
		$contextlist.eq( 3 ).removeClass( 'hide' );
	} else {
		$contextlist.eq( 3 ).addClass( 'hide' );
	}
	var contextnum = $contextmenu.find( 'a:not(.hide)' ).length;
	$( '.menushadow' ).css( 'height', contextnum * 41 - 1 );
	$contextmenu
		.removeClass( 'hide' )
		.css( 'top', menutop );
	var targetB = $contextmenu.offset().top + $contextmenu.height();
	var wH = window.innerHeight;
	if ( targetB > wH - ( GUI.bars ? 80 : 40 ) + $( window ).scrollTop() ) $( 'html, body' ).animate( { scrollTop: targetB - wH + 42 } );
} );
$( '#pl-entries' ).on( 'click', '.pl-remove', function() { // remove from playlist
	removeFromPlaylist( $( this ).parent() );
} );
$( '#pl-editor' ).on( 'click', 'li', function( e ) {
	if ( GUI.swipe ) return
	
	var $this = $( this );
	if ( GUI.plappend ) {
		$.post( 'enhance.php', { plappend: GUI.plappend, plfile: $this.find( '.lipath' ).text() }, function() {
			renderSavedPlaylist( $this.find( 'span' ).text() );
			$( 'html, body' ).animate( { scrollTop: window.innerHeight / 2 } );
			GUI.plappend = '';
		} );
		return
	}
	
	var $target = $( e.target );
	if ( $target.hasClass( 'pl-icon' ) || !$this.find( '.fa-list-ul' ).length ) {
		plContextmenu( $this, $target );
	} else {
		renderSavedPlaylist( $this.find( 'span' ).text() );
	}
} );
$( '#pl-index li' ).click( function() {
	var $this = $( this );
	if ( $this.hasClass( 'gr' ) ) return
	
	var index = $this.text();
	if ( index === '#' ) {
		$( 'html, body' ).scrollTop( 0 );
		return
	}
	
	$( '#pl-editor li' ).each( function() {
		if ( $( this ).find( '.lisort' ).text().charAt( 0 ) === index ) {
			$( 'html, body' ).scrollTop( this.offsetTop - ( GUI.bars ? 80 : 40 ) );
			return false
		}
	} );
} );

} ); // document ready end <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

document.addEventListener( 'visibilitychange', function() {
	if ( document.hidden ) {
		clearInterval( GUI.intKnob );
		clearInterval( GUI.intElapsed );
		clearInterval( GUI.intElapsedPl );
	} else {
		var libraryhome = $( '#home-blocks' ).hasClass( 'hide' );
		$.post( 'enhance.php', { getdisplay: 1, data: 1 }, function( data ) {
			GUI.display = data;
			$.post( 'enhance.php', { getbookmarks: 1 }, function( bookmarks ) {
				if ( !libraryhome ) renderLibraryBlocks( bookmarks );
			}, 'json' );
		}, 'json' );
		if ( GUI.playback ) {
			getPlaybackStatus();
		} else if ( GUI.library ) {
			if ( !libraryhome ) renderLibrary();
		} else {
			displayTopBottom();
			if ( GUI.pleditor ) {
				$( '#pl-currentpath .lipath' ).text() ? renderSavedPlaylist( $( '#pl-currentpath .lipath' ).text() ) : $( '#plopen' ).click();
			} else {
				setPlaylistScroll();
			}
		}
	}
} );
window.addEventListener( 'orientationchange', function() {
	if ( GUI.playback ) {
		$( '#playback-row' ).addClass( 'hide' );
		setTimeout( function() {
			if ( $( '.playback-block.hide' ).length && window.innerHeight > 420 ) $( '#page-playback, #playback-row' ).removeAttr( 'style' );
			displayPlayback()
			scrollLongText();
			$( '#playback-row' ).removeClass( 'hide' );
		}, 300 );
	} else if ( GUI.playlist && !GUI.pleditor ) {
		setTimeout( function() {
			setNameWidth();
			getTitleWidth();
			setTitleWidth();
			var scrollpos = $( '#pl-entries li.active' ).offset().top - $( '#pl-entries' ).offset().top - ( 49 * 3 );
			$( 'html, body' ).scrollTop( scrollpos );
		}, 300 );
	} else if ( GUI.dblist || GUI.pleditor  || !$( '#divcoverarts' ).hasClass( 'hide' ) ) {
		displayIndexBar();
	}
} );

var pushstreams = {};
var streams = [ 'bookmark', 'display', 'idle', 'notify', 'playlist', 'volume', 'webradio' ];
streams.forEach( function( stream ) {
	pushstreams[ stream ] = new PushStream( { modes: 'websocket' } );
	pushstreams[ stream ].addChannel( stream );
} );

pushstreams.bookmark.onmessage = function( data ) {
	var bookmarks = data[ 0 ];
	if ( GUI.bookmarkedit || !bookmarks.length ) return
		
	clearTimeout( GUI.debounce );
	GUI.debounce = setTimeout( function() {
		renderLibraryBlocks( bookmarks );
	}, GUI.debouncems );
}
pushstreams.display.onmessage = function( data ) {
	var data = data[ 0 ];
	if ( typeof data !== 'object' ) return
	
	$.each( data, function( key, val ) {
		GUI.display[ key ] = val;
	} );
	if ( data.order && $( '#home-blocks' ).hasClass( 'hide' ) ) return
	
	clearTimeout( GUI.debounce );
	GUI.debounce = setTimeout( function() {
		if ( GUI.playback ) {
			getPlaybackStatus();
		} else if ( GUI.library ) {
			if ( !$( '#home-blocks' ).hasClass( 'hide' ) ) {
				renderLibrary();
			} else {
				if ( GUI.display.coverfile ) {
					if ( !$( '.licover' ).length ) $( '#db-currentpath a:last-child' ).click();
				} else {
					$( '.licover' ).remove();
				}
			}
		} else {
			displayTopBottom();
		}
	}, GUI.debouncems );
}
var timeoutUpdate;
pushstreams.idle.onmessage = function( changed ) {
	var changed = changed[ 0 ];
	clearTimeout( GUI.debounce );
	GUI.debounce = setTimeout( function() {
		if ( changed === 'player' ) { // on track changed
				getPlaybackStatus();
				if ( GUI.playlist && !GUI.pleditor ) setPlaylistScroll();
		} else if ( changed === 'playlist' ) { // on playlist changed
			if ( GUI.pleditor || GUI.contextmenu || $( '#pl-entries .pl-remove' ).length ) return
			
			$.post( 'enhance.php', { getplaylist: 1 }, function( data ) {
				if ( data.playlist.length ) {
					GUI.status.playlistlength = data.playlist.length;
					GUI.lsplaylists = data.lsplaylists || [];
					GUI.pllist = data.playlist;
				} else {
					GUI.status.playlistlength = 0;
				}
				if ( GUI.playlist ) {
					renderPlaylist();
				} else if ( GUI.playback ) {
					getPlaybackStatus();
				}
			}, 'json' );
		} else if ( changed === 'options' ) { // on mode toggled
			$.post( 'enhancestatus.php', { statusonly: 1 }, function( status ) {
				$.each( status, function( key, value ) {
					GUI.status[ key ] = value;
				} );
				if ( GUI.playback ) setButtonToggle();
			}, 'json' );
		} else if ( changed === 'update' ) {
			$.post( 'enhance.php', { getcount: 1 }, function( data ) {
				$( '.home-block gr' ).remove();
				$.each( data, function( id, val ) {
					if ( val ) $( '#home-'+ id ).find( 'i' ).after( '<gr>'+ numFormat( val ) +'</gr>' );
				} );
			}, 'json' );
			if ( $( '#db-currentpath .lipath' ).text() === 'Webradio' ) return;
			
			$.post( 'enhancestatus.php', { statusonly: 1 }, function( status ) {
				if ( status.updating_db ) {
					GUI.status.updating_db = 1;
				} else {
					clearInterval( GUI.intUpdate );
					GUI.status.updating_db = 0;
					notify( 'Update Database', 'Database updated.' );
				}
			}, 'json' );
		} else if ( changed === 'database' ) { // on files changed (for webradio rename)
			if ( $( '#db-currentpath .lipath' ).text() === 'Webradio' ) $( '#home-webradio' ).tap();
		}
	}, GUI.debouncems );
}
pushstreams.notify.onmessage = function( data ) {
	var notify = data[ 0 ];
	new PNotify( {
		  icon        : notify.icon
		, title       : notify.title || 'Info'
		, text        : notify.text
	} );
}
pushstreams.playlist.onmessage = function( data ) {
	GUI.lsplaylists = data[ 0 ] || [];
	if ( !GUI.playlist ) return
	
	if ( !$( '#pl-entries' ).hasClass( 'hide' ) || !GUI.lsplaylists.length ) {
		renderPlaylist();
	} else {
		$( '#plopen' ).click();
	}
}
pushstreams.volume.onmessage = function( data ) {
	var data = data[ 0 ];
	clearTimeout( GUI.debounce );
	GUI.debounce = setTimeout( function() {
		var vol = data[ 0 ];
		var volumemute = data[ 1 ];
		$volumeRS.setValue( vol );
		$volumehandle.rsRotate( - $volumeRS._handle1.angle );
		volumemute ? muteColor( volumemute ) : unmuteColor();
	}, GUI.debouncems );
}
pushstreams.webradio.onmessage = function( data ) {
	var data = data[ 0 ];
	var count = Number( $( '#home-webradio gr' ).text() );
	count = count + data;
	$( '#home-webradio gr' ).text( count ? numFormat( count ) : '' );
	if ( $( '#db-currentpath .lipath' ).text() === 'Webradio' ) $( '#home-webradio' ).click();
	if ( GUI.playlist && !GUI.pleditor ) $( '#tab-playlist' ).click();
}
streams.forEach( function( stream ) {
	pushstreams[ stream ].connect();
} );
