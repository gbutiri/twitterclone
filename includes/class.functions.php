<?php 
Class Functions {
	/***************************************************/
	/****************** GLOBAL FUNCTIONS ***************/
	/***************************************************/
	function makeUserFolder($username) {

		if(!is_dir(_DOCROOT."/users")) {
			mkdir(_DOCROOT."/users", 0777);
		}

		// MAKE FIRST LETTER FOLDERS
		$letterfolder = strtolower(substr($username,0,1));
		if (!is_dir(_DOCROOT."/users/".$letterfolder)) {
			mkdir(_DOCROOT."/users/".$letterfolder, 0777);
		}
		
		// MAKE THE users FOLDER IF NOT EXIST
		if(!is_dir(_DOCROOT."/users/".$letterfolder."/".$username) ){
			mkdir(_DOCROOT."/users/".$letterfolder."/".$username, 0777);
		}
		// MAKE photo DIRECTORY
		if (!is_dir(_DOCROOT."/users/".$letterfolder."/".$username."/photos")) {
			mkdir(_DOCROOT."/users/".$letterfolder."/".$username."/photos", 0777);
		}
		// MAKE video DIRECTORY
		if (!is_dir(_DOCROOT."/users/".$letterfolder."/".$username."/videos")) {
			mkdir(_DOCROOT."/users/".$letterfolder."/".$username."/videos", 0777);
		}
		// MAKE video thumbs DIRECTORY
		if (!is_dir(_DOCROOT."/users/".$letterfolder."/".$username."/vthumbs")) {
			mkdir(_DOCROOT."/users/".$letterfolder."/".$username."/vthumbs", 0777);
		}
	}

	function userFolder($username) {
		$letterfolder = strtolower(substr($username,0,1));
		$folder = _DOCROOT."/users/".$letterfolder."/".$username;
		return $folder;
	}
	function userLink($username) {
		$letterfolder = strtolower(substr($username,0,1));
		$folder = "/users/".$letterfolder."/".$username;
		return $folder;
	}
	var $imageSizes = array(
		array('small',50,50),
		array('medium',100,100)
	);
	function resizeHeadshot($photonum, $sizes = array(),$username = '') {
		//require_once($_SERVER['DOCUMENT_ROOT'].'/config.php');
		$fn = new Functions();
		
		$userfolder = $fn->userFolder(_USERNAME);
		//var_dump($config);
		$imageSizes = $sizes;
		if (count($sizes) == 0) {
			$imageSizes = $this->imageSizes;
		}
		
		
		
		$photoFolder = $userfolder.'/photos/';
		//var_dump($photoFolder);
		//var_dump('is_dir($photoFolder)',is_dir($photoFolder),$photoFolder);
		if (is_dir($photoFolder)) {
			$absPhotoFile = $photoFolder.'/orig_'.$photonum.'.jpg';
			if (is_file($absPhotoFile)) {
				list($src_w, $src_h) = getimagesize($absPhotoFile);
				foreach($imageSizes as $imageSize) {
					//var_dump($photonum,$imageSize,'<br>');
					$dst_w = $imageSize[1];
					$dst_h = $imageSize[2];
					$new_name = $imageSize[0];
					$newPhotoFile = $photoFolder.'/'.$photonum.'_'.$new_name.'.jpg';
					
					$src_ratio = $src_w / $src_h;
					$dst_ratio = $dst_w / $dst_h;
					
					$dst_image = imagecreatetruecolor($dst_w, $dst_h);
					$src_image = imagecreatefromjpeg($absPhotoFile);
					
					// this is to place the image in the center.
					$dst_x = 0;
					$dst_y = 0;
					if ($src_ratio > $dst_ratio) {
						$shrinkRatio = $dst_h / $src_h ;
						$dst_w = $src_w*$shrinkRatio;
						$diffOffset = (($dst_w-$imageSize[1])/2);
						$dst_x = -$diffOffset;
					} elseif ($src_ratio < $dst_ratio) {
						$shrinkRatio = $dst_w / $src_w ;
						$dst_h = $src_h*$shrinkRatio;
						$diffOffset = (($dst_h-$imageSize[2])/2);
						$dst_y = -$diffOffset;
					}

					// dst_ variables can be re-set to the post variables passed in.
					
					$src_x = 0;
					$src_y = 0;
					/* This is from the plugin.
					imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],$targ_w,$targ_h,$_POST['w'],$_POST['h']);     */
					if (isset($_POST['x'])) {
						$src_x=$_POST['x'];
						$src_y=$_POST['y'];
						$src_w=$_POST['w'];
						$src_h=$_POST['h'];
					}
					imagecopyresampled($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
					imagejpeg($dst_image, $newPhotoFile, 75);
				}
			}
		}
	}
}