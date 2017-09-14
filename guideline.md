Guideline
---
**Addons Menu Process:**  
- Menu > Addons > download: `addonsdl.php`
  - `addonslist.php`
  - `changelog.md` > `addonsdl.sh` > `addonslog.php`
- Addons page: `addons.php`
  - list from `addonslist.php` (link to each block)
  - changelog from `addonslog.php` (toggle show/hide)
  - each addon block from `addonslist.php`
  - install/uninstall/update buttons based on `version` from `addonslist.php` vs database
  - user input options
- Addons Terminal page: `addonsbash.php`
  - on-screen messages (stdout/stderr of bash scripts)
  - nofify on finished
  - `X` button > `opcache_reset()` and back to Addons page
  
---

**Each addon requires:**  
1. install and uninstall scripts
2. an `array(...)` and a request to enlist it in Addons Menu
  
**1. install and uninstall scripts:**  
> bash script files stored anywhere reviewable  
> use format as of the following example  
> default variables and functions will take care most of on-screen messages and database
> use non-invasive modification so other addons can survive after install / uninstall  
> `<alias>` must be unique  

- install script  
  - use modify over replace files unless necessary
  - make backup if replace files
- uninstall script
  - restore everything to pre-install state
  - no need for non-install type
- no update script required
  - update will be done by 'uninstall' > 'install'
  
**install script** - `<any_name>.sh`  
```sh
#!/bin/bash

### required
alias=<alias>

### import default variables and functions
wget -qN https://github.com/rern/RuneAudio_Addons/raw/master/title.sh; . title.sh; rm title.sh

### function - start message, installed check
installstart $1

# start main script ---------------------------------------------------------------------------------->>>

echo -e "$bar Get files ..."
wgetnc https://github.com/<name>/<repository>/archive/master.zip

echo -e "$bar Backup files ..."
mv /<path>/<file>{,.backup}

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

echo -e "$bar Create new files ..."
echo 'content' > <newfile>

echo -e "$bar Modify files ..."
sed 's/existing/new/' /<path>/<file>

# end main script ------------------------------------------------------------------------------------<<<

### function - save version to database, finish message
installfinish $1

title -nt "extra info"

# RuneUI opcache will be cleared on 'Addons Terminal' closed
```

**uninstall script** - `/usr/local/bin/uninstall\_<alias>.sh`  
```sh
#!/bin/bash

### required
alias=<alias>

### import default variables and functions
wget -qN https://github.com/rern/RuneAudio_Addons/raw/master/title.sh; . title.sh; rm title.sh

# function - start message, installed check
uninstallstart $1

# start main script ----------------------------------------------------------------------------------->>>

echo -e "$bar Remove files ..."
rm -v /<path>/<file>

echo -e "$bar Restore files ..."
sed 's/new/existing/' /<path>/<file>
mv /<path>/<file>{.backup,}

# end main script -----------------------------------------------------------------------------------<<<

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
