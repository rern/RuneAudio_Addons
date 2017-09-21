Addons for RuneAudio
---
_Tested on RuneAudio beta 20160313_

RuneAudio Addons Menu for easy addons installation via RuneUI.  

![addons](https://github.com/rern/_assets/blob/master/RuneAudio_Addons/addons.gif)  

- install / uninstall directly in RuneUI, no need for ssh terminal
- always reload list from source
- show messages during install
- installed indication
- update indication
- modular block for easy to add scripts from other sources

Install
---
**for beginners:**  [Addons Menu Installation](https://github.com/rern/RuneAudio/blob/master/Addons_install/README.md) `<< click`

**for ssh terminal:**
```sh
wget -qN --show-progress https://github.com/rern/RuneAudio_Addons/raw/master/install.sh; chmod +x install.sh; ./install.sh
```

**Clean up**  
Problems with install / uninstall / update, clean up then reinstall will fix it.
```sh
sed -i '/id="addons"/ d' /srv/http/app/templates/header.php
sed -i '/addons.js/ d' /srv/http/app/templates/footer.php
rm -rv /srv/http/{addons*,title.sh}
rm -rv /srv/http/assets/{css/addons.css,js/addons.js}
rm -v /usr/local/bin/uninstall_addo.sh
redis-cli del addons
systemctl reload php-fpm
```
---
  
[**Guideline for Addons enthusiasts**](https://github.com/rern/RuneAudio_Addons/blob/master/guideline.md)  
