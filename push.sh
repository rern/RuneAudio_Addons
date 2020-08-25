#!/bin/bash

file=/srv/http/data/addons/rre4
if [[ -e $file ]]; then
	echo 20200822 > $file
	mv $file ${file:0:-1}5
fi
