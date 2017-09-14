Guideline
---

**Each addon requires:**  

1. bash script files (stored anywhere reviewable)  
2. an `array(...)` in `/srv/http/addonslist.php`  
3. a pull request for `/srv/http/addonslist.php`  
---
  
**1. bash script files:**  
use format and functions as of the following example  

- install script   - `<any_name>.sh`
  - use non-invasive modification so other addons can survive after install / uninstall
  - use modify over replace files unless necessary
- uninstall script - `/usr/local/bin/uninstall\_<alias>.sh`
  - `<alias>` must be unique
  - restore everything to pre-install state
  - no need for non-install type
- no update script required
  - update will be done by 'uninstall' > 'install'
  
**install script**
```sh
#!/bin/bash

alias=<alias>

# import default variables and functions (detail: https://github.com/rern/title_script)
wget -qN https://github.com/rern/title_script/raw/master/title.sh; . title.sh; rm title.sh

# function - start message, installed check
installstart $1

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

# function - save version to database, finish message
installfinish $1

# extra info
title -nt "extra info"

# RuneUI opcache will be cleared on 'Addons Terminal' closed
```

**uninstall script**
```sh
#!/bin/bash

# import functions for timer, heading, badge, wget
wget -qN https://github.com/rern/title_script/raw/master/title.sh; . title.sh; rm title.sh

# function - start message, installed check
uninstallstart $1

# remove files 
echo -e "$bar Remove files ..."
rm -v /<path>/<file>

# restore files
echo -e "$bar Restore files ..."
sed 's/new/existing/' /<path>/<file>

# function - remove version from database, finish message
uninstallfinish $1
```
    
**2. an `array()` in `/srv/http/addonslist.php`**  
```php
array(
	'alias'         => 'alias',
	'* version'     => 'version',
	'title'         => 'title',
	'maintainer'    => 'maintainer',
	'description'   => 'description',
	'* thumbnail'   => 'https://url/to/image/w100px',
	'* buttonlabel' => 'install button label',
	'sourcecode'    => 'https://url/to/sourcecode',
	'installurl'    => 'https://url/for/wget/install.sh'
	'* option'      => '!confirm;'
	                  .'?yes/no;'
	                  .'#password;'
	                  ."input line 1\n"
	                      ."input line 2"
),
```
`'alias'`, `'version'`, `'title'` : must be in this order  
`'* ...'` : optional  

**alias**  
- must be unique

**version:** for buttons enable/disable  
- `'version'` stored/removed from database > disable/enable buttons
- change `'version'` > show `Update` button
- non-install addons:
	- (none) + (none)          - `Install` button always enable, no `Uninstall` button
	- (none) + 'install scipt' - `Install` button disable after run (run once)
    
**description:**  
- html allowed  

**option:** for user input  
- each input will be appended as <install>.sh arguments
- `;` = delimiter each input
- message
```
select type by start with:
    ! = 'js confirm' continue => ok = continue, cancel = exit
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
