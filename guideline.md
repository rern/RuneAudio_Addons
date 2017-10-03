Guideline
---
_revision 20171001_

### Addons Menu Process:    
- Menu > Addons > download: `addonsdl.php`
	- `addonslist.php`
	- download update and reinstall if there's an update
- Addons page: `addons.php`
	- revision and list from `addonslist.php` (link to each block)
	- each addon block from `addonslist.php`
	- install/uninstall/update status based on:
		- installed markers:
			- `uninstall_<alias>.sh` - file: installed status
			- `redis-cli hget addons <alias>` - database: installed version
		- `addonslist.php` - `'version'`: current version
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
		
- consult with [JS plugin list]() used by othr addons to avoid redundant install or critical uninstall
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
		'wait'   : 'message text',
		'confirm': 'message text',
		'yesno'   : 'message text',
		'yesno1'  : 'message text 1',
		'yesno2'  : 'message text 2',
		'text'    : {
			'message': 'message text',
			'label'  : 'label text'
		},
		'password': {
			'message': 'message text',
			'label'  : 'label text'
		},
		'radio'   : {
			'message': 'message text',
			'list'   : {
				'*item1': 'value1',
				'item2' : 'value2',
				'custom': '?'
			}
		},
		'checkbox': {
			'message': 'message text',
			'list'   : {
				'item1' : 'value1',
				'*item2': 'value2'
			}
		},
		'select'  : {
			'message': 'message text',
			'label'  : 'label text',
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
- `'version'` changed > show `Update` button
- non-install addons:
	- omit > `Install` button always enable, no `Uninstall` button
- run once addons:
	- omit but `redis-cli hset addons <alias> 1` in install script > `Install` button disable after run

**`'buttonlabel'`**  
- `'Show'` - for open `'sourceurl'` in new window

**`'option'`** - user inputs  
- each `'key': ...` open a dialog
- each `'value'` will be appended as `install.sh` arguments / parameters
- options must be **single quoted** json, `" 'key': 'value' "`
- `*` leading `itemN` = pre-select items
- dialog types:
	- `X` - cancel and back to main page
	- `'wait'` = `Ok`
		- `Ok` = continue (no value)
	- `'confirm'` = `Cancel` `Ok`
		- `Ok`  = continue (no value) / `Cancel` = cancel and back
	- `'yesno'` = `No` `Yes`
		- `Yes` = 1 / `No` = 0
	- `'text'` = `<input type="text">`
		- `Ok`  = input
	- `'password'` = `<input type="password">`
		- `Ok` > verification > `Ok` = input
	- `'radio'` = `<input type="radio">` - single value
		- `Ok` = selected value
		- `*` pre-select must be specified
		- `'custom': '?'` > `Ok` = `'text'`
	- `'checkbox'` = `<input type="checkbox">` - multiple values
		- `Ok` = checked values
		- `*` pre-select optional
	- `'select'` = `<select><option>...` - single value, too long for `'radio'`
		- `Ok` = selected value
		- `*` pre-select optional
		- `'custom': '?'` > `Ok` = `'text'`
- multiple dialogs of the same type must add trailing numbers to avoid duplicate `key`
---

**styling** for `description`, `option`
- text / html
	- `&nbsp;` = space
	- `&ensp;` = medium space
	- `&emsp;` = wide space
- quotes use html entities to avoid conflict with php quotes
    - `&quot;` = `"`
    - `&#039;` = `'`
- preset css:
	- `<white>...</white>`
	- `<code>...</code>`
