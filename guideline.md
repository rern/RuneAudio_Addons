Guideline
---
_revision 20171115_

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
2. `<alias> => array(...)` in `addonslist.php`
  

### 1. `install.sh` and `uninstall_<alias>.sh` scripts  

> bash scripts and files hosted on `https://github.com/RuneAddons/<addon_title>`  
> trusted maintainers may host the scripts on their own repositories `https://github.com/<GitHubID>/<addon_title>`  
> use script default `### template` lines except non-install addons  
> default variables and functions will take care most of install activities  
> use non-invasive modifications so other addons can survive after install / uninstall  

- install script 
	- for update, get saved options as install parameters / arguments
	- install `.../archive/$branch.zip` files from repository with `getinstallzip`
		- extracted to respective directory of target root
		- files in repository root will be removed
	- use override over modify:
		- `runeui.css`: append modified css with the same selector
		- `runeui.js`: append modified function with the same name
	- use modify over replace files unless necessary
	- make backup if replace files

- uninstall script
	- for update, save installed options to redis database before files remove / restore
	- restore everything to pre-install state
	- no need for non-install type
	- file path:
		- for install with `master.zip`
			- must be at `/usr/local/bin/`
		- for install with individual downloads
			- must be the same as `install.sh` to use `getuninstall` function
			- destination must be `/usr/local/bin/`
	- consult with [JS plugin list](https://github.com/rern/RuneAudio_Addons/blob/master/js_plugins.md) used by other addons to avoid critical uninstall
	
- update:
	- will be done by uninstall > install
  
**1.1  `install.sh` template**
```sh
#!/bin/bash

# main reference
alias=<alias>

### template - import default variables, functions
. /srv/http/addonstitle.sh

### template - function: start message, installed check
installstart $@

### template - function: get repository zip and extract to system directories
getinstallzip

### template - function: (optional) rank miror servers and 'pacman -Sy' before install packages
rankmirrors

# start custom script ------------------------------------------------------------------------------>>>

echo -e "$bar Restore options ..."
if [[ $1 == u ]]; then
	<option>=$( redis-cli get <option> &> /dev/null )
	redis-cli del <option> &> /dev/null
fi

echo -e "$bar <package> package ..."
pacman -S --noconfirm <packages>

echo -e "$bar Modify files ..."
file=/<path>/<file>
echo $file
if ! grep -q 'check string' $file; then
	echo 'content' >> $file
	sed -i 's/existing/new/' $file
fi

echo 'content' >> /<path>/<newfile>

# end custom script --------------------------------------------------------------------------------<<<

### template - function: save version to database, finish message
installfinish $@

# extra info if any
title -nt "extra info"
```

**1.2  `uninstall_<alias>.sh` template**
```sh
#!/bin/bash

# main reference
alias=<alias>

### template - import default variables, functions
. /srv/http/addonstitle.sh

### template - function: start message, installed check
uninstallstart $@

# start custom script ------------------------------------------------------------------------------>>>

echo -e "$bar Save options ..."
if [[ $1 == u ]]; then
	<value>=$( <get value1> )
	redis-cli set <option> $<value> &> /dev/null
fi

echo -e "$bar Remove <package> ..."
pacman -R noconfirm <packages>

echo -e "$bar Remove files ..."
rm -v /<path>/<file>

echo -e "$bar Restore files ..."
file=/<path>/<file>
echo $file
sed 's/new/existing/' $file
mv -v /<path>/<file>{.backup,}

# end custom script --------------------------------------------------------------------------------<<<

### template - function: remove version from database, finish message
uninstallfinish $@
```
  

### 2. `<alias> => array(...)` in `addonslist.php`

**`<alias> => array(...)` template**   
```php
<alias> => array(
/**/	'version'       => '<yyyymmdd>',
/**/	'revision'      => '<revision summary>',
/**/	'only03'        => '1',
/**/	'needspace'     =>  <MB>,
	'title'         => '<display name>',
	'maintainer'    => '<maintainer>',
	'description'   => '<description>',
	'sourcecode'    => 'https://github.com/RuneAddons/<addon_title>',
	'installurl'    => 'https://github.com/RuneAddons/<addon_title>/raw/master/install.sh',
/**/	'thumbnail'     => 'https://github.com/RuneAddons/<addon_title>/image/<w100px.png>',
/**/	'buttonlabel'   => '<install button label>',
/**/	'option'        => array(
		'wait'     => '<message text>',
		'confirm'  => '<message text>',
		'yesno'    => '<message text>',
		'yesno1'   => '<message text 1>',
		'yesno2'   => '<message text 2>',
		'text'     => array(
			'message' => '<message text>',
			'label'   => '<label text>'
		),
		'password' => array(
			'message' => '<message text>',
			'label'   => '<label text>'
		),
		'radio'    => array(
			'message' => '<message text>',
			'list'    => array(
				'*item1' => '<value1>',
				'item2'  => '<value2>',
				'custom' => '?'
			),
		),
		'checkbox' => array(
			'message' => '<message text>',
			'list'    => array(
				'item1'  => '<value1>',
				'*item2' => '<value2>'
			),
		),
		'select'   => array(
			'message' => '<message text>',
			'label'   => '<label text>',
			'list'    => array(
				'item1'  => '<value1>',
				'item2'  => '<value2>',
				'custom' => '?'
			),
		),
	),

),
```
`/**/` - optional  
`'sourcecode'` - 'blank' = no 'detail' link (only for built-in scripts)  

**`'version'`** - buttons enable/disable  
- `'version'` changed > show `Update` button
- non-install addons:
	- omit > `Install` button always enable, no `Uninstall` button
- run once addons:
	- omit but `redis-cli hset addons <alias> 1` in install script > `Install` button disable after run

**`'only03'`** - compatability
- hide if for RuneAudio 0.3 only
- omit for both versions compatible

**`'needspace'`**
- MB: downloaded packages files + installed files + downloaded files + decompress files
- omit for 1 MB or less

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
- quotes use html entities to avoid conflict with php quotes
    - `&quot;` = `"`
    - `&#039;` = `'`
- preset css:
	- `<white>...</white>`
	- `<code>...</code>`

## Enlist to Addons Menu
- test scripts:
	- get `install.sh`, `uninstall_<alias>.sh` and files ready on your `https://github.com/<GitHubID>/<addon_title>`
	- open Addons Menu
	- add addon `<alias> => array(...)` to `/srv/http/addonslist.php`:
		- change values according to the **template**
		- change `RuneAddons` to your `<GitHubID>`
			- test url: `'installurl' => 'https://github.com/<GitHubID>/<addon_title>/raw/master/install.sh'`
	- refresh browser to show the added addon (reopen will download and overwrite `addonslist.php`)
	- test install / uninstall scripts
	
- add repository to **RuneAddons**:
	- a request to join with your `<GitHubID>` and `'installurl'`
	- ---a new repository created as `https://github.com/RuneAddons/<addon_title>`
	- ---a `Branch` named your `<GitHubID>` created
	- add scripts and files to the ropository
	- `Pull request`
	- ---the `Branch` merged and ready for **Addons Menu**

- add addon data to **Addons Menu**:
	- `Fork` Addons Menu - `https://github.com/rern/RuneAudio_Addons`
	- add `<alias> => array(...)` to `/srv/http/addonslist.php`
		- change `<GitHubID>` to `RuneAddons`
			- actual url: `'installurl' => 'https://github.com/RuneAddons/<addon_title>/raw/master/install.sh'`
	- `Pull request`
	- ---the `Fork` merged and officially is online
	
### Update an addon
- **update scripts:**
	- your `Branch` - `https://github.com/RuneAddons/<addon_title>` - update / modify
	- test the update in **Addons Menu**
		- `Uninstall` the addon
		- long-press `Install` button and type the branch name, your `<GitHubID>`
	- `Pull request`
- **update the list**
	- `SSH` - `/srv/http/addonslist.php` - edit `<alias> => array(...)` (`Menu` > `Addons` will overwrite this edit)
		- change `'version'` to enable `Update` button
		- change `'revision'` to describe this update
	- refresh browser to show the changes
	- your `Fork` - `https://github.com/<GitHubID>/AddonsMenu` - copy the changes to `.../srv/http/addonslist.php`
	- `Pull request` 

