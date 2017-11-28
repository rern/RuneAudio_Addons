<script>
    var localSStime = parseInt("<?php echo $this->localSStime; ?>");
    var remoteSStime = parseInt("<?php echo $this->remoteSStime; ?>");
</script>
<div class="screen-saver-content" style="display:none;">
    <!-- PLAYBACK PANEL -->
    <div id="playback-ss" class="tab-pane active">
        <div class="container-fluid">
            <div id="leftpanel-ss" style="float: left; width: 40%; height: 100%">
                <div id="currentartist-ss" style="font-size: 30px; display: block; box-sizing: border-box; text-align: center; font-family: "Lato,Helvetica Neue,Helvetica,Arial,sans-serif"; line-height: 1.42857; color: rgb(224, 231, 238)"><i class="fa fa-spinner fa-spin"></i></div>
                <div id="currentsong-ss" style="white-space: nowrap; overflow: hidden; height: 40px; font-size: 40px; display: block; box-sizing: border-box; text-align: center; font-weight: bold; color: rgb(0, 149, 216); line-height: 34px;"><i class="fa fa-spinner fa-spin"></i></div>
                <div id="currentalbum-ss" style=" font-size: 30px; display: block; box-sizing: border-box; text-align: center; font-family: "Lato,Helvetica Neue,Helvetica,Arial,sans-serif"; line-height: 1.42857; color: rgb(224, 231, 238);"><i class="fa fa-spinner fa-spin"></i></div>
                <div class="col-sm-4 coverart-ss" style="height: auto; margin: 10px auto 11px; width: 100%;">
                    <img id="cover-art-ss" style="width: 100%; height: auto; border: 2px solid rgb(52, 73, 94); margin-bottom: 20px; border-radius: 6px; background-size: 100% 100%;" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" alt="transparent-square">
                    <div id="countdown-display-ss" style="float: left; width: 50%; line-height: 38px; font-size: 38px; font-weight: bold; text-align: center;"><i class="fa fa-spinner fa-spin"></i></div>
                    <div id="total-ss" style="line-height: 38px; font-size: 28px; font-weight: bold; text-align: center;"><i class="fa fa-spinner fa-spin"></i></div>
                </div>
            </div>
            <div id="rightpanel-ss" style="float: right; width: 60%; height: 100%">
                <div style="height: 110px">
                    <div id="playlist-position-ss" style="padding-top: 30px; font-size: 22px; display: block; box-sizing: border-box; text-align: center; font-weight: bold; color: rgb(0, 149, 216); line-height: 34px;"><span></span></div>
                    <div id="format-bitrate-ss" style="font-size: 22px; display: block; box-sizing: border-box; text-align: center; font-weight: bold; color: rgb(0, 149, 216); line-height: 34px;"><i class="fa fa-spinner fa-spin"></i></div>
                </div>
                <div style="height: 100%">
                    <div class="col-sm-4 coverart" style="height: auto; margin: 10px auto 11px; width: 100%;">
                        <div id="artist-ss" style="font-size: 30px; display: block; box-sizing: border-box; text-align: left; font-weight: bold; color: rgb(0, 149, 216); line-height: 34px;"><i>Artist:</i></div>
                        <img id="artist-image-ss" style="margin-right: 20px; margin-left: 10px; float: left; width: 130px; height: auto; border: 2px solid rgb(52, 73, 94); border-radius: 6px; background-size: 100% 100%;" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" alt="transparent-square"></img>
                        <div id="artist-bio-ss" style="font-size: 20px"><pre class="fa fa-spinner fa-spin"></pre></div>
                    </div>
                </div>
                <div style="height: auto">
                    <div class="col-sm-4 coverart" style="height: auto; margin: 10px auto 11px; width: 100%;">
                        <div id="addinfo-ss" style="font-size: 24px; display: block; box-sizing: border-box; text-align: left; font-weight: bold; color: rgb(0, 149, 216); line-height: 34px;"><i>Additional Info:</i></div>
                        <div id="addinfo-text-ss" style="font-size: 20px; box-sizing: border-box;"><pre class="fa fa-spinner fa-spin"></pre></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="tab-content">
    <!-- PLAYBACK PANEL -->
    <div id="playback" class="tab-pane active">
        <div class="container-fluid">
			<span id="currentartist"><i class="fa fa-spinner fa-spin"></i></span>
            <span id="currentsong"><i class="fa fa-spinner fa-spin"></i></span>
            <span id="currentalbum"><i class="fa fa-spinner fa-spin"></i></span>
			<div id="overlay-playsource-open" title="View and change playback source" <?php if ($this->spotify === '0'): ?>class="disabled"<?php endif; ?>>
				<span id="playlist-position"><button class="btn btn-default btn-xs">MPD</button><span></span></span>
				<span id="format-bitrate"><i class="fa fa-spinner fa-spin"></i></span>
			</div>
            <div class="knobs row">
                <div id="time-knob" class="col-sm-<?=$this->colspan ?>">
                    <input id="time" value="0" data-width="230" data-height="230" data-bgColor="#34495E" data-fgcolor="#0095D8" data-thickness="0.30" data-min="0" data-max="1000" data-displayInput="false" data-displayPrevious="true">
                    <span id="countdown-display"><i class="fa fa-spinner fa-spin"></i></span>
                    <span id="total"><i class="fa fa-spinner fa-spin"></i></span>
                    <div class="btn-group">
                        <button id="repeat" class="btn btn-default btn-lg btn-cmd btn-toggle" type="button" title="Repeat" data-cmd="repeat"><i class="fa fa-repeat"></i></button>
                        <button id="random" class="btn btn-default btn-lg btn-cmd btn-toggle" type="button" title="Random" data-cmd="random"><i class="fa fa-random"></i></button>
                        <button id="single" class="btn btn-default btn-lg btn-cmd btn-toggle <?php if ($this->activePlayer === 'Spotify'): ?>disabled<?php endif; ?>" type="button" title="Single" data-cmd="single"><i class="fa fa-refresh"></i></button>
                        <!--<button type="button" id="consume" class="btn btn-default btn-lg btn-cmd btn-toggle" title="Consume Mode" data-cmd="consume"><i class="fa fa-compress"></i></button>-->
                    </div>
                </div>
                <?php if ($this->coverart == 1): ?>
                <div class="col-sm-<?=$this->colspan ?> coverart">
                    <img id="cover-art" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" alt="transparent-square">
                    <button id="overlay-social-open" class="btn btn-default" type="button" title="Share this track"><i class="fa fa-share"></i></button>
                    <button id="songinfo-open" class="btn btn-default" type="button" title="Song Info" href="#songinfo-modal" data-toggle="modal"><i class="fa fa-info"></i></button>
                </div>
                <?php endif ?>
                <div id="volume-knob" class="col-sm-<?=$this->colspan ?> <?=$this->volume['divclass'] ?>">
                    <input id="volume" value="100" data-width="230" data-height="230" data-bgColor="#f00" data-thickness=".25" data-skin="tron" data-cursor="true" data-angleArc="250" data-angleOffset="-125" data-readOnly="<?=$this->volume['readonly'] ?>" data-fgColor="<?=$this->volume['color'] ?>" data-dynamic="<?=$this->volume['dynamic'] ?>" disabled="disabled" >
                    <!--
                    <input id="volume" value="100" data-width="230" data-height="230" data-bgColor="#f00" data-thickness=".25" data-skin="tron" data-cursor="true" data-angleArc="250" data-angleOffset="-125" data-readOnly="<?=$this->volume['readonly'] ?>" data-fgColor="<?=$this->volume['color'] ?>" data-dynamic="<?=$this->volume['dynamic'] ?>" <?php if (isset($this->volume['disabled'])): ?> disabled="disabled" <?php endif ?>>
                    -->
                    <div class="btn-group">
                        <button id="volumedn" class="btn btn-default btn-lg btn-cmd btn-volume" type="button" <?php if (isset($this->volume['disabled'])): ?> disabled="disabled" <?php endif ?> title="Volume down" data-cmd="volumedn"><i class="fa fa-volume-down"></i></button>
                        <button id="volumemute" class="btn btn-default btn-lg btn-cmd btn-volume" type="button" <?php if (isset($this->volume['disabled'])): ?> disabled="disabled" <?php endif ?> title="Volume mute/unmute" data-cmd="volumemute"><i class="fa fa-volume-off"></i> <i class="fa fa-exclamation"></i></button>
                        <button id="volumeup" class="btn btn-default btn-lg btn-cmd btn-volume" type="button" <?php if (isset($this->volume['disabled'])): ?> disabled="disabled" <?php endif ?> title="Volume up" data-cmd="volumeup"><i class="fa fa-volume-up"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- LIBRARY PANEL -->
    <div id="panel-sx" class="tab-pane">
        <div class="btnlist btnlist-top">
            <form id="db-search" class="form-inline" action="javascript:getDB({cmd: 'search', path: GUI.currentpath, browsemode: GUI.browsemode});">
                <div class="input-group">
                    <input id="db-search-keyword" class="form-control osk-trigger" type="text" value="" placeholder="search in DB...">
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="submit" title="Search"><i class="fa fa-search"></i></button>
                    </span>
                </div>
            </form>
            <button id="db-level-up" class="btn hide" type="button" title="Go back one level"><i class="fa fa-arrow-left sx"></i> back</button>
            <button id="db-search-results" class="btn hide" type="button" title="Close search results and go back to the Library browsing"><i class="fa fa-times sx"></i> back</button>
        </div>
        <div id="database">
            <ul id="database-entries" class="database">
                <!-- DB entries -->
            </ul>
            <div id="home-blocks" class="row">
                <div class="col-sm-12">
                    <h1 class="txtmid">Browse your library</h1>
                </div>
            </div>
        </div>
        <div class="btnlist btnlist-bottom">
            <div id="db-controls">
                <button id="db-homeSetup" class="btn btn-default hide" type="button" title="Setup the Library home screen"><i class="fa fa-gear"></i></button>
                <button id="db-firstPage" class="btn btn-default" type="button" title="Scroll to the top"><i class="fa fa-angle-double-up"></i></button>
                <button id="db-prevPage" class="btn btn-default" type="button" title="Scroll one page up"><i class="fa fa-angle-up"></i></button>
                <button id="db-nextPage" class="btn btn-default" type="button" title="Scroll one page down"><i class="fa fa-angle-down"></i></button>
                <button id="db-lastPage" class="btn btn-default" type="button" title="Scroll to the bottom"><i class="fa fa-angle-double-down"></i></button>
            </div>
            <div id="db-currentpath">
                <i class="fa fa-folder-open"></i> <span>Home</span>
            </div>
        </div>
        <div id="spinner-db" class="csspinner duo hide"></div>
    </div>
    <!-- QUEUE PANEL -->
    <div id="panel-dx" class="tab-pane">
        <div class="btnlist btnlist-top">
            <form id="pl-search" class="form-inline" method="post" onSubmit="return false;" role="form">
                <div class="input-group">
                    <input id="pl-filter" class="form-control osk-trigger ttip" type="text" value="" placeholder="search in queue..." data-placement="bottom" data-toggle="tooltip" data-original-title="Type here to search on the fly">
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="button" title="Search"><i class="fa fa-search"></i></button>
                    </span>
                </div>
            </form>
            <button id="pl-filter-results" class="btn hide" type="button" title="Close filter results and go back to the playing Queue"><i class="fa fa-times sx"></i> back</button>
            <span id="pl-count" class="hide">2143 entries</span>
        </div>
        <div id="playlist">
            <ul id="playlist-entries" class="playlist">
                <!-- playing queue entries -->
            </ul>
            <ul id="pl-editor" class="playlist hide">
                <!-- playlists -->
            </ul>
            <ul id="pl-detail" class="playlist hide">
                <!-- playlist entries -->
            </ul>
            <div id="playlist-warning" class="playlist hide">
                <div class="col-sm-12">
                    <h1 class="txtmid">Playing queue</h1>
                </div>
                <div class="col-sm-6 col-sm-offset-3">
                    <div class="empty-block">
                        <i class="fa fa-exclamation"></i>
                        <h3>Empty queue</h3>
                        <p>Add some entries from your library</p>
                        <p><a id="open-library" href="#panel-sx" class="btn btn-primary btn-lg" data-toggle="tab">Browse Library</a></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="btnlist btnlist-bottom">
            <div id="pl-controls">
                <button id="pl-firstPage" class="btn btn-default" type="button" title="Scroll to the top"><i class="fa fa-angle-double-up"></i></button>
                <button id="pl-prevPage" class="btn btn-default" type="button" title="Scroll one page up"><i class="fa fa-angle-up"></i></button>
                <button id="pl-nextPage" class="btn btn-default" type="button" title="Scroll one page down"><i class="fa fa-angle-down"></i></button>
                <button id="pl-lastPage" class="btn btn-default" type="button" title="Scroll to the bottom"><i class="fa fa-angle-double-down"></i></button>
            </div>
            <div id="pl-manage">
                <button id="pl-manage-list" class="btn btn-default" type="button" title="Manage playlists"><i class="fa fa-file-text-o"></i></button>
                <button id="pl-manage-save" class="btn btn-default" type="button" title="Save current queue as playlist" data-toggle="modal" data-target="#modal-pl-save"><i class="fa fa-save"></i></button>
                <button id="pl-manage-clear" class="btn btn-default" type="button" title="Clear the playing queue" data-toggle="modal" data-target="#modal-pl-clear"><i class="fa fa-trash-o"></i></button>
            </div>
            <div id="pl-currentpath" class="hide">
                <i class="fa fa-folder-open"></i>
                <span>Playlists</span>
            </div>
        </div>
        <div id="spinner-pl" class="csspinner duo hide"></div>
    </div>
