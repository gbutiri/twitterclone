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