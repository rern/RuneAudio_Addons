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
> must use script default `### template` lines except non-install addons  
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

# required
alias=<alias>

### template - import default variables, functions
. /srv/http/title.sh

### template - function - start message, installed check
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

### template - function - save version to database, finish message
installfinish $1

# extra info
title -nt "extra info"
```

**uninstall script** - `/usr/local/bin/uninstall\_<alias>.sh`  
```sh
#!/bin/bash

# required
alias=<alias>

### template - import default variables, functions
. /srv/http/title.sh

### template - function - start message, installed check
uninstallstart $1

# start main script ----------------------------------------------------------------------------------->>>

echo -e "$bar Remove files ..."
rm -v /<path>/<file>

echo -e "$bar Restore files ..."
sed 's/new/existing/' /<path>/<file>
mv /<path>/<file>{.backup,}

# end main script -----------------------------------------------------------------------------------<<<

### template - function - remove version from database, finish message
uninstallfinish $1
```
    
**2. an `array()` in `/srv/http/addonslist.php`**  
`'alias'`, `'title'`, `'* version'` : must be in sequence for `installstart`  
`'* ...'` : optional 
```php
array(
	'alias'         => 'alias',
	'title'         => 'title',
	'* version'     => 'version',
	'maintainer'    => 'maintainer',
	'description'   => 'description',
	'* thumbnail'   => 'https://url/to/image/w100px',
	'* buttonlabel' => 'install button label',
	'sourcecode'    => 'https://url/to/sourcecode',
	'installurl'    => 'https://url/for/wget/install.sh'
	'option'        => "{ 
		'alert': 'message text',
		'confirm': 'message text',
		'confirm1': 'message text 1',
		'confirm2': 'message text 2',
		'prompt': {
			'message': 'message text',
			'label': 'label text'
		},
		'password': {
			'message': 'message text',
			'label': 'label text'
		},
		'radio': {
			'message': 'message text',
			'list': {
				'*item1': 'value1',
				'item2': 'value2'
			}
		},
		'checkbox': {
			'message': 'message text',
			'list': {
				'item1': 'value1',
				'*item2': 'value2'
			}
		},
		'select': {
			'message': 'message text',
			'list': {
				'item1': 'value1',
				'item2': 'value2'
			}
		}
	}"

),
```

**alias** for addon reference  
- must be unique

**version:** for buttons enable/disable  
- `'version'` stored/removed from database > disable/enable buttons
- change `'version'` > show `Update` button
- non-install addons:
	- omit > `Install` button always enable, no `Uninstall` button
- run once addons:
	- omit but `redis-cli hset addons <alias> 1` in install script > `Install` button disable after run
    
**description:** for summary  
- text / html
- detail should be a linked to source code

**option:** for user input dialogs  
- each input will be appended as <install>.sh arguments
- options must be **single quoted** json,` key:value` format
- `*` leading `itemN` = pre-select items
- multiple dialogs of the same type must add trailing numbers to make `key`s unique
- multiple `confirm` dialog should be switched to `checkbox`
- `message text`, `label text`, `itemN`, `valueN` will be parsed as html - use entity for:
    - `&quot;` = `"`
    - `&#039;` = `'`
    - `&amp;`  = `&`
    - `&lt;`   = `<`
    - `&gt;`   = `>` 
```
