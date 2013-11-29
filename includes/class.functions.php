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
		// MAKE post photos DIRECTORY
		if (!is_dir(_DOCROOT."/users/".$letterfolder."/".$username."/posts")) {
			mkdir(_DOCROOT."/users/".$letterfolder."/".$username."/posts", 0777);
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
	var $postSizes = array(
		array('small',50,50),
		array('medium',500,0)
	);
	function resizeHeadshot($photonum, $sizes = array(),$username = '',$post=false) {
		//require_once($_SERVER['DOCUMENT_ROOT'].'/config.php');
		$fn = new Functions();
		
		$userfolder = $fn->userFolder(_USERNAME);
		//var_dump($config);
		$imageSizes = $sizes;
		if (count($sizes) == 0) {
			$imageSizes = $this->imageSizes;
		}
		
		
		$photoFolder = $userfolder.'/photos/';
		if ($post) {
			$photoFolder = $userfolder.'/posts/';
		}
		//var_dump($photoFolder);
		//var_dump('is_dir($photoFolder)',is_dir($photoFolder),$photoFolder);
		if (is_dir($photoFolder)) {
			$absPhotoFile = $photoFolder.'/'.$photonum.'_orig.jpg';
			if (is_file($absPhotoFile)) {
				list($src_w, $src_h) = getimagesize($absPhotoFile);
				foreach($imageSizes as $imageSize) {
					$src_ratio = $src_w / $src_h;
					
					//var_dump($photonum,$imageSize,'<br>');
					$dst_w = $imageSize[1];
					$dst_h = $imageSize[2];
					if ($imageSize[2] == 0) {
						$shrinkRatio = $dst_w / $src_w ;
						$imageSize[2] = $src_h * $shrinkRatio;
						$dst_h = $imageSize[2];
					}
					if ($imageSize[1] == 0) {
						$shrinkRatio = $dst_h / $src_h ;
						$imageSize[1] = $src_w * $shrinkRatio;
						$dst_w = $imageSize[1];
					}
					$new_name = $imageSize[0];
					$newPhotoFile = $photoFolder.'/'.$photonum.'_'.$new_name.'.jpg';
					
					$dst_ratio = $dst_w / $dst_h;
					//var_dump($src_ratio,$dst_ratio,$dst_w,$dst_h);
					
					$dst_image = imagecreatetruecolor($dst_w, $dst_h);
					$src_image = imagecreatefromjpeg($absPhotoFile);
					
					// this is to place the image in the center.
					$dst_x = 0;
					$dst_y = 0;
					if (!$post) {
						if ($src_ratio > $dst_ratio) {
							$shrinkRatio = $dst_h / $src_h ;
							$dst_w = $imageSize[1]; //$src_w*$shrinkRatio;
							$diffOffset = (($dst_w-$imageSize[1])/2);
							$dst_x = -$diffOffset;
						} elseif ($src_ratio < $dst_ratio) {
							$shrinkRatio = $dst_w / $src_w ;
							$dst_h = $imageSize[2]; //$src_h*$shrinkRatio;
							$diffOffset = (($dst_h-$imageSize[2])/2);
							$dst_y = -$diffOffset;
						}
					}
					//var_dump($src_ratio,$dst_ratio,$dst_w,$dst_h,$shrinkRatio);
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
					//var_dump($_POST,$dst_x,$dst_y,$dst_w,$dst_h);
					imagejpeg($dst_image, $newPhotoFile, 75);
				}
			}
		}
	}
	function getLatLong($inZip) {
		$inZip = urlencode($inZip);
		$url = "http://maps.googleapis.com/maps/api/geocode/json?address=".$inZip."&sensor=true";
		$zip_obj = file_get_contents($url);
		$zip_obj = json_decode($zip_obj);
		
		//var_dump($zip_obj);
		
		$Latitude = $zip_obj->results[0]->geometry->location->lat;
		$Longitude = $zip_obj->results[0]->geometry->location->lng;
		return $latlong = array(
			"lat" => $Latitude,
			"long" => $Longitude
		);
	}
	function getZipInfo($inZip, $echo = true) {
		$inZip = urlencode($inZip);
		$url = "http://maps.googleapis.com/maps/api/geocode/json?address=".$inZip."&sensor=true";
		$zip_obj = file_get_contents($url);
		$zip_obj = json_decode($zip_obj);
		
		//var_dump($zip_obj);
		$output = '';
		
		foreach ($zip_obj->results as $zips) {
			$zipcode="";
			$city="";
			$city2="";
			$state="";
			$country="";
			
			foreach ($zips->address_components as $add_comp) {
				// var_dump ($add_comp);
				switch ($add_comp->types[0]) {
					case "postal_code":
						$zipcode = $add_comp->long_name;
						// var_dump("postcode: ".$zipcode);
						break;
					case "locality":
						$city = $add_comp->long_name;
						// var_dump("city: ".$city);
						break;
					case "administrative_area_level_2":
						$city2 = $add_comp->long_name;
						// var_dump("city2: ".$city2);
						break;
					case "administrative_area_level_1":
						$state = $add_comp->long_name;
						// var_dump("state: ".$state);
						$state_abbr = $add_comp->short_name;
						// var_dump("state abbr: ".$state_abbr);
						break;
					case "country":
						$country = $add_comp->long_name;
						// var_dump("country: ".$country);
						$country_abbr = $add_comp->short_name;
						// var_dump("country abbr: ".$country_abbr);
						break;
				}
			}
			if ($echo) {
				$output .= '<a href="#">'.$city.', '.$city2.', '.$state.' '.$zipcode.', '.$country.'</a>';
				$output = str_replace(", , ",", ",$output);
				$output = str_replace(" , ",", ",$output);
			} elseif ($output === '') {
				$output = array(
					"city" => $city,
					"state" => $state,
					"zip" => $zipcode,
					"country" => $country
				);
			}
		}
		if ($echo) {
			echo json_encode(array(
				"locationvalue" => $output
			));
		} else {
			return $output;
		}
	}
	function getLike($post_id) {
		$sql = "SELECT COUNT(*) AS ilike FROM likes 
				WHERE post_id = ".$post_id." 
				AND username = '"._USERNAME."'";
		$res = mysql_query($sql);
		$row = mysql_fetch_assoc($res);
		return $row['ilike'];
	}
	function getLikes($post_id) {
		$sql = "SELECT COUNT(*) AS likes FROM likes 
				WHERE post_id = ".$post_id;
		$res = mysql_query($sql);
		$row = mysql_fetch_assoc($res);
		return $row['likes'];
	}
}