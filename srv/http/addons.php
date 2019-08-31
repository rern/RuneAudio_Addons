<?php
$redis = new Redis();
$redis->pconnect( '127.0.0.1' );
$time = time();
$sudo = '/usr/bin/sudo /usr/bin';
$MiBused = exec( "df / | tail -n 1 | awk '{print $3 / 1024}'" );
$MiBavail = exec( "df / | tail -n 1 | awk '{print $4 / 1024}'" );
$MiBall = $MiBused + $MiBavail;

$Wall = 170;
$Wused = round( $MiBused / $MiBall * $Wall );
$Wavail = round( $MiBavail / $MiBall * $Wall );
$htmlused = '<p id="diskused" class="disk" style="width: '.$Wused.'px;">&nbsp;</p>';
$htmlavail = $Wavail ? '<p id="diskfree" class="disk" style="width: '.$Wavail.'px;">&nbsp;</p>' : '';
$htmlfree = '<white>'.( $MiBavail < 1024 ? round( $MiBavail, 2 ).' MiB' : round( $MiBavail / 1024, 2 ).' GiB' ).'</white> free';
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Rune Addons</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="msapplication-tap-highlight" content="no">
	<link rel="stylesheet" href="/assets/css/bootstrap.min.<?=$time?>.css">
	<style>
		@font-face {
			font-family: addons;
			src        : url( '/assets/fonts/addons.<?=$time?>.woff' ) format( 'woff' ),
			             url( '/assets/fonts/addons.<?=$time?>.ttf' ) format( 'truetype' );
			font-weight: normal;
			font-style : normal;
		}
	</style>
	<link rel="stylesheet" href="/assets/css/info.<?=$time?>.css">
	<link rel="stylesheet" href="/assets/css/addons.<?=$time?>.css">
	<link rel="icon" href="/assets/img/addons/addons.<?=$time?>.png">
</head>
<body>
<div class="container">
	<h1>
		<i class="fa fa-addons"></i>&ensp;Addons
		<i class="close-root fa fa-times"></i>
	</h1>
	<p class="bl"></p>
	<?=$htmlused.$htmlavail ?>&nbsp;
	<p id="disktext" class="disk"><?=$htmlfree?>&emsp;<i class="fa fa-addons"></i> e1.1</p>
	<a id="issues" class="disk" href="http://www.runeaudio.com/forum/addons-menu-install-addons-the-easy-way-t5370-1000.html" target="_blank">issues&ensp;<i class="fa fa-external-link"></i>
	</a>
