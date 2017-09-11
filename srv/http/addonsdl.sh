#!/bin/bash

if (( $# == 0 )); then
	wget -qN https://github.com/rern/RuneAudio_Addons/raw/master/srv/http/addonslist.php -P /srv/http
	wget -qN https://github.com/rern/RuneAudio_Addons/raw/master/changelog.md -P /srv/http
fi

sed -e '/^```note/,/^```/ d
' -e '/^\s*$/ d
' -e $'s/\'/"/g
' -e 's|\*\*\(.\+\)\*\*|<strong>\1</strong>|
' -e 's|__\(.\+\)__|<strong>\1</strong>|
' -e 's|\*\(.\+\)\*|<em>\1</em>|
' -e 's|_\(.\+\)_|<em>\1</em>|
' changelog.md | sed -e '1 {
s/^## //
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
' > addonslog.php

rm changelog.md
