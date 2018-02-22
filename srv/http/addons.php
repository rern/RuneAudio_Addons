<?php
require_once( 'addonshead.php' );

$runeversion = ( $redis->get( 'release' ) == '0.4b' ) ? '0.4b' : '0.3';
$available = round( disk_free_space( '/' ) / 1024 / 1024 );
$expandable = round( exec( '/usr/bin/sudo /usr/bin/sfdisk -F | grep mmc | cut -d" " -f6' ) / 1024 / 1024 );
$expandable = $expandable > 10 ? ' (expandable: '.number_format( $expandable ).' MB)' : '';

$redisaddons = $redis->hGetAll( 'addons' );
// -------------------------------------------------------------------------------------------------
echo '
	<div class="container">
	<h1>ADDONS</h1><a id="close" href="/"><i class="fa fa-times fa-2x"></i></a>
	<legend class="bl">RuneAudio '.$runeversion.' ● available: '.number_format( $available ).' MB'.$expandable.'</legend>
	<a id="issues" href="http://www.runeaudio.com/forum/addons-menu-install-addons-the-easy-way-t5370-1000.html" target="_blank">
			issues&ensp;<i class="fa fa-external-link"></i>
	</a>
';
// -------------------------------------------------------------------------------------------------
$list = '';
$blocks = '';

// sort
$arraytitle = array_column( $addons, 'title' );
$addoindex = array_search( 'Addons Menu', $arraytitle );
$arraytitle[ $addoindex ] = 0;
array_multisort( $arraytitle, SORT_NATURAL | SORT_FLAG_CASE, $addons );
$arrayalias = array_keys( $addons );

