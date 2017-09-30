```note  
Addons Menu uses this file for:
	- get its own 'version' for 'install.sh', 'addonsdl.sh'
	- convert to 'addonslog.php' for display as 'Changelog'
styling
	###, ## for 'date' lines only
	- for each line
	**, *, __, _, ~~ markdowns will be converted to html for RuneUI
	
*This code block, lines between ```note and ```, will be removed from 'Changelog'.
```

### 20171001
- Improve code for better performance
- Improve terminal messages  and errors handling
- Fix missing installed status

### 20170925
- Press `Enter` in dialogs = click `Ok`
- **Hide password** (and options) in terminal page
- Add **custom input** for `radio` and `select` dialogs
- Improve changelog styling
- Fix `hammer.min.js` removal by RuneUI Enhancement

### 2017022
- Much better user dialogs
- Add force reinstall/update by **long-press** `Uninstall`
- Improve speed by avoid unnecessary downloads
- Add **BASH Command** for running commands and scripts
- Improve **templates** for install/uninstall scripts
- Support every dialog types: alert, confirm, textbox, password, radio, checkbox, select

### 20170917
- Normalize install / uninstall / upgrade across addons
- Normalize commands between **Addons Menu** and ssh terminal
- Release **Guideline** with templates for addons scripts
- Improve update messages

### 20170909
- Auto update **Addons Menu**
- Populate previous installed addons to redis database
- Custom label for `Install` for non-install addons

### 20170901
- Initial release
