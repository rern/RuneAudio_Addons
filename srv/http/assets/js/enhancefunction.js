var psOption = {
	  host: window.location.hostname
	, port: window.location.port
	, modes: 'websocket'
};
var pushstreams = {};
var streams = [ 'display', 'volume', 'library', 'playlist', 'idle', 'notify' ];
$.each( streams, function( i, stream ) {
	pushstreams[ stream ] = new PushStream( psOption );
	pushstreams[ stream ].addChannel( stream );
} );

pushstreams.display.onmessage = function( data ) {
	if ( typeof data[ 0 ] === 'object' ) GUI.display = data[ 0 ];
	if ( GUI.local ) return
	
	if ( !$( '#page-playback' ).hasClass( 'hide' ) ) {
		getPlaybackStatus();
	} else if ( !$( '#page-library' ).hasClass( 'hide' ) ) {
		if ( !GUI.local ) renderLibrary();
	} else {
		displayTopBottom();
	}
}
pushstreams.volume.onmessage = function( data ) {
	if ( GUI.local ) return
	
	var data = data[ 0 ];
	var vol = data[ 0 ];
	var volumemute = data[ 1 ];
	$volumeRS.setValue( vol );
	$volumehandle.rsRotate( - $volumeRS._handle1.angle );
	volumemute ? muteColor( volumemute ) : unmuteColor();
}
pushstreams.library.onmessage = function( data ) {
	GUI.libraryhome = data[ 0 ];
	if ( !$( '#home-blocks' ).hasClass( 'hide' ) && !GUI.local && !GUI.bookmarkedit ) renderLibrary();
}
pushstreams.playlist.onmessage = function( data ) {
	GUI.lsplaylists = data[ 0 ] || [];
	if ( $( '#page-playlist' ).hasClass( 'hide' ) ) return
	
	if ( !$( '#pl-entries' ).hasClass( 'hide' ) || !GUI.lsplaylists.length ) {
		renderPlaylist();
	} else {
		$( '#plopen' ).click();
	}
}
var timeoutUpdate;
pushstreams.idle.onmessage = function( changed ) {
	var changed = changed[ 0 ];
	if ( changed === 'player' ) { // on track changed
		if ( !$( '#page-playlist' ).hasClass( 'hide' ) ) {
			if ( !GUI.pleditor ) setPlaylistScroll();
		} else {
			if ( !GUI.player ) {
				GUI.player = 1;
				getPlaybackStatus();
				setTimeout( function() { GUI.player = 0 }, 500 );
			}
		}
	} else if ( changed === 'playlist' ) { // on playlist changed
		if ( GUI.pleditor || GUI.local ) return
		
		if ( !$( '#page-playlist' ).hasClass( 'hide' ) ) {
			$.post( 'enhance.php', { getplaylist: 1 }, function( data ) {
				if ( !data ) {
					GUI.status.playlistlength = 0;
					renderPlaylist();
					return
				}
				GUI.lsplaylists = data.lsplaylists || [];
				GUI.playlist = data.playlist;
				renderPlaylist();
			}, 'json' );
		} else if ( !$( '#page-playback' ).hasClass( 'hide' ) ) {
			getPlaybackStatus();
		}
	} else if ( changed === 'options' ) { // on mode toggled
		if ( GUI.local ) return
		
		$.post( 'enhancestatus.php', { statusonly: 1 }, function( status ) {
			$.each( status, function( key, value ) {
				GUI.status[ key ] = value;
			} );
			setButtonToggle();
		}, 'json' );
	} else if ( changed === 'update' ) {
		if ( !$( '#home-blocks' ).hasClass( 'hide' ) ) {
			$.post( 'enhance.php', { library: 1 } );
			renderLibrary();
			return
		}
		if ( $( '#db-currentpath .lipath' ).text() === 'Webradio' ) return;
		
		clearTimeout( timeoutUpdate );
		timeoutUpdate = setTimeout( function() { // skip on brief update
			$.post( 'enhancestatus.php', { statusonly: 1 }, function( status ) {
				if ( status.updating_db ) {
					GUI.status.updating_db = 1;
					setButtonUpdate();
				} else {
					new PNotify( {
						  title : 'Update Database'
						, text  : 'Database updated.'
					} );
				}
			}, 'json' );
		}, 3000 );
	} else if ( changed === 'database' ) { // on files changed (for webradio rename)
		if ( $( '#db-currentpath .lipath' ).text() === 'Webradio' ) $( '#home-webradio' ).click();
	}
}
pushstreams.notify.onmessage = function( data ) {
	var notify = data[ 0 ];
	new PNotify( {
		  icon        : notify.icon || 'fa fa-check'
		, title       : notify.title || 'Info'
		, text        : notify.text
	} );
}

$.each( streams, function( i, stream ) {
	pushstreams[ stream ].connect();
} );

