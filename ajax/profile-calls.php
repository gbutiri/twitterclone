<?php
include ($_SERVER['DOCUMENT_ROOT'].'/config.php');
include (_DOCROOT.'/includes/pre-header.php');
include (_DOCROOT.'/includes/class.functions.php');

$action = isset($_GET['action']) ? $_GET['action'] : '';
$db = new DB();
$db->open();
call_user_func($action);
$db->close();

function showimageuploader() {
	$filetype = 'image';
	?>
	<h3>Upload a photo</h3>
	<div>
		<form id="upload" method="post" enctype="multipart/form-data" action="/ajax/upload-file.php?do=upload&type=<?php echo $filetype; ?>">
			
			<div id="drop">
				<span class="drophere">Drop Here</span>
				<a class="cta cta-1">
					Browse
					<input type="file" id="mainimage" name="mainimage" />
				</a>
			</div>

			<ul>
				<!-- The file uploads will be shown here -->
			</ul>
		</form>
	</div>
	<script src="/js/mini-upload/script.js"></script>


	<?php
}

function rotateimage() {
	$deg = isset($_GET['deg']) ? intval($_GET['deg']) : 0;
	$imgid = isset($_GET['img']) ? intval($_GET['img']) : 0;
	$f = new Functions();
	$imgLink = $f->userLink(_USERNAME).'/photos/orig_'.$imgid.'.jpg' ;
	$imgLoc = $f->userFolder(_USERNAME).'/photos/orig_'.$imgid.'.jpg' ;
	
	$source = imagecreatefromjpeg($imgLoc);
	$rotate = imagerotate($source,$deg,0);
	//file_put_contents($imgLoc,$rotate);
	
	imagejpeg($rotate,$imgLoc,75);
	$imageSizes = $f->imageSizes;
	$f->resizeHeadshot($imgid,$imageSizes);
	imagedestroy($rotate);
	imagedestroy($source);
	//$retval = ;
	echo json_encode(array(
		'smallFilePath' => $f->userLink(_USERNAME).'/photos/'.$imgid.'_small.jpg',
		'mediumFilePath' => $f->userLink(_USERNAME).'/photos/'.$imgid.'_medium.jpg'
	));
}

function showimagecropper() {
	exit(0);
	$f = new Functions();
	$img = isset($_GET['img']) ? $_GET['img'] : '';
	$imgSize = getimagesize($f->userFolder(_USERNAME).'/photos/orig_'.$img.'.jpg');
	$g_img = $f->userLink(_USERNAME).'/photos/orig_'.$img.'.jpg' ;
	?>
	<link rel="stylesheet" href="/css/jcrop/jquery.Jcrop.css" type="text/css" />
	<link rel="stylesheet" href="/css/imagecrop.css" type="text/css" />
	<script src="/js/jcrop/jquery.Jcrop.js"></script>
	<script>var imgIn = '<?php echo $img;?>'</script>
	<script src="/js/imagecrop.js"></script>

	<div class="videobox jcrop-holder">
		<img src="<?php echo $g_img ; ?>" alt="user photo" id="target" width="<?php echo $imgSize[0]; ?>" height="<?php echo $imgSize[1]; ?>">
		<?php 
		//var_dump($imgSize);
		?>
		<?php if (_USERNAME != '') { ?>
		<div id="preview-pane">
			<form action="crop.php" method="post" onsubmit="return checkCoords();">
				<input type="hidden" id="x" name="x" />
				<input type="hidden" id="y" name="y" />
				<input type="hidden" id="w" name="w" />
				<input type="hidden" id="h" name="h" />
				<input type="hidden" id="frmUsername" name="frmUsername" value="<?php echo _USERNAME; ?>" />
				<input type="hidden" id="img" name="img" value="<?php echo $g_img; ?>" />
				<input type="submit" value="Crop Image" class="cta cta-1 btn-large btn-inverse btn-cropimage" />
			</form>
		</div>
		<?php } ?>
	</div>
	
	<script>
		$(document).ready(function() {
			var height = parseInt($(document).find('#notification').outerHeight());
			var width = parseInt($(document).find('#notification').outerWidth());
			var winH = $(window).outerHeight();
			var winW = $(window).outerWidth();
			
			var resizeBox = function() {
				
				if (height > winH) {
					console.log(winH, winW, width, height);
					
					$(document).find("#notification .videobox").css({'height':'100%','width':'auto'});
					$(document).find("#notification img#target").css({'height':winH-60,'width':'auto'});
					$(document).find("#notification").css({"height":winH - 60,"top":0});
					var origW = parseInt($(document).find("#notification img").attr('width'));
					var origH = parseInt($(document).find("#notification img").attr('height'));
					var resizedW = $(document).find("#notification img").width()-60;
					var resizedH = $(document).find("#notification img").height()-60;
					imgRatio = resizedW / origW;
					console.log(imgRatio, resizedW, origW, origH, resizedH);
					$(document).find("#notification .jcrop-tracker").css({'height':resizedH,'width':resizedW});
					$(document).find("#notification .jcrop-holder").css({'height':resizedH,'width':resizedW});
					$(document).find("#notification .jcrop-holder img").css({'height':resizedH,'width':resizedW});
				} else {
					$("#notification").css({"top" : ( ( (winH - height) / 2)) + "px"});
				}
			}
			
			$('#notification').animate({
				"top" : "0px",
				//"left" : ( ( (winW - width) / 2)) + "px"
			},'fast',function(){
				resizeBox();
			});
		});
	</script>
	<?php
}

function savecroppedimages() {
	$f = new Functions();
	$img = isset($_POST['img']) ? $_POST['img'] : '';
	$imgPath = $img;
	$imgId = $img;
	$imgId = explode("_",$imgId);
	$imgId = explode(".",$imgId[1]);
	$imgId = $imgId[0];
	$imageSizes = $f->imageSizes;
	$f->resizeHeadshot($imgId,$imageSizes);
	
	
	$retVal = array(
		"error" => false,
		"imageId" => $imgId,
		"msg" => "Your picture has been cropped."
	);
	echo json_encode($retVal);
	//$fn->resizeHeadshot($photonum,$imageSizes);
	//var_dump($imgId);
	//var_dump($_POST);
	exit(0);
}

function getzipcode() {
	$f = new Functions();
	$f->getZipInfo($_GET['zipcode']);
}

function savefield() {
	$f = new Functions();
	$latlong = $f->getLatLong(trim($_POST['location']));
	$sql = "UPDATE signup SET `location` = '".$_POST['location']."',
			`lat` = ".$latlong['lat'].",
			`long` = ".$latlong['long']."
			WHERE username = '"._USERNAME."';";
	
	mysql_query($sql);
	echo json_encode(array(
		"data" => $_POST,
		"sql" => $sql
	));
}

function like() {
	$f = new Functions();
	$like = $_GET['like'];
	$post_id = $_GET['postid'];
	$likeval = 0;
	if ($like == 'like') {
		$sql = "REPLACE INTO likes (post_id,username) 
			VALUES (".$post_id.",'"._USERNAME."')";
		$likeval = 1;
	} else {
		$sql = "DELETE FROM likes 
				WHERE post_id = ".$post_id." 
				AND username = '"._USERNAME."';";
	}
	$res = mysql_query($sql);
	$likes = $f->getLikes($post_id);
	echo json_encode(array(
		"likes" => $likes,
		"like" => $likeval,
		"sql" => $sql
	));
}

?>