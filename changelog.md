```note  
This file: 
- file name and path ./RuneAudio_Addons/changelog.md cannot be change
- Addons Menu uses it for:
    - get 'version' number for both 'install.sh' and 'addonslist.php'
    - get content to display as 'changelog' on RuneUI
- lines between ```note and ``` will be omitted
- ##, **, *, __, _, ~~ markdowns will be converted to html for RuneUI
```

## 2017022
- Much better user prompt dialogs
- Add force reinstall/update by long-press uninstall button
- Improve speed by avoid unnecessary downloads
- Add **BASH Command** for running commands and scripts
- Improve template for install/uninstall scripts
- Accommodate most prompt types: alert, confirm, textbox, password, radio, checkbox, select

## 20170917
- Normalize install / uninstall / upgrade across addons
- Normalize commands between **Addons Menu** and ssh terminal
- Release guideline with templates for addons scripts

## 20170913
- Improve update messages

## 20170909
- Auto update **Addons Menu**
- Improve layout
- Optimize code
- Populate previous installed addons to redis database
- Custom label for install button for non-install addons

## 20170901
- Initial release
