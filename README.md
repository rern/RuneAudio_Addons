Addons Menu for RuneAudio
---
  
> All tested on RuneAudio 0.3 20160313_  
> (0.3+0.4b) - RuneAudio 0.3, 0.4b compatible_  
> Most should work on 0.4b but some not tested yet_  
  
   
**Addons Menu** for easy addons installation via RuneUI.  
It's the same as running on SSH terminal.  
Just much easier.  
<hr>

Currently available:
- Aria2
- Backup-Restore Update (0.3+0.4b)
- BASH Commands
- Expand Partition (0.3+0.4b)
- Fonts - Extended Characters (0.3+0.4b)
- Login Logo for SSH Terminal
- Rank Mirror Package Servers (0.3+0.4b)
- RuneUI Enhancements (0.3+0.4b)
- RuneUI GPIO
- RuneUI Password
- Transmission
- Webradio Import
- Webradio Sorting

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
