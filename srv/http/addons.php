<?php
include 'addonshead.php';
if ( $redisaddons[ 'expa' ] ) {
	$mbunpart = 0;
} else {
	exec( '/usr/bin/sudo /usr/bin/fdisk -l /dev/mmcblk0', $fdisk );
	$fdisk = array_values( $fdisk );
	$sectorbyte = preg_replace( '/.*= (.*) bytes/', '${1}', implode( preg_grep( '/^Units/', $fdisk ) ) );
	$sectorall = preg_replace( '/.* (.*) sectors/', '${1}', implode( preg_grep( '/sectors$/', $fdisk ) ) );
	$sectorused = preg_split( '/\s+/', end( $fdisk ) )[ 2 ];
	$mbtotal = round( $sectorall * $sectorbyte / 1024 / 1024 );
	$mbunpart = round( ( $sectorall - $sectorused ) * $sectorbyte / 1024 / 1024 );
	
	if ( $mbunpart < 10 ) $redis->hSet( 'addons', 'expa', 1 );
}
$mbtotal = isset( $mbtotal ) ? $mbtotal : round( disk_total_space( '/' ) / 1000000 );
$mbfree = round( disk_free_space( '/' ) / 1000000 );
$wtotal = 200;
$wfree = round( ( $mbfree / $mbtotal ) * $wtotal );
$wunpart = round( ( $mbunpart / $mbtotal ) * $wtotal );
$wused = $wtotal- $wfree - $wunpart;
$available = '<white>'.( $mbfree < 1000 ? $mbfree.' MB' : round( $mbfree / 1000, 2 ).' GB' ).'</white> free';
$expandable = ( $mbunpart < 10 ) ? '' : ( ' ● <a>'.( $mbunpart < 1000 ? $mbunpart.' MB' : round( $mbunpart / 1000, 2 ).' GB' ).'</a> expandable' );
echo '
<div class="container">
	<a id="close" class="close-root" href="/"><i class="fa fa-times fa-2x"></i></a>
	<h1><i class="fa fa-addons"></i> ADDONS</h1>
	<legend class="bl">
		<div id="diskused" style="width: '.$wused.'px;"></div><div id="diskfree" style="width: '.$wfree.'px;"></div><div id="diskunpart" style="width: '.$wunpart.'px;"></div>&ensp;'.$available.$expandable.'
	</legend>
	<a id="issues" href="http://www.runeaudio.com/forum/addons-menu-install-addons-the-easy-way-t5370-1000.html" target="_blank">issues&ensp;<i class="fa fa-external-link"></i>
	</a>
';
// ------------------------------------------------------------------------------------
$list = '';
$blocks = '';
// sort
$arraytitle = array_column( $addons, 'title' );
$addoindex = array_search( 'Addons Menu', $arraytitle );
$arraytitle[ $addoindex ] = 0;
$updatecount = 0;
array_multisort( $arraytitle, SORT_NATURAL | SORT_FLAG_CASE, $addons );
$arrayalias = array_keys( $addons );
foreach( $arrayalias as $alias ) {
	$addon = $addons[ $alias ];
	// hide by conditions
	if ( $addon[ 'hide' ] === 1 ) continue;
	
	$thumbnail = isset( $addon[ 'thumbnail' ] ) ? $addon[ 'thumbnail' ] : '';
	if ( isset( $addon[ 'buttonlabel' ] ) ) {
		$buttonlabel = $addon[ 'buttonlabel' ];
	} else {
		$buttonlabel = '<i class="fa fa-plus-circle"></i>&ensp;Install';
	}
	
	if ( $redisaddons[ $alias ] || $redis->hGet( 'addons', $alias ) ) {
		$check = '<i class="fa fa-check status"></i> ';
		if ( !isset( $addon[ 'version' ] ) 
			|| $addon[ 'version' ] == $redisaddons[ $alias ] ) {
			// !!! mobile browsers: <button>s submit 'formtemp' with 'get' > 'failed', use <a> instead
			$btnin = '<a class="btn btn-default disabled">&ensp;'.$buttonlabel.'</a>';
		} else {
			$updatecount++;
			$check = '<i class="fa fa-refresh status"></i> ';
			$btnin = '<a class="btn btn-primary"><i class="fa fa-refresh"></i>&ensp;Update</a>';
		}
		$btnunattr = isset( $addon[ 'rollback' ] ) ?' rollback="'.$addon[ 'rollback' ].'"' : '';
		$btnun = '<a class="btn btn-default btnbranch"'.$btnunattr.'><i class="fa fa-minus-circle"></i>&ensp;Uninstall</a>';
	} else {
		$check = '';
		$needspace = isset( $addon[ 'needspace' ] ) ? $addon[ 'needspace' ] : 1;
		if ( $needspace < $mbfree ) {
			$btninclass =  'btnbranch';
			$btninattr = '';
		} else {
			$btninclass = 'btnneedspace';
			$btninattr = ' needspace="Need: <white>'.number_format( $needspace ).' MB</white><br>'.$available.$expandable.'"';
		}
		$btnin = '<a class="btn btn-default '.$btninclass.'"'.$btninattr.'>'.$buttonlabel.'</a>';
		$btnun = '<a class="btn btn-default disabled"><i class="fa fa-minus-circle"></i>&ensp;Uninstall</a>';
	}
	
	// addon list ---------------------------------------------------------------
	$title = $addon[ 'title' ];
	// hide Addons Menu in list
	if ( $alias !== 'addo' ) {
		if ( substr( $title, -1 ) === '*' ) {
			$last = array_pop( explode( ' ', $title ) );
			$listtitle = preg_replace( '/\**$/', '', $title );
			$star = '&nbsp;<a>'.str_replace( '*', '★', $last ).'</a>';
		} else {
			$listtitle = $title;
			$star = '';
		}
		if ( $check === '<i class="fa fa-refresh"></i> ' ) $listtitle = '<blue>'.$listtitle.'</blue>';
		$list .= '<li alias="'.$alias.'" title="Go to this addon">'.$check.$listtitle.$star.'</li>';
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
				&emsp;<p><a class="'.$revisionclass.'">'.$version.( $version ? '&ensp;<i class="fa fa-chevron-down"></i>' : '' ).'</a>
				&ensp;by<white>&ensp;'.$addon[ 'maintainer' ].'</white></p><a>&#129129</a>
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
$redis->hSet( 'addons', 'update', $updatecount );
// ------------------------------------------------------------------------------------
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
