<!--
custom alert box

depend:
	info.css
	button.css
feature:
	(icon font)
init:
	require_once 'info.php' (in body)
usage:
	
-->
<style>
#infoOverlay {
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	position: fixed;
	background: rgba(250, 250, 250, 0.85);
	z-index: 100000;
	text-align: center;
}
#infoBox {
	position: relative;
	left: 50%;
	top: 40%;
	width: 440px;
	margin-left: -200px;
	margin-top: -100px;
	background: #19232d;
	border-radius: 10px;
	box-shadow: 4px 4px 10px #555;  /*  V-offfset, H-Offset, Blur width, Shadow width , Color */
}

#infoTopBg {
	width: 100%;
	height: 40px;
	border-radius: 10px 10px 0 0;
	background: black;
}
#infoTop {
	color: #e0e7ee;
	line-height: 40px;
	padding: 0 20px;
	text-align: left;
}
#infoTop a {
    color: #e0e7ee;
}
#infoTitle {
	font-size: 1.1em;
	line-height: 30px;
}
#infoContent {
	padding: 25px 10px 10px 10px;
}
#infoMessage {
	word-wrap: break-word;
	overflow: auto;
	max-height: 800px;
}
#infoButtons {
	padding: 10px 0 20px 0;
}
#infoButtons a {
	margin: 0 5px;
	min-width: 70px;
}
</style>

<div id="infoOverlay" class="hide">
	<div id="infoBox">
		<!-- header -------------------------------------------------->
		<div id="infoTopBg">
			<div id="infoTop">
				<a id="infoIcon"></a>&emsp;<a id="infoTitle"></a>
			</div>
		</div>
		<!-- content -------------------------------------------------->
		<div id="infoContent">
			<div id="infoText" class="info">
				<a id="infoTextLabel"></a> <input type="text" class="infoBox" id="infoTextbox">
			</div>
			<div id="infoSelect" class="info">
				<a id="infoSelectLabel"></a> <select class="infoBox" id="infoSelectbox"></select>
			</div>
			<div id="infoCheck" class="info">
				<input type="checkbox" id="infoCheckbox"> <a id="infoCheckLabel"></a>
			</div>
			<div id="infoRadio" class="info">
			</div>
			<p id="infoMessage" class="info"></p>
		</div>
		<!-- button -------------------------------------------------->
		<div id="infoButtons">
			<a id="infoCancel" class="btn btn-default"></a>
			<a id="infoOk" class="btn btn-primary"></a>
		</div>
	</div>
</div>

<script>
function info(option) {
	// reset to default
	$('#infoIcon').html('<i class="fa fa-info-circle fa-lg">');
	$('#infoTitle').html('I n f o');
	$('#infoSelectbox, #infoRadio, #infoMessage').empty();
	$('#infoTextbox, #infoSelectbox').val('');
	$('#infoCheckbox').prop('checked', false);
	$('.infoBox').width(200);
	$('.info, #infoCancel').hide();
	$('#infoOk').html('Ok');
	$('#infoCancel').html('Cancel')

	// simple use as info('message')
	if (typeof option != 'object') {
		$('#infoOk').off('click').on('click', function () {
			$('#infoOverlay').hide();
		});
		$('#infoMessage').html(option).show();
	} else {
		// option use as info({x: 'x', y: 'y'})
		var icon = option['icon'];
		var title = option['title'];
		var message = option['message'];
		var textbox = option['textbox'];
		var textvalue = option['textvalue'];
		var selectbox = option['selectbox'];
		var selecthtml = option['selecthtml'];
		var selectvalue = option['selectvalue'];
		var checkbox = option['checkbox'];
		var radiobox = option['radiobox'];
		var radiohtml = option['radiohtml'];
		var boxwidth = option['boxwidth'];
		var ok = option['ok'];
		var oktext = option['oktext'];
		var okcolor = option['okcolor'];
		var cancel = option['cancel'];
		var canceltext = option['canceltext'];
		
		if (icon) $('#infoIcon').html(icon);
		if (title) $('#infoTitle').html(title);
		if (message) $('#infoMessage').html(message).show();
		if (textbox) {
			$('#infoTextLabel').html(textbox);
			if (textvalue) $('#infoTextbox').val(textvalue);
			$('#infoText').show();
			var infofocus = $('#infoTextbox');
		}
		if (selectbox) {
			$('#infoSelectLabel').html(selectbox);
			if (selecthtml) $('#infoSelectbox').html(selecthtml);
			if (selectvalue) $('#infoSelectbox').val(selectvalue);
			$('#infoSelect').show();
		}
		if (checkbox) {
			$('#infoCheckLabel').html(checkbox);
			$('#infoCheck').show();
		}
		if (radiobox) {
			$('#infoRadio').html(radiobox +' '+ radiohtml).show();
		}
		if (boxwidth) $('.infoBox').width(boxwidth);
		
		if (ok) {
			$('#infoOk').off('click').on('click', function () {
				$('#infoOverlay').hide();
				(typeof ok === 'function') && ok();
			});
		} else {
			$('#infoOk').off('click').on('click', function () {
				$('#infoOverlay').hide();
			});
		}
		if (oktext) $('#infoOk').html(oktext);
		if (okcolor) $('#infoOk').css('background', okcolor);
		if (cancel) {
			$('#infoCancel').show();
			$('#infoCancel').off('click').on('click', function () {
				$('#infoOverlay').hide();
				(typeof cancel === 'function') && cancel();
			});
		}
		if (canceltext) $('#infoCancel').html(canceltext);
	}
	
	$('#infoOverlay').show();
	if (infofocus) infofocus.select();
	
	$('#infoOverlay').keypress(function (e) {
		if (e.which == 13) {
			$('#infoOverlay').hide();
		}
	});
	
	$('#infoX').click(function () {
		$('#infoOverlay').hide();
	});

}
</script>