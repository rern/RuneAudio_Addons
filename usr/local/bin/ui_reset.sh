#/bin/bash

. /srv/http/addonstitle.sh

yesno "All RuneUI addons and custom UI modifications will be removed.\nContinue?" answer
if [[ $answer == 0 ]]; then
	title "$info RuneUI Reset cancelled."
	exit
fi

wgetnc https://github.com/rern/RuneAudio/raw/master/ui_reset/reset.sh
chmod +x reset.sh
./reset.sh
