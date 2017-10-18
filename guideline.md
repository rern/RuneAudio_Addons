Guideline
---
_revision 20171015_

### Addons Menu Process:    

- **Menu** > **Addons**
	- download list
		- `addonsmenu.js` > `addonsdl.php` > `addonsdl.sh` > `addonslist.php`
	- compare version
		- version from `addonslist.php`
		- installed version `redis-cli hget addons addo`
	- update if available
		- switch spinning refresh 'connecting...' to spinning gear 'updating...'
		- download, uninstall and reinstall if update available
	- clear cache
		- `opcache_reset()` > `addons.php`
		
- **Addons** page
	- populate list and block
		- `addonslist.php` > `addons.php`
	- install/uninstall/update buttons status based on:
		- installed markers:
			- `uninstall_<alias>.sh` - file: installed status
			- `redis-cli hget addons <alias>` - database: installed version
		- `addonslist.php` - `'version'`: current version
	- user input options
		- `addonsinfo.js`
		- confirm dialog
		- user input dialogs for options
		- cancel any time by `X` button
	- send script
		- append options
		- `addons.js` > `addonsbash.php`
		
- **Addons Terminal** page
	- prepare command and options
		- get download url from `addonslist.php`
	- run script
		- `addonsbash.php`
	- line-by-line output of bash scripts on screen
		- `ob_implicit_flush(); ob_end_flush();`
	- finish
		- `opcache_reset()`
		- enable `X` button after finished > back to Addons page
---

### Each addon requires:  
1. `install.sh` and `uninstall_<alias>.sh` scripts
2. `array(...)` in `addonslist.php`
  

### 1. `install.sh` and `uninstall_<alias>.sh` scripts  

> bash script  
> must use script default `### template` lines except non-install addons  
> default variables and functions will take care most of on-screen messages and addons database  
> use non-invasive modifications so other addons can survive after install / uninstall  

- install script  
	- use modify over replace files unless necessary
	- make backup if replace files
- uninstall script
	- restore everything to pre-install state
	- no need for non-install type
	- file path:
		- for install with `master.zip`
			- must be at `/usr/local/bin/`
		- for install with individual downloads
			- must be the same as `install.sh` to use `getuninstall` function
			- destination must be `/usr/local/bin/`
		
- consult with [JS plugin list]() used by other addons to avoid redundant install or critical uninstall
- update will be done by uninstall > install
  
**1.1  `install.sh` template**
```sh
#!/bin/bash

# required
alias=<alias>

branch=master   # change for testing

### template - import default variables, functions
. /srv/http/addonstitle.sh

### template - function
installstart $1            # start message, installed check
getuninstall               # only if uninstall_<alias>.sh not in /usr/local/bin of 'master.zip'
value=$( getvalue <key> )  # get value from array(...)
rankmirrors                # rank miror servers and 'pacman -Sy' before install packages

# start main script -------------------------------------------------------------------------------->>>

echo -e "$bar Get files ..."
wgetnc https://github.com/<name>/<repository>/archive/$branch.zip           # whole repository download

echo -e "$bar Backup files ..."
mv /<path>/<file>{,.backup}

echo -e "$bar Install new files ..."
rm -rf /tmp/install
mkdir -p /tmp/install
bsdtar -xf $branch.zip --strip 1 --exclude '<directory>/' -C /tmp/install
rm $branch.zip /tmp/install/* &> /dev/null

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

# end main script ----------------------------------------------------------------------------------<<<

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

# start main script -------------------------------------------------------------------------------->>>

echo -e "$bar Remove files ..."
rm -v /<path>/<file>

echo -e "$bar Restore files ..."
file=/<path>/<file>
echo $file
sed 's/new/existing/' $file
mv -v /<path>/<file>{.backup,}

# end main script ---------------------------------------------------------------------------------<<<

### template - function - remove version from database, finish message
uninstallfinish $1
```
  

### 2. `array(...)` in `addonslist.php`

**`array(...)` template**   
```php
array(
	'alias'         => 'alias',
	'± version'     => 'version',
	'± only03'      => '1',
	'title'         => 'title',
	'maintainer'    => 'maintainer',
	'description'   => 'description',
	'± thumbnail'   => 'https://url/to/image/w100px',
	'± buttonlabel' => 'install button label',
	'sourcecode'    => 'https://url/to/sourcecode',
	'installurl'    => 'https://url/for/wget/install.sh'
	'± option'      => "{ 
		'wait'    : 'message text',
		'confirm' : 'message text',
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
				'item1' : 'value1',
				'item2' : 'value2',
				'custom': '?'
			}
		}
	}"

),
```
`'± ...'` - optional  
`'sourcecode'` - 'blank' = no 'detail' link (only for built-in scripts)  
  
**`'alias'`** - reference point
- must be 1st in each addon
- must be unique among aliases

**`'version'`** - buttons enable/disable  
- `'version'` changed > show `Update` button
- non-install addons:
	- omit > `Install` button always enable, no `Uninstall` button
- run once addons:
	- omit but `redis-cli hset addons <alias> 1` in install script > `Install` button disable after run

**`'only03'`** - compatability
- hide if for RuneAudio 0.3 only
- omit for both versions compatible

**`'buttonlabel'`** - for non-install only
- `'Link'` - for information only (open `'sourceurl'`)

**`'option'`** - user inputs  
- each `'key': ...` open a dialog
- each `'value'` will be appended as `install.sh` arguments / parameters
- options must be **single quoted** json, `" 'key': 'value' "`
- `*` leading `itemN` = pre-select items
- dialog types:
	- `'wait'` = `Ok`
		- `Ok` = continue (no value)
	- `'confirm'` = `Cancel` `Ok`
		- `Ok`  = continue (no value) | `Cancel` = cancel and back
	- `'yesno'` = `No` `Yes`
		- `Yes` = 1 | `No` = 0
	- `'text'` = `<input type="text">`
		- `Ok`  = input
	- `'password'` = `<input type="password">`
		- input + `Ok` > verification + `Ok` = input | blank + `Ok` = 0
	- `'radio'` = `<input type="radio">` - single value
		- `Ok` = selected value | custom + `Ok` > `'text'` > `Ok` = input
		- `*` pre-select must be specified
		- `'?'` custom input marker
	- `'checkbox'` = `<input type="checkbox">` - multiple values
		- `Ok` = checked values
		- `*` pre-select optional
	- `'select'` = `<select><option>...` - single value, too long for `'radio'`
		- `Ok` = selected value | custom + `Ok` > `'text'` > `Ok` = input
		- `*` pre-select optional
		- `'?'` custom input marker
- multiple dialogs of the same type must add trailing numbers to avoid duplicate `key`
- last `key:value` not allow trailing `,`
- `X` - cancel and back to main page
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

**scripts testing**  
- get `install.sh`, `uninstall_<alias>.sh` ready for `wget` download
- open Addons Menu
- add addon `array(...)` to `/srv/http/addonslist.php`
- refresh browser to show the added addon (reopen will download and overwrite `addonslist.php`)
- test install / uninstall
- for branch test, append argument `branchname` to `installurl`
