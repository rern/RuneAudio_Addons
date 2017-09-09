#!/bin/bash

wget -qN https://github.com/rern/RuneAudio_Addons/raw/master/srv/http/addonslist.php -P /srv/http
wget -qN https://github.com/rern/RuneAudio_Addons/raw/master/changelog.md -P /srv/http

sed -e '/^\s*$/ d
' -e '1 {
s/## //
i\
<?php
s/^/$addonsversion = "/
s/$/";/
a\
$log = \
$addonsversion.'"'"' &nbsp; <a id="detail">changelog &#x25BC</a><br>\
<div  id="message" style="display: none;">\
	<ul>
}
' -e '/^- / {
s/^- //
s/^/	<li>/
s|$|</li>|
}
' -e '/^## / {
s/^## //
i\
	</ul>
a\
	<ul>
}
' -e '$ a\
	</ul>\
	<br>\
</div>'"';"'
' changelog.md > addonslog.php

rm changelog.md
