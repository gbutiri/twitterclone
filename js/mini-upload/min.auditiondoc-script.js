$(function () {
	var ul = $('#upload ul');
	$(document).find('#drop a').click(function () {
		$(this).parent().find('input').click();
	});
	$(document).find('#upload').fileupload({
		dropZone : $(document).find('#drop'),
		add : function (e, data) {

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
					 + '<div>Your document has been uploaded.</div>');
			}
		},
		fail : function (e, data) {
			data.context.addClass('error');
		},
		done : function (e, data) {
			data.result = $.parseJSON(data.result);
			if (typeof(data.result.error) != 'undefined') {
				if (data.result.error == true) {
					$('.loading_bar').remove()
					openVbox('modules/ajax/message.php?message=' + data.result.msg, 200, 100);
				} else {
					var username = data.result.username;

					//$('#upload').html('Your document has been uploaded.');
					var str = '<li data-filename="' + data.result.file + '">'
						 + '<a class="border" href="' + data.result.filePath + '">'
						 + '' + data.result.file + ''
						 + '</a>'
						 + '</li>';
					$("ul.photolist").append(str);
				}
			}
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
});
