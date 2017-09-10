Guideline
---

**Each addon requires:**  

1. 2 bash script files
2. an `array(...)` in `/srv/http/addonslist.php`  
---
  
**1. bash script files:**  

- install script   - <any_name>.sh
- uninstall script - /usr/local/bin/uninstall\_<unique_alias>.sh (no need for non-install addons)
- (update)         - none
  - use 'uninstall > install' to update
  - different `version` in this file and install file will show update button
  - `exit 1` for 'already installed' check to stop reinstall from running
```sh
version=yyyymmdd

# delete itself
rm $0

# import functions for timer, heading, badge, wget
    # detail: https://github.com/rern/title_script
wget -qN https://github.com/rern/title_script/raw/master/title.sh; . title.sh; rm title.sh

# start timer
timestart

# check 'already installed'
if [[ true ]] &> /dev/null; then
	echo -e "$info <title> already installed."
	exit
fi

# start heading
title -l = "$bar Install <title> ..."

# user input

# get uninstall script
wgetnc https://github.com/<path>/raw/master/uninstall_<alias>.sh -P /usr/local/bin

# backup existing files
echo -e "$bar Backup files ..."
mv /<path>/file{,backup}

# get files
echo -e "$bar Get <title> files ..."
wgetnc https://github.com/<path>/archive/master.zip
bsdtar -xf master.zip --strip 1 -C /
rm master.zip
# chmod, chown
# place files in destinations
# remove install files
# create files
# modify files

# finish info

# clear opcache if needed
# restart midori if needef
```
    
**2. an 'array()' in /srv/http/addonslist.php**  
```php
array(
	'* version'     => 'version',
	'title'         => 'title',
	'maintainer'    => 'maintainer',
	'description'   => 'description',
	'* thumbnail'   => 'https://url/to/image/w100px',
	'* buttonlabel' => 'install button label',
	'sourcecode'    => 'https://url/to/sourcecode',
	'installurl'    => 'https://url/for/wget/install.sh',
	'alias'         => 'alias (must be unique)',
	'* option'      => '!confirm;'
	                  .'?yes/no;'
	                  .'#password;'
	                  ."input line 1\n"
	                      ."input line 2"
),
```
`'* ...'` = optional  

**version:** for buttons enable/disable  
- specified both in `array(...)` and 'install script'
- version from 'install script' stored in database then disable/enable buttons
- database vs `array(...)` difference will show update button
- non-install addons:
	- (none) + (none)          - install button always enable, no uninstall button
	- (none) + 'install scipt' - install button disable after run (run once)
    
**description:** html allowed  

**option:** for user input  
- each input will be appended as <install>.sh arguments
- `;` = delimiter each input
- message
```
select type by start with:
    ! = 'js confirm' continue => ok = continue, cancel = exit install
    ? = 'js confirm' yes/no   => ok = 1,        cancel = 0
    # = 'js prompt'  password => ok = password, blank-ok/cancel = 0
      = 'js prompt'  input    => ok = input,    blank-ok/cancel = 0
message will be parsed as html, use entity code for:
    &quot; = "
    &#039; = '
    &amp;  = &
    &lt;   = <
    &gt;   = >  
multiple lines:
    "...\n"  = escaped n    - new line (must be inside double quotes)
    ."...\n" = starting dot - concatenate between lines
```
