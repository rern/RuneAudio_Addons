Addons for RuneAudio
---
_Tested on RPi3_  
_for RuneAudio **0.3 and 0.4b** (0n 0.4b - incompatible addons will be hidden)_
   
**Addons** for easy addons installation via RuneUI.  
- It's the same as running on SSH terminal.  
- Just much easier.  
- Something like mini App Store or mini Play Store.  
- It does nothing using zero CPU cycle unless run it from the menu.
<hr>

Currently available:
- [Aria2](https://github.com/rern/RuneAudio_Addons)
- [Boot Logo](https://github.com/rern/RuneAudio/tree/master/boot_splash)
- [Expand Partition](https://github.com/rern/RuneAudio/tree/master/expand_partition)
- [Login Logo for SSH Terminal](https://github.com/rern/RuneAudio/tree/master/motd)
- [MPD Upgrade](https://github.com/rern/RuneAudio/tree/master/mpd)
- [Rank Mirror Package Servers](https://github.com/rern/RuneAudio/tree/master/rankmirrors)
- [RuneUI Backup-Restore Enable](https://github.com/rern/RuneAudio/tree/master/backup-restore)
- [RuneUI Enhancements](https://github.com/rern/RuneUI_enhancement)
- [RuneUI Fonts - Extended Characters](https://github.com/rern/RuneAudio/tree/master/font_extended)
- [RuneUI GPIO](https://github.com/rern/RuneUI_GPIO)
- [RuneUI Lyrics](https://github.com/RuneAddons/Lyrics)
- [RuneUI Password](https://github.com/rern/RuneUI_password)
- [RuneUI Pause Button](https://github.com/rern/RuneAudio/tree/master/pause_button)
- [RuneUI Youtube](https://github.com/RuneAddons/RuneYoutube/tree/master)
- [Samba Upgrade](https://github.com/rern/RuneAudio/tree/master/samba)
- [Setting - RuneUI Notification Duration](https://github.com/rern/RuneAudio/tree/master/notify_duration)
- [Setting - Zoom Level of Local Browser](https://github.com/rern/RuneAudio/tree/master/zoom_browser)
- [Transmission](https://github.com/rern/RuneAudio/tree/master/transmission)
- [USB DAC Auto Switch](https://github.com/rern/RuneAudio/tree/master/USB_DAC_switch)
- [Webradio Import](https://github.com/rern/RuneAudio/tree/master/webradio)


![addons](https://github.com/rern/_assets/blob/master/RuneAudio_Addons/addons.gif)  

- install / uninstall directly in RuneUI, no need for ssh terminal
- always reload list from source
- show messages during install
- installed indication
- update indication
- modular template for easy to add addon scripts from other sources

Install
---
**for beginners:**  [Addons Installation](https://github.com/rern/RuneAudio/blob/master/Addons_install/README.md) `<< click`

**for ssh terminal:**
```sh
wget -qN --show-progress --no-check-certificate https://github.com/rern/RuneAudio_Addons/raw/master/install.sh; chmod +x install.sh; ./install.sh
```
`--no-check-certificate` - for RuneAudio out-of-sync system date which causes failed download

---
  
[**Guideline for Addons enthusiasts**](https://github.com/rern/RuneAudio_Addons/blob/master/guideline.md)  
