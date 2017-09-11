#!/bin/bash

if (( $# == 0 )); then
	wget -qN https://github.com/rern/RuneAudio_Addons/raw/master/srv/http/addonslist.php -P /srv/http
	wget -qN https://github.com/rern/RuneAudio_Addons/raw/master/changelog.md -P /srv/http
fi

# remove -------------------------------------------------------
sed -e '/^```note/,/^```/ d                   # note block
' -e '/^\s*$/ d                               # emptyline
# replace ------------------------------------------------------
' -e $'s/\'/"/g                               # single quote
' -e 's|\*\*\(.\+\)\*\*|<strong>\1</strong>|  # bold **
' -e 's|__\(.\+\)__|<strong>\1</strong>|      # bold __
' -e 's|\*\(.\+\)\*|<em>\1</em>|              # italic *
' -e 's|_\(.\+\)_|<em>\1</em>|                # italic _
' changelog.md |
# addonslog.php ------------------------------------------------
sed -e '1 {                                   # prepend
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
' -e '/^## / {                               # 'version'
s/^## //
i\
	</ul>
a\
	<ul>
}
' -e '/^- / {                                # bullet list
s/^- //
s/^/	<li>/
s|$|</li>|
}
' -e '$ a\
	</ul>\
	<br>\
</div>'"';"'
' > addonslog.php

rm changelog.md
