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

# import heading function:
wget -qN https://github.com/rern/title_script/raw/master/title.sh; . title.sh; rm title.sh
# - heading
# - badge
# - dialog
# - wget

# check 'already installed'

# start info

# user input
# get uninstall script
# get files
# backup existing files
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

**version:**  
for buttons enable/disable  
- specified both in `array(...)` and 'install script'
- version from 'install script' stored in database then disable/enable buttons
- database vs `array(...)` difference will show update button
- non-install addons:
	- (none) + (none)          - install button always enable, no uninstall button
	- (none) + 'install scipt' - install button disable after run (run once)
    
**description:**  
- html allowed  

**option:**  
for user input  
- each input will be appended as <install>.sh arguments
- `;` = delimiter each input
- message (js alert/confirm/prompt):
  - starts with `! = 'js confirm' continue => ok = continue, cancel = exit install`
  - starts with `? = 'js confirm' yes/no   => ok = 1,        cancel = 0`
  - starts with `# = 'js prompt'  password => ok = password, blank-ok/cancel = 0`
  - starts with `  = 'js prompt'  input    => ok = input,    blank-ok/cancel = 0`
  - message will be parsed for html value, use entity code for:
    - `&quot;  = "`
    - `&#039;  = '`
    - `&amp;   = &`
    - `&lt;    = <`
    - `&gt;    = >`
  - multiple lines:
    - `"...\n"` = escaped n    - new line (must be inside double quotes)
    - `."...\n"` = starting dot - concatenate between lines
