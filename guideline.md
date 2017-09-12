Guideline
---
**`changelog.md`** of Addons Menu   
- file name and path cannot be change - Addons Menu uses it for:
    - get `version` number for both `install.sh` and `addonslist.php`  
    - get content to display on web page
- format must be:
```
## 20170901
- dark blue, 'quote', "double quote"
- *italic*, _italic_
- ~~strikethrough~~
- **white**, __white__
```

**Each addon requires:**  

1. bash script files
2. an `array(...)` in `/srv/http/addonslist.php`  
---
  
**1. bash script files:**  

- install script   - `<any_name>.sh`
  - use non-invasive modification so other addons can survive after install / uninstall
  - use modify over replace files unless necessary
- uninstall script - `/usr/local/bin/uninstall\_<alias>.sh`
  - `<alias>` must be unique
  - restore everything to pre-install state
  - no need for non-install type
- (update script)  - none
  - default update: reinstall by 'uninstall' > 'install'
  - `version` in this file vs install file > update button
  - `exit 1` for 'not found' check in uninstall to stop reinstall from running
  
**install script**
```sh
#!/bin/bash

version=<yyyymmdd>

# delete itself
rm $0

# import functions for timer, heading, badge, wget
    # detail: https://github.com/rern/title_script
wget -qN https://github.com/rern/title_script/raw/master/title.sh; . title.sh; rm title.sh

# start timer
timestart

# check 'already installed'
if [[ -e <file> ]] &> /dev/null; then
	echo -e "$info <title> already installed."
	exit 1
fi

# start install
[[ $1 != u ]] && title -l = "$bar Install $runepwd ..." # skip if update
echo -e "$bar Get files ..."
wgetnc https://github.com/<name>/<repository>/archive/master.zip

# backup existing files
echo -e "$bar Backup files ..."
mv /<path>/<file>{,.backup}

# add files
echo -e "$bar Install new files ..."
rm -rf /tmp/install
mkdir -p /tmp/install
bsdtar -xf master.zip --strip 1 --exclude '<directory>/' -C /tmp/install
rm master.zip /tmp/install/* &> /dev/null

chown -R http:http /tmp/install
chown -R root:root /tmp/install/usr/local/bin/uninstall*
chmod -R 755 /tmp/install

cp -rp /tmp/install/* /
rm -r /tmp/install

# create files
echo -e "$bar Install new files ..."
echo 'content' > <newfile>

# modify files
sed 's/existing/new/' /<path>/<file>

# stop timer
timestop

# finish install
if [[ $1 != u ]]; then
	title -l = "$bar <title> installed successfully."
	[[ -t 1 ]] && echo 'Uninstall: uninstall_<alias>.sh' # skip if web install
	title -nt "$info <additional info>"
else
	title -l = "$bar <title> updated successfully."
fi

# restart local browser if needed
if pgrep midori > /dev/null; then
	killall midori
	sleep 1
	xinit &> /dev/null &
fi
```

**uninstall script**
```sh
#!/bin/bash

# import functions for timer, heading, badge, wget
wget -qN https://github.com/rern/title_script/raw/master/title.sh; . title.sh; rm title.sh

# check not installed - exit code '1' needed
if [[ ! -e <file> ]]; then
	echo -e "$info <title> not found."
	exit 1
fi

# start uninstall
[[ $1 != u ]] && type=Uninstall || type=Update
title -l = $bar $type <title> ...

# remove files 
echo -e "$bar Remove files ..."
rm -v /<path>/<file>

# restore files
echo -e "$bar Restore files ..."
sed 's/new/existing/' /<path>/<file>

# finish uninstall
[[ $1 != u ]] && title -l = "$bar <title> uninstalled successfully." # skip if update

# delete itself
rm $0
```
    
**2. an `array()` in `/srv/http/addonslist.php`**  
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