foreach( $arrayalias as $alias ) {
	$addon = $addons[ $alias ];
	
	// hide by conditions
	$hide = $addon[ 'hide' ];
	if ( $hide ) {
		$hidden = 0;
		foreach ( $hide as $key => $val ) {
			if ( $key == 'only03' && $redis->get( 'release' ) == '0.4b' ) $hidden = 1;
			if ( $key == 'installed' && $redis->hGet( 'addons', $val ) != '' ) $hidden = 1;
			if ( $key == 'exec' ) {
				$hiddenexec = 1;
				foreach ( $val as $cmd ) {
					$command = str_replace( '\\', '', $cmd );
					$hiddenexec = ( $hiddenexec && exec( $command ) ) ? 1 : 0;
				}
				$hidden = ( $hiddenexec || $hidden );
			}
			if ( $key == 'php' ) {
				$hiddenphp = 1;
				foreach ( $val as $cmd ) {
					$command = str_replace( '\\', '', $cmd );
					$hiddenphp = ( $hiddenphp && $command ) ? 1 : 0;
				}
				$hidden = ( $hiddenphp || $hidden );
			}
		}
		if ( $hidden == 1 ) continue;
	}

	$thumbnail = isset( $addon[ 'thumbnail' ] ) ? $addon[ 'thumbnail' ] : '';
	$buttonlabel = isset( $addon[ 'buttonlabel' ]) ? $addon[ 'buttonlabel' ] : 'Install';
	
	if ( $redisaddons[ $alias ] || $redis->hGet( 'addons', $alias ) ) {
		$check = '<i class="fa fa-check"></i> ';
		if ( !isset( $addon[ 'version' ] ) 
			|| $addon[ 'version' ] == $redisaddons[ $alias ] ) {
			// !!! mobile browsers: <button>s submit 'formtemp' with 'get' > 'failed', use <a> instead
			$btnin = '<a class="btn btn-default disabled"><i class="fa fa-check"></i> '.$buttonlabel.'</a>';
		} else {
			$check = '<i class="fa fa-refresh"></i> ';
			$btnin = '<a class="btn btn-primary"><i class="fa fa-refresh"></i> Update</a>';
		}
		$btnun = '<a class="btn btn-default btnbranch"><i class="fa fa-close"></i> Uninstall</a>';
	} else {
		$check = '';
		$needspace = isset( $addon[ 'needspace' ] ) ? $addon[ 'needspace' ] : 1;
		if ( $needspace < $available ) {
			$btninclass =  'btnbranch';
			$btninattr = '';
		} else {
			$btninclass = 'btnneedspace';
			$btninattr = ' diskspace="Need: '.number_format( $needspace ).' MB - Available: '.number_format( $available ).' MB<br>'
				.$expandable.'"';
		}
		$btnin = '<a class="btn btn-default '.$btninclass.'"'.$btninattr.'><i class="fa fa-check"></i> '.$buttonlabel.'</a>';
		$btnun = '<a class="btn btn-default disabled"><i class="fa fa-close"></i> Uninstall</a>';
	}
	
	// addon list ---------------------------------------------------------------
	$title = $addon[ 'title' ];
	// hide Addons Menu in list
	if ( $alias !== 'addo' ) {
		$listtitle = preg_replace( '/\*$/', ' <a>●</a>', $title );
		if ( $check === '<i class="fa fa-refresh"></i> ' ) $listtitle = '<blue>'.$listtitle.'</blue>';
		$list .= '<li alias="'.$alias.'" title="Go to this addon">'.$check.$listtitle.'</li>';
	}
	// addon blocks -------------------------------------------------------------
	$version = isset( $addon[ 'version' ] ) ? $addon[ 'version' ] : '';
	$revisionclass = $version ? 'revision' : 'revisionnone';
	$revision = str_replace( '\\', '', $addon[ 'revision' ] ); // remove escaped [ \" ] to [ " ]
	$revision = '<li>'.str_replace( '<br>', '</li><li>', $revision ).'</li>';
	$description = str_replace( '\\', '', $addon[ 'description' ] );
	$sourcecode = $addon[ 'sourcecode' ];
	if ( $sourcecode ) {
		$detail = ' <a href="'.$sourcecode.'" target="_blank">&emsp;detail &nbsp;<i class="fa fa-external-link"></i></a>';
	} else {
		$detail = '';
	}
	$blocks .= '
		<div id="'.$alias.'" class="boxed-group">';
	if ( $thumbnail ) $blocks .= '
		<div style="float: left; width: calc( 100% - 110px);">';
	$blocks .= '
			<legend title="Back to top">'
				.$check.'<span>'.preg_replace( '/\s*\*$/', '', $title ).'</span>
				&emsp;<p><a class="'.$revisionclass.'">'.$version.'</a>
				&ensp;by<white>&ensp;'.$addon[ 'maintainer' ].'</white></p>
			</legend>
			<ul class="detailtext" style="display: none;">'
				.$revision.'
			</ul>
			<form class="form-horizontal" alias="'.$alias.'">
				<p class="detailtext">'.$description.$detail.'</p>'
				.$btnin; if ( $version ) $blocks .= ' &nbsp; '.$btnun;
	$blocks .= '
			</form>';
	if ( $thumbnail ) $blocks .= '
		</div>
		<img src="'.$thumbnail.'" class="thumbnail">
		<div style="clear: both;"></div>';
	$blocks .= '
		</div>';
}
// -------------------------------------------------------------------------------------------------
echo '
	<ul id="list">'.
		$list.'
	</ul>
	<br>
';
echo $blocks;
?>
</div>
<div id="bottom"></div>

<script>
	addons = JSON.parse( '<?php echo json_encode( $addons );?>' );
</script>
<script src="assets/js/vendor/jquery-2.1.0.min.js"></script>
<script src="assets/js/vendor/hammer.min.js"></script>
<script src="assets/js/addonsinfo.js"></script>
<script src="assets/js/addons.js"></script>
<script src="assets/js/vendor/bootstrap.min.js"></script>
<script src="assets/js/vendor/bootstrap-select.min.js"></script>

</body>
</html>
