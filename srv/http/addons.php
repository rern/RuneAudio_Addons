<?php
require_once( 'addonshead.php' );
require_once( 'addonslog.php' );
// -------------------------------------------------------------------------------------------------
echo '
	<div class="container">
	<h1>ADDONS</h1><a id="close" href="/"><i class="fa fa-times fa-2x"></i></a>
	<legend class="bl">Currently available:</legend>
';
// -------------------------------------------------------------------------------------------------
$redis = new Redis(); 
$redis->pconnect( '127.0.0.1' );

$GLOBALS[ 'addonsmenu' ] = 'Addons Menu';
$GLOBALS[ 'version' ] = $redis->hGetAll( 'addons' );
$GLOBALS[ 'list' ] = '';
$GLOBALS[ 'blocks' ] = '';

// sort
$arraytitle = array_column( $addons, 'title' );
array_multisort( $arraytitle, SORT_NATURAL | SORT_FLAG_CASE, $addons );
$length = count( $addons );
for ( $i = 0; $i < $length; $i++ ) {
	addonblock( $addons[ $i ] );
}
// -------------------------------------------------------------------------------------------------
echo '
	<ul id="list">'.
	$list.'
	</ul>'.
	$log.'
	<br>
';
echo $blocks;
// -------------------------------------------------------------------------------------------------
function addonblock( $pkg ) {
	$thumbnail = isset( $pkg[ 'thumbnail' ] ) ? $pkg[ 'thumbnail' ] : '';
	$buttonlabel = isset( $pkg[ 'buttonlabel' ]) ? $pkg[ 'buttonlabel' ] : 'Install';
	$alias = $pkg[ 'alias' ];
	$fileuninstall = file_exists( '/usr/local/bin/uninstall_'.$alias.'.sh' );
	
	if ( $GLOBALS[ 'version' ][ $alias ] && $fileuninstall ) {
		$check = '<i class="fa fa-check"></i> ';
		if ( !isset( $pkg[ 'version' ] ) || $pkg[ 'version' ] == $GLOBALS[ 'version' ][ $alias ] ) {
			// !!! mobile browsers: <button>s submit 'formtemp' with 'get' > 'failed', use <a> instead
			$btnin = '<a class="btn btn-default disabled"><i class="fa fa-check"></i> '.$buttonlabel.'</a>';
		} else {
			$btnin = '<a alias="'.$alias.'" class="btn btn-primary"><i class="fa fa-refresh"></i> Update</a>';
		}
		$btnun = '<a alias="'.$alias.'" class="btn btn-default btnun"><i class="fa fa-close"></i> Uninstall</a>';
	} else {
		if ( isset( $pkg[ 'option' ])) {
			$pkgoption = preg_replace( '/\n|\t/', '', $pkg[ 'option' ] );
			$pkgoption = htmlspecialchars( $pkgoption );
			$option = 'option="'.$pkgoption.'"';
		} else {
			$option = '';
		}
		$check = '';
		$btnin = '<a '.$option.' alias="'.$alias.'" class="btn btn-default"><i class="fa fa-check"></i> '.$buttonlabel.'</a>';
		$btnun = '<a class="btn btn-default disabled"><i class="fa fa-close"></i> Uninstall</a>';
	}
	
	// addon list
	$title = $pkg[ 'title' ];
	// Addons Menu: hide in list and change to actual title
	if ( $alias !== 'addo' ) {
		$listtitle = preg_replace( '/\*$/', ' <white>&star;</white>', $title );
		$GLOBALS[ 'list' ] .= '<li alias="'.$alias.'">'.$listtitle.'</li>';
	} else {
		$title = $GLOBALS[ 'addonsmenu' ];
	}
	// addon blocks
	$GLOBALS[ 'blocks' ] .= '
		<div id="'.$alias.'" class="boxed-group">';
	if ( $thumbnail ) $GLOBALS[ 'blocks' ] .= '
		<div style="float: left; width: calc( 100% - 110px);">';
	$GLOBALS[ 'blocks' ] .= '
			<legend>'.$check.strip_tags( preg_replace( '/\s*\*$/', '', $title ) ).'&emsp;<p>by<white>'.strip_tags( $pkg[ 'maintainer' ] ).'</white></p><a>&#x25B2</a></legend>
			<form class="form-horizontal">
				<p>'.$pkg[ 'description' ].' <a href="'.$pkg[ 'sourcecode' ].'" target="_blank">&emsp;detail &nbsp;<i class="fa fa-external-link"></i></a></p>'
				.$btnin; if ( isset( $pkg[ 'version' ] ) ) $GLOBALS[ 'blocks' ] .= ' &nbsp; '.$btnun;
	$GLOBALS[ 'blocks' ] .= '
			</form>';
	if ( $thumbnail ) $GLOBALS[ 'blocks' ] .= '
		</div>
		<div class="thumbnail" style="float: right; width: 100px;">
			<a href="'.$pkg[ 'sourcecode' ].'"><img src="'.$thumbnail.'"></a>
		</div>
		<div style="clear: both;"></div>';
	$GLOBALS[ 'blocks' ] .= '
		</div>';
}
// -------------------------------------------------------------------------------------------------
?>
</div>
<div id="bottom"></div>

<script src="assets/js/vendor/jquery-2.1.0.min.js"></script>
<script src="assets/js/vendor/bootstrap.min.js"></script>
<script src="assets/js/vendor/bootstrap-select.min.js"></script>
<script src="assets/js/vendor/hammer.min.js"></script>
<script src="assets/js/addonsinfo.js"></script>
<script src="assets/js/addons.js"></script>

</body>
</html>
