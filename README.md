Addons Menu for RuneAudio
---
_for RuneAudio 0.3 and 0.4b_
   
**Addons Menu** for easy addons installation via RuneUI.  
It's the same as running on SSH terminal.  
Just much easier.  
Something like mini App Store or mini Play Store.  
<hr>

Currently available:
- Aria2
- Backup-Restore Update
- BASH Commands
- Expand Partition
- Fonts - Extended Characters
- Login Logo for SSH Terminal
- Rank Mirror Package Servers
- RuneUI Enhancements
- RuneUI GPIO
- RuneUI Password (0.3 - 0.4 already built-in)
- Samba Upgrade
- Transmission
- Webradio Import
- Webradio Sorting (0.3 - 0.4 no need)

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
