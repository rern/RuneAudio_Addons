#/bin/bash

[[ ! -e /srv/http/addonstitle.sh ]] && wget -qN --no-check-certificate https://github.com/rern/RuneAudio_Addons/raw/master/srv/http/addonstitle.sh -P /srv/http
. /srv/http/addonstitle.sh

yesno "All RuneUI addons and custom UI modifications will be removed.\nContinue?"
[[ $answer == 0 ]] && title "$info RuneUI Reset cancelled." && exit

wgetnc https://github.com/rern/RuneAudio/raw/master/ui_reset/reset.sh
chmod +x reset.sh
./reset.sh
