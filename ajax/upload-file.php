<?php 
include ($_SERVER['DOCUMENT_ROOT'].'/config.php');
include (_DOCROOT.'/includes/pre-header.php');
include (_DOCROOT.'/includes/class.functions.php');
include (_DOCROOT.'/templates/post-templates.php');


$action = isset($_GET['action']) ? $_GET['action'] : '';
$db = new DB();
$db->open();
call_user_func($action);
$db->close();


function uploadphotopost() {

	//var_dump($_POST);
	
	$imgname = time();
	
	// add a post.

	$fn = new Functions();
	$savetofile = $fn->userFolder(_USERNAME).'/posts/'.$imgname."_orig.jpg";
	//var_dump($savetofile, $_POST, $_FILES);
	if (!file_exists ( $savetofile ) ) {
		$postvars = array(
			"image"    		=> trim($_FILES["mainimage"]["name"]),
			"image_tmp"    	=> $_FILES["mainimage"]["tmp_name"],
			"image_size"    => (int)$_FILES["mainimage"]["size"]
		);
		
		
		$valid_exts = array("jpg","jpeg","gif","png");
		$filename = explode(".",strtolower(trim($postvars["image"])));
		$ext = strtolower(pathinfo($fn->userFolder(_USERNAME).'/photos/'.$postvars["image"], PATHINFO_EXTENSION));
		if($postvars["image_size"] <= (50*1024*1024)){
			if(in_array($ext,$valid_exts)){
				if($ext == "jpg" || $ext == "jpeg"){
					$image = imagecreatefromjpeg($postvars["image_tmp"]);
				} else if($ext == "gif"){
					$image = imagecreatefromgif($postvars["image_tmp"]);
				} else if($ext == "png"){
					$image = imagecreatefrompng($postvars["image_tmp"]);
				}
				// Grab the width and height of the image.
				list($width,$height) = getimagesize($postvars["image_tmp"]);

				// If the max width input is greater than max height we base the new image off of that, otherwise we
				// use the max height input.
				// We get the other dimension by multiplying the quotient of the new width or height divided by
				// the old width or height.

				$newheight = $height;
				$newwidth = $width;
				// Create temporary image file.
				$tmp = imagecreatetruecolor($newwidth,$newheight);

				// Copy the image to one with the new width and height.
				imagecopyresampled($tmp,$image,0,0,0,0,$newwidth,$newheight,$width,$height);
				$filename = $savetofile;

				// Create image file with 75% quality.
				imagejpeg($tmp,$filename,75);
				imagedestroy($image);
				imagedestroy($tmp);
				
				$imageSizes = $fn->postSizes;
				
		$handle = fopen("log.txt","w+");
		//fwrite($handle,var_export($postvars["image_tmp"],true));
		fwrite($handle,var_export(exif_read_data($filename),true));
		fclose($handle);

				$fn->resizeHeadshot($imgname,$imageSizes,'',$post=true);
					
					if (trim($_POST['writearea']) == 'La ce va gândiți?') {
						$descr = '';
					} else {
						$descr = trim($_POST['writearea']);
					}
					
					$sql = "INSERT INTO posts (
								`details`,`dtm`,`poster`,`postimg`
							) VALUES (
								'".addslashes($descr)."','".date("Y-m-d H:i:s")."','"._USERNAME."',".$imgname."
							)";
							$res = mysql_query($sql);

				
			}
		}
	}
	
	$retval = array(
		'error' => false
		//'file' => $filename,
		//'smallFilePath' => $fn->userLink(_USERNAME).'/photos/'.$photonum.'_small.jpg',
		//'mediumFilePath' => $fn->userLink(_USERNAME).'/photos/'.$photonum.'_medium.jpg',
		//'image' => $photonum,
		//'username' => _USERNAME
	);
	echo json_encode($retval);
	
}

function uploadphotoprofile() {

	$fn = new Functions();
	$photonum = time();
	$savetofile = $fn->userFolder(_USERNAME).'/photos/'.$photonum."_orig.jpg";
	// die ($savetofile);
	// echo file_exists ( $savetofile );
	if (!file_exists ( $savetofile ) ) {


		// Collect the post variables.
		$postvars = array(
			"image"    		=> trim($_FILES["mainimage"]["name"]),
			"image_tmp"    	=> $_FILES["mainimage"]["tmp_name"],
			"image_size"    => (int)$_FILES["mainimage"]["size"]
		);
		
		//$handle = fopen("log.txt","w+");
		//fwrite($handle,var_export($postvars,true));
		//fwrite($handle,var_export(exif_read_data($absPhotoFile),true));
		//fclose($handle);
		
		// Array of valid extensions.
		$valid_exts = array("jpg","jpeg","gif","png");

		// Select the extension from the file.
		$filename = explode(".",strtolower(trim($postvars["image"])));
		$ext = strtolower(pathinfo($fn->userFolder(_USERNAME).'/photos/'.$postvars["image"], PATHINFO_EXTENSION));
		//var_dump($ext,$filename);

		// Check not larger than 50MB.
		if($postvars["image_size"] <= (50*1024*1024)){

			// Check is valid extension.
			if(in_array($ext,$valid_exts)){

				if($ext == "jpg" || $ext == "jpeg"){
					$image = imagecreatefromjpeg($postvars["image_tmp"]);
				} else if($ext == "gif"){
					$image = imagecreatefromgif($postvars["image_tmp"]);
				} else if($ext == "png"){
					$image = imagecreatefrompng($postvars["image_tmp"]);
				}
				// Grab the width and height of the image.
				list($width,$height) = getimagesize($postvars["image_tmp"]);

				// If the max width input is greater than max height we base the new image off of that, otherwise we
				// use the max height input.
				// We get the other dimension by multiplying the quotient of the new width or height divided by
				// the old width or height.

				$newheight = $height;
				$newwidth = $width;
				// Create temporary image file.
				$tmp = imagecreatetruecolor($newwidth,$newheight);

				// Copy the image to one with the new width and height.
				imagecopyresampled($tmp,$image,0,0,0,0,$newwidth,$newheight,$width,$height);
				$filename = $savetofile;

				// Create image file with 75% quality.
				imagejpeg($tmp,$filename,75);
				imagedestroy($image);
				imagedestroy($tmp);
				
				$imageSizes = $fn->imageSizes;

				$fn->resizeHeadshot($photonum,$imageSizes);
				
				$sql = "UPDATE signup SET mainimgid = '".$photonum."' WHERE username = '"._USERNAME."'";
				mysql_query($sql);
				
				$retval = array(
					'error' => false,
					'file' => $filename,
					'smallFilePath' => $fn->userLink(_USERNAME).'/photos/'.$photonum.'_small.jpg',
					'mediumFilePath' => $fn->userLink(_USERNAME).'/photos/'.$photonum.'_medium.jpg',
					'image' => $photonum,
					'username' => _USERNAME
				);
				echo json_encode($retval);

			} else {
				echo '{"error":"true","message":"Invalid file type. You must upload an image file. (jpg, jpeg, gif, png)."}';
			}
		} else {
			echo '{"error":"true","message":"File size too large. Max allowed file size is 5MB."}';
		}
		
	}
}

?>