</div>
<div id="context-menus">
    <div id="context-menu" class="context-menu">
        <ul class="dropdown-menu" role="menu">
            <li><a href="javascript:;" data-cmd="add"><i class="fa fa-plus-circle sx"></i> Add</a></li>
            <li><a href="javascript:;" data-cmd="addplay"><i class="fa fa-play sx"></i> Add and play</a></li>
            <li><a href="javascript:;" data-cmd="addreplaceplay"><i class="fa fa-share-square-o sx"></i> Add, replace and play</a></li>
            <li><a href="javascript:;" data-cmd="rescan"><i class="fa fa-refresh sx"></i> Update this folder</a></li>
            <li><a href="javascript:;" data-cmd="bookmark"><i class="fa fa-star sx"></i> Save as bookmark</a></li>
        </ul>
    </div>
    <div id="context-menu-file" class="context-menu">
        <ul class="dropdown-menu" role="menu">
            <li><a href="javascript:;" data-cmd="add"><i class="fa fa-plus-circle sx"></i> Add</a></li>
            <li><a href="javascript:;" data-cmd="addplay"><i class="fa fa-play sx"></i> Add and play</a></li>
            <li><a href="javascript:;" data-cmd="addreplaceplay"><i class="fa fa-share-square-o sx"></i> Add, replace and play</a></li>
            <li><a href="javascript:;" data-cmd="lastfmaddreplaceplay"><i class="fa fa-lastfm sx"></i> Last.fm playlist from this</a></li>
        </ul>
    </div>
    <div id="context-menu-dirble" class="context-menu">
        <ul class="dropdown-menu" role="menu">
            <li><a href="javascript:;" data-cmd="wradd"><i class="fa fa-plus-circle sx"></i> Add</a></li>
            <li><a href="javascript:;" data-cmd="wraddplay"><i class="fa fa-play sx"></i> Add and play</a></li>
            <li><a href="javascript:;" data-cmd="wraddreplaceplay"><i class="fa fa-share-square-o sx"></i> Add, replace and play</a></li>
            <li><a href="javascript:;" data-cmd="wrsave"><i class="fa fa-microphone sx"></i> Save in My Webradios</a></li>
        </ul>
    </div>
	<div id="context-menu-spotify-pl" class="context-menu">
        <ul class="dropdown-menu" role="menu">
            <li><a href="javascript:;" data-cmd="spadd" data-type="spotify-playlist"><i class="fa fa-plus-circle sx"></i> Add</a></li>
            <li><a href="javascript:;" data-cmd="spaddplay" data-type="spotify-playlist"><i class="fa fa-play sx"></i> Add and play</a></li>
            <li><a href="javascript:;" data-cmd="spaddreplaceplay" data-type="spotify-playlist"><i class="fa fa-share-square-o sx"></i> Add, replace and play</a></li>
        </ul>
    </div>
	<div id="context-menu-spotify" class="context-menu">
        <ul class="dropdown-menu" role="menu">
            <li><a href="javascript:;" data-cmd="spadd" data-type="spotify-track"><i class="fa fa-plus-circle sx"></i> Add</a></li>
            <li><a href="javascript:;" data-cmd="spaddplay" data-type="spotify-track"><i class="fa fa-play sx"></i> Add and play</a></li>
            <li><a href="javascript:;" data-cmd="spaddreplaceplay" data-type="spotify-track"><i class="fa fa-share-square-o sx"></i> Add, replace and play</a></li>
        </ul>
    </div>
    <div id="context-menu-webradio" class="context-menu">
        <ul class="dropdown-menu" role="menu">
            <li><a href="javascript:;" data-cmd="add"><i class="fa fa-plus-circle sx"></i> Add</a></li>
            <li><a href="javascript:;" data-cmd="addplay"><i class="fa fa-play sx"></i> Add and play</a></li>
            <li><a href="javascript:;" data-cmd="addreplaceplay"><i class="fa fa-share-square-o sx"></i> Add, replace and play</a></li>
            <li><a href="javascript:;" data-cmd="wredit"><i class="fa fa-edit sx"></i> Edit</a></li>
            <li><a href="javascript:;" data-cmd="wrdelete"><i class="fa fa-trash-o sx"></i> Delete</a></li>
        </ul>
    </div>
    <div id="context-menu-playlist" class="context-menu">
        <ul class="dropdown-menu" role="menu">
            <li><a href="javascript:;" data-cmd="pl-add"><i class="fa fa-plus-circle sx"></i> Add to queue</a></li>
            <li><a href="javascript:;" data-cmd="pl-replace"><i class="fa fa-undo sx"></i> Replace the queue</a></li>
            <li><a href="javascript:;" data-cmd="pl-addreplaceplay"><i class="fa fa-share-square-o sx"></i> Replace and play the queue</a></li>
			<li><a href="javascript:;" data-cmd="pl-ashuffle"><i class="fa fa-random sx"></i> Randomly add songs to queue</a></li>
            <li><a href="javascript:;" data-cmd="pl-rename"><i class="fa fa-edit sx"></i> Rename</a></li>
            <li><a href="javascript:;" data-cmd="pl-rm"><i class="fa fa-trash-o sx"></i> Delete</a></li>
        </ul>
    </div>
    <div id="context-menu-album" class="context-menu">
        <ul class="dropdown-menu" role="menu">
            <li><a href="javascript:;" data-cmd="albumadd"><i class="fa fa-plus-circle sx"></i> Add</a></li>
            <li><a href="javascript:;" data-cmd="albumaddplay"><i class="fa fa-play sx"></i> Add and play</a></li>
            <li><a href="javascript:;" data-cmd="albumaddreplaceplay"><i class="fa fa-share-square-o sx"></i> Add, replace and play</a></li>
        </ul>
    </div>
    <div id="context-menu-artist" class="context-menu">
        <ul class="dropdown-menu" role="menu">
            <li><a href="javascript:;" data-cmd="artistadd"><i class="fa fa-plus-circle sx"></i> Add</a></li>
            <li><a href="javascript:;" data-cmd="artistaddplay"><i class="fa fa-play sx"></i> Add and play</a></li>
            <li><a href="javascript:;" data-cmd="artistaddreplaceplay"><i class="fa fa-share-square-o sx"></i> Add, replace and play</a></li>
        </ul>
    </div>
    <div id="context-menu-genre" class="context-menu">
        <ul class="dropdown-menu" role="menu">
            <li><a href="javascript:;" data-cmd="genreadd"><i class="fa fa-plus-circle sx"></i> Add</a></li>
            <li><a href="javascript:;" data-cmd="genreaddplay"><i class="fa fa-play sx"></i> Add and play</a></li>
            <li><a href="javascript:;" data-cmd="genreaddreplaceplay"><i class="fa fa-share-square-o sx"></i> Add, replace and play</a></li>
        </ul>
    </div>
    <div id="context-menu-composer" class="context-menu">
        <ul class="dropdown-menu" role="menu">
            <li><a href="javascript:;" data-cmd="composeradd"><i class="fa fa-plus-circle sx"></i> Add</a></li>
            <li><a href="javascript:;" data-cmd="composeraddplay"><i class="fa fa-play sx"></i> Add and play</a></li>
            <li><a href="javascript:;" data-cmd="composeraddreplaceplay"><i class="fa fa-share-square-o sx"></i> Add, replace and play</a></li>
        </ul>
    </div>
