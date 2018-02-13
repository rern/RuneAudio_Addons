<?php
$pgid = exec( '/usr/bin/sudo /usr/bin/ps -o pgid -C install.sh | /usr/bin/grep -o "[0-9]*"' );
posix_kill( $pgid, 9 );
