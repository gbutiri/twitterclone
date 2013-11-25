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
		//console.log($writearea.val());
		if ($writearea.val() == $writearea.attr('place-holder') || $writearea.val().trim() == '') {
			//console.log('invalid');
			$writearea.val('');
			$writearea.blur();
			autosize($writearea);
		} else {
			//console.log('valid');
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
					$writearea.val('');
					$writearea.blur();
				}
			});
		}
	}).on('keyup','#write-area',function(e) {
		if (e.keyCode == 13) {
			$('#write-area').blur();
			$('#post-button').trigger('click');
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
		var $username = $('#tweets').attr('data-username');
		if (typeof($username) === 'undefined') {
			$username = '';
		}
		$.ajax({
			url: '/ajax/postcalls.php?action=loadmore&username='+$username+'&firstid='+$firstId+'&timezone='+objLocalZoneBit,
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
					//console.log(data.nummore - data.messagesleft);
					$(document).find('#load-more span').text(data.nummore - data.messagesleft);
				}
				
			}
		});
	}).on('submit','#signin-form,#signup-form',function(e) {
		var $this = $(this);
		//console.log($this.serialize());
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
					window.location = '/login.html';
				}
			}
		});
	}).on('click','#fuzz-close',function(e) {
		e.preventDefault();
		$('#fuzz').remove();
	}).on('click','#fuzz',function(e) {
		var $this = $(this)[0].id;
		var $target = $(e.target)[0].id;
		//console.log(e,$this,$target);
		if ($this == $target) {
			e.preventDefault();
			$('#fuzz').remove();
		}
	}).on('click','#profile-avatar a',function(e){
		e.preventDefault();
		$.ajax({
			url: '/ajax/profile-calls.php?action=showimageuploader',
			success: function(data) {
				notify(data);
			}
		});
	}).on('click','#drop cta',function() {
		$(document).find('#mainimage').trigger('click');
	}).on('focus','#profile-form .autosave',function(e){
		var $this = $(this);
		if ($this.val() == $this.attr('place-holder')) {
			$this.val('');
		}
		$this.addClass('active');
	}).on('blur','#profile-form .autosave',function(e){
		var $this = $(this);
		if ($this.val() == '') {
			$this.val($this.attr('place-holder'));
			$this.removeClass('active');
		}
	}).on('keyup','#zipcode',function() {
		var $this = $(this);
		if ($this.val().length > 2) {
			// show the zip code results
			$.ajax({
				url: '/ajax/profile-calls.php?action=getzipcode&zipcode='+$this.val().trim(),
				dataType: 'JSON',
				success: function (data) {
					$(document).find('#zipcode-results').remove();
					$this.after('<div id="zipcode-results">&nbsp;</div>');
					$(document).find('#zipcode-results').html(data.locationvalue);
				}
			});
		}
	}).on('click','#zipcode-results a',function(e){
		var $this = $(this);
		e.preventDefault();
		var $zipval = $this.html();
		if ($this.html() == 'REMOVE') {
			$zipval = '';
		}
		$('#zipcode').val($zipval);
		saveField($('#zipcode'));
		$(document).find('#zipcode-results').remove();
	}).on('click','#tweets .like-bar a',function(e) {
		e.preventDefault();
		var $this = $(this);
		var $counter = $this.parent().find('.like-count');
		var $postId = $this.attr('data-id');
		var $like='like';
		if ($this.hasClass('unlike-button')) {
			$like='unlike';
		}
		$.ajax({
			url: '/ajax/profile-calls.php?action=like&postid='+$postId+'&like='+$like,
			dataType: 'JSON',
			success: function (data) {
				$counter.html(data.likes);
				$this.toggleClass('like-button unlike-button');
				if (data.like) {
					$this.html('Nu-mi place');
				} else {
					$this.html('Îmi place');
				}
			}
		});
	});
	
	var saveField = function($this) {
		var $form = $this.closest('form');
		var $url = $this.closest('form').attr('action');
		$.ajax({
			url: '/ajax/profile-calls.php?action=savefield',
			type: 'POST',
			dataType: 'JSON',
			data: $form.serialize(),
			success: function(data) {
				console.log(data);
			}
		});
		//console.log($url);
	}
	
	var notify = function (message) {
		if ($('#fuzz').length == 0) {
			$(document).find('body').append('<div id="fuzz"><div id="notification">'+message+'<a id="fuzz-close" href="#close">&times;</a></div></div>');
		}
	}
	
	var autosize = function($this) {
		$this.height(0);
		$this.height($this[0].scrollHeight);
	}
	
	var loadInitialMessages = function () {
		var $username = $('#tweets').attr('data-username');
		if (typeof($username) === 'undefined') {
			$username = '';
		}
		//console.log($username);
		$.ajax({
			url:'/ajax/postcalls.php?action=loadinitialmessages&username='+$username+'&timezone='+objLocalZoneBit,
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
					//console.log(data.nummore - data.messagesleft);
					$(document).find('#load-more span').text(data.nummore - data.messagesleft);
				}
			}
		});
	}
	if ($('#tweets').length > 0) {
		loadInitialMessages();
	}
	
	var k;
	var loadLatestMessages = function () {
		var $latestMessage = $(document).find('#tweets li').first();
		
		var $username = $('#tweets').attr('data-username');
		if (typeof($username) === 'undefined') {
			$username = '';
		}
		
		if ($latestMessage[0]) {
			$lastId = $latestMessage[0].id.replace('post_','');
		} else {
			$lastId = 0;
		}

		$.ajax({
			url:'/ajax/postcalls.php?action=getlatestposts&username='+$username+'&lastid='+$lastId+'&timezone='+objLocalZoneBit,
			type:'GET',
			dataType:'JSON',
			success:function(data) {
				$('#tweets').prepend(data.messages);
			},
			error: function() {
				notify('<div>Ceva s-a intamplat. Va rugam sa incercati din nou. <a href="/">Reveniti!</a></div>');
				clearTimeout(k);
			}
		});
	}
	
	var siteclock = function () {
		clearTimeout(k);
		k = setTimeout(function(){
			loadLatestMessages();
			siteclock();
		},2000);
	}
	if ($('#tweets').length > 0) {
		siteclock();
	}
	
	$('.autosize').css('overflow', 'hidden').on('keyup',function(e) {
		var $this = $(this);
		autosize($this);
	});
});