</div>
<div id="modal-pl-save" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-pl-save-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title" id="modal-pl-save-label">Save current queue as playlist</h3>
            </div>
            <div class="modal-body">
                <label for="pl-save-name">Give a name to this playlist</label>
                <input id="pl-save-name" class="form-control osk-trigger" type="text" placeholder="Enter playlist name" list='pl-list'>
                    <datalist id='pl-list'>
                    </datalist>
                </input>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Close</button>
                <button type="button" id="modal-pl-save-btn" class="btn btn-primary btn-lg" data-dismiss="modal">Save playlist</button>
            </div>
        </div>
    </div>
</div>
<div id="modal-pl-clear" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-pl-clear-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title" id="modal-pl-clear-label">Clear current queue</h3>
            </div>
            <div class="modal-body">
                This will clear the current playing queue.<br>
                Are you sure?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary btn-lg btn-cmd" data-cmd="clear" data-dismiss="modal">Clear</button>
            </div>
        </div>
    </div>
</div>
<div id="modal-pl-rename" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-pl-rename-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title" id="modal-pl-rename-label">Rename the playlist</h3>
            </div>
            <div class="modal-body">
                <label for="pl-rename-name">Rename "<strong id="pl-rename-oldname"></strong>" playlist to:</label>
                <input id="pl-rename-name" class="form-control osk-trigger" type="text" placeholder="Enter playlist name">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Close</button>
                <button id="pl-rename-button" type="button" class="btn btn-primary btn-lg" data-dismiss="modal">Rename</button>
            </div>
        </div>
    </div>
