#!/bin/bash

pgid=$( ps -o pgid -C install.sh | grep -o "[0-9]*" )
kill $pgid
rm /var/lib/pacman/db.lck