<?php
// ------------------------------------------------------------------------------------
$list = '';
$blocks = '';
// sort
include 'addonslist.php';
$arraytitle = array_column( $addons, 'title' );
//$addoindex = array_search( 'Addons Menu', $arraytitle );
//$arraytitle[ $addoindex ] = 0;
$updatecount = 0;
array_multisort( $arraytitle, SORT_NATURAL | SORT_FLAG_CASE, $addons );
$arrayalias = array_keys( $addons );
foreach( $arrayalias as $alias ) {
	$addon = $addons[ $alias ];
	$versioninstalled = $redis->hGet( 'addons', $alias );
	// hide by conditions
	if ( $addon[ 'hide' ] ) continue;
	
	if ( isset( $addon[ 'buttonlabel' ] ) ) {
		$buttonlabel = $addon[ 'buttonlabel' ];
	} else {
		$buttonlabel = '<i class="fa fa-plus-circle"></i>Install';
	}
	
	if ( $versioninstalled ) {
		$check = '<i class="fa fa-check status"></i> ';
		$hide = '';
		if ( isset( $addon[ 'nouninstall' ] ) ) {
			$taphold = ' alias="'.$alias.'" style="pointer-events: unset"';
			$hide = ' hide';
		}
		if ( !isset( $addon[ 'version' ] ) || $addon[ 'version' ] == $versioninstalled ) {
			// !!! mobile browsers: <button>s submit 'formtemp' with 'get' > 'failed', use <a> instead
			$btnin = '<a class="btn btn-default disabled"'.$taphold.'>'.$buttonlabel.'</a>';
		} else {
			$updatecount++;
			$check = '<i class="fa fa-refresh status"></i> ';
			$btnin = '<a class="btn btn-primary" alias="'.$alias.'"><i class="fa fa-refresh"></i>Update</a>';
		}
		$btnunattr = isset( $addon[ 'rollback' ] ) ?' rollback="'.$addon[ 'rollback' ].'"' : '';
		$btnun = '<a class="btn btn-default'.$hide.'" alias="'.$alias.'"'.$btnunattr.'><i class="fa fa-minus-circle"></i>Uninstall</a>';
	} else {
		$check = '';
		$needspace = isset( $addon[ 'needspace' ] ) ? $addon[ 'needspace' ] : 1;
		if ( $needspace < $MiBavail ) {
			$attrspace = '';
		} else {
			$expandable = $MiBunpart < 1000 ? round( $MiBunpart ).' MB' : number_format( round( $MiBunpart / 1000 ) ).' GB';
			$attrspace = ' needmb="'.$needspace.'" space="Available: <white>'.round( $MiBavail ).' MB</white><br>Expandable: <white>'.$expandable.'</white>"';
		}
		$conflict = isset( $addon[ 'conflict' ] ) ? $addon[ 'conflict' ] : '';
		$conflictaddon = $conflict ? $redis->hget( 'addons', $conflict ) : '';
		$attrconflict = !$conflictaddon ? '' : ' conflict="'.preg_replace( '/ *\**$/', '', $addons[ $conflict ][ 'title' ] ).'"';
		$attrdepend = '';
		if ( isset( $addon[ 'depend' ] ) ) {
			$depend = $addon[ 'depend' ];
			$dependaddon = $redis->hget( 'addons', $depend );
			if ( !$dependaddon ) $attrdepend = ' depend="'.preg_replace( '/ *\**$/', '', $addons[ $depend ][ 'title' ] ).'"';
		}
		$btnin = '<a class="btn btn-default" alias="'.$alias.'"'.$btninclass.$attrspace.$attrconflict.$attrdepend.'>'.$buttonlabel.'</a>';
		$btnun = '<a class="btn btn-default disabled"><i class="fa fa-minus-circle"></i>Uninstall</a>';
	}
	
	// addon list ---------------------------------------------------------------
	$title = $addon[ 'title' ];
	if ( substr( $title, -1 ) === '*' ) {
		$last = array_pop( explode( ' ', $title ) );
		$listtitle = preg_replace( '/\**$/', '', $title );
		$star = '&nbsp;<a>'.str_replace( '*', '★', $last ).'</a>';
	} else {
		$listtitle = $title;
		$star = '';
	}
	if ( $check === '<i class="fa fa-refresh status"></i> ' ) $listtitle = '<blue>'.$listtitle.'</blue>';
	$list.= '<li alias="'.$alias.'" title="Go to this addon">'.$check.$listtitle.$star.'</li>';
	// addon blocks -------------------------------------------------------------
	$version = isset( $addon[ 'version' ] ) ? $addon[ 'version' ] : '';
	$revisionclass = $version ? 'revision' : 'revisionnone';
	$revision = str_replace( '\\', '', $addon[ 'revision' ] ); // remove escaped [ \" ] to [ " ]
	$revision = '<li>'.str_replace( '<br>', '</li><li>', $revision ).'</li>';
	$description = str_replace( '\\', '', $addon[ 'description' ] );
	$sourcecode = $addon[ 'sourcecode' ];
	if ( $sourcecode && $addon[ 'buttonlabel' ] !== 'Link' ) {
		$detail = '<br><a href="'.$sourcecode.'" target="_blank">detail&ensp;<i class="fa fa-external-link"></i></a>';
	} else {
		$detail = '';
	}
	$blocks .= '
		<div id="'.$alias.'" class="boxed-group">';
	$thumbnail = $addon[ 'thumbnail' ] ?: '';
	if ( $thumbnail ) $blocks .= '
		<div style="float: left; width: calc( 100% - 110px);">';
	$blocks .= '
			<legend title="Back to top">'
				.$check.'<span>'.preg_replace( '/\**$/', '', $title ).'</span>
				&emsp;<p><a class="'.$revisionclass.'">'.$version.( $version ? '&ensp;<i class="fa fa-chevron-down"></i>' : '' ).'</a>
				&ensp;by<white>&ensp;'.$addon[ 'maintainer' ].'</white></p><i class="fa fa-arrow-up"></i>
			</legend>
			<ul class="detailtext" style="display: none;">'
				.$revision.'
			</ul>
			<form class="form-horizontal" alias="'.$alias.'">
				<p class="detailtext">'.$description.$detail.'</p>';
	if ( $alias !== 'addo' ) $blocks .= $version ? $btnin.' &nbsp; '.$btnun : $btnin;
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
$redis->hSet( 'display', 'update', $updatecount );
// ------------------------------------------------------------------------------------
echo '
	<ul id="list">'.
		$list.'
	</ul>
';
echo $blocks;
?>
</div>
<p id="bottom"></p> <!-- for bottom padding -->
<div id="loader" class="hide"><i class="fa fa-addons blink"></i></div>

<?php
$keepkey = array( 'title', 'installurl', 'rollback', 'option' );
foreach( $arrayalias as $alias ) {
	if ( $alias === 'addo' ) continue;
	$addonslist[ $alias ] = array_intersect_key( $addons[ $alias ], array_flip( $keepkey ) );
}
$restart = $redis->get( 'restart' );
$redis->del( 'restart' );
?>
<script src="/assets/js/vendor/jquery-2.1.0.min.<?=$time?>.js"></script>
<script src="/assets/js/vendor/jquery.mobile.custom.min.<?=$time?>.js"></script>
<script src="/assets/js/info.<?=$time?>.js"></script>
<script src="/assets/js/addons.<?=$time?>.js"></script>
<script>
var addons = <?=json_encode( $addonslist )?>;
var restart = '<?=$restart?>';
if ( restart ) {
	setTimeout( function() {
		$.post( 'addonsdl.php', { bash: 'systemctl restart '+ restart } );
	}, 1000 );
}
</script>

</body>
</html>