</div>
<div id="modal-webradio-add" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-webradio-add-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title" id="modal-pl-webradio-add">Add new webradio</h3>
            </div>
            <div class="modal-body">
                <label for="webradio-add-name">Radio name</label>
                <input id="webradio-add-name" name="radio[label]" class="form-control osk-trigger" type="text" placeholder="Enter webradio name">
                <br>
                <label for="webradio-add-url">Radio url</label>
                <input id="webradio-add-url" name="radio[label]" class="form-control osk-trigger" type="text" placeholder="Enter webradio url">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Close</button>
                <button id="webradio-add-button" type="button" class="btn btn-primary btn-lg">Add to Library</button>
            </div>
        </div>
    </div>
</div>
<div id="modal-webradio-edit" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-webradio-edit-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title" id="modal-pl-webradio-edit">Edit webradio</h3>
            </div>
            <div class="modal-body">
                <input id="webradio-edit-oldname" name="radio[oldlabel]" class="form-control" type="hidden" value="">
                <label for="webradio-edit-name">Radio name</label>
                <input id="webradio-edit-name" name="radio[label]" class="form-control osk-trigger" type="text" placeholder="Enter webradio name">
                <br>
                <label for="webradio-edit-url">Radio url</label>
                <input id="webradio-edit-url" name="radio[label]" class="form-control osk-trigger" type="text" placeholder="Enter webradio url">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Close</button>
                <button id="webradio-edit-button" type="button" class="btn btn-primary btn-lg" data-dismiss="modal">Save</button>
            </div>
        </div>
    </div>
