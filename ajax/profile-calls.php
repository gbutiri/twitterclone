<?php
include ($_SERVER['DOCUMENT_ROOT'].'/config.php');
include (_DOCROOT.'/includes/pre-header.php');
include (_DOCROOT.'/includes/class.functions.php');

$action = isset($_GET['action']) ? $_GET['action'] : '';
$db = new DB();
$db->open();
call_user_func($action);
$db->close();

function showchangepass() {
	?>
	<form id="changepass">
		<div>
			<label>parola curenta</label>
			<input type="password" name="pass-current" id="pass-current" />
		</div>
		<div>
			<label>parola dorita</label>
			<input type="password" name="pass-new" id="pass-new" />
		</div>
		<div>
			<button id="change-password-submit">schimbati</button>
		</div>
		<div id="change-messages"></div>
	</form>
	<?php
}

function changepasssubmit() {
	$f = new Functions();
	$currentpass = addslashes(trim($_GET['currentpass']));
	$newpass = addslashes(trim($_GET['newpass']));
	$sql = "SELECT username FROM signup
			WHERE username = '"._USERNAME."' 
				AND password = '".md5($currentpass)."'";
	$res = mysql_query($sql);
	
	$passCheck = $f->checkPassword($newpass);
	if ($passCheck != '') {
		$retArray = array(
			"message" => $passCheck
		);
	} else {
		if (mysql_num_rows($res) > 0) {
			$sql_u = "UPDATE signup SET password = '".md5($newpass)."'
					WHERE username = '"._USERNAME."' 
						AND password = '".md5($currentpass)."'";
			mysql_query($sql_u);
			$retArray = array(
				"message" => "Parola schimbata cu succes."
			);
		} else {
			$retArray = array(
				"message" => "Parola curenta e incorecta."
			);
		}
	}
	echo json_encode($retArray);
}

function showimageuploader() {
	$filetype = 'image';
	$post = isset($_GET['posttype']) ? $_GET['posttype'] : "profile";
	?>
	<h3>Adaugati o poza.</h3>
	<div>
		<form id="upload" method="post" enctype="multipart/form-data" action="/ajax/upload-file.php?action=uploadphoto<?php echo $post; ?>&type=<?php echo $filetype; ?>">
			
			<?php if ($post == 'post') {?>
				<div class="write-container">
					<textarea class="autosize" autocomplete="off" id="write-area2" name="writearea" place-holder="La ce va gândiți?">La ce va gândiți?</textarea>
				</div>
			<?php } ?>
			
			<div id="drop">
				<span class="drophere">Adaugati poza aici.</span>
				<a class="cta cta-1">
					Cautati
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
	$imgLink = $f->userLink(_USERNAME).'/photos/'.$imgid.'_orig.jpg' ;
	$imgLoc = $f->userFolder(_USERNAME).'/photos/'.$imgid.'_orig.jpg' ;
	
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
	$f = new Functions();
	$img = isset($_GET['img']) ? $_GET['img'] : '';
	$imgSize = getimagesize($f->userFolder(_USERNAME).'/photos/'.$img.'_orig.jpg');
	$g_img = $f->userLink(_USERNAME).'/photos/'.$img.'_orig.jpg' ;
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
				<input type="hidden" id="img" name="img" value="<?php echo $img; ?>" />
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
				
				//if (height > winH) {
					console.log(winH, winW, width, height);
					
					$(document).find("#notification .videobox").css({'height':'100%','width':'auto'});
					$(document).find("#notification img#target").css({'height':winH-60,'width':'auto'});
					$(document).find("#notification").css({"height":winH - 60,"top":0});
					var origW = parseInt($(document).find("#notification img").attr('width'));
					console.log("origW",origW);
					var origH = parseInt($(document).find("#notification img").attr('height'));
					var resizedW = $(document).find("#notification img").width()-60;
					console.log("resizedW",resizedW);
					if (resizedW > 270) {
						resizedW = 270;
					}
					imgRatio = resizedW / origW;
					var resizedH = origH * imgRatio;
					console.log(imgRatio, resizedW, origW, origH, resizedH);
					$(document).find("#notification .jcrop-tracker").css({'height':resizedH,'width':resizedW});
					$(document).find("#notification .jcrop-holder").css({'height':resizedH,'width':resizedW});
					$(document).find("#notification .jcrop-holder img").css({'height':resizedH,'width':resizedW});
				//} else {
				//	$("#notification").css({"top" : ( ( (winH - height) / 2)) + "px"});
				//}
			}
			
			$('#notification').animate({
				"top" : "0px"
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
	$imgid = isset($_GET['imgid']) ? $_GET['imgid'] : '';
	$imageSizes = $f->imageSizes;
	$f->resizeHeadshot($imgid,$imageSizes);
	
	
	echo json_encode(array(
		'smallFilePath' => $f->userLink(_USERNAME).'/photos/'.$imgid.'_small.jpg',
		'mediumFilePath' => $f->userLink(_USERNAME).'/photos/'.$imgid.'_medium.jpg'
	));
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
		"like" => $likeval
	));
}

function follow() {
	$tofollow = isset($_GET['tofollow']) ? trim($_GET['tofollow']) : '';
	$sql = "REPLACE INTO follows (`username`,`isfollowing`) VALUES ('"._USERNAME."','".$tofollow."')";
	$res = mysql_query($sql);
	
	echo json_encode(array(
		"error" => false,
		"replace" => '<span class="active">mă urmați</span><span class="hover">nu urmați</span>'
	));
}
function unfollow() {
	$tofollow = isset($_GET['tofollow']) ? trim($_GET['tofollow']) : '';
	$sql = "DELETE FROM follows 
			WHERE `username` = '"._USERNAME."'
			AND `isfollowing` = '".$tofollow."';";
	$res = mysql_query($sql);
	
	echo json_encode(array(
		"error" => false,
		"replace" => "urmați-mă"
	));
}

?>