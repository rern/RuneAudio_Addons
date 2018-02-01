#!/bin/bash

kill $( ps -o pgid -C install.sh | tail -1 )
rm /var/lib/pacman/db.lck
