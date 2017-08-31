<?php
$result = exec('wget -qN https://github.com/rern/RuneAudio/raw/master/Addons/addons.php -O /srv/http/addons.php; echo $?');
if ($result == 0) exec('/usr/bin/sudo /usr/bin/systemctl reload php-fpm &; sleep 1');
echo $result;
