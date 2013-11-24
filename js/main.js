var objLocalZone = new Date();
var servertime = 6;
var objLocalZoneBit = (parseInt(objLocalZone.toString().split(" ")[5].replace("GMT",""))/100)+servertime;
// testing for Romania timezone.
//	objLocalZoneBit = "+2";

$(document).ready(function(){
	$(document).on('focus','#write-area',function(e){
		var $this = $(this);
		if ($this.val() == $this.attr('place-holder')) {
			$this.val('');
		}
		$this.addClass('active');
	}).on('blur','#write-area',function(e){
		var $this = $(this);
		if ($this.val() == '') {
			$this.val($this.attr('place-holder'));
			$this.removeClass('active');
		}
	}).on('click','#post-button',function(e){
		e.preventDefault();
		var $writearea = $('#write-area');
		if ($writearea.val() != $writearea.attr('place-holder')) {
			var $val = $('#write-area').serialize();
			$writearea.val('');
			autosize($writearea);
			$.ajax({
				url: '/ajax/postcalls.php?action=makeapost&timezone',
				type: 'POST',
				dataType: 'JSON',
				data: $val,
				success: function(data) {
					//console.log(data);
					$writearea.val($writearea.attr('place-holder'));
				}
			});
		}
	}).on('keyup','#write-area',function(e) {
		if (e.keyCode == 13) {
			$('#post-button').trigger('click');
			$('#write-area').blur();
		}
		if ($('#write-area').val().length > 140) {
			$('#write-area').val($('#write-area').val().substr(0,140));
		}
	}).on('click','#load-more',function (e) {
		var $firstMessage = $(document).find('#tweets li').last();
		if ($firstMessage[0]) {
			$firstId = $firstMessage[0].id.replace('post_','');
		} else {
			$firstId = 0;
		}
		$.ajax({
			url: '/ajax/postcalls.php?action=loadmore&firstid='+$firstId+'&timezone='+objLocalZoneBit,
			type: 'GET',
			dataType:'JSON',
			success:function(data) {
				if (data.nummore == data.messagesleft) {
					if(data.messages != '') {
						$('#tweets').append(data.messages);
					}
					$('#load-more').remove();
				} else {
					$('#load-more').show();
					$('#tweets').append(data.messages);
					console.log(data.nummore - data.messagesleft);
					$(document).find('#load-more span').text(data.nummore - data.messagesleft);
				}
				
			}
		});
	}).on('submit','#signin-form,#signup-form',function(e) {
		var $this = $(this);
		console.log($this.serialize());
		e.preventDefault();
		$.ajax({
			url: $this.attr('action'),
			type: 'POST',
			dataType: 'JSON',
			data: $this.serialize(),
			success: function(data) {
				if (data.error == true) {
					notify(data.message);
				}
				if (data.error == false) {
					window.location = '/';
				}
			}
		});
	}).on('click','#logout-link',function (e) {
		e.preventDefault();
		$.ajax({
			url: '/ajax/registration.php?action=logout',
			dataType: 'JSON',
			success: function (data) {
				if (data.error == false) {
					window.location = '/login.php';
				}
			}
		});
	}).on('click','#fuzz-close',function(e) {
		e.preventDefault();
		$('#fuzz').remove();
	}).on('click','#fuzz',function(e) {
		e.preventDefault();
		var $this = $(this)[0].id;
		var $target = $(e.target)[0].id;
		//console.log(e,$this,$target);
		if ($this == $target) {
			$('#fuzz').remove();
		}
	});
	
	var notify = function (message) {
		$(document).find('body').append('<div id="fuzz"><div id="notification">'+message+'<a id="fuzz-close" href="#close">&times;</a></div></div>');
	}
	
	var autosize = function($this) {
		$this.height(0);
		$this.height($this[0].scrollHeight);
	}
	
	var loadInitialMessages = function () {
		$.ajax({
			url:'/ajax/postcalls.php?action=loadinitialmessages&timezone='+objLocalZoneBit,
			type:'GET',
			dataType:'JSON',
			success:function(data) {
				if (data.nummore == data.messagesleft) {
					if(data.messages != '') {
						$('#tweets').append(data.messages);
					}
					$('#load-more').remove();
				} else {
					$('#load-more').show();
					$('#tweets').append(data.messages);
					console.log(data.nummore - data.messagesleft);
					$(document).find('#load-more span').text(data.nummore - data.messagesleft);
				}
			}
		});
	}
	if ($('#tweets').length > 0 && $('#write-area').length > 0) {
		loadInitialMessages();
	}
	
	var loadLatestMessages = function () {
		var $latestMessage = $(document).find('#tweets li').first();
		if ($latestMessage[0]) {
			$lastId = $latestMessage[0].id.replace('post_','');
		} else {
			$lastId = 0;
		}

		$.ajax({
			url:'/ajax/postcalls.php?action=getlatestposts&lastid='+$lastId+'&timezone='+objLocalZoneBit,
			type:'GET',
			dataType:'JSON',
			success:function(data) {
				$('#tweets').prepend(data.messages);
			}
		});
	}
	
	var k;
	var siteclock = function () {
		clearTimeout(k);
		k = setTimeout(function(){
			loadLatestMessages();
			siteclock();
		},2000);
	}
	if ($('#tweets').length > 0 && $('#write-area').length > 0) {
		siteclock();
	}
	
	$('.autosize').css('overflow', 'hidden').on('keyup',function(e) {
		var $this = $(this);
		autosize($this);
	});
});