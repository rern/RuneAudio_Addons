#!/bin/bash

# convert changelog.md to html

#   ## abc -> abc
#             <ul>
#   - def  -> <li>def</li>
#             </ul>
#             <br>

file=changelog.md

sed -e '/^\s*$/ d
' -e '1 s/## //
' -e '1 s/^/$addonsversion = "/
' -e '1 s/$/";/
' -e '1 a\
$log = \
$addonsversion.'"'"' &nbsp; <a id="detail">changelog &#x25BC</a><br>\
<div  id="message" style="display: none;">\
	<ul>
' -e '/^- / s/^/<li>/
' -e 's/^<li>- /	<li>/
' -e '/^\s*<li>/ s|$|</li>|;
' -e '/^## / i\
	</ul>\
	<br>
' -e 's/^## //
' -e '$ s/$/'"'"'/
' $file
