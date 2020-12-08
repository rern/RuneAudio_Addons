Guideline
---
_revision 20190308_

- [Addons Process](#addons-process)
- [Requirement For Each Addon](#requirement-for-each-addon)
- [Enlist To Addons](#enlist-to-addons)
- [Update An Addon](#update-an-addon)


### Addons Process  

- **Menu** > **Addons**
	- download list
		- `addons-list.json`
		
- **Addons** page
	- populate list and block
		- `addons-list.json` > `addons.php`
	- install/uninstall/update buttons status based on:
		- current version vs installed version : `addons-list.json` vs`/srv/http/data/addons/ADDON`
	- user input options
		- `addons.js`
		- user input dialogs for options
		- cancel any time by `X` button
	- send script
		- append options
		- `addons.js` > `addons-progress.php`
		
- **Addons Progress** page
	- prepare command and options
		- get download url from `addons-list.json`
	- run script
		- `addons.php`
	- line-by-line output of bash scripts on screen
		- `ob_implicit_flush(); ob_end_flush();`
---

### Requirement for each addon  
1. `install.sh` and `uninstall_<alias>.sh` scripts
2. addon in `addonslist.php`
  

### 1. `install.sh` and `uninstall_<alias>.sh` scripts  

> bash scripts and files hosted on `https://github.com/RuneAddons/<addon_title>`  
> trusted maintainers may host the scripts on their own repositories `https://github.com/<GitHubID>/<addon_title>`  
> use script default `### template` lines except non-install addons  
> default variables and functions will take care most of install activities    

- install script 
	- for update, get saved options as install parameters / arguments
	- install required packages and verify
	- install `.../archive/$branch.zip` files from repository with `getinstallzip`
		- extracted to respective directory of target root
		- files in repository root will be removed

- uninstall script
	- restore everything to pre-install state
		- restore files modified by [provided edit commands](#provided-edit-commands) with `restorefile FILE1 FILE2 ...`
	- no need for non-install type
	- file path:
		- for install with `master.zip`
			- must be at `/usr/local/bin/`
		- for install with individual downloads
			- must be the same as `install.sh` to use `getuninstall` function
			- destination must be `/usr/local/bin/`
	
- update:
	- will be done by uninstall > install
  
**1.1  `install.sh` template**
```sh
#!/bin/bash

# main reference
alias=<alias>

### template - import default variables, functions
. /srv/http/addons.sh
. /srv/http/addonsedit.sh

### template - function: start message, installed check
installstart $@

### template - verify installed > download > install > verify install
if [[ $( pacman -Ss 'mainpackage$' | head -n1 | awk '{print $NF}' ) != '[installed]' ]]; then
	pkgs='package package1 package2'
	checklist='package package1 package2 depend other'
	fallbackurl=https://path/to/single/tarball
	installPackages "$pkgs" "$checklist" "$fallbackurl"
fi

### template - function: get repository zip and extract to system directories
getinstallzip

### template - function: (optional) rank miror servers and 'pacman -Sy' before install packages
rankmirrors

# start custom script ------------------------------------------------------------------------------>>>

echo -e "$bar Restore options ..."
if [[ $1 == u ]]; then
	<option>=$( redis-cli get <option> )  # already no output - '&> /dev/null' with 'get's return null
	redis-cli del <option> &> /dev/null   # '&> /dev/null' for others to hide output
fi

echo -e "$bar <package> package ..."
pacman -S --noconfirm <packages>

echo -e "$bar Modify files ..."
file=/<path>/<file>
echo $file
if ! grep -q 'check string' $file; then
	comment 'search'
	string=$( cat <<'EOF'
place code inside this heredoc literally
last line
EOF
)
	insert 'search'
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
. /srv/http/addonsedit.sh

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
mv -v /<path>/<file>{.backup,}

file=/<path>/<file>
echo $file
restorefile $file

# end custom script --------------------------------------------------------------------------------<<<

### template - function: remove version from database, finish message
uninstallfinish $@
```

#### Provided edit commands
```
insert / append / comment : for /*...*/  <?php /*...*/ ?>  #...
====================================================================================

pre-defined variable:
------------------------------------------------------------------------------------
alias=name                                already in install.sh / uninstall_alias.sh
file=/path/file                           before all commands of each file

string=$( cat <<'EOF'                     before each insert and append
place code without escapes                to use variable inside, unquote 'EOF' (literal $ must be escaped)
last line
EOF
)
    
usage:
------------------------------------------------------------------------------------
match [-n N] SEARCH [-n N] [SEARCH2]      test sed search pattern

comment [-n N] SEARCH [-n N] [SEARCH2]    /*alias js,php,css alias*/

commentH [-n N] SEARCH [-n N] [SEARCH2]   <?php /*alias html,php alias*/ ?>
commentP [-n N] SEARCH [-n N] [SEARCH2]

commentS [-n N] SEARCH [-n N] [SEARCH2]   #alias ...

insert [-n N] SEARCH                      //0alias0
append [-n N] SEARCH                      js,php,css
                                          //1alias1

insertH [-n N] SEARCH                     <?php //0alias0 ?>
appendH [-n N] SEARCH                     html
                                          <?php //1alias1 ?>
   
insertP [-n N] SEARCH                     <?php //0alias0
appendP [-n N] SEARCH                     php
                                          <?php //1alias1

insertS [-n N] SEARCH                     #0alias0
appendS [-n N] SEARCH                     ...
                                          #1alias1

insertAsset SEARCH FILE.ext               <?php //0alias0 ?>
appendAsset SEARCH FILE.ext               <style> @font-face { ... } </style>
                                          <link rel="stylesheet" href="<?=$this->asset('/css/FILE.css')?>">
                                          <script src="<?=$this->asset('/js/FILE.js')?>"></script>
                                          <?php //1alias1 ?>

restorefile FILE [FILE2 ...]              remove all insert / append / comment

argument:
------------------------------------------------------------------------------------
-n N                                      -n -N    N lines above SEARCH
                                          -n +N    N lines below SEARCH
SEARCH                                    normal sed regex syntax inside quotes
                                          '..."'"...'  escape single quote inside itself
                                          "...\"..."   escape double quote inside itself
                                          |  as delimiter - no need to escape  /
                                          literal  | ^ $ . * [ ] \  need  \  escape
                                          or use  .  as placeholder instead of escape

tips:
------------------------------------------------------------------------------------
test run SEARCH:
    # . /srv/http/addonsedit.sh
    # file=/path/file
	# match [-n N] SEARCH [-n N] [SEARCH2]
cache busting - insert/append FILE.ttf/FILE.woff/FILE.css/FILE.js with insertAsset/appendAsset
insert/append with SEARCH itself in $string:
    must be after comment to the same SEARCH (avoid commented after insert)
    must be combined with insert/append to the same SEARCH (avoid double insert)
do not isert/append into another insert/append
    it will be uninstalled with that addon 
```
  

### 2. `addons-list.json`

**template**   
```sh
ALIAS: {
/**/	'version'       : 'YYYMMDD',
/**/	'revision'      : 'SUMMARY',
	'title'         : 'ADDON NAME',
	'maintainer'    : 'AUTHOR NAME',
	'description'   : 'DESCRIPTION',
	'sourcecode'    : 'https://github.com/RuneAddons/REPOSITORY',
	'installurl'    : 'https://github.com/RuneAddons/REPOSITORY/raw/master/install.sh',
/**/	'thumbnail'     : 'https://github.com/RuneAddons/REPOSITORY/image/FILENAME',
/**/	'postinfo'      : 'POST INSTALL MESSAGE',
/**/	'buttonlabel'   : 'LABEL',
/**/	'nouninstall'   : TRUE,
/**/	'hide'          : BOOLEAN,
/**/	'verify' : {
		'command'     : 'COMMAND LINE'
		'notverified' : 'MESSAGE'
	}
/**/	'option'        : {
		'wait'      : 'MESSAGE',
		'warning'   : 'MESSAGE',
		'confirm'   : 'MESSAGE',
		'skip'      : 'MESSAGE',
		'yesno'     : {
			'message'  : 'MESSAGE',
/**/				'mgsalign'    : 'CSS',
/**/				'cancellabel' : 'LABEL',
/**/				'oklabel'     : 'LABEL',
		},
		'text'      : {
/**/			'message'  : 'MESSAGE',
			'label'    : 'LABEL',
/**/			'boxwidth' : PIXEL,
/**/			'required' : INDEX
		},
		'password'  : {
/**/			'message'  : 'MESSAGE',
			'label'    : 'LABEL',
/**/			'required' : TRUE,
		},
		'file'  : {
/**/			'message' : 'MESSAGE',
			'label'   : 'LABEL',
/**/			'type'    : 'FILETYPE'
		},
		'radio'     : {
/**/			'message' : 'MESSAGE',
			'list'    : {
				'LABEL'  : 'VALUE',
/**/				'custom' : '?',
			},
/**/			'ckecked' : INDEX,
		},
		'select'    : {
/**/			'message' : 'MESSAGE',
/**/			'label'   : 'LABEL',
			'list'    : {
				'LABEL' : 'VALUE',
/**/				'custom' : '?',
			},
/**/			'ckecked' : INDEX
		},
		'checkbox'  : {
/**/			'message' : 'MESSAGE',
			'list'    : [
				'LABEL' : 'VALUE',
			},
/**/			'ckecked' : INDEX
		},
	},
}
```
`/**/` - optional  
`'sourcecode'` - 'blank' = no 'detail' link (only for built-in scripts)  

**`'alias'`**  
- should be 4 charaters

**`'version'`** - buttons enable/disable 
- `'version'` changed > show `Update` button
- non-install addons:
	- omit > `Install` button always enable, no `Uninstall` button
- run once addons:
	- omit but `redis-cli hset addons <alias> 1` in install script > `Install` button disable after run

**`'buttonlabel'`** - for non-install only
- `'Link'` - for information only (open `'sourceurl'`)

**`'nouninstall'`** - for non-uninstall addon
- hide `Uninstall` button

**`'hide'`** - for compatability and redundant
- `'only03'` if for RuneAudio 0.3 only - omit for both versions compatible
- `'installed'` if redundant addon already installed
- `'exec'` if bash script result = true
- `'php'` if php script result = true

**`'option'`** - user inputs  
- each `'key' : 'value'` open a dialog
- each `'key'` will be appended as `install.sh` arguments / parameters
- options must be **single quoted**, `" 'key' : 'value' "`
- dialog types:
	- `'wait'` = `Ok`
		- `Ok` = continue (no value)
	- `'confirm'` = `Cancel` `Ok`
		- `Ok`  = continue (no value) | `Cancel` = cancel and back
	- `'skip'` = `No` `Yes`
		- `Yes` = no more options | `No` = continue options
	- `'yesno'` = `No` `Yes`
		- `Yes` = 1 | `No` = 0
		- `checked` = set primary button
	- `'warning'` = `Ok` (activated if not blank)
		- `Ok` = back
	- `'text'` = `<input type="text">`
		- `Ok` = input
		- `required` = ok button disabled if required field blank
	- `'password'` = `<input type="password">`
		- input + `Ok` > verification + `Ok` = input | blank + `Ok` = 0
		- `required` = ok button disabled if password blank
	- `'file'` = `<input type="file">`
		- `Ok` = upload
		- `type` filetype filter and verify
	- `'radio'` = `<input type="radio">` - single value
		- `Ok` = selected value | custom + `Ok` > `'text'` > `Ok` = input
		- `checked` = default 0
		- `'?'` custom input marker
	- `'select'` = `<select><option>...` - single value, too long for `'radio'`
		- `Ok` = selected value | custom + `Ok` > `'text'` > `Ok` = input
		- `checked` = default 0
		- `'?'` custom input marker
	- `'checkbox'` = `<input type="checkbox">` - multiple values
		- `Ok` = checked values
		- `checked` = default none
- `'radio'`, `'select'`, `'checkbox'` can be html instead
- multiple tems of the same type must be array
- blank value get passed as a ` ''` arguement to bash
- `X` - cancel and back to main page
---

**styling** for `revision`, `description`, `option`
- preset css:
	- `<white>white text</white>`
	- `<code>code block</code>`
- quotes in strings: (otherwise json errors)
    - double quote: `\"` or `&#034;` = `"`
    - single quote: `&#039;` = `'` (no `\` escape for single quote)
- FontAwesome:
	- `<i class=\"fa fa-<icon>\"></i>` (escape double quotes)
- `msgalign`:
	- none(`left`), `center`, `right`
- `boxwidth`:
	- none(`medium`), `Npx`, `'max'`

## Enlist To Addons
- **test scripts:**
	- get `install.sh`, `uninstall_<alias>.sh` and files ready on your `https://github.com/<GitHubID>/<addon_title>`
	- open Addons Menu
	- add addon to `/srv/http/addons-list.json`:
		- change values according to the **template**
		- change `RuneAddons` to your `<GitHubID>`
			- test url: `'installurl' : 'https://github.com/<GitHubID>/<addon_title>/raw/master/install.sh'`
	- refresh browser to show the added addon (reopen will download and overwrite `addonslist.php`)
	- test install / uninstall scripts
	
- **add a repository to `RuneAddons`**:
	- a request to join with your `<GitHubID>` and `'installurl'`
	- ---a new repository created as `https://github.com/RuneAddons/<addon_title>`
	- ---a `Branch` named your `<GitHubID>` created
	- add scripts and files to the ropository
	- `Pull request`
	- ---the `Branch` merged and ready for **Addons Menu**

- **add addon data to Addons Menu**:
	- `Fork` Addons Menu - `https://github.com/rern/RuneAudio_Addons`
	- add addon to `/srv/http/addonslist.php`
		- change `<GitHubID>` to `RuneAddons`
			- actual url: `'installurl' : 'https://github.com/RuneAddons/<addon_title>/raw/master/install.sh'`
	- `Pull request`
	- ---the `Fork` merged and officially is online
	
### Update An Addon
- **update the scripts and files in `RuneAddons`:**
	- your **`Branch`** - `https://github.com/RuneAddons/<addon_title>` - update / modify
	- test `Install` `Uninstall` from branch
		- `Uninstall` existing version
		- long-press `Install` button and type the branch name, your `<GitHubID>`
		- test the updated addon functions
		- `Uninstall`
	- test `Update` from branch
		- `Install` existing version
		- long-press `Uninstall` button and type the branch name, your `<GitHubID>`
	- `Pull request`
- **update the list in Addons:**
	- test updating as a user
		- **`SSH`** - `/srv/http/addonslist.php` - edit addon
		- (next `Menu` > `Addons` will overwrite this edit)
		- change `'version'` - enable `Update` button
		- change `'revision'` - summary list of this update
		- refresh browser to show the changes
		- long-press `Update` button
	- your **`Fork`** - `https://github.com/<GitHubID>/AddonsMenu` - copy the changes to `.../srv/http/addonslist.php`
	- `Pull request` 

### Aborting from browser  
(Should not do unless necessary)  
**`connection_status() !== 0` :**
- Stop button
- Back button
- Close / Exit

**Process:**
- Running script killed
- `wget` killed and `*.zip` deleted
- `pacman` killed and `db.lck` lock file deleted
- `uninstall_<alias>.sh` deleted
- redis install data deleted