function setButtonToggle() {
	if ( GUI.local ) return
	
	var timehide = $( '#time-knob' ).hasClass( 'hide' );
	if ( GUI.display.buttons && !$( '#play-group' ).hasClass( 'hide' ) ) {
		$( '#irandom' ).addClass( 'hide' )
		$( '#irepeat' ).attr( 'class', 'fa hide' );
		if ( timehide ) {
			$( '#posrandom' ).toggleClass( 'hide', GUI.status.random === 0 );
			$( '#posrepeat' ).attr( 'class', GUI.status.repeat ? ( GUI.status.single ? 'fa fa-repeat-single' : 'fa fa-repeat' ) : 'fa hide' );
		} else {
			$( '#random' ).toggleClass( 'btn-primary', GUI.status.random === 1 );
			$( '#repeat' ).toggleClass( 'btn-primary', GUI.status.repeat === 1 );
			$( '#single' ).toggleClass( 'btn-primary', GUI.status.single === 1 );
			$( '#posrandom' ).addClass( 'hide' );
			$( '#posrepeat' ).attr( 'class', 'fa hide' );
		}
	} else {
		if ( timehide ) {
			$( '#irandom' ).addClass( 'hide' )
			$( '#irepeat' ).attr( 'class', 'fa hide' );
			if ( GUI.status.playlistlength ) {
				$( '#posrandom' ).toggleClass( 'hide', GUI.status.random === 0 );
				$( '#posrepeat' ).attr( 'class', GUI.status.repeat ? ( GUI.status.single ? 'fa fa-repeat-single' : 'fa fa-repeat' ) : 'fa hide' );
			}
		} else {
			$( '#posrandom' ).addClass( 'hide' )
			$( '#posrepeat' ).attr( 'class', 'fa hide' );
			$( '#irandom' ).toggleClass( 'hide', GUI.status.random === 0 );
			$( '#irepeat' ).attr( 'class', GUI.status.repeat ? ( GUI.status.single ? 'fa fa-repeat-single' : 'fa fa-repeat' ) : 'fa hide' );
		}
	}
	if ( GUI.display.update ) {
		if ( GUI.display.bars ) {
			$( '#badge' ).text( GUI.display.update ).removeClass( 'hide' );
			$( '#iaddons' ).addClass( 'hide' );
		} else {
			if ( timehide ) {
				$( '#posaddons' ).removeClass( 'hide' );
				$( '#iaddons' ).addClass( 'hide' );
			} else {
				$( '#posaddons' ).addClass( 'hide' );
				$( '#iaddons' ).removeClass( 'hide' );
			}
		}
	} else {
		$( '#badge' ).empty();
		$( '#badge, #posaddons, #iaddons' ).addClass( 'hide' );
	}
}
function setButtonUpdate() {
	if ( GUI.status.updating_db ) {
		$( '#tab-library i, #db-home i' ).addClass( 'blink' );
		if ( !GUI.display.bars ) {
			if ( $( '#time-knob' ).hasClass( 'hide' ) ) {
				$( '#posupdate' ).removeClass( 'hide' );
				$( '#iupdate' ).addClass( 'hide' );
			} else {
				$( '#posupdate' ).addClass( 'hide' );
				$( '#iupdate' ).removeClass( 'hide' );
			}
		}
	} else {
		$( '#tab-library i, #db-home i' ).removeClass( 'blink' );
		$( '#posupdate, #iupdate' ).addClass( 'hide' );
	}
}
function setButton() {
	$( '#playback-controls' ).toggleClass( 'hide', GUI.status.playlistlength === 0 );
	var state = GUI.status.state;
	if ( GUI.display.bars ) {
		$( '#stop' ).toggleClass( 'btn-primary', state === 'stop' );
		$( '#play' ).toggleClass( 'btn-primary', state === 'play' );
		$( '#pause' ).toggleClass( 'btn-primary', state === 'pause' );
	}
	setTimeout( function() {
		setButtonToggle();
		setButtonUpdate();
	}, 100 );
}
function numFormat( num ) {
	return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
function HMS2Second( HMS ) {
	var hhmmss = HMS.split( ':' ).reverse();
	if ( !hhmmss[ 1 ] ) return +hhmmss[ 0 ];
	if ( !hhmmss[ 2 ] ) return +hhmmss[ 0 ] + hhmmss[ 1 ] * 60;
	return +hhmmss[ 0 ] + hhmmss[ 1 ] * 60 + hhmmss[ 2 ] * 3600;
}
function second2HMS( second ) {
	if ( second <= 0 ) return 0;
	
	var second = Math.round( second );
	var hh = Math.floor( second / 3600 );
	var mm = Math.floor( ( second % 3600 ) / 60 );
	var ss = second % 60;
	
	hh = hh ? hh +':' : '';
	mm = hh ? ( mm > 9 ? mm +':' : '0'+ mm +':' ) : ( mm ? mm +':' : '' );
	ss = mm ? ( ss > 9 ? ss : '0'+ ss ) : ss;
	return ss ? hh + mm + ss : '';
}
function scrollLongText() {
	setTimeout( function() {
		$( '#divartist, #divsong, #divalbum' ).each( function() {
			var $this = $( this );
			$this.toggleClass( 'scroll-left', $this.find( 'span' ).width() > window.innerWidth * 0.98 );
		} );
	}, 300 );
}
function removeSplash() {
	$( '#splash' ).remove();
	$( '.rs-animation .rs-transition' ).css( 'transition-property', '' ); // restore animation after load
}
function setPlaybackBlank() {
	$( '#playback-controls' ).addClass( 'hide' );
	$( '#divartist, #divsong, #divalbum' ).removeClass( 'scroll-left' );
	$( '#song' ).html( '<i class="fa fa-plus-circle"></i>' );
	$( '#divpos i' ).addClass( 'hide' );
	$( '#artist, #album, #songposition, #timepos, #elapsed, #total' ).empty();
	$( '#format-bitrate' ).text( 'Add music from Library' );
	$( '#cover-art' )
		.attr( 'src', $( '#cover' ).val() )
		.css( 'border-radius', 0 )
		.one( 'load', removeSplash );
	$( '#coverartoverlay' ).addClass( 'hide' );
	if ( GUI.display.time ) $( '#time' ).roundSlider( 'setValue', 0 );
}
function renderPlayback() {
	if ( $( '#splash' ).length ) { // in case too long to get coverart
		setTimeout( function() {
			$( '#splash' ).remove();
			$( '.rs-animation .rs-transition' ).css( 'transition-property', '' ); // restore animation after load
		}, 2000 );
	}
	var status = GUI.status;
	// song and album before update for song/album change detection
	var previoussong = $( '#song' ).text();
	var previousalbum = $( '#album' ).text();
	// volume
	$volumeRS.setValue( status.volume );
	$volumehandle.rsRotate( - $volumeRS._handle1.angle );
	if ( GUI.display.volume && GUI.display.volumempd ) {
		status.volumemute != 0 ? muteColor( status.volumemute ) : unmuteColor();
	}
	clearInterval( GUI.intKnob );
	clearInterval( GUI.intElapsed );
	// empty queue
	if ( !status.playlistlength ) {
		setPlaybackBlank();
		return
	}
	
	$( '.playback-controls' ).css( 'visibility', 'visible' );
	$( '#artist' ).html( status.Artist );
	$( '#song' ).html( status.Title );
	$( '#album' ).html( status.ext !== 'radio' ? status.Album : '<gr>'+ status.Album +'</gr>' ).promise().done( function() {
		scrollLongText();
	} );
	
	$( '#songposition' ).text( ( +status.song + 1 ) +'/'+ status.playlistlength );
	var ext = ( status.ext !== 'radio' ) ? '<wh> • </wh>' + status.ext : '';
	if ( !GUI.display.time ) {
		var dot = '';
	} else {
		var dot = '<wh id="dot0"> • </wh>';
		$( '#divpos, #format-bitrate' ).css( 'display', window.innerWidth < 500 ? 'inline' : '' );
	}
	$( '#format-bitrate' ).html( dot + status.sampling + ext );
	if ( status.ext === 'radio' ) {
		var radiosrc = $( '#cover-art' ).attr( 'src' );
		var vu = $( '#vu' ).val();
		var vustop = $( '#vustop' ).val();
		if ( status.state === 'play' ) {
			if ( radiosrc !== vu ) $( '#cover-art' ).attr( 'src', vu );
			$( '#elapsed' ).html( status.state === 'play' ? blinkdot : '' );
			var elapsed = status.elapsed;
			if ( GUI.display.time ) {
				$( '#timepos' ).empty();
				$( '#time' ).roundSlider( 'setValue', 0 );
				if ( !GUI.display.radioelapsed ) {
					$( '#total' ).empty();
				} else {
					GUI.intElapsed = setInterval( function() {
						elapsed++;
						elapsedhms = second2HMS( elapsed );
						$( '#total' ).text( elapsedhms ).css( 'color', '#587ca0' );
					}, 1000 );
				}
			} else {
				$( '#total' ).empty();
				if ( GUI.display.radioelapsed ) {
					GUI.intElapsed = setInterval( function() {
						elapsed++;
						elapsedhms = second2HMS( elapsed );
					$( '#timepos' ).html( '&ensp;<i class="fa fa-play"></i>&ensp;'+ elapsedhms );
					}, 1000 );
				} else {
					$( '#timepos' ).empty();
				}
			}
		} else {
			if ( radiosrc !== vustop ) $( '#cover-art' ).attr( 'src', vustop );
			$( '#elapsed, #total, #timepos' ).empty();
		}
		$( '#cover-art' )
			.css( 'border-radius', '18px' )
			.one( 'load', removeSplash );
		$( '#coverartoverlay' ).removeClass( 'hide' );
		return
	}
	
	$( '#cover-art' ).css( 'border-radius', '' );
	$( '#coverartoverlay' ).addClass( 'hide' );
	if ( status.Album !== previousalbum || !status.Album ) {
		var coverart = status.coverart || $( '#cover' ).val();
		$( '#cover-art' )
			.attr( 'src', coverart )
			.css( 'border-radius', 0 )
			.one( 'load', removeSplash );
	}
	// time
	time = status.Time;
	var timehms = second2HMS( time );
	$( '#total' ).text( timehms );
	// stop <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
	if ( status.state === 'stop' ) {
		$( '#song' ).css( 'color', '' );
		if ( GUI.display.time ) {
			$( '#time' ).roundSlider( 'setValue', 0 );
			$( '#elapsed' ).text( timehms ).css( 'color', '#587ca0' );
			$( '#total, #timepos' ).empty();
		} else {
			$( '#timepos' ).html( '&ensp;<i class="fa fa-stop"></i>&ensp;'+ timehms );
		}
		return
	}
	
	$( '#elapsed, #total' ).css( 'color', '' );
	$( '#song' ).css( 'color', status.state === 'pause' ? '#587ca0' : '' );
	var elapsed = status.elapsed || 0;
	var elapsedhms = second2HMS( elapsed );
	if ( !elapsedhms ) $( '#elapsed' ).empty();
	var position = Math.round( elapsed / time * 1000 );
	// pause <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
	if ( status.state === 'pause' ) {
		if ( GUI.display.time ) {
			$( '#time' ).roundSlider( 'setValue', position );
			$( '#elapsed' ).text( elapsedhms );
			$( '#elapsed' ).css( 'color', '#0095d8' );
			$( '#total' ).css( 'color', '#e0e7ee' );
			$( '#timepos' ).empty();
		} else {
			$( '#timepos' ).html( '&ensp;<i class="fa fa-pause"></i>&ensp;<bl>'+ elapsedhms +'</bl> / '+ timehms );
		}
		return
	}
	
	$( '#elapsed, #total' ).css( 'color', '' );
	if ( GUI.display.time ) {
		GUI.intElapsed = setInterval( function() {
			elapsed++;
			elapsedhms = second2HMS( elapsed );
			$( '#elapsed' ).text( elapsedhms );
		}, 1000 );
		GUI.intKnob = setInterval( function() {
			position++;
			if ( position === 1000 ) {
				clearInterval( GUI.intKnob );
				clearInterval( GUI.intElapsed );
				$( '#elapsed' ).empty();
			}
			$( '#time' ).roundSlider( 'setValue', position );
		}, time );
	} else {
		GUI.intElapsed = setInterval( function() {
			elapsed++;
			elapsedhms = second2HMS( elapsed );
			$( '#timepos' ).html( '&ensp;<i class="fa fa-play"></i>&ensp;<wh>'+ elapsedhms +'</wh> / '+ timehms );
		}, 1000 );
	}

	// playlist current song ( and lyrics if installed )
	if ( status.Title !== previoussong || status.Album !== previousalbum ) {
		if ( !$( '#page-playlist' ).hasClass( 'hide' ) && !GUI.pleditor ) setPlaylistScroll();
		if ( $( '#lyricscontainer' ).length && $( '#lyricscontainer' ).is( ':visible' ) )  getlyrics();
	}
}

function getPlaybackStatus() {
	//if ( GUI.local ) return; // suppress 2nd firing from 'pushstreams.idle.onmessage'
	if ( !$( '#page-playlist' ).hasClass( 'hide' ) ) {
		setPlaylistScroll();
		return
	}
	
	$.post( 'enhancestatus.php', { artist: $( '#artist' ).text(), album: $( '#album' ).text() }, function( status ) {
		// 'gpio off' > audio output switched > restarts mpd which makes status briefly unavailable
		if( typeof status !== 'object' ) return
		
		if ( status.activePlayer === 'Airplay' ) {
			displayAirPlay();
			return
		}
		
		$.each( status, function( key, value ) {
			GUI.status[ key ] = value;
		} );
		setButton();
		renderPlayback();
		// imodedelay fix imode flashing on audio output switched
		if ( !GUI.imodedelay ) displayPlayback();
	}, 'json' );
}

function setPageCurrent( panel ) {
	if ( !$( '#page-library' ).hasClass( 'hide' ) && $( '#home-blocks' ).hasClass( 'hide' ) ) {
		var path = $( '#db-currentpath .lipath' ).text();
		if ( path ) GUI.dbscrolltop[ path ] = $( window ).scrollTop();
	} else if ( !$( '#page-playlist' ).hasClass( 'hide' ) && GUI.pleditor ) {
		GUI.plscrolltop = $( window ).scrollTop();
	}
	$( '#menu-bottom li' ).removeClass( 'active' );
	$( '.page' ).addClass( 'hide' );
	$( '#page-'+ panel ).removeClass( 'hide' );
	$( '#tab-'+ panel ).addClass( 'active' );
	if ( !GUI.display.bars ) {
		$( '.btnlist-top' ).css( 'top', 0 );
		$( '#db-list' ).css( 'padding-top', '40px' );
	}
	if ( panel === 'playback'
		|| ( panel === 'library' && !$( '#home-block' ).hasClass( 'hide' ) )
	) $( 'html, body' ).scrollTop( 0 );
	if ( panel !== 'playlist' ) $( '#pl-entries li' ).removeClass( 'active' );
}
function getBio( artist ) {
	$( '#loader' ).removeClass( 'hide' );
	$.get( 'enhancebio.php',
		{ artist: artist },
		function( data ) {
			$( '#biocontent' ).html( data ).promise().done( function() {
				$( '#bio' ).scrollTop( 0 );
				renderBio();
			} );
		}
	);
}
function renderBio() {
	$( '#menu-top, #menu-bottom, #loader' ).addClass( 'hide' );
	$( '#bio' ).removeClass( 'hide' );
}
$( '#artist, #bio-open' ).click( function() {
	if ( GUI.status.ext === 'radio' ) return
	
	if ( $( '#bio legend' ).text() != GUI.status.Artist ) {
		getBio( GUI.status.Artist );
	} else {
		renderBio();
	}
} );
function mpdSeek( seekto ) {
	if ( GUI.status.state !== 'stop' ) {
		clearInterval( GUI.intKnob );
		clearInterval( GUI.intElapsed );
		$.post( 'enhance.php', { mpc: 'mpc seek '+ seekto } );
	} else {
		$.post( 'enhance.php', { mpc: [ 'mpc play', 'mpc seek '+ seekto, 'mpc pause' ] } );
	}
}
function muteColor( volumemute ) {
	$volumetooltip.text( volumemute ).css( 'color', '#0095d8' );
	$volumehandle.css( 'background', '#587ca0' );
	$( '#volmute' ).addClass( 'btn-primary' )
		.find( 'i' ).removeClass( 'fa-volume' ).addClass( 'fa-mute' );
}
function unmuteColor() {
	$volumetooltip.css( 'color', '' );
	$volumehandle.css( 'background', '' );
	$( '#volmute' ).removeClass( 'btn-primary' )
		.find( 'i' ).removeClass( 'fa-mute' ).addClass( 'fa-volume' );
}
function displayTopBottom() {
	if ( !$( '#bio' ).hasClass( 'hide' ) ) return
	
	var wH = window.innerHeight;
	if ( !GUI.display.bars ) {
		$( '#menu-top, #menu-bottom' ).addClass( 'hide' );
		$( '#db-list, #pl-list' ).css( 'padding', '40px 0' );
		$( '.btnlist-top' ).css( 'top', 0 );
		$( '#home-blocks' ).css( 'padding-top', '50px' );
	} else {
		$( '#menu-top, #menu-bottom' ).removeClass( 'hide' );
		$( '#page-playback' ).css( 'padding-top', '' );
		$( '#db-list, #pl-list' ).css( 'padding', '' );
		$( '.btnlist-top' ).css( 'top', '40px' );
		$( '#home-blocks' ).css( 'padding-top', '' );
	}
	$( '#debug' ).toggleClass( 'hide', GUI.display.debug === '' );
	$( '#dev' ).toggleClass( 'hide', GUI.display.dev === '' );
	var menuH = ( $( '#settings a' ).length - $( '#settings a.hide' ).length ) * 41 - 1;
	$( '#settings .menushadow' ).css( 'height', menuH +'px' );
}
function displayAirPlay() {
	$( '#playback-controls' ).addClass( 'hide' );
	$( '#divartist, #divsong, #divalbum' ).removeClass( 'scroll-left' );
	$( '#songposition, #format-bitrate, #total' ).empty();
	$( '#artist' ).html( GUI.airplay.currentartist );
	$( '#song' ).html( GUI.airplay.currentsong );
	$( '#album' ).html( GUI.airplay.currentalbum );
	$( '#elapsed, #total' ).empty();
	var time = new Date().getTime();
	$( '#cover-art' ).css( {
		  'background-image': 'url("assets/img/airplay-cover.jpg?v='+ time +'")'
		, 'border-radius': 0
	} );
	scrollLongText();
	$( '#menu-top, #menu-bottom' ).toggleClass( 'hide', GUI.display.bars === '' );
	$( '#playback-row' ).removeClass( 'hide' );
	$( '#time-knob' ).toggleClass( 'hide', GUI.display.time === '' );
	$( '#irandom, #irepeat, #posrandom, #posrepeat, #coverartoverlay, #volume-knob, #play-group, #share-group, #vol-group' ).addClass( 'hide' );
	$( '#playsource-mpd' ).addClass( 'inactive' );
	$( '#playsource-airplay' ).removeClass( 'inactive' );
	if ( GUI.display.time ) {
		$( '#time-knob, #play-group, #coverart, #share-group' ).css( 'width', '45%' );
		clearInterval( GUI.intKnob );
		clearInterval( GUI.intElapsed );
		$( '#time' ).roundSlider( 'setValue', 0 );
		$( '#elapsed' ).html( blinkdot );
		$( '#total' ).empty();
		$( '#iplayer' ).attr( 'class', 'fa fa-airplay' );
	} else {
		$( '#coverart, #share-group' ).css( 'width', '90%' );
	}
}
function PlaybackCssOrder( el, ord ) {
	el.css( { order: ord, '-webkit-order': ord } );
}
function displayPlayback() {
	var wW = window.innerWidth;
	var wH = window.innerHeight;
	if ( ( wW < 750 && wW ) > wH || wH < 475 ) {
		var scale = wH > 475 ? wW / 800 : wH / 450;
		$( '#page-playback' ).css( {
			  transform          : 'scale( '+ scale +' )'
			, 'transform-origin' : 'top'
			, 'padding-top'      : 60 * scale +'px'
		} );
		$( '#playback-row' ).css( {
			  width         : 100 / scale +'%'
			, 'margin-left' : ( 100 / scale - 100 ) / -2 +'%'
		} );
	} else {
		$( '#page-playback' ).css( {
			  transform          : ''
			, 'padding-top'      : ''
		} );
		$( '#playback-row' ).css( {
			  width         : ''
			, 'margin-left' : ''
		} );
	}
	$( '#time-knob, #play-group' ).toggleClass( 'hide', GUI.display.time === '' );
	$( '#coverart, #share-group' ).toggleClass( 'hide', GUI.display.coverart === '' );
	var volume = ( GUI.display.volumempd && GUI.display.volume ) ? 1 : 0;
	$( '#volume-knob, #vol-group' ).toggleClass( 'hide', !volume );
	
	var column = ( GUI.display.time ? 1 : 0 ) + ( GUI.display.coverart ? 1 : 0 ) + volume;
	var $elements = $( '#time-knob, #coverart, #volume-knob, #play-group, #share-group, #vol-group' );
	if ( column === 2 && window.innerWidth > 499 ) {
		PlaybackCssOrder( $( '#time-knob' ), volume ? 1 : '' );
		PlaybackCssOrder( $( '#coverart' ), volume ? 2 : '' );
		PlaybackCssOrder( $( '#volume-knob' ), volume ? 3 : '' );
		PlaybackCssOrder( $( '#play-group' ), volume ? 4 : '' );
		PlaybackCssOrder( $( '#share-group' ), volume ? 5 : '' );
		PlaybackCssOrder( $( '#vol-group' ), volume ? 6 : '' );
		$( '#playback-row' ).css( 'max-width', '900px' );
		$elements.css( 'width', '45%' );
	} else if ( column === 1 ) {
		$( '#playback-row' ).css( 'max-width', '' );
		$elements.css( 'width', '90%' );
	} else {
		$elements.css( 'width', '' );
	}
	if ( !GUI.display.buttons ) {
		$( '#play-group, #share-group, #vol-group' ).addClass( 'hide' );
		if ( GUI.display.time ) $( '#iplayer' ).attr( 'class', GUI.status.activePlayer === 'MPD' ? 'fa hide' : 'fa fa-'+ GUI.status.activePlayer.toLowerCase() );
	}
	// no scaling for webradio vu meter
	if ( GUI.display.coverlarge
		&& $( '#album' ).text().slice( 0, 4 ) !== 'http'
		|| ( !GUI.display.time && !GUI.display.volume )
	) {
		$( '#divcover, #cover-art, #coverartoverlay, #controls-cover' ).removeClass( 'coversmall' );
		var maxW = GUI.display.bars ? '45vh' : '55vh';
		$( '#divcover, #cover-art' ).css( { 'max-width': maxW, 'max-height': maxW } );
		if ( wW < 500 ) $( '#format-bitrate' ).css( 'display', GUI.display.time ? 'inline' : 'block' );
		if ( !GUI.display.time && !GUI.display.volume ) $( '#share-group' ).addClass( 'hide' );
	} else {
		$( '#divcover, #cover-art, #coverartoverlay, #controls-cover' ).addClass( 'coversmall' );
		if ( wW < 500 ) $( '#divcover, #cover-art' ).css( { 'max-width': '100%', 'max-height': '100%' } );
	}
	if ( GUI.display.time ) {
		$( '#divpos' ).css( 'font-size', '' );
		$( '#timepos' ).empty();
		$( '#playback-row' ).css( 'margin-top', '' );
	} else {
		$( '#divpos' ).css( 'font-size', '20px' );
		$( '#format-bitrate' ).css( 'display', 'block' );
		$( '#playback-row' ).css( 'margin-top', '30px' );
	}
	displayTopBottom();
}
function switchPlaysource( source ) {
	$.get( '/command/?switchplayer='+ source, function() {
		setTimeout( function() {
			$( '#tab-playback' ).click();
			$( '#playsource li a' ).addClass( 'inactive' );
			$( '#playsource-'+ source.toLowerCase() ).removeClass( 'inactive' )
			$( '#playsource-close' ).click();
		}, 2000 );
	} );
}
function displayIndexBar() {
	setTimeout( function() {
		var wH = window.innerHeight;
		var indexoffset = $( '#menu-top' ).hasClass( 'hide' ) ? 80 : 160;
		var indexline = wH < 500 ? 13 : 27;
		$( '.half' ).toggleClass( 'hide', wH < 500 );
		$index = ( !$( '#page-library' ).hasClass( 'hide' ) && GUI.dblist ) ? $( '#db-index' ) : $( '#pl-index' );
		$index.css( 'line-height', ( ( wH - indexoffset ) / indexline ) +'px' );
	}, 50 );
}
function setToggleButton( name, append ) {
	$( 'input[name="'+ name +'"]' )
		.prop( 'disabled', true )
		.parent().css( 'color', '#7795b4' )
		.append( append ? ' '+ append : ' (auto hide)' );
}
function displayCheckbox( name, label ) {
	return '<label><input name="'+ name +'" type="checkbox" '+ GUI.display[ name ] +'>&ensp;'+label+'</label><br>';
}
function toggleLibraryHome( name ) {
	$( '#home-'+ name ).parent().toggleClass( 'hide', GUI.display[ name ] === '' );
}
var namepath = {
	  sd          : [ 'SD',           'LocalStorage', 'microsd' ]
	, usb         : [ 'USB',          'USB',          'usbdrive' ]
	, nas         : [ 'Network',      'NAS',          'network' ]
	, webradio    : [ 'Webradio',     'Webradio',     'webradio' ]
	, album       : [ 'Album',        'Album',        'album' ]
	, artist      : [ 'Artist',       'Artist',       'artist' ]
	, albumartist : [ 'Album Artist', 'AlbumArtist',  'albumartist' ]
	, composer    : [ 'Composer',     'Composer',     'composer' ]
	, genre       : [ 'Genre',        'Genre',        'genre' ]
	, spotify     : [ 'Spotify',      'Spotify',      'spotify' ]
	, dirble      : [ 'Dirble',       'Dirble',       'dirble' ]
	, jamendo     : [ 'Jamendo',      'Jamendo',      'jamendo' ]
}
function setLibraryBlock( id ) {
	var status = GUI.libraryhome;
	if ( id === 'spotify' && !status.spotify ) return '';

	var iconmusic = id === 'sd' ? ' <i class="fa fa-music"></i>' : '';
	var count = GUI.display.count && status[ id ] !== undefined ? ( '<gr>'+ numFormat( status[ id ] ) + iconmusic +'</gr>' ) : '';
	var label = GUI.display.label ? ( '<wh>'+ namepath[ id ][ 0 ] +'</wh>' ) : '';
	var browsemode = ( $.inArray( id, [ 'album', 'artist', 'albumartist', 'composer', 'genre' ] ) !== -1 ) ? ' data-browsemode="'+ id +'"' : '';
	var plugin = ( id === 'spotify' || id === 'dirble' || id === 'jamendo' ) ? ( ' data-plugin="'+ namepath[ id ][ 1 ] +'"' ) : '';
	
	return '<div class="col-md-3">'
			+'<div id="home-'+ id +'" class="home-block"'+ browsemode + plugin +'><a class="lipath">'+ namepath[ id ][ 1 ] +'</a>'
				+'<i class="fa fa-'+ namepath[ id ][ 2 ] +'"></i>'+ count + label
			+'</div>'
		+'</div>';
}
function renderLibrary() {
	GUI.dbbackdata = [];
	if ( GUI.bookmarkedit ) return
	GUI.plugin = '';
	$( '#db-currentpath' ).css( 'width', '' );
	$( '#db-currentpath .lipath' ).empty()
	$( '#db-entries' ).empty();
	$( '#db-search, #db-search-results, #db-index, #db-back, #db-webradio-new' ).addClass( 'hide' );
	$( '#db-searchbtn' ).removeClass( 'hide' );
	$( '#db-search-keyword' ).val( '' );
	if ( $( '#db-entries' ).hasClass( 'hide' ) ) return
	
	$( '#page-library .btnlist-top, db-entries' ).addClass( 'hide' );
	var status = GUI.libraryhome;
	if ( GUI.display.count ) {
		$( '#db-currentpath span' ).html( '<bl class="title">LIBRARY<gr>·</gr></bl><a id="li-count"><wh>'+ numFormat( status.song ) +'</wh><i class="fa fa-music"></i></a>' );
	} else {
		$( '#db-currentpath span' ).html( '<bl class="title">LIBRARY</bl></a>' );
	}
	$( '#page-library .btnlist-top, #home-blocks' ).removeClass( 'hide' );
	var content = '';
	var bookmarks = status.bookmark;
	if ( bookmarks ) {
		bookmarks.sort( function( a, b ) {
			return stripLeading( a.name ).localeCompare( stripLeading( b.name ), undefined, { numeric: true } );
		} );
		var bookmarkL = bookmarks.length;
		$.each( bookmarks, function( i, bookmark ) {
			var count = GUI.display.count ? '<gr>'+ numFormat( bookmark.count ) +' <i class="fa fa-music"></i></gr>' : '';
			var name = bookmark.name.replace( /\\/g, '' );
			content += '<div class="col-md-3"><div class="home-block home-bookmark"><a class="lipath">'+ bookmark.path +'</a><i class="fa fa-bookmark"></i>'+ count +'<div class="divbklabel"><span class="bklabel">'+ name +'</span></div></div></div>';
		} );
	}
	var order = GUI.display.library || 'sd,usb,nas,webradio,album,artist,albumartist,composer,genre,dirble,jamendo';
	order = order.split( ',' );
	content += '<div id="divhomeblocks">';
	$.each( order, function( i, val ) {
		content += setLibraryBlock( val );
	} );
	content += '</div>';
	$( '#home-blocks' ).html( content ).promise().done( function() {
		toggleLibraryHome( 'nas' );
		toggleLibraryHome( 'sd' );
		toggleLibraryHome( 'usb' );
		toggleLibraryHome( 'webradio' );
		toggleLibraryHome( 'album' );
		toggleLibraryHome( 'artist' );
		toggleLibraryHome( 'albumartist' );
		toggleLibraryHome( 'composer' );
		toggleLibraryHome( 'genre' );
		toggleLibraryHome( 'dirble' );
		toggleLibraryHome( 'jamendo' );
		
		$( '.home-bookmark' ).each( function() {
			var $hammer = new Hammer( this );
			var $this = $( this )
			$hammer.on( 'press', function( e ) {
				GUI.local = 1;
				setTimeout( function() { GUI.local = 0 }, 1000 );
				$( '.home-bookmark' )
					.append( '<i id="home-block-edit" class="fa fa-edit"></i><i id="home-block-remove" class="fa fa-minus-circle"></i>' )
					.find( '.fa-bookmark, gr' ).css( 'opacity', 0.2 );
			} ).on( 'tap', function( e ) {
				var path = $this.find( '.lipath' ).text();
				var name = $this.find( '.bklabel' ).text();
				if ( e.target.id === 'home-block-edit' ) {
					bookmarkRename( name, path, $this );
				} else if ( e.target.id === 'home-block-remove' ) {
					bookmarkDelete( name, $this );
				} else {
					GUI.dblist = 1;
		//			mutationLibrary.observe( observerLibrary, observerOption );
					GUI.dbbrowsemode = 'file';
					getDB( {
						  browsemode : 'file'
						, path       : path
					} );
				}
			} );
		} );
		var txt = '';
		if ( GUI.display.label ) {
			$( '.home-block gr' ).css( 'color', '' );
			$( '.home-block' ).css( 'padding', '' );
		} else {
			$( '.home-block gr' ).css( 'color', '#e0e7ee' );
			$( '.home-block' ).css( 'padding-bottom', '30px' );
			$( '.home-bookmark' ).css( 'padding', '15px 5px 5px 5px' );
		}
		displayTopBottom();
		$( '.bklabel' ).each( function() {
			var $this = $( this );
			var tW = $this.width();
			var pW = $this.parent().width();
			if ( tW > pW ) $this.addClass( 'bkscroll' ).css( 'animation-duration', Math.round( 3 * tW / pW ) +'s' );
		} );
		
		new Sortable( document.getElementById( 'divhomeblocks' ), {
			  delay      : 500
			, onStart    : function( e ) {
				$icon = $( e.item ).find( 'i' );
				$icon.css( 'color', '#e0e7ee' );
			  }
			, onEnd      : function() {
				$icon.css( 'color', '' );
			  }
			, onUpdate   : function ( e ) {
				var $blocks = $( '.home-block:not(.home-bookmark)' );
				var homeorder = '';
				$.each( $blocks, function( i, el ) {
					homeorder += el.id.replace( 'home-', '' ) +',';
				} );
				homeorder = homeorder.slice( 0, -1 );
				GUI.display.library = homeorder;
				GUI.local = 1;
				setTimeout( function() { GUI.local = 0 }, 500 );
				$.post( 'enhance.php', { homeorder: homeorder } );
			}
		} );
	} );
}
function getDB( options ) {
	$( '#loader' ).removeClass( 'hide' );
	var cmd = options.cmd || 'browse',
		path = options.path ? options.path.toString().replace( /"/g, '\"' ) : '',
		browsemode = options.browsemode || 'file',
		uplevel = options.uplevel || '',
		plugin = options.plugin || '',
		querytype = options.querytype || '',
		args = options.args || '',
		artist = options.artist ? options.artist.toString().replace( /"/g, '\"' ) : '',
		mode,
		command;
	var currentpath = $( '#db-currentpath .lipath' ).text();
	currentpath = currentpath ? currentpath.toString().replace( /"/g, '\"' ) : '';
	if ( !GUI.dbback && cmd !== 'search' && GUI.dbbrowsemode !== 'file' ) {
		GUI.dbbackdata.push( {
			  path       : path
			, browsemode : browsemode
			, uplevel    : uplevel
			, plugin     : plugin
			, args       : args
			, querytype  : querytype
		} );
	} else if ( cmd === 'search' && currentpath ) {
		if ( GUI.dbbackdata.length ) {
			GUI.dbbackdata.push( GUI.dbbackdata[ GUI.dbbackdata.length - 1 ] );
		} else {
			GUI.dbbackdata.push( {
				  path       : currentpath
				, browsemode : GUI.browsemode
			} );
		}
	} else {
		GUI.dbback = 0;
	}
	GUI.browsemode = browsemode;
	var keyword = $( '#db-search-keyword' ).val();
	keyword = keyword ? keyword.toString().replace( /"/g, '\"' ) : '';
	if ( !plugin ) {
		if ( $( '#rootpath' ).data( 'path' ) !== 'AlbumArtist' ) {
			var artistalbum = ' artist "'+ ( artist ? artist : currentpath ) +'"';
		} else {
			var artistalbum = '';
		}
		var command = {
			  file        : { mpc: 'mpc ls -f "%title%^^%time%^^%artist%^^%album%^^%file%" "'+ path +'" 2> /dev/null', list: 'file' }
			, playlist    : { mpc: 'mpc load "'+ path +'"' }
			, album       : { mpcalbum: path } 
			, artistalbum : { mpc: 'mpc find -f "%title%^^%time%^^%artist%^^%album%^^%file%^^%albumartist%"'+ artistalbum +' album "'+ path +'"', list: 'file' } 
			, artist      : { mpc: 'mpc list album artist "'+ path +'" | awk NF', list: 'album' }
			, albumartist : { mpc: 'mpc list album albumartist "'+ path +'" | awk NF', list: 'album' }
			, composer    : { mpc: 'mpc list album composer "'+ path +'" | awk NF', list: 'album' }
			, genre       : { mpc: 'mpc list artist genre "'+ path +'" | awk NF', list: 'artist' }
			, type        : { mpc: 'mpc list '+ GUI.browsemode +' | awk NF', list: GUI.browsemode }
			, search      : { mpc: 'mpc search -f "%title%^^%time%^^%artist%^^%album%^^%file%" any "'+ keyword +'"', list: 'file' }
			, Webradio    : { getwebradios: 1 }
		}
		if ( cmd === 'search' ) {
			if ( path.match(/Dirble/)) {
				$.post( '/db/?cmd=dirble', { querytype: 'search', args: keyword }, function( data ) {
					dataSort( data, path, 'Dirble', 'search' );
				}, 'json' );
				return
			} else {
				mode = 'search';
			}
		} else if ( cmd === 'browse' ) {
			var ext = path.slice( -3 ).toLowerCase();
			if ( $.inArray( path, [ 'Album', 'Artist', 'AlbumArtist', 'Composer', 'Genre' ] ) !== -1 ) {
				mode = 'type';
			} else if ( path === 'Webradio' ) {
				mode = 'Webradio';
			} else if ( GUI.browsemode === 'album' && currentpath !== 'Album' && artist ) { // <li> in 'Artist' and 'Genre'
				mode = 'artistalbum';
				GUI.albumartist = path +'<gr> • </gr>'+ artistalbum;
			} else if ( $.inArray( ext, [ 'm3u', 'pls', 'cue' ] ) !== -1 ) {
				mode = 'playlist';
			} else {
				mode = GUI.browsemode;
				if ( mode === 'composer' ) GUI.browsemode = 'composeralbum';
			}
		}
		$.post( 'enhance.php', command[ mode ], function( data ) {
			if ( data ) {
				dataSort( data, path );
			} else {
				$( '#loader' ).addClass( 'hide' );
				info( 'No data in this location.' );
			}
		}, 'json' );
		return
	}

	if ( plugin === 'Spotify' ) {
		$.post( '/db/?cmd=spotify', { plid: args }, function( data ) {
			dataSort( data, path, plugin, querytype, arg );
		}, 'json' );
	} else if ( plugin === 'Dirble' ) {
		if ( querytype === 'childs' ) {
			$.post( '/db/?cmd=dirble', { querytype: 'childs', args: args }, function( data ) {
				dataSort( data, path, plugin, 'childs' );
			}, 'json' );
			$.post( '/db/?cmd=dirble', { querytype: 'childs-stations', args: args }, function( data ) {
				dataSort( data, path, plugin, 'childs-stations' );
			}, 'json' );            
		} else {
			$.post( '/db/?cmd=dirble', { querytype: querytype ? querytype : 'categories', args: args }, function( data ) {
				dataSort( data, path, plugin, querytype );
			}, 'json' );
		}
	} else if ( plugin === 'Jamendo' ) {
		$.post( '/db/?cmd=jamendo', { querytype: querytype ? querytype : 'radio', args: args }, function( data ) {
			if ( !data ) {
				$( '#oader' ).addClass( 'hide' );
				info( {
					  icon    : 'warning'
					, title   : 'Jamendo'
					, message : 'Jamendo not response. Please try again later'
				} );
				GUI.dbbackdata.pop();
				return
			}
			dataSort( data.results, path, plugin, querytype );
		}, 'json' );
	}
}
// strip leading A|An|The|(|[|.|'|"|\ (for sorting)
function stripLeading( string ) {
	if ( typeof string === 'number' ) string = string.toString();
	return string.replace( /^A +|^An +|^The +|^\(\s*|^\[\s*|^\.\s*|^\'\s*|^\"\s*|\\/i, '' );
}
function dataSort( data, path, plugin, querytype, arg ) {
	var data = data,
		path = path || '',
		plugin = plugin || '',
		querytype = querytype || '',
		args = args || '',
		content = '',
		i = 0,
		row = [];
	GUI.albumartist = '';
	
	if ( path ) GUI.currentpath = path;
	$( '#db-entries, #db-currentpath .lipath' ).empty();
	$( '#db-currentpath span, #db-entries, #db-back' ).removeClass( 'hide' );
	$( '#home-blocks' ).addClass( 'hide' );

	if ( !plugin ) {
		if ( !data.length ) return
		
		var type = {
			  Album        : 'album'
			, Artist       : 'artist'
			, AlbumArtist  : 'albumartist'
			, Composer     : 'composer'
			, Genre        : 'genre'
			, Webradio     : 'playlist'
		}
		var mode = {
			  file          : 'file'
			, album         : 'file'
			, artist        : 'album'
			, albumartist   : 'album'
			, genre         : 'artist'
			, composer      : 'file'
			, composeralbum : 'album'
		}
		// undefined type are directory names
		prop = type[ path ] ? type[ path ] : 'directory';
		if ( data[ 0 ].artistalbum ) prop = 'artistalbum'; // for common albums like 'Greatest Hits'
		if ( data[ 0 ].directory || data[ 0 ].file || data[ 0 ].playlist ) {
			var arraydir = [];
			var arrayfile = [];
			var arraypl = [];
			$.each( data, function( index, value ) {
				if ( value.directory ) {
					arraydir.push( value );
				} else if ( value.file ) {
					arrayfile.push( value );
				} else {
					arraypl.push( value );
				}
			} );
			arraydir.sort( function( a, b ) {
				var adir = stripLeading( a[ 'directory' ].split( '/' ).pop() );
				var bdir = stripLeading( b[ 'directory' ].split( '/' ).pop() );
				return adir.localeCompare( bdir, undefined, { numeric: true } );
			} );
			var arraydirL = arraydir.length;
			for ( i = 0; i < arraydirL; i++ ) content += data2html( arraydir[ i ], i, 'db', path );
			arraypl.sort( function( a, b ) {
				return stripLeading( a[ 'playlist' ] ).localeCompare( stripLeading( b[ 'playlist' ] ), undefined, { numeric: true } );
			} );
			var arrayplL = arraypl.length;
			for ( i = 0; i < arrayplL; i++ ) content += data2html( arraypl[ i ], i, 'db', path );
			arrayfile.sort( function( a, b ) {
				return stripLeading( a[ 'Title' ] ).localeCompare( stripLeading( b[ 'Title' ] ), undefined, { numeric: true } );
			} );
			var arrayfileL = arrayfile.length;
			for ( i = 0; i < arrayfileL; i++ ) content += data2html( arrayfile[ i ], i, 'db', path );
		} else {
			data.sort( function( a, b ) {
				if ( a[ prop ] === undefined ) prop = mode[ GUI.browsemode ];
				return stripLeading( a[ prop ] ).localeCompare( stripLeading( b[ prop ] ), undefined, { numeric: true } );
			} );
			var dataL = data.length;
			for ( i = 0; i < dataL; i++ ) content += data2html( data[ i ], i, 'db', path );
		}
		$( '#db-webradio-new' ).toggleClass( 'hide', path !== 'Webradio' );
	} else {
		if ( plugin === 'Spotify' ) {
			data = ( querytype === 'tracks' ) ? data.tracks : data.playlists;
			data.sort( function( a, b ) {
				if ( path === 'Spotify' && querytype === '' ) {
					return stripLeading( a[ 'name' ] ).localeCompare( stripLeading( b[ 'name' ] ), undefined, { numeric: true } )
				} else if ( querytype === 'tracks' ) {
					return stripLeading( a[ 'title' ]) .localeCompare( stripLeading( b[ 'title' ] ), undefined, { numeric: true } )
				} else {
					return 0;
				}
			} );
			for ( i = 0; ( row = data[ i ] ); i++ ) content += data2html( row, i, 'Spotify', arg, querytype );
		} else if ( plugin === 'Dirble' ) {
			if ( querytype === 'childs-stations' ) {
				content = $( '#db-entries' ).html();
			} else {
				data.sort( function( a, b ) {
					if ( !querytype || querytype === 'childs' || querytype === 'categories' ) {
						return stripLeading( a[ 'title' ] ).localeCompare( stripLeading( b[ 'title' ] ), undefined, { numeric: true } )
					} else if ( querytype === 'childs-stations' || querytype === 'stations' ) {
						return stripLeading( a[ 'name' ] ).localeCompare( stripLeading( b[ 'name' ] ), undefined, { numeric: true } )
				   } else {
						return 0;
					}
				} );
				for ( i = 0; ( row = data[ i ] ); i++ ) content += data2html( row, i, 'Dirble', '', querytype );
			}
		} else if ( plugin === 'Jamendo' ) {
			data.sort( function( a, b ) {
				if ( path === 'Jamendo' && querytype === '' ) {
					return stripLeading( a[ 'dispname' ] ).localeCompare( stripLeading( b[ 'dispname' ] ), undefined, { numeric: true } )
				} else {
					return 0;
				}
			} );
			for (i = 0; ( row = data[ i ] ); i++ ) content += data2html( row, i, 'Jamendo', '', querytype );
		}
	}
	$( '#db-entries' ).html( content +'<p></p>' ).promise().done( function() {
		// fill bottom of list to mave last li movable to top
		$( '#db-entries p' ).css( 'min-height', window.innerHeight - ( GUI.display.bars ? 140 : 100 ) +'px' );
		displayIndexBar();
	} );
// breadcrumb directory path link
	var iconName = {
		  LocalStorage  : '<i class="fa fa-microsd"></i>'
		, USB           : '<i class="fa fa-usbdrive"></i>'
		, NAS           : '<i class="fa fa-network"></i>'
		, album         : [ '<i class="fa fa-album"></i>',       'ALBUM' ]
		, artist        : [ '<i class="fa fa-artist"></i>',      'ARTIST' ]
		, albumartist   : [ '<i class="fa fa-albumartist"></i>', 'ALBUM ARTIST' ]
		, genre         : [ '<i class="fa fa-genre"></i>',       'GENRE' ]
		, composer      : [ '<i class="fa fa-composer"></i>',    'COMPOSER' ]
		, composeralbum : [ '<i class="fa fa-composer"></i>',    'ALBUM COMPOSER' ]
		, Dirble        : '<i class="fa fa-dirble"></i>'
		, Jamendo       : '<i class="fa fa-jamendo"></i>'
		, Spotify       : '<i class="fa fa-spotify"></i>'
	}
	var mode = {
		  album       : 'Album'
		, artist      : 'Artist'
		, albumartist : 'AlbumArtist'
		, genre       : 'Genre'
		, composer    : 'Composer'
	}
	if ( GUI.browsemode !== 'file' ) {
		if ( GUI.browsemode !== 'album' && GUI.browsemode !== 'composeralbum' ) {
			var dotpath = ( path === mode[ GUI.browsemode ] ) ? '' : '<a id="artistalbum"><gr> • </gr><span class="white">'+ path +'</span></a>';
		} else {
			var albumpath = path === 'Album' ? '' : path;
			var albumtext = GUI.albumartist ? GUI.albumartist : albumpath;
			var dotpath = albumtext ? '<a id="artistalbum"><gr> • </gr><span class="white">'+ albumtext +'</span></a>' : '';
		}
		$( '#db-currentpath .lipath' ).text( path ); // for back navigation
		$( '#db-currentpath span' ).html( iconName[ GUI.browsemode ][ 0 ] +' <a id="rootpath" data-path="'+ mode[ GUI.browsemode ] +'">'+ iconName[ GUI.browsemode ][ 1 ] +'</a>'+ dotpath );
	} else {
		var folder = path.split( '/' );
		var folderRoot = folder[ 0 ];
		$( '#db-currentpath' ).css( 'width', '' );
		if ( $( '#db-search-keyword' ).val() ) {
		// search results
			var results = ( data.length ) ? data.length : '0';
			$( '#db-currentpath' ).css( 'width', '40px' );
			$( '#db-back, #db-index' ).addClass( 'hide' );
			$( '#db-entries' ).css( 'width', '100%' );
			$( '#db-search-results' )
				.removeClass( 'hide' )
				.html( '<i class="fa fa-times sx"></i><span class="visible-xs-inline"></span>\
					<span>' + results + ' <a>of</a> </span>' );
		} else if ( folderRoot === 'Webradio' ) {
			$( '#db-currentpath .lipath' ).text( 'Webradio' );
			$( '#db-currentpath' ).find( 'span' ).html( '<i class="fa fa-webradio"></i> <a>WEBRADIOS</a>' );
		} else {
			var folderCrumb = iconName[ folderRoot ];
			var folderPath = '';
			var ilength = folder.length;
			for ( i = 0; i < ilength; i++ ) {
				folderPath += ( i > 0 ? '/' : '' ) + folder[ i ];
				folderCrumb += ' <a>'+ ( i > 0 ? '<w> / </w>' : '' ) + folder[ i ] +'<span class="lipath">'+ folderPath +'</span></a>';
			}
			$( '#db-currentpath .lipath' ).text( path );
			$( '#db-currentpath' ).find( 'span' ).html( folderCrumb );
		}
	}
	// hide index bar in file mode
	if ( $( '#db-entries li:eq( 0 ) i.db-icon' ).hasClass( 'fa-music' ) ) {
		$( '#db-index' ).addClass( 'hide' );
		$( '#db-entries' ).css( 'width', '100%' );
	} else {
		$( '#db-index' ).removeClass( 'hide' );
		$( '#db-entries' ).css( 'width', '' );
	}
	if( $( '#db-search-results' ).hasClass( 'hide' ) ) {
		$( '#db-search' ).addClass( 'hide' );
		$( '#db-searchbtn' ).removeClass( 'hide' );
	} else {
		$( '#db-search' ).removeClass( 'hide' );
		$( '#db-searchbtn' ).addClass( 'hide' );
	}
	$( '#loader' ).addClass( 'hide' );
}
// set path, name, artist as text to avoid double quote escape
function data2html( inputArr, i, respType, inpath, querytype ) {
	var inputArr = inputArr || '',
		i = i || 0,
		respType = respType || '',
		inpath = inpath || '',
		querytype = querytype || '';
	switch ( respType ) {
		case 'db':
			if ( GUI.browsemode === 'file' ) {
				if ( inpath === '' && inputArr.file ) {
					var file = inputArr.file
					inpath = file.slice( 0, file.lastIndexOf( '/' ) );
				}
				if ( ( inputArr.file && !inputArr.playlist ) || inpath === 'Webradio' ) {
					if ( inpath !== 'Webradio' ) {
						if ( inputArr.Title ) {
							if ( $( '#db-search-keyword' ).val() ) {
								var bl = inputArr.Artist +' - '+ inputArr.Album;
							} else {
								var bl = inputArr.file.split( '/' ).pop(); // filename
							}
							var liname = inputArr.Title
							content = '<li class="file"><a class="lipath">'+ inputArr.file +'</a><a class="liname">'+ liname +'</a><i class="fa fa-bars db-action" data-target="#context-menu-file"></i><i class="fa fa-music db-icon"></i>';
							content += '<span class="sn">'+ liname +'&ensp;<span class="time">'+ inputArr.Time +'</span></span>';
							content += '<span class="bl">'+ bl;
						} else {
							var liname = inputArr.file.split( '/' ).pop(); // filename
							content = '<li class="file"><a class="lipath">'+ inputArr.file +'</a><a class="liname">'+ liname +'</a><i class="fa fa-bars db-action" data-target="#context-menu-file"></i><i class="fa fa-music db-icon"></i>';
							content += '<span class="sn">'+ liname +'&ensp;<span class="time">' + second2HMS( inputArr.Time ) +'</span></span>';
							content += '<span class="bl">'+ inpath;
						}
					} else { // Webradio
						var liname = inputArr.playlist.replace( /Webradio\/|\\|.pls$/g, '' );
						content = '<li class="db-webradio file" ><a class="lipath">'+ inputArr.url +'</a><a class="liname">'+ liname +'</a><i class="fa fa-bars db-action" data-target="#context-menu-webradio"></i><i class="fa fa-webradio db-icon db-radio"></i>';
						content += '<span class="sn">'+ liname +'</span>';
						content += '<span class="bl">'+ inputArr.url;
					}
					content += '</span></li>';
				} else if ( inputArr.playlist ) {
					var liname = inputArr.playlist;
					content = '<li class="file"><a class="lipath">'+ inputArr.filepl +'</a><a class="liname">'+ liname +'</a><i class="fa fa-bars db-action"';
					content += ' data-target="#context-menu-filepl"></i><span><i class="fa fa-list-ul"></i>'
					content += '<span class="single">'+ liname +'</span></span></li>';
				} else {
					var liname = inputArr.directory.replace( inpath +'/', '' );
					content = '<li><a class="lipath">'+ inputArr.directory +'</a><a class="liname">'+ liname +'</a><i class="fa fa-bars db-action"';
					content += ' data-target="#context-menu-folder"></i><span><i class="fa fa-folder"></i>'
					content += '<span class="single">'+ liname +'</span></span></li>';
				}
			} else if ( GUI.browsemode === 'album' ) {
				if ( inputArr.file ) {
					var liname = inputArr.Title;
					content = '<li><a class="lipath">'+ inputArr.file +'</a><a class="liname">'+ liname +'</a><i class="fa fa-bars db-action" data-target="#context-menu-file"></i><i class="fa fa-music db-icon"></i>';
					content += '<span class="sn">'+ liname +'&ensp;<span class="time">'+ inputArr.Time +'</span></span>';
					content += '<span class="bl">'+ inputArr.file +'</span></li>';
					var artist = inputArr.AlbumArtist || inputArr.Artist;
					if ( !GUI.albumartist ) GUI.albumartist = inputArr.Album +'<gr> • </gr>'+ artist;
				} else {
					var liname = inputArr.album;
					var artistalbum = inputArr.artistalbum;
					if ( artistalbum ) {
						var lialbum = artistalbum;
						var dataartist = '<a class="liartist">'+ inputArr.artist +'</a>';
					} else {
						var lialbum = liname;
						var dataartist = '';
					}
					content = '<li mode="album"><a class="lipath">'+ inputArr.album +'</a><a class="liname">'+ liname +'</a>'+ dataartist +'<i class="fa fa-bars db-action" data-target="#context-menu-album"></i>';
					content += '<span><i class="fa fa-album"></i>'+ lialbum +'</span></li>';
				}
			} else if ( GUI.browsemode === 'artist' || GUI.browsemode === 'composeralbum' ) {
				if ( inputArr.album ) {
					var liname = inputArr.album ? inputArr.album : 'Unknown album';
					content = '<li mode="album"><a class="lipath">'+ inputArr.album +'</a><a class="liname">'+ liname +'</a><i class="fa fa-bars db-action" data-target="#context-menu-album"></i>';
					content += '<span><i class="fa fa-album"></i>'+ liname +'</span></li>';
				} else {
					var liname = inputArr.artist;
					content = '<li mode="artist"><a class="lipath">'+ inputArr.artist +'</a><a class="liname">'+ liname +'</a><i class="fa fa-bars db-action" data-target="#context-menu-artist"></i>';
					content += '<span><i class="fa fa-artist"></i>'+ liname +'</span></li>';
				}
			} else if ( GUI.browsemode === 'albumartist' ) {
				if ( inputArr.album ) {
					var liname = inputArr.album ? inputArr.album : 'Unknown album';
					content = '<li mode="album"><a class="lipath">'+ inputArr.album +'</a><a class="liname">'+ liname +'</a><i class="fa fa-bars db-action" data-target="#context-menu-album"></i>';
					content += '<span><i class="fa fa-album"></i>'+ liname +'</span></li>';
				} else {
					var liname = inputArr.albumartist;
					content = '<li mode="albumartist"><a class="lipath">'+ inputArr.albumartist +'</a><a class="liname">'+ liname +'</a><i class="fa fa-bars db-action" data-target="#context-menu-artist"></i>';
					content += '<span><i class="fa fa-albumartist"></i>'+ liname +'</span></li>';
				}
			} else if ( GUI.browsemode === 'composer' ) {
				if ( inputArr.file ) {
					var liname = inputArr.Title;
					content = '<li><a class="lipath">'+ inputArr.file +'</a><a class="liname">'+ liname +'</a><i class="fa fa-bars db-action" data-target="#context-menu-file"></i><i class="fa fa-music db-icon"></i>';
					content += '<span class="sn">'+ liname +'&ensp;<span class="time">'+ second2HMS( inputArr.Time ) +'</span></span>';
					content += '<span class="bl">'+ inputArr.Artist +' - '+ inputArr.Album +'</span></li>';
				} else {
					var liname = inputArr.composer;
					content = '<li mode="composer"><a class="lipath">'+ inputArr.composer +'</a><a class="liname">'+ liname +'</a><i class="fa fa-bars db-action" data-target="#context-menu-composer"></i>';
					content += '<span><i class="fa fa-composer"></i>'+ inputArr.composer +'</span></li>';
				}
			} else if ( GUI.browsemode === 'genre' ) {
				if ( inputArr.artist ) {
					var liname = inputArr.artist ? inputArr.artist : 'Unknown artist';
					content = '<li mode="artist"><a class="lipath">'+ inputArr.artist +'</a><a class="liname">'+ liname +'</a><i class="fa fa-bars db-action" data-target="#context-menu-artist"></i>';
					content += '<span><i class="fa fa-artist"></i>'+ liname +'</span></li>';
				} else {
					var liname = inputArr.genre ;
					content = '<li mode="genre"><a class="lipath">'+ inputArr.genre +'</a><a class="liname">'+ liname +'</a><i class="fa fa-bars db-action" data-target="#context-menu-genre"></i>';
					content += '<span><i class="fa fa-genre"></i>'+ liname;+'</span></li>';
				}
			}
			break;
		case 'Spotify':
			if ( querytype === '' ) {
				var liname = inputArr.name ? inputArr.name : 'Favorites';
				content = '<li mode="spotify"><i class="fa fa-bars db-action" data-target="#context-menu-spotify-pl"><a class="lipath">'+ inputArr.index +'</a><a class="liname">'+ liname +'</a></i>'
				content += '<span><i class="fa fa-genre"></i>'+ liname +' ( '+ inputArr.tracks +' )</span></li>';
			} else if ( querytype === 'tracks' ) {
				var liname = inputArr.Title;
				content = '<li data-plid="'+ inpath +'" data-type="spotify-track" mode="spotify"><a class="lipath">'+ inputArr.index +'</a><a class="liname">'+ liname +'</a><i class="fa fa-bars db-action" data-target="#context-menu-spotify"></i><i class="fa fa-spotify db-icon"></i>';
				content += '<span class="sn">'+ liname +'&ensp;<span class="time">'+ second2HMS( inputArr.duration / 1000 ) +'</span></span>';
				content += ' <span class="bl">'+ inputArr.artist +' - '+ inputArr.album +'</span></li>';
			}
			break;
		case 'Dirble':
			if ( querytype === '' || querytype === 'childs' ) {
				var liname = inputArr.title;
				var childClass = ( querytype === 'childs' ) ? ' db-dirble-child' : '';
				content = '<li class="db-dirble'+ childClass +'" mode="dirble"><a class="lipath">'+ inputArr.id +'</a><a class="liname">'+ liname +'</a>'
				content += '<span><i class="fa fa-genre"></i>'+ liname +'</span></li>';
			} else if ( querytype === 'search' || querytype === 'stations' || querytype === 'childs-stations' ) {
				if ( !inputArr.streams.length ) {
					break; // Filter stations with no streams
				}
				var liname = inputArr.name;
				var url = inputArr.streams[ 0 ].stream
				content = '<li mode="dirble"><a class="lipath">'+ url +'</a><a class="liname">'+ liname +'</a><i class="fa fa-bars db-action" data-target="#context-menu-dirble"></i><i class="fa fa-webradio db-icon"></i>';
				content += '<span class="sn">'+ liname +'&ensp;<span>( '+ inputArr.country +' )</span></span>';
				content += '<span class="bl">'+ url +'</span></li>';
			}
			break;
		case 'Jamendo':
			var liname = inputArr.dispname;
			content = '<li mode="jamendo"><a class="lipath">'+ inputArr.stream +'</a><a class="liname">'+ liname +'</a><img class="jamendo-cover" src="'+ inputArr.image +'" alt=""><i class="fa fa-bars db-action" data-target="#context-menu-file"></i>';
			content += '<span>'+ liname +'</span></div></li>';
			break;
	}
	return content;
}
function setPlaylistScroll() {
	if ( GUI.local ) return // 'skip for Sortable'
	
	$.post( 'enhancestatus.php', { statusonly: 1 }, function( status ) {
		$.each( status, function( key, value ) {
			GUI.status[ key ] = value;
		} );
		setButton();
		var $liactive = $( '#pl-entries li' ).eq( status.song );
		$( '#plcrop' ).toggleClass( 'disable', ( status.state === 'stop' || GUI.status.playlistlength === 1 ) );
		$( '#pl-entries li' ).removeClass( 'active' );
		$liactive.addClass( 'active' );
		var $elapsed = $( '#pl-entries li.active .elapsed' );
		if ( !$elapsed.html() ) $( '.elapsed' ).empty();
		clearInterval( GUI.intElapsed );
		var elapsed = status.elapsed;
		var slash = $liactive.hasClass( 'radio' ) ? '' : ' / ';
		if ( status.state === 'pause' ) {
			var elapsedtxt = second2HMS( elapsed ) + slash;
			$elapsed.html( '<i class="fa fa-pause"></i> '+ elapsedtxt );
		} else if ( status.state === 'play' ) {
			GUI.intElapsed = setInterval( function() {
				elapsed++;
				var elapsedtxt = second2HMS( elapsed );
				$elapsed.html( '<i class="fa fa-play"></i> <wh>'+ elapsedtxt +'</wh>'+ slash );
			}, 1000 );
		} else {
			$( '.elapsed' ).empty();
		}
		setTimeout( function() {
			var scrollpos = $liactive.offset().top - $( '#pl-entries' ).offset().top - ( 49 * 3 );
			$( 'html, body' ).scrollTop( scrollpos );
		}, 300 );
	}, 'json' );
}
function renderPlaylist() {
	$( '#pl-filter' ).val( '' );
	$( '#pl-filter-results' ).empty();
	$( '#pl-currentpath, #pl-editor, #pl-index, #pl-search' ).addClass( 'hide' );
	$( '#db-currentpath>span, #pl-searchbtn' ).removeClass( 'hide' );
	$( '#plopen' ).toggleClass( 'disable', !GUI.lsplaylists.length );
	if ( !GUI.status.playlistlength ) {
		$( '#pl-count' ).html( '<bl class="title">PLAYLIST</bl>' );
		$( '#plsave, #plcrop, #plclear' ).addClass( 'disable' );
		$( '#pl-entries' ).empty();
		$( '.playlist' ).removeClass( 'hide' );
		$( '#playlist-warning' ).css( 'margin-top', GUI.display.bars ? '27px' : '47px' );
		$( 'html, body' ).scrollTop( 0 );
		return
	}
	
	var content, pl, iconhtml, topline, bottomline, classradio, hidetotal;
	content = iconhtml = topline =bottomline = classradio = hidetotal = '';
	var id, totaltime, pltime, seconds, countsong, countradio;
	id = totaltime = pltime = seconds = countsong = countradio = 0;
	var ilength = GUI.playlist.length;
	GUI.status.playlistlength = ilength;
	var classradio
	for ( i = 0; i < ilength; i++ ) {
		var pl = GUI.playlist[ i ];
		if ( pl.file.slice( 0, 4 ) === 'http' ) {
			iconhtml = '<i class="fa fa-webradio pl-icon"></i>';
			classradio = 1;
			countradio++
			var title = pl.title || pl.file;
			topline =  title +'&ensp;<span class="elapsed"></span>';
			bottomline = pl.file;
		} else {
			iconhtml = '<i class="fa fa-music pl-icon"></i>';
			classradio = 0;
			sec = HMS2Second( pl.time );
			topline = pl.title +'&ensp;<span class="elapsed"></span><span class="time" time="'+ sec +'">'+ pl.time +'</span>';
			bottomline = pl.track
			pltime += sec;
		}
		content += ( classradio ? '<li class="radio">' : '<li>' )
			+ iconhtml
			+'<i class="fa fa-minus-circle pl-action"></i>'
			+'<span class="sn">'+ topline +'</span>'
			+'<span class="bl">'+ bottomline +'</span>'
			+'</li>';
	}
	countsong = ilength - countradio;
	var counthtml = '<bl class="title">PLAYLIST<gr>·</gr></bl>';
	var countradiohtml = '<wh id="countradio" count="'+ countradio +'">'+ countradio +'</wh>&ensp;<i class="fa fa-webradio"></i>';
	if ( countsong ) {
		var pltimehtml = ' id="pltime" time="'+ pltime +'">'+ second2HMS( pltime ) +'&emsp;';
		var totalhtml = countradio ? '<gr'+ pltimehtml +'</gr>'+ countradiohtml : '<wh'+ pltimehtml +'&emsp;</wh>';
		counthtml += '<wh id="countsong" count="'+ countsong +'">'+ numFormat( countsong ) +'</wh>&ensp;<i class="fa fa-music"></i>&ensp;'+ totalhtml;
	} else {
		counthtml += countradiohtml;
	}
	$( '.playlist' ).removeClass( 'hide' );
	$( '#playlist-warning' ).addClass( 'hide' );
	$( '#pl-count' ).html( counthtml );
	$( '#plsave, #plclear' ).removeClass( 'disable' );
	$( '#pl-entries' ).html( content +'<p></p>' ).promise().done( function() {
		$( '#pl-entries p' ).css( 'min-height', window.innerHeight - 140 +'px' );
		setPlaylistScroll();
	} );
}
function renderSavedPlaylist( name ) {
	$.post( 'enhance.php', { getplaylist: 1, name: name.toString().replace( /"/g, '\\"' ) }, function( data ) {
		var countradio = 0;
		var content, pl, iconhtml, topline, bottomline, classradio, hidetotal;
		content = iconhtml = topline =bottomline = classradio = hidetotal = '';
		var id, totaltime, pltime, seconds, countsong, countradio;
		id = totaltime = pltime = seconds = countsong = countradio = 0;
		data = data.playlist;
		var ilength = data.length;
		for ( i = 0; i < ilength; i++ ) {
			var pl = data[ i ];
			if ( pl.file.slice( 0, 4 ) === 'http' ) {
				iconhtml = '<i class="fa fa-webradio pl-icon"></i>';
				classradio = 1;
				countradio++
				topline = pl.title;
				bottomline = pl.file;
			} else {
				iconhtml = '<i class="fa fa-music pl-icon"></i>';
				classradio = 0;
				sec = HMS2Second( pl.time );
				topline = pl.title +'&ensp;<gr>'+ pl.time +'</gr>';
				bottomline = pl.track
				pltime += sec;
			}
			content += '<li class="pl-song"><a class="liname">'+ pl.file +'</a>'
				+ iconhtml
				+'<i class="fa fa-bars pl-action" data-target="#context-menu-file"></i>'
				+'<span class="sn">'+ topline +'</span>'
				+'<span class="bl">'+ bottomline +'</span>'
				+'</li>';
		}
		countsong = ilength - countradio;
		var counthtml = '<wh><i class="fa fa-list-ul"></i></wh><bl class="title">'+ name +'<gr>&emsp;•</gr></bl>';
		var countradiohtml = '<wh>&emsp;'+ countradio +'</wh>&ensp;<i class="fa fa-webradio"></i>';
		if ( countsong ) {
			var pltimehtml = ' id="pltime" time="'+ pltime +'">'+ second2HMS( pltime );
			var totalhtml = countradio ? '<gr'+ pltimehtml +'</gr>'+ countradiohtml : '<wh'+ pltimehtml +'</wh>';
			counthtml += '<wh>'+ numFormat( countsong ) +'</wh>&ensp;<i class="fa fa-music"></i>&ensp;'+ totalhtml;
		} else {
			counthtml += countradiohtml;
		}
		$( '#pl-currentpath' ).html( '<a class="lipath">'+ name +'</a></ul>'+ counthtml +'<i class="fa fa-arrow-left plsback"></i>' );
		$( '#pl-currentpath, #pl-editor' ).removeClass( 'hide' );
		$( '#pl-currentpath bl' ).removeClass( 'title' );
		$( '#pl-editor' ).html( content +'<p></p>' ).promise().done( function() {
			GUI.pleditor = 1;
			// fill bottom of list to mave last li movable to top
			$( '#pl-editor p' ).css( 'min-height', window.innerHeight - ( GUI.display.bars ? 140 : 100 ) +'px' );
			$( '#pl-editor' ).css( 'width', '100%' );
			$( '#loader, #pl-index' ).addClass( 'hide' );
			$( 'html, body' ).scrollTop( GUI.plscrolltop );
		} );
	}, 'json' );
}
