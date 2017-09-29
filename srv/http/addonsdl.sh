#!/bin/bash

if (( $# == 0 )); then # skip redownload on update Addons Menu
	dl=$( wget -qN https://github.com/rern/RuneAudio_Addons/raw/master/srv/http/addonslist.php -P /srv/http )
	if [[ $? != 0 ]]; then
		if [[ $? == 5 ]]; then # github 'ca certificate failed' code > update time
			systemctl stop ntpd
			ntpdate pool.ntp.org
			systemctl start ntpd
			echo "$dl"
			[[ $? != 0 ]] && exit 1
		else
			exit 1
		fi
	fi
	wget -qN https://github.com/rern/RuneAudio_Addons/raw/master/srv/http/changelog.md -P /srv/http
fi

### changelog.md > addonslog.php
# remove ---------------------------------------------------------------
sed -e '/^```note/,/^```/ d                      # note block  > delete
' -e '/^\s*$/ d                                  # emptyline   > delete
# replace --------------------------------------------------------------
' -e $'s/\'/"/g                                  # singlequote > "
' /srv/http/changelog.md |
perl -pe 's|`(.*?)`|<code>\1</code>|g' |         # code   `  > <code>
perl -pe 's|\*\*(.*?)\*\*|<white>\1</white>|g' | # bold   ** > <white>
perl -pe 's|__(.*?)__|<white>\1</white>|g' |     # bold   ** > <white>
perl -pe 's|\*(.*?)\*|<em>\1</em>|g' |           # italic *  > <em>
perl -pe 's|_(.*?)_|<em>\1</em>|g' |             # italic _  > <em>
perl -pe 's|~~(.*?)~~|<strike>\1</strike>|g' |   # strike ~~ > <strike>
# php start -----------------------------------------------------------
sed -e '1 {
s/^### \|^## //
i\
<?php
s/^/$addonsversion = "/
s/$/";/
a\
$log = \
$addonsversion.'"'"'&emsp; <a id="detail">changelog &#x25BC</a><br>\
<div  id="message" style="display: none;">\
	<ul>
}
# replace --------------------------------------------------------------
' -e '/^### \|^## / {                     # bold   "### " > </ul>...<ul>
s/^### \|^## //
i\
	</ul>
a\
	<ul>
}
' -e '/^- / {                             # bullet "- " > <li>
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
