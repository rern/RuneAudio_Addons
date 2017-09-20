function info(option) {
	// reset to default
	$('#infoIcon').html('<i class="fa fa-question-circle fa-lg">');
	$('#infoTitle').html('Information');
	$('#infoSelectbox, #infoRadio, #infoMessage').empty();
	$('#infoTextbox, #infoPasswordbox, #infoSelectbox').val('');
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
		var passwordbox = option['passwordbox'];
		var selectbox = option['selectbox'];
		var selecthtml = option['selecthtml'];
		var selectvalue = option['selectvalue'];
		var checkbox = option['checkbox'];
		var radiobox = option['radiobox'];
		var boxwidth = option['boxwidth'];
		var ok = option['ok'];
		var oktext = option['oktext'];
		var okcolor = option['okcolor'];
		var cancel = option['cancel'];
		var canceltext = option['canceltext'];
		
		if (icon) $('#infoIcon').html(icon);
		if (title) $('#infoTitle').html(title);
		if (message) {
			$('#infoMessage').html(message).show();
			var infofocus = $('#infoOk');
		}
		if (textbox) {
			$('#infoTextLabel').html(textbox);
			if (textvalue) $('#infoTextbox').val(textvalue);
			$('#infoText').show();
			var infofocus = $('#infoTextbox');
		}
		if (passwordbox) {
			if (passwordbox) $('#infoPasswordLabel').html(passwordbox);
			$('#infoPassword').show();
			var infofocus = $('#infoPasswordbox');
		}
		if (selecthtml) {
			$('#infoSelectbox').html(selecthtml);
			$('#infoSelect').show();
		}
		if (checkbox) {
			$('#infoCheck').html(checkbox);
			$('#infoCheck').show();
		}
		if (radiobox) {
			$('#infoRadio').html(radiobox).show();
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
	if (infofocus) infofocus.focus();
	
	$('#infoOverlay').keypress(function (e) {
		if (e.which == 13) {
//			$('#infoOverlay').hide();
		}
	});
	
}
