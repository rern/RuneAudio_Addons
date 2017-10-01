Guideline
---
_revision 20170927_

### Addons Menu Process:    
- Menu > Addons > download: `addonsdl.php`
	- `addonslist.php`
- Addons page: `addons.php`
	- revision and list from `addonslist.php` (link to each block)
	- each addon block from `addonslist.php`
	- install/uninstall/update status based on:
		- `uninstall_<alias>.sh` file - installed marker
		- `version` from `addonslist.php` vs database - buttons status
	- confirm dialog
	- user input dialogs for options
	- cancel by dialog `X` button
- Addons Terminal page: `addonsbash.php`
	- on-screen messages, stdout/stderr of bash scripts
	- `X` button > `opcache_reset()` and back to Addons page
---

### Each addon requires:  
1. `install.sh` and `uninstall_<alias>.sh` scripts
2. `array(...)` in `addonslist.php`
  

### 1. `install.sh` and `uninstall_<alias>.sh` scripts  

> bash script files stored anywhere reviewable  
> must use script default `### template` lines except non-install addons
> default variables and functions will take care most of on-screen messages and database data 
> use non-invasive modifications so other addons can survive after install / uninstall  

- install script  
	- use modify over replace files unless necessary
	- make backup if replace files
- uninstall script
	- restore everything to pre-install state
	- no need for non-install type
	- file path:
		- must be the same as `install.sh`
		- destination `/usr/local/bin/` for custom download
- update will be done by uninstall > install
  
**1.1  `install.sh` template**
```sh
#!/bin/bash

# required
alias=<alias>

### template - import default variables, functions
. /srv/http/addonstitle.sh

### template - function - start message, installed check
installstart $1
getuninstall # only if uninstall_<alias>.sh not in /usr/local/bin of 'master.zip'

# start main script ---------------------------------------------------------------------------------->>>

echo -e "$bar Get files ..."
wgetnc https://github.com/<name>/<repository>/archive/master.zip # whole repository download

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

cp -rv /tmp/install/* /
rm -r /tmp/install

echo -e "$bar Create new files ..."
echo 'content' > /<path>/<newfile>

echo -e "$bar Modify files ..."
file=<path>/<file>
echo $file
if ! grep -q 'check string' $file; then
	echo 'content' >> $file
	sed -i 's/existing/new/' $file
fi

# end main script ------------------------------------------------------------------------------------<<<

### template - function - save version to database, finish message
installfinish $1

# extra info if any
title -nt "extra info"
```

**1.2  `uninstall_<alias>.sh` template**
```sh
#!/bin/bash

# required
alias=<alias>

### template - import default variables, functions
. /srv/http/addonstitle.sh

### template - function - start message, installed check
uninstallstart $1

# start main script ----------------------------------------------------------------------------------->>>

echo -e "$bar Remove files ..."
rm -v /<path>/<file>

echo -e "$bar Restore files ..."
file=/<path>/<file>
echo $file
sed 's/new/existing/' $file
mv -v /<path>/<file>{.backup,}

# end main script -----------------------------------------------------------------------------------<<<

### template - function - remove version from database, finish message
uninstallfinish $1
```
  

### 2. `array(...)` in `addonslist.php`

**`array(...)` template**   
```php
array(
	'alias'         => 'alias',
	'± version'     => 'version',
	'title'         => 'title',
	'maintainer'    => 'maintainer',
	'description'   => 'description',
	'± thumbnail'   => 'https://url/to/image/w100px',
	'± buttonlabel' => 'install button label',
	'sourcecode'    => 'https://url/to/sourcecode',
	'installurl'    => 'https://url/for/wget/install.sh'
	'± option'        => "{ 
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
				'item2': 'value2',
				'custom': '?'
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
			'label': 'label text',
			'list': {
				'item1': 'value1',
				'item2': 'value2',
				'custom': '?'
			}
		}
	}"

),
```
`'± ...'` : optional  
  
**`'alias'`** - reference point
- must be 1st, at index `[0]`
- must be unique among aliases

**`'version'`** - buttons enable/disable  
- `'version'` stored/removed from database > disable/enable buttons
- change `'version'` > show `Update` button
- non-install addons:
	- omit > `Install` button always enable, no `Uninstall` button
- run once addons:
	- omit but `redis-cli hset addons <alias> 1` in install script > `Install` button disable after run

**`'option'`** - user inputs  
- each `'key': ...` open a dialog
- each `'value'` will be appended as `install.sh` arguments / parameters
- options must be **single quoted** json, `" 'key': 'value' "`
- `*` leading `itemN` = pre-select items
- dialog types:
	- `X` - cancel and back to main page
	- `'alert'` - wait > `Ok` = continue (no value)
	- `'confirm'` - 1 / 0 > `Yes` = 1 : `No` = 0
	- `'prompt'` - 1 input > `Ok` = input
	- `'password'` - masked input > `Ok` > verify input > `Ok` = input
	- `'radio'` - 1 choice > `Ok` = selected `valueN`
		- `*` pre-select must be specified
		- `'custom': '?'` - `?` >  `'prompt'` for custom value
	- `'checkbox'` - multiple choices > `Ok` = selected `valueN`s
		- `*` pre-select optional
	- `'select'` - 1 choice > `Ok` = selected `valueN`
		- `*` pre-select optional
		- `'custom': '?'` - `?` >  `'prompt'` for custom value
- multiple dialogs of the same type must add trailing numbers to make each `key` unique
---

**styling** for `description`, `option`
- text / html
- only quotes use html entities
    - `&quot;` = `"`
    - `&#039;` = `'`
- preset styles:
	- `<white>...</white>`
	- `<code>...</code>`
