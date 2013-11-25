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
			`long` = ".$latlong['long'].";";
	
	mysql_query($sql);
	echo json_encode(array(
		"data" => $_POST,
		"sql" => $sql
	));
}

?>