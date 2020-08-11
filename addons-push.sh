#!/bin/bash

file=/srv/http/addons.php
if ! grep -q 'addonhide === 1' $file; then
	wget -q https://github.com/rern/RuneAudio-Re5/raw/master$file -O $file
fi
