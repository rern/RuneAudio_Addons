<?php
if ( isset( $_POST[ 'bash' ] ) ) {
	echo shell_exec( '/usr/bin/sudo '.$_POST[ 'bash' ] );
	exit();
}
// with redis
$redis = new Redis();
$redis->pconnect( '127.0.0.1' );

$sudo = '/usr/bin/sudo /usr/bin';
$sudosrv = '/usr/bin/sudo /srv/http';

if ( isset( $_POST[ 'mpc' ] ) ) {
	$mpc = $_POST[ 'mpc' ];
	if ( !is_array( $mpc ) ) { // multiples commands is array
		if ( loadCue( $mpc ) ) exit();
		
		$result = shell_exec( $mpc );
		// query 'various artist album' with 'artist name' > requery without
		if ( !$result && isset( $_POST[ 'name' ] ) ) {
			$result = shell_exec( 'mpc find -f "%title%^^%time%^^%artist%^^%album%^^%file%^^%genre%^^%composer%^^%albumartist%" album "'.$_POST[ 'name' ].'"' );
		}
		$cmd = $mpc;
	} else {
		foreach( $mpc as $cmd ) {
			if ( loadCue( $cmd ) ) {
				$loadCue = 1;
				continue;
			}
			$result = shell_exec( $cmd );
		}
		if ( isset( $loadCue ) ) exit();
	}
	$cmdpl = explode( ' ', $cmd )[ 1 ];
	if ( $cmdpl === 'save' || $cmdpl === 'rm' ) {
		$data = lsPlaylists();
		pushstream( 'playlist', $data );
	}
	if ( isset( $_POST[ 'list' ] ) ) {
		if ( !$result ) {
			echo 0;
			exit();
		}
		$type = $_POST[ 'list' ];
		if ( $type === 'file' ) {
			$data = search2array( $result );
			if ( $redis->hGet( 'display', 'coverfile' ) && !isset( $data[ 'playlist' ] ) && substr( $mpc, 0, 10 ) !== 'mpc search' ) {
				$data[][ 'coverart' ] = getCover( $data[ 0 ][ 'file' ] );
			}
		} else {
			$lists = explode( "\n", rtrim( $result ) );
			foreach( $lists as $list ) {
				$sort = stripLeading( $list );
				$index[] = $sort[ 1 ];
				$data[] = array( 
					  $type    => $list
					, 'sort'   => $sort[ 0 ]
					, 'lisort' => $sort[ 1 ]
				);
			}
			$data = sortData( $data, $index );
		}
		echo json_encode( $data );
	} else if ( isset( $_POST[ 'result' ] ) ) {
		echo $result;
	}
} else if ( isset( $_POST[ 'coverartalbum' ] ) ) {
	$album = $_POST[ 'coverartalbum' ];
	$albums = shell_exec( 'mpc find -f "%album% - [%albumartist%|%artist%]" album "'.$album.'" | awk \'!a[$0]++\'' );
	$count = count( explode( "\n", rtrim( $albums ) ) );
	$cmd = 'mpc find -f "%title%^^%time%^^%artist%^^%album%^^%file%^^%genre%^^%composer%^^%albumartist%" album "'.$album.'"';
	if ( $count === 1 ) {
		$result = shell_exec( $cmd );
	} else {
		$result = shell_exec( $cmd.' albumartist "'.$_POST[ 'artist' ].'"' );
	}
	$data = search2array( $result );
	if ( $redis->hGet( 'display', 'coverfile' ) && !isset( $data[ 'playlist' ] ) && substr( $mpc, 0, 10 ) !== 'mpc search' ) {
		$data[][ 'coverart' ] = getCover( $data[ 0 ][ 'file' ] );
	}
	echo json_encode( $data );
} else if ( isset( $_POST[ 'getcount' ] ) ) {
	$status = getLibraryCount();
	echo json_encode( $status, JSON_NUMERIC_CHECK );
} else if ( isset( $_POST[ 'setorder' ] ) ) {
	$order = $_POST[ 'setorder' ]; 
	$redis->hSet( 'display', 'order', $order );
	$order = explode( '^^', $order );
	pushstream( 'display', array( 'order' => $order ) );
} else if ( isset( $_POST[ 'webradiocoverart' ] ) ) {
	$urlname = str_replace( '/', '|', $_POST[ 'webradiocoverart' ] );
	$file = "/srv/http/assets/img/webradiopl/$urlname";
	file_put_contents( $file, $_POST[ 'base64' ], FILE_APPEND ) || exit( '-1' );
	echo 1;
} else if ( isset( $_POST[ 'webradios' ] ) ) {
	$dir = '/srv/http/assets/img/webradios';
	$name = $_POST[ 'webradios' ];
	$url = $_POST[ 'url' ];
	$rename = isset( $_POST[ 'rename' ] ) ? $_POST[ 'rename' ] : '';
	$urlname = str_replace( '|', '/', $url );
	$file = "/srv/http/assets/img/webradios/$urlname";
	if ( isset( $_POST[ 'new' ] ) ) {
		if ( file_exists( $file ) ) exit( 1 );
		
		file_put_contents( "$dir/$urlname", $name );
	} else if ( isset( $_POST[ 'rename' ] ) ) {
		$content = explode( '^^', file_get_contents( $file ) );
		if ( count( $content ) > 1 ) $name.= '^^'.$content[ 1 ].'^^'.$content[ 2 ];
		file_put_contents( "$dir/$urlname", $name ); // name^^thumbnail^^coverart
	} else if ( isset( $_POST[ 'delete' ] ) ) {
		unlink( $file );
	} else if ( isset( $_POST[ 'save' ] ) ) {
		if ( file_exists( $file ) ) exit( 1 );
		
		rename( "/srv/http/assets/img/webradiopl/$urlname", $file );
	}
	pushstream( 'webradio', 1 );
} else if ( isset( $_POST[ 'bookmarks' ] ) ) {
	$name = $_POST[ 'bookmarks' ];
	$path = $_POST[ 'path' ];
	$pathname = str_replace( '/', '|', $path );
	$dir = '/srv/http/assets/img/bookmarks';
	$file = "$dir/$pathname";
	$order = $redis->hGet( 'display', 'order' );
	$order = explode( '^^', $order );
	if ( $order ) {
		if ( !$name ) {
			$index = array_search( $path, $order );
			if ( $index !== false ) unset( $order[ $index ] );
		} else if ( !$oldname ) {
			array_push( $order, $path ); // append
		}
		pushstream( 'display', array( 'order' => $order ) );
		$order = implode( '^^', $order );
		$redis->hSet( 'display', 'order', $order );
	}
	if ( isset( $_POST[ 'new' ] ) ) {
		if ( isset( $_POST[ 'base64' ] ) ) {
			file_put_contents( "$file", $_POST[ 'base64' ] );
		} else {
			file_put_contents( "$file", $name );
		}
	} else if ( isset( $_POST[ 'rename' ] ) ) {
		file_put_contents( "$file", $name );
	} else if ( isset( $_POST[ 'delete' ] ) ) {
		unlink( $file );
	}
	$data = getBookmark( $redis );
	pushstream( 'bookmark', $data );
} else if ( isset( $_POST[ 'imagefile' ] ) ) {
	$imagefile = $_POST[ 'imagefile' ];
	if ( isset( $_POST[ 'bookmarkbase64' ] ) ) {
		$filename = '/srv/http/assets/img/bookmarks/'.$imagefile;
		file_put_contents( $filename, $_POST[ 'bookmarkbase64' ] );
		exit;
	}
	
	$coverfile = isset( $_POST[ 'coverfile' ] );
	if ( !isset( $_POST[ 'base64' ] ) ) {
		if ( $coverfile ) { // backup coverart in album dir
			exec( "$sudo/mv -f \"$imagefile\"{,.backup}", $output, $std );
		} else {
			unlink( $imagefile );
		}
		exit;
	}
	
	$base64 = explode( ',', $_POST[ 'base64' ] )[ 1 ];
	if ( $coverfile ) {
		$tmpfile = '/srv/http/tmp/tmp.jpg';
		file_put_contents( $tmpfile, base64_decode( $base64 ) ) || exit( '-1' );
		exec( "$sudo/cp $tmpfile \"$newfile\"", $output, $std );
	} else {
		$newfile = substr( $imagefile, 0, -3 ).'jpg'; // if existing is 'cover.svg'
		file_put_contents( $imagefile, base64_decode( $base64 ) ) || exit( '-1' );
		$std = 0;
	}
	echo $std;
	if ( $std === 0 && isset( $_POST[ 'urlname' ] ) ) {
		$file = '/srv/http/assets/img/webradios/'.$_POST[ 'urlname' ];
		$name = explode( "\n", file_get_contents( $file ) )[ 0 ];
		file_put_contents( $file, $name."\n".$imagefile );
		// 100x100 thumbnail
		$thumbfile = substr( $imagefile, 0, -4 ).'-100px.jpg';
		exec( '/usr/bin/sudo /usr/bin/convert "'.$imagefile.'" -thumbnail 100x100 -unsharp 0x.5 "'.$thumbfile.'"' );
	}
} else if ( isset( $_POST[ 'getbookmarks' ] ) ) {
	$data = getBookmark( $redis );
	echo json_encode( $data );
} else if ( isset( $_POST[ 'getwebradios' ] ) ) {
	$dir = '/srv/http/assets/img/webradios';
	$files = array_slice( scandir( $dir ), 2 );
	if ( !count( $files ) ) {
		echo 0;
		exit;
	}
	
	foreach( $files as $file ) {
		$content = file_get_contents( "$dir/$file" ); // name^^base64thumbnail^^base64image
		$nameimg = explode( '^^', $content );
		$sort = stripLeading( $name );
		$index[] = $sort[ 1 ];
		$data[] = array(
			  'webradio' => $nameimg[ 0 ]
			, 'url'      => str_replace( '|', '/', $file )
			, 'thumb'    => $nameimg[ 1 ]
			, 'sort'     => $sort[ 0 ]
			, 'lisort'   => $sort[ 1 ]
		);
	}
	$data = sortData( $data, $index );
	echo json_encode( $data );
} else if ( isset( $_POST[ 'getplaylist' ] ) ) {
	$name = isset( $_POST[ 'name' ] ) ? '"'.$_POST[ 'name' ].'"' : '';
	if ( !$name ) $data[ 'lsplaylists' ] = lsplaylists();
	$lines = shell_exec( 'mpc -f "%title%^^%time%^^[##%track% • ][%artist%][ • %album%]^^%file%^^[%albumartist%|%artist%]^^%album%^^%genre%^^%composer%" playlist '.$name );
	$data[ 'playlist' ] = $lines ? list2array( $lines ) : '';
	echo json_encode( $data );
} else if ( isset( $_POST[ 'getdisplay' ] ) ) {
	usleep( 100000 ); // !important - get data must wait connection start at least (0.05s)
	$data = $redis->hGetAll( 'display' );
	$data[ 'volumempd' ] = $redis->get( 'volume' );
	$data[ 'spotify' ] = $redis->hGet( 'spotify', 'enable' ) == 1 ? 'checked' : '';
	$data[ 'order' ] = explode( '^^', $data[ 'order' ] );
	if ( isset( $_POST[ 'data' ] ) ) {
		echo json_encode( $data, JSON_NUMERIC_CHECK );
	} else {
		pushstream( 'display', $data );
	}
} else if ( isset( $_POST[ 'setdisplay' ] ) ) {
	$data = $_POST[ 'setdisplay' ];
	foreach( $data as $key => $value ) {
		$redis->hSet( 'display', $key, $value );
	}
	pushstream( 'display', $data );
} else if ( isset( $_POST[ 'playlist' ] ) ) { //cue, m3u, pls
	$plfiles = $_POST[ 'playlist' ];
	foreach( $plfiles as $file ) {
		$ext = pathinfo( $file, PATHINFO_EXTENSION );
		$plfile = preg_replace( '/([&\[\]])/', '#$1', $file ); // escape literal &, [, ] in %file% (operation characters)
		$lines.= shell_exec( 'mpc -f "%title%^^%time%^^[##%track% • ][%artist%][ • %album%]^^%file% + '.$ext.'^^[%albumartist%|%artist%]^^%album%^^%genre%^^%composer%^^'.$plfile.'" playlist "'.$file.'"' );
	}
	$data = list2array( $lines );
	$data[][ 'path' ] = dirname( $plfiles[ 0 ] );
	if ( $redis->hGet( 'display', 'coverfile' ) ) {
		$data[][ 'coverart' ] = getCover( $data[ 0 ][ 'file' ] );
	}
	echo json_encode( $data );
} else if ( isset( $_POST[ 'album' ] ) ) {
	$albums = shell_exec( $_POST[ 'album' ] );
	$name = isset( $_POST[ 'albumname' ] ) ? $_POST[ 'albumname' ] : '';
	if ( isset( $_POST[ 'albumname' ] ) ) {
		$type = 'album';
		$name = $_POST[ 'albumname' ];
	} else if ( isset( $_POST[ 'genrename' ] ) ) {
		$type = 'genre';
		$name = $_POST[ 'genrename' ];
	} else {
		$name = '';
	}
	$lines = explode( "\n", rtrim( $albums ) );
	$count = count( $lines );
	if ( $count === 1 ) {
		$albums = shell_exec( 'mpc find -f "%title%^^%time%^^%artist%^^%album%^^%file%^^%genre%^^%composer%^^%albumartist%" '.$type.' "'.$name.'"' );
		$data = search2array( $albums );
		if ( $redis->hGet( 'display', 'coverfile' ) && !isset( $data[ 'playlist' ] ) ) {
			$data[][ 'coverart' ] = getCover( $data[ 0 ][ 'file' ] );
		}
	} else {
		foreach( $lines as $line ) {
			$list = explode( '^^', $line );
			$album = $list[ 0 ];
			$artist = $list[ 1 ];
			if ( $name ) {
				$artistalbum = $artist.'<gr> • </gr>'.$album;
				$sort = stripLeading( $artist.' - '.$album );
			} else {
				$artistalbum = $album.'<gr> • </gr>'.$artist;
				$sort = stripLeading( $album.' - '.$artist );
			}
			$index[] = $sort[ 1 ];
			$data[] = array(
				  'artistalbum' => $artistalbum
				, 'album'       => $album
				, 'artist'      => $artist
				, 'sort'        => $sort[ 0 ]
				, 'lisort'      => $sort[ 1 ]
			);
		}
		$data = sortData( $data, $index );
	}
	echo json_encode( $data );
} else if ( isset( $_POST[ 'volume' ] ) ) {
	$volume = $_POST[ 'volume' ];
	$volumemute = $redis->hGet( 'display', 'volumemute' );
	if ( $volume == 'setmute' ) {
		if ( $volumemute == 0 ) {
			$currentvol = exec( "mpc volume | tr -d ' %' | cut -d':' -f2" );
			$vol = 0;
		} else {
			$currentvol = 0;
			$vol = $volumemute;
		}
	} else {
		$currentvol = 0;
		$vol = $volume;
	}
	$redis->hSet( 'display', 'volumemute', $currentvol );
	exec( 'mpc volume '.$vol );
	pushstream( 'volume', array( $vol, $currentvol ) );
} else if ( isset( $_POST[ 'power' ] ) ) {
	$mode = $_POST[ 'power' ];
	if ( $mode === 'screenoff' ) {
		exec( 'export DISPLAY=:0; xset dpms force off' );
		exit();
	}
	
	// dual boot
	exec( "$sudo/mount | /usr/bin/grep -q mmcblk0p8 && /usr/bin/echo 8 > /sys/module/bcm2709/parameters/reboot_part" );
	
	if ( file_exists( '/srv/http/gpio/gpiooff.py' ) ) $cmd.= "$sudosrv/gpio/gpiooff.py;";
	if ( $redis->get( local_browser ) === '1' ) $cmd .= "$sudo/killall Xorg; /usr/local/bin/ply-image /srv/http/assets/img/bootsplash.png;";
	$cmd.= "$sudo/umount -f -a -t cifs nfs -l;";
	$cmd.= "$sudo/shutdown ".( $mode === 'reboot' ? '-r' : '-h' ).' now';
	exec( $cmd );
} else if ( isset( $_POST[ 'dirble' ] ) ) {
	$querytype = $_POST[ 'dirble' ];
	$args = isset( $_POST[ 'args' ] ) ? $_POST[ 'args' ] : '';
	if ( $querytype === 'categories' ) {
		$query = 'categories/primary';
	} else if ( $querytype === 'childs' ) {
		$query = 'category/'.$args.'/childs';
	} else if ( $querytype === 'stations' ) {
		$query = 'category/'.$args.'/stations';
	}
	$data = curlGet( 'http://api.dirble.com/v2/'.$query.'?token='.$redis->hGet('dirble', 'apikey') );
	$array = json_decode( $data, true );
	$aL = count( $array );
	for( $i = 0; $i < $aL; $i++ ) {
		$name = $array[ $i ][ 'title' ] ?: $array[ $i ][ 'name' ];
		$sort = stripLeading( $name );
		$index[] = $sort[ 1 ];
		$array[ $i ][ 'sort' ] = $sort[ 0 ];
		$array[ $i ][ 'lisort' ] = $sort[ 1 ];
	}
	$data = sortData( $array, $index );
	echo json_encode( $data );
} else if ( isset( $_POST[ 'jamendo' ] ) ) {
	$apikey = $redis->hGet( 'jamendo', 'clientid' );
	$args = $_POST[ 'jamendo' ];
	if ( $args ) {
		echo curlGet( 'http://api.jamendo.com/v3.0/radios/stream?client_id='.$apikey.'&format=json&name='.$args );
		exit();
	}
	
	$array = json_decode( curlGet('http://api.jamendo.com/v3.0/radios/?client_id='.$apikey.'&format=json&limit=200' ) );
	foreach ( $array->results as $station ) {
		$channel = json_decode( curlGet('http://api.jamendo.com/v3.0/radios/stream?client_id='.$apikey.'&format=json&name='.$station->name ) );
		$station->stream = $channel->results[ 0 ]->stream;
		$sort = stripLeading( $station->dispname );
		$index[] = $sort[ 1 ];
		$station->sort = $sort[ 0 ];;
		$station->lisort = $sort[ 1 ];;
	}
	usort( $array->results, function( $a, $b ) {
		return strnatcmp( $a->sort, $b->sort );
	} );
	$result = $array->results;
	$result[] = array( 'index' => $index );
	echo json_encode( $result );
}
function stripLeading( $string ) {
	// strip articles | non utf-8 normal alphanumerics , fix: php strnatcmp ignores spaces + tilde for sort last
	$names = strtoupper( strVal( $string ) );
	$stripped = preg_replace(
		  array( '/^A\s+|^AN\s+|^THE\s+|[^\w\p{L}\p{N}\p{Pd} ~]/u', '/\s+/' )
		, array( '', '-' )
		, $names
	);
	$init = mb_substr( $stripped, 0, 1, 'UTF-8' );
	return array( $stripped, $init );
}
function sortData( $data, $index = null ) {
	usort( $data, function( $a, $b ) {
		return strnatcmp( $a[ 'sort' ], $b[ 'sort' ] );
	} );
	unset( $data[ 'sort' ] );
	if ( $index ) $data[][ 'index' ] = array_keys( array_flip( $index ) ); // faster than array_unique
	return $data;
}
function search2array( $result, $playlist = '' ) { // directories or files
	$lists = explode( "\n", rtrim( $result ) );
	$genre = $composer = $albumartist = '';
	foreach( $lists as $list ) {
		$root = in_array( explode( '/', $list )[ 0 ], [ 'USB', 'NAS', 'LocalStorage' ] );
		if ( $root ) {
			$ext = pathinfo( $list, PATHINFO_EXTENSION );
			if ( in_array( $ext, [ 'cue', 'm3u', 'm3u8', 'pls' ] ) ) {
				$data[] = array(
					  'playlist' => basename( $list )
					, 'filepl'   => $list
				);
			} else {
				$sort = stripLeading( basename( $list ) );
				$index[] = $sort[ 1 ];
				$data[] = array(
					  'directory' => $list
					, 'sort'      => $sort[ 0 ]
					, 'lisort'    => $sort[ 1 ]
				);
			}
		} else {
			$list = explode( '^^', rtrim( $list ) );
			$file = $list[ 4 ];
			$data[] = array(
				  'Title'  => $list[ 0 ] ?: '<gr>*</gr>'.pathinfo( $file, PATHINFO_FILENAME )
				, 'Time'   => $list[ 1 ]
				, 'Artist' => $list[ 2 ]
				, 'Album'  => $list[ 3 ]
				, 'file'   => $file
			);
			$index = [];
			if ( !$genre ) {
				if ( $list[ 5 ] !== '' ) $genre = $list[ 5 ];
			} else {
				if ( $list[ 5 ] !== $genre ) $genre = -1;
			}
			if ( !$composer && $list[ 6 ] !== '' ) $composer = $list[ 6 ];
			if ( !$albumartist && $list[ 7 ] !== '' ) $albumartist = $list[ 7 ];
		}
	}
	if ( $root ) $data = sortData( $data, $index );
	$data[][ 'artist' ] = $data[ 0 ][ 'Artist' ];
	$data[][ 'album' ] = $data[ 0 ][ 'Album' ];
	$data[][ 'albumartist' ] = $albumartist ?: $data[ 0 ][ 'Artist' ];
	if ( $genre ) $data[][ 'genre' ] = $genre;
	if ( $composer ) $data[][ 'composer' ] = $composer;
	return $data;
}
function list2array( $result ) {
	$lists = explode( "\n", rtrim( $result ) );
	$artist = $album = $genre = $composer = $albumartist = $file = '';
	foreach( $lists as $list ) {
		$list = explode( '^^', rtrim( $list ) );
		$cuem3u = isset( $list[ 8 ] ) ? $list[ 8 ] : '';
		if ( $cuem3u !== $prevcue ) {
			$prevcue = $cuem3u;
			$i = 1;
		}
		$file = $list[ 3 ];
		$track = $list[ 2 ] ?: dirname( $file );
		$webradio = substr( $track, 0, 4 ) === 'http';
		if ( $webradio ) {
			$filename = str_replace( '/', '|', $file );
			$webradiofile = "/srv/http/assets/img/webradios/$filename";
			if ( !file_exists( $webradiofile ) ) $webradiofile = "/srv/http/assets/img/webradiopl/$filename";
			if ( file_exists( $webradiofile ) ) {
				$content = file_get_contents( $webradiofile );
				$nameimg = explode( '^^', $content );
				if ( count( $nameimg ) > 1 ) {
					$title = $nameimg[ 0 ];
				} else {
					$title = $content;
				}
			} else {
				$title = $file;
			}
		} else if ( $list[ 0 ] ) {
			$title = $list[ 0 ];
		} else {
			$title = basename( $file );
		}
		if ( !$artist && $list[ 4 ] !== '' ) $artist = $list[ 4 ];
		if ( !$album && $list[ 5 ] !== '' ) $album = $list[ 5 ];
		if ( !$genre ) {
			if ( $list[ 6 ] !== '' ) $genre = $list[ 6 ];
		} else {
			if ( $list[ 6 ] !== $genre ) $genre = -1;
		}
		if ( !$composer && $list[ 7 ] !== '' ) $composer = $list[ 7 ];
		$data[] = array(
			  'file'  => $file
			, 'track' => $track
			, 'Title' => $title
			, 'Time'  => $list[ 1 ]
			, 'index' => $i++
			, 'cuem3u'   => $cuem3u
		);
	}
	if ( !$webradio ) {
		$data[][ 'artist' ] = $artist;
		$data[][ 'album' ] = $album;
		$data[][ 'albumartist' ] = $albumartist ?: $data[ 0 ][ 'Artist' ];
		if ( $genre ) $data[][ 'genre' ] = $genre;
		if ( $composer ) $data[][ 'composer' ] = $composer;
	}
	return $data;
}
function loadCue( $mpc ) { // 'mpc ls "path" | mpc add' from enhancecontext.js
	if ( substr( $mpc, 0, 8 ) !== 'mpc ls "' ) return;
	
	$ls = chop( $mpc, ' | mpc add' );
	$result = shell_exec( $ls );
	$lists = explode( "\n", rtrim( $result ) );
	$cuefiles = preg_grep( '/.cue$/', $lists );
	if ( count( $cuefiles ) ) {
		asort( $cuefiles );
		foreach( $cuefiles as $cue ) {
			shell_exec( 'mpc load "'.$cue.'" | mpc add' );
		}
		return 1;
	}
}
function getCover( $file ) {
	require_once( '/srv/http/enhancegetcover.php' );
	return getCoverart( '/mnt/MPD/'.$file );
}
function pushstream( $channel, $data ) {
	$ch = curl_init( 'http://localhost/pub?id='.$channel );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type:application/json' ) );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $data, JSON_NUMERIC_CHECK ) );
	curl_exec( $ch );
	curl_close( $ch );
}
function curlGet( $url ) {
	$ch = curl_init( $url );
	curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT_MS, 400 );
	curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
	curl_setopt( $ch, CURLOPT_HEADER, 0 );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	$response = curl_exec( $ch );
	curl_close( $ch );
	return $response;
}
function getBookmark() {
	$dir = '/srv/http/assets/img/bookmarks';
	$files = array_slice( scandir( $dir ), 2 );
	if ( !count( $files ) ) return 0;
	
	$time = time();
	foreach( $files as $file ) {
		$content = file_get_contents( "$dir/$file" );
		$isimage = substr( $content, 0, 10 ) === 'data:image';
		if ( $isimage ) {
			$name = '';
			$coverart = $content;
		} else {
			$name = $content;
			$coverart = '';
		}
		$data[] = array(
			  'name'     => $name
			, 'path'     => str_replace( '|', '/', $file )
			, 'coverart' => $coverart
		);
	}
	return $data;
}
function getLibraryCount() {
	$redis = new Redis();
	$redis->pconnect( '127.0.0.1' );
	$count = exec( '/srv/http/enhancecount.sh' );
	$count = explode( ' ', $count );
	$status = array(
		  'artist'       => $count[ 0 ]
		, 'album'        => $count[ 1 ]
		, 'song'         => $count[ 2 ]
		, 'albumartist'  => $count[ 3 ]
		, 'composer'     => $count[ 4 ]
		, 'genre'        => $count[ 5 ]
		, 'nas'          => $count[ 6 ]
		, 'usb'          => $count[ 7 ]
		, 'webradio'     => $count[ 8 ]
	);
	return $status;
}
function lsPlaylists() {
	$lines = shell_exec( 'mpc lsplaylists' );
	if ( $lines ) {
		$lists = explode( "\n", rtrim( $lines ) );
		foreach( $lists as $list ) {
			$sort = stripLeading( $list );
			$index[] = $sort[ 1 ];
			$data[] = array(
				  'name'   => $list
				, 'sort'   => $sort[ 0 ]
				, 'lisort' => $sort[ 1 ]
			);
		}
		$data = sortData( $data, $index );
		return $data;
	} else {
		return 0;
	}
}
