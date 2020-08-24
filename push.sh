#!/bin/bash

dir=/srv/http/data/addons
file=$dir/rre4
if [[ -e $file ]]; then
	rm $file
	sed -n '/rre5/ {n;n;s/.*:\s*"\(.*\)"/\1/ p}' $dir/addons-list.json > $dir/rre5
fi
