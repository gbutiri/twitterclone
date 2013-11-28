jQuery(function($){

	// Create variables (in this scope) to hold the API and image size
	var jcrop_api,
		boundx,
		boundy,

		// Grab some information about the preview pane
		$preview = $('#preview-pane'),
		$pcnt = $('#preview-pane .preview-container'),
		$pimg = $('#preview-pane .preview-container img'),

		xsize = 300,//$pcnt.width(),
		ysize = 300//$pcnt.height();

	//console.log('init',[xsize,ysize]);
	$('#target').Jcrop({
	  onChange: updatePreview,
	  onSelect: updatePreview,
	  aspectRatio: xsize / ysize,
	  onSelect: updateCoords
	},function(){
	  // Use the API to get the real image size
	  var bounds = this.getBounds();
	  boundx = bounds[0];
	  boundy = bounds[1];
	  // Store the API in the jcrop_api variable
	  jcrop_api = this;

	  // Move the preview into the jcrop container for css positioning
	  $preview.appendTo(jcrop_api.ui.holder);
	});

	function updatePreview(c)
	{
	  if (parseInt(c.w) > 0)
	  {
		var rx = xsize / c.w;
		var ry = ysize / c.h;

		$pimg.css({
		  width: Math.round(rx * boundx) + 'px',
		  height: Math.round(ry * boundy) + 'px',
		  marginLeft: '-' + Math.round(rx * c.x) + 'px',
		  marginTop: '-' + Math.round(ry * c.y) + 'px'
		});
	  }
	};

	$('.btn-cropimage').on('click',function(e) {
		e.preventDefault();
		$formdata = $('#preview-pane form').serialize();
		var $imgid = $('#img').val();
		$.ajax({
			url: '/ajax/profile-calls.php?action=savecroppedimages&imgid='+$imgid,
			type: 'POST',
			dataType: 'JSON',
			data: 'img='+imgIn+'&'+$formdata,
			success: function(data) {
				var randomh=Math.random();
				$('.avatar img').attr('src',data.smallFilePath+'?x='+randomh);
				$('#profile-avatar img').attr('src',data.mediumFilePath+'?x='+randomh);
				$('#fuzz').remove();
			}
			
		});
	});


});

function updateCoords(c)
{
	$('#x').val(c.x /imgRatio);
	$('#y').val(c.y /imgRatio);
	$('#w').val(c.w /imgRatio);
	$('#h').val(c.h /imgRatio);
};

function checkCoords()
{
if (parseInt($('#w').val())) return true;
alert('Please select a crop region then press submit.');
return false;
};