</div>
<div id="modal-webradio-delete" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-webradio-delete-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title" id="modal-pl-webradio-delete">Delete the webradio</h3>
            </div>
            <div class="modal-body">
                <p><strong id="webradio-delete-name">Radio.pls</strong><br>
                Delete this entry from your Library?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Close</button>
                <button id="webradio-delete-button" type="button" class="btn btn-primary btn-lg" data-dismiss="modal">Delete</button>
            </div>
        </div>
    </div>
</div>
<div id="songinfo-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="songinfo-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <!-- <h4 class="modal-title" id="songinfo-modal-label" >Song info</h4> -->
            </div>
            <div class="modal-body txtmid">
                <div class="col-sm-4 coverart" style="height: auto; margin: 10px auto 11px; width: 100%;">
                    <div id="artist-overlay" style="font-size: 30px; display: block; box-sizing: border-box; text-align: center; font-weight: bold; color: rgb(0, 149, 216); line-height: 34px; margin-bottom: 20px; margin-top: 20px;"><i>Artist:</i></div>
                    <img id="artist-image-overlay" style="margin-right: 20px; margin-left: 10px; float: left; width: 130px; height: auto; border: 2px solid rgb(52, 73, 94); border-radius: 6px; background-size: 100% 100%;" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" alt="transparent-square"></img>
                    <div id="artist-bio-overlay" style="font-size: 20px"><pre class="fa fa-spinner fa-spin"></pre></div>
                    <div id="artist-bio-full-overlay" style="font-size: 20px" hidden="true"><pre class="fa fa-spinner fa-spin"></pre></div>
                </div>
                <div style="height: auto">
                    <div class="col-sm-4 coverart" style="height: auto; margin: 10px auto 11px; width: 100%;">
                        <div id="addinfo-text-overlay" style="font-size: 20px; box-sizing: border-box;"><pre class="fa fa-spinner fa-spin"></pre></div>
                        <div id="lyric-overlay" style="font-size: 30px; display: block; box-sizing: border-box; text-align: center; font-weight: bold; color: rgb(0, 149, 216); line-height: 24px; margin-bottom: 20px; margin-top: 20px;"><i>Lyric:</i></div>
                        <div id="lyric-text-overlay" style="font-size: 20px; box-sizing: border-box;"><pre class="fa fa-spinner fa-spin"></pre></div>
                    </div>
                </div>
			</div>
			<div class="modal-footer">
                <button class="btn btn-default btn-lg" data-dismiss="modal" aria-hidden="true">Cancel</button>
            </div>
        </div>
    </div>
