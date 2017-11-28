<?php
exec( '/usr/bin/sudo /usr/bin/cp -r /srv/http/assets/default/* /srv/http' );

exec( '/usr/bin/sudo /usr/bin/rm /usr/local/bin/uninstall_addo.sh' );

$redis = new Redis(); 
$redis->pconnect( '127.0.0.1' );
$redis->del( 'addons' );

// clear cache must be before echo
opcache_reset();

echo '<br>
RuneUI restored to default.<br>
Reinstall Addons Menu: Menu > Addons';
