<?php
exec( '/usr/bin/sudo /usr/bin/cp -r /srv/http/assets/default/* /srv/http' );
exec( '/usr/bin/sudo /usr/bin/rm /usr/local/bin/uninstall_addo.sh' );
exec( '/usr/bin/sudo /usr/bin/redis-cli del addons' );

opcache_reset();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="assets/css/runeui.css">
    <link rel="stylesheet" href="assets/css/addonsinfo.css">
</head>
<body>
	
<script src="assets/js/vendor/jquery-2.1.0.min.js"></script>
<script src="assets/js/addonsinfo.js"></script>
<script>
$(function() {
	info( {
		  icon    : '<i class="fa fa-info-circle fa-2x"></i>'
		, title   : 'RuneUI Restore'
		, message : 'RuneUI restored to default.'
		, ok      : function() {
			location.href = '/';
		  }
		, nox     : 1
	} );
} );
</script>

</body>
</html>