</div>
<div id="overlay-social" class="overlay-scale closed">
    <nav>
        <ul>
            <li><span>Share this track</span></li>
            <li><a id="urlTwitter" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" class="btn btn-default btn-lg btn-block share-twitter" href="#"><i class="fa fa-twitter sx"></i> Share on Twitter</a></li>
            <li><a id="urlFacebook" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" class="btn btn-default btn-lg btn-block share-facebook" href="#"><i class="fa fa-facebook sx"></i> Share on Facebook</a></li>
            <li><a id="urlGooglePlus" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" class="btn btn-default btn-lg btn-block share-google-plus" href="#"><i class="fa fa-google-plus sx"></i> Share on Google+</a></li>
            <li><a id="support-us" class="btn btn-default btn-lg btn-block" href="http://www.runeaudio.com/support-us/" target="_blank"><i class="fa fa-heart sx"></i> Support RuneAudio</a></li>
            <li><button id="overlay-social-close" class="btn btn-link" type="button"><i class="fa fa-times"></i> close this layer</button></li>
        </ul>
    </nav>
</div>
<div id="overlay-playsource" class="overlay-scale closed">
    <nav>
        <ul>
            <li><span>Playback source</span></li>
			<li><a href="javascript:;" id="playsource-mpd" class="btn btn-default btn-lg btn-block" title="Switch to MPD"><i class="fa fa-linux sx"></i> MPD</a></li>
			<li><a href="javascript:;" id="playsource-spotify" class="btn btn-default btn-lg btn-block inactive" title="Switch to Spotify"><i class="fa fa-spotify sx"></i> <span>spop</span> Spotify</a></li>
			<li><a href="javascript:;" id="playsource-airplay" class="btn btn-default btn-lg btn-block inactive"><i class="fa fa-apple sx"></i> <span>ShairPort</span> Airplay</a></li>
			<li><a href="javascript:;" id="playsource-dlna" class="btn btn-default btn-lg btn-block inactive"><i class="fa fa-puzzle-piece sx"></i> <span>upmpdcli</span> DLNA</a></li>
            <li><button id="overlay-playsource-close" class="btn btn-link" type="button"><i class="fa fa-times"></i> close this layer</button></li>
        </ul>
    </nav>
</div>