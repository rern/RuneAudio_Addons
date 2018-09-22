#/bin/bash

. /srv/http/addonstitle.sh

if [[ -t 1 ]]; then
	yesno "Reset RuneUI to default?" answer
	if [[ $answer == 0 ]]; then
		title "$info RuneUI Reset cancelled."
		exit
	fi
fi

wgetnc https://github.com/rern/RuneAudio/raw/master/ui_reset/reset.sh
chmod +x reset.sh
./reset.sh
