#!/bin/bash

if (( $# == 0 )); then # skip redownload for install.sh
	wget -qN https://github.com/rern/RuneAudio_Addons/raw/master/srv/http/addonslist.php -P /srv/http
	wget -qN https://github.com/rern/RuneAudio_Addons/raw/master/changelog.md -P /srv/http
fi

### changelog.md > addonslog.php
# remove ---------------------------------------------------------------
sed -e '/^```note/,/^```/ d               # note block
' -e '/^\s*$/ d                           # emptyline
# replace --------------------------------------------------------------
' -e $'s/\'/"/g                           # singlequote > "
' -e 's|\*\*\(.\+\)\*\*|<span>\1</span>|  # bold   "**" > <span> (white)
' -e 's|__\(.\+\)__|<span>\1</span>|      # bold   "__" > <span> (white)
' -e 's|\*\(.\+\)\*|<em>\1</em>|          # italic "*"  > <em>
' -e 's|_\(.\+\)_|<em>\1</em>|            # italic "_"  > <em>
' -e 's|~~\(.\+\)~~|<strike>\1</strike>|  # strike "~~" > <strike>
' changelog.md |
# php start -----------------------------------------------------------
sed -e '1 {
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
# replace --------------------------------------------------------------
' -e '/^## / {                           # bold   "## " > </ul>...<ul>
s/^## //
i\
	</ul>
a\
	<ul>
}
' -e '/^- / {                            # bullet "- " > <li>
s/^- //
s/^/	<li>/
s|$|</li>|
}
# php end --------------------------------------------------------------
' -e '$ a\
	</ul>\
	<br>\
</div>'"';"'
' > /srv/http/addonslog.php

rm /srv/http/changelog.md
