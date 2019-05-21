#!/bin/bash

sed -i -e '/infoIcon/ {n;d}
' -e '/vertical-align: 12px/ i\
	float: left;
' /srv/http/assets/css/addonsinfo.css
