<?php
require_once( 'addonshead.php' );

$diskspace = number_format( round( disk_free_space('/') / 1024 / 1024 ) );
// -------------------------------------------------------------------------------------------------
echo '
	<div class="container">
	<h1>ADDONS</h1><a id="close" href="/"><i class="fa fa-times fa-2x"></i></a>
	<a id="issues" href="http://www.runeaudio.com/forum/addons-menu-install-addons-the-easy-way-t5370-1000.html" target="_blank">
			issues&ensp;<i class="fa fa-external-link"></i>
	</a>
	<p id="diskspace"> available space: '.$diskspace.' MB</p>
';
// -------------------------------------------------------------------------------------------------
$redis = new Redis(); 
$redis->pconnect( '127.0.0.1' );

$GLOBALS[ 'release' ] = $redis->get( 'release' );
$GLOBALS[ 'redis' ] = $redis->hGetAll( 'addons' );
$GLOBALS[ 'list' ] = '';
$GLOBALS[ 'blocks' ] = '';
//$GLOBALS[ 'addons' ] = $addons;

// sort
$arraytitle = array_column( $addons, 'title' );
$arraytitle[ 0 ] = 0;
array_multisort( $arraytitle, SORT_NATURAL | SORT_FLAG_CASE, $addons );
$arraytitle[ 0 ] = 'Addons Menu';
$arrayalias = array_keys( $addons );

foreach( $arrayalias as $alias ) {
	addonblock( $alias );
}
// -------------------------------------------------------------------------------------------------
echo '
	<ul id="list">'.
		$list.'
	</ul>
	<br>
';
echo $blocks;
// -------------------------------------------------------------------------------------------------
function addonblock( $alias ) {
	$addon = $GLOBALS[ 'addons' ][ $alias ];
	if ( $GLOBALS[ 'release' ] == '0.4b' && isset( $addon[ 'only03' ] ) ) return;
	
	$thumbnail = isset( $addon[ 'thumbnail' ] ) ? $addon[ 'thumbnail' ] : '';
	$buttonlabel = isset( $addon[ 'buttonlabel' ]) ? $addon[ 'buttonlabel' ] : 'Install';
	
	if ( $GLOBALS[ 'redis' ][ $alias ] || file_exists( "/usr/local/bin/uninstall_$alias.sh" ) ) {
		$check = '<i class="fa fa-check"></i> ';
		if ( !isset( $addon[ 'version' ] ) || $addon[ 'version' ] == $GLOBALS[ 'redis' ][ $alias ] ) {
			// !!! mobile browsers: <button>s submit 'formtemp' with 'get' > 'failed', use <a> instead
			$btnin = '<a class="btn btn-default disabled"><i class="fa fa-check"></i> '.$buttonlabel.'</a>';
		} else {
			$btnin = '<a class="btn btn-primary"><i class="fa fa-refresh"></i> Update</a>';
		}
		$btnun = '<a class="btn btn-default"><i class="fa fa-close"></i> Uninstall</a>';
	} else {
		$check = '';
		$btnin = '<a class="btn btn-default btnin"><i class="fa fa-check"></i> '.$buttonlabel.'</a>';
		$btnun = '<a class="btn btn-default disabled"><i class="fa fa-close"></i> Uninstall</a>';
	}
	
	// addon list ---------------------------------------------------------------
	$title = $addon[ 'title' ];
	// hide Addons Menu in list
	if ( $alias !== 'addo' ) {
		$listtitle = preg_replace( '/\*$/', ' <a>●</a>', $title );
		$GLOBALS[ 'list' ] .= '<li alias="'.$alias.'" title="Go to this addon">'.$check.$listtitle.'</li>';
	}
	// addon blocks -------------------------------------------------------------
	$version = isset( $addon[ 'version' ] ) ? $addon[ 'version' ] : '';
	$revisionclass = $version ? 'revision' : 'revisionnone';
	$revision = '<ul id="list"><li>';
	$revision = preg_replace( '<br>', '</li><li>', $addon[ 'revision' ] ).'</li></ul><br>';
	$sourcecode = $addon[ 'sourcecode' ];
	if ( $sourcecode ) {
		$detail = ' <a href="'.$sourcecode.'" target="_blank">&emsp;detail &nbsp;<i class="fa fa-external-link"></i></a>';
	} else {
		$detail = '';
	}
	$GLOBALS[ 'blocks' ] .= '
		<div id="'.$alias.'" class="boxed-group">';
	if ( $thumbnail ) $GLOBALS[ 'blocks' ] .= '
		<div style="float: left; width: calc( 100% - 110px);">';
	$GLOBALS[ 'blocks' ] .= '
			<legend title="Back to top">'
				.$check.'<span>'.preg_replace( '/\s*\*$/', '', $title ).'</span>
				&emsp;<p><a class="'.$revisionclass.'">'.$version.'</a>
				&ensp;by<white>&ensp;'.$addon[ 'maintainer' ].'</white></p>
			</legend>
			<div class="detail" style="display: none;">
				<ul>'
					.$revision.'
				</ul>
			</div>
			<form class="form-horizontal" alias="'.$alias.'">
				<p>'.$addon[ 'description' ].$detail.'</p>'
				.$btnin; if ( $version ) $GLOBALS[ 'blocks' ] .= ' &nbsp; '.$btnun;
	$GLOBALS[ 'blocks' ] .= '
			</form>';
	if ( $thumbnail ) $GLOBALS[ 'blocks' ] .= '
		</div>
		<img src="'.$thumbnail.'" class="thumbnail">
		<div style="clear: both;"></div>';
	$GLOBALS[ 'blocks' ] .= '
		</div>';
}
// -------------------------------------------------------------------------------------------------
?>
</div>
<div id="bottom"></div>

<script>
addons = JSON.parse( '<?php echo json_encode( $GLOBALS[ 'addons' ] );?>' );
</script>
<script src="assets/js/vendor/jquery-2.1.0.min.js"></script>
<script src="assets/js/vendor/hammer.min.js"></script>
<script src="assets/js/addonsinfo.js"></script>
<script src="assets/js/addons.js"></script>
<script src="assets/js/vendor/bootstrap.min.js"></script>
<script src="assets/js/vendor/bootstrap-select.min.js"></script>

</body>
</html>
