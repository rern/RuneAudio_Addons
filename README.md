Addons Menu for RuneAudio
---
_for RuneAudio **0.3 and 0.4b** (0n 0.4b - incompatible addons will be hidden)_
   
**Addons Menu** for easy addons installation via RuneUI.  
It's the same as running on SSH terminal.  
Just much easier.  
Something like mini App Store or mini Play Store.  
<hr>

Currently available:
- [Aria2](https://github.com/rern/RuneAudio_Addons)
- [Backup-Restore Update](https://github.com/rern/RuneAudio/tree/master/backup-restore)
- BASH Commands
- [Boot Logo](https://github.com/rern/RuneAudio/tree/master/boot_splash)
- [Expand Partition](https://github.com/rern/RuneAudio/tree/master/expand_partition)
- [Fonts - Extended Characters](https://github.com/rern/RuneAudio/tree/master/font_extended)
- [Login Logo for SSH Terminal](https://github.com/rern/RuneAudio/tree/master/motd)
- [Rank Mirror Package Servers](https://github.com/rern/RuneAudio/tree/master/rankmirrors)
- [RuneUI Enhancements](https://github.com/rern/RuneUI_enhancement)
- [RuneUI GPIO](https://github.com/rern/RuneUI_GPIO)
- [RuneUI Password](https://github.com/rern/RuneUI_password)
- [Samba Upgrade](https://github.com/rern/RuneAudio/tree/master/samba)
- [Transmission](https://github.com/rern/RuneAudio/tree/master/transmission)
- [Webradio Import](https://github.com/rern/RuneAudio/tree/master/webradio)
- [Webradio Sorting](https://github.com/rern/RuneAudio/tree/master/webradio)

![addons](https://github.com/rern/_assets/blob/master/RuneAudio_Addons/addons.gif)  

- install / uninstall directly in RuneUI, no need for ssh terminal
- always reload list from source
- show messages during install
- installed indication
- update indication
- modular template for easy to add addon scripts from other sources

Install
---
**for beginners:**  [Addons Menu Installation](https://github.com/rern/RuneAudio/blob/master/Addons_install/README.md) `<< click`

**for ssh terminal:**
```sh
wget -qN --show-progress https://github.com/rern/RuneAudio_Addons/raw/master/install.sh; chmod +x install.sh; ./install.sh
```

**get stuck?**
```sh
rm /usr/local/bin/uninstall_addo.sh
redis-cli hdel addons addo
```
then reinstall.

---
  
[**Guideline for Addons enthusiasts**](https://github.com/rern/RuneAudio_Addons/blob/master/guideline.md)  
