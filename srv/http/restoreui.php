<?php
exec( '/usr/bin/sudo /usr/bin/cp -r /srv/http/assets/default/* /srv/http' );

exec( '/usr/bin/sudo /usr/bin/rm /usr/local/bin/uninstall_addo.sh' );

exec( '/usr/bin/sudo /usr/bin/redis-cli del addons' );

// clear cache must be before echo
opcache_reset();

echo '<br>
RuneUI restored to default.<br>
Reinstall Addons Menu: Menu > Addons';
