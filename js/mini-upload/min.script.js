$(function () {
	var ul = $('#upload ul');
	$('#drop a').click(function (e) {
		$(this).parent().find('input').click();
	});
	$('#upload').fileupload({
		dropZone : $('#drop'),
		add : function (e, data) {
			var err = '';
			if ($('.video[name=title]').val() == '') {
				err += "<br>Title";
			}
			if ($('.video[name=description]').val() == '') {
				err += "<br>Description";
			}
			if ($('.video[name=keywords]').val() == '') {
				err += "<br>Keywords";
			}
			if ($('.video[name=channel]').val() == '') {
				err += "<br>Channel";
			}
			if (err.length != 0) {
				err = 'The following fields are required:<br>' + err;
				openVbox('modules/ajax/message.php?message=' + err, 300, 100);
				return false;
			}
			var tpl = $('<li class="working"><input type="text" value="0" data-width="48" data-height="48"' + ' data-fgColor="#0788a5" data-readOnly="1" data-bgColor="#3e4043" /><p></p><span></span></li>');
			tpl.find('p').text(data.files[0].name).append('<i>' + formatFileSize(data.files[0].size) + '</i>');
			data.context = tpl.appendTo(ul);
			tpl.find('input').knob();
			tpl.find('span').click(function () {
				if (tpl.hasClass('working')) {
					jqXHR.abort();
				}
				tpl.fadeOut(function () {
					tpl.remove();
				});
			});
			var jqXHR = data.submit();
		},
		progress : function (e, data) {
			var progress = parseInt(data.loaded / data.total * 100, 10);
			data.context.find('input').val(progress).change();
			if (progress == 100) {
				data.context.removeClass('working');
				$("#fields .video").attr("disabled", "disabled");
				$('#upload').append('<div id="percentage">0%</div>'
					 + '<div>Your photo has been uploaded.<br>We are now cropping it to fit everywhere on our site.<br>Thank you!</div>');
			}
		},
		fail : function (e, data) {
			data.context.addClass('error');
		},
		done : function (e, data) {
			/*
			data.result = $.parseJSON(data.result);
			if (typeof(data.result.error) != 'undefined') {
				if (data.result.error == true) {
					$('.loading_bar').remove()
					openVbox('modules/ajax/message.php?message=' + data.result.msg, 200, 100);
				} else {
					var username = data.result.username;
					var duration_minutes = parseInt(data.result.duration / 60);
					var duration_seconds = parseInt(data.result.duration % 60);
					if (duration_seconds < 10) {
						duration_seconds = "0" + duration_seconds;
					}
					var duration = duration_minutes + ":" + duration_seconds
						$('#upload').html('Your photo has been processed and ready to view from everywhere.');
					str = '<li id="myphoto_medium_' + data.result.image + '">'
						 + '<a class="border" href="javascript:openVbox(\'modules/ajax/show_photo.php?un=' + data.result.user + '&img=' + data.result.filePath + '\',250,300);void(0);">'
						 + '<img width="120" src="' + data.result.file.replace('orig_', 'medium_') + '" alt="' + username + '">'
						 + '</a>'
						 + '<a href="javascript:removePhoto(\'' + data.result.image + '\');">remove</a><br>'
						 + '<a href="javascript:mainPhoto(\'' + data.result.image + '\');">make main</a>'
						 + '</li>';
					if ($("ul.photolist")[0]) {
						$("ul.photolist").append(str);
					}
				}
			}
			*/
		}
	});
	$(document).on('drop dragover', function (e) {
		e.preventDefault();
	});
	function formatFileSize(bytes) {
		if (typeof bytes !== 'number') {
			return '';
		}
		if (bytes >= 1000000000) {
			return (bytes / 1000000000).toFixed(2) + ' GB';
		}
		if (bytes >= 1000000) {
			return (bytes / 1000000).toFixed(2) + ' MB';
		}
		return (bytes / 1000).toFixed(2) + ' KB';
	}
	$(document).find('#upload').on('submit',function(e){
		//e.preventDefault();
	});
});