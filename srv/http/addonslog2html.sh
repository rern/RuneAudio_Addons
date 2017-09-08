#!/bin/bash

# convert changelog.md to html

#   ## abc -> abc
#             <ul>
#   - def  -> <li>def</li>
#             </ul>
#             <br>

file=changelog.md

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
' $file > changelog.php
