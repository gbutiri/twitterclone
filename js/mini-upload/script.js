$(function(e){
    var ul = $(document).find('#upload ul');
	var $theuploader = $(document).find('#upload');
	
	$(document).find('#upload').on('submit',function(e) {
		//console.log(this);
		//e.preventDefault();
		//$theuploader.fileupload();
	});
	
	//console.log(ul);
	
    $('#drop a').click(function(){
        // Simulate a click on the file input button
        // to show the file browser dialog
        $(this).parent().find('input').click();
    });

    // Initialize the jQuery File Upload plugin
	
   $theuploader.fileupload({
		
        // This element will accept file drag/drop uploading
        dropZone: $(document).find('#drop'),

        // This function is called when a file is added to the queue;
        // either via the browse button, or via drag/drop:
        add: function (e, data) {
			//console.log(e,data);
			//alert($('#video[name=video]').val());
			/*
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
				err = 'The following fields are required:<br>'+err;
				openVbox('modules/ajax/message.php?message='+err,300,100);
				return false;
			}
			*/
			
            var tpl = $('<li class="working"><input type="text" value="0" data-width="48" data-height="48"'+
                ' data-fgColor="#0788a5" data-readOnly="1" data-bgColor="#3e4043" /><p></p><span></span></li>');

            // Append the file name and file size
            tpl.find('p').text(data.files[0].name)
                         .append('<i>' + formatFileSize(data.files[0].size) + '</i>');

            // Add the HTML to the UL element
            data.context = tpl.appendTo(ul);

            // Initialize the knob plugin
            tpl.find('input').knob();

            // Listen for clicks on the cancel icon
            tpl.find('span').click(function(){

                if(tpl.hasClass('working')){
                    jqXHR.abort();
                }

                tpl.fadeOut(function(){
                    tpl.remove();
                });

            });

            // Automatically upload the file once it is added to the queue
			$theuploader = $(this);
            var jqXHR = data.submit();
			//data.files.length = 0;
        },

        progress: function(e, data){

            // Calculate the completion percentage of the upload
            var progress = parseInt(data.loaded / data.total * 100, 10);

            // Update the hidden input field and trigger a change
            // so that the jQuery knob plugin knows to update the dial
            data.context.find('input').val(progress).change();

            if(progress == 100){
                data.context.removeClass('working');
				$("#fields .video").attr("disabled", "disabled");
				$theuploader.append(
							'<div id="percentage">0%</div>'
							+'<div>Your file has been uploaded.<br>We are now processing it.<br>Thank you for your patience!</div>'
				);
				
            }
        },

        fail:function(e, data){
            // Something has gone wrong!
			//e.preventDefault();
			//return false;
			
			//console.log('err',e,data);
            data.context.addClass('error');
        },
		done:function (e, data) {
			//$('#upload cta').unbind('click');
			if (typeof(data) != 'undefined') {
				
				
				//data.result = $.parseJSON(data.result);
						///*
				if (typeof(data.result) === 'string') {
					data.result = $.parseJSON(data.result);
				}
				
				// TODO - Check to see what's being uploaded.
				// Do additionals logic on this.
				if (typeof(data.result) != 'undefined') {
					if(typeof(data.result.error) != 'undefined') {
						if(data.result.error == true) {
							// console.log(data);
							//$('.loading_bar').remove()
							//openVbox('modules/ajax/message.php?message='+data.result.message,200,100);
							
						} else {
							/*
							if (data.result.type == 'headshot') {
								if ($('.photolist').length > 0) {
									//console.log(data.result.htmlsAppend);
									for ($htmls in data.result.htmlsAppend) {
										$($htmls).append(data.result.htmlsAppend[$htmls]);
										//closeVbox();
									}
								}
							}
							if (data.result.type == 'video') {
								if ($('.myvideolist').length > 0) {
									for ($htmls in data.result.htmlsPrepend) {
										$($htmls).prepend(data.result.htmlsPrepend[$htmls]);
										//closeVbox();
									}
								}
							}
							closeVbox();
							*/
							
							//console.log(data.result);
							$('.avatar img').attr('src',data.result.smallFilePath);
							$('#profile-avatar img').attr('src',data.result.mediumFilePath);
							$('#fuzz').remove();
							/*
							
							var username = data.result.username;
							//console.log(.resultdata);
							var duration_minutes = parseInt(data.result.duration / 60);
							var duration_seconds = parseInt(data.result.duration % 60);
							if (duration_seconds < 10 ) {
								duration_seconds = "0"+duration_seconds;
							}
							var duration = duration_minutes+":"+duration_seconds
							
							//$('.loading_bar').remove();
							//$theuploader.after('Your photo has been processed and ready to view from everywhere.').remove();
							
							if ($("ul.photolist").length > 0) {
								str='<li id="myphoto_medium_'+data.result.image+'">'
										+'<a class="border" href="javascript:openVbox(\'modules/ajax/show_photo.php?un='+data.result.user+'&img='+data.result.filePath+'\',250,300);void(0);">'
										+'<img width="120" src="'+data.result.file.replace('orig_','medium_')+'" alt="'+username+'">'
										+'</a>'
										+'<a href="javascript:removePhoto(\''+data.result.image+'\');">remove</a><br>'
										+'<a href="javascript:mainPhoto(\''+data.result.image+'\');">make main</a>'
									+'</li>';
								$("ul.photolist").append(str);
							} else {
								showNotification("Your file has been added.");
								closeVbox();
							}
							*/
						}
					}
				}
						//*/
			}
			
		}

    });


    // Prevent the default action when a file is dropped on the window
    $(document).on('drop dragover', function (e) {
        e.preventDefault();
    });

    // Helper function that formats the file sizes
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