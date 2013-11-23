<?php
include ($_SERVER['DOCUMENT_ROOT'].'/config.php');
include (_DOCROOT.'/includes/pre-header.php');

$action = isset($_GET['action']) ? $_GET['action'] : '';
$db = new DB();
$db->open();
call_user_func($action);
$db->close();

function loadinitialmessages() {
	include(_DOCROOT.'/templates/post-templates.php');
	
	$sql_a = "SELECT COUNT(*) AS totalMessages FROM posts ORDER BY id DESC";
	$res_a = mysql_query($sql_a);
	$row_a = mysql_fetch_assoc($res_a);
	$numMore = $row_a['totalMessages'];
	
	$sql = "SELECT * FROM posts ORDER BY id DESC LIMIT 10";
	$res = mysql_query($sql);
	$numMessages = mysql_num_rows($res);
	ob_start();
	
	while ($row = mysql_fetch_assoc($res)) {
		template_post($row);
	}
	$messages = ob_get_contents();
	ob_end_clean();
	echo json_encode(array(
		'messages' => $messages,
		'messagesleft' => $numMessages,
		'nummore' => intval($numMore)
	));
}

function makeapost() {
	
	$sql = "INSERT INTO posts (
		`details`,`dtm`,`poster`
	) VALUES (
		'".addslashes(trim($_POST['writearea']))."','".date("Y-m-d H:i:s")."','"._USERNAME."'
	)";
	mysql_query($sql);
	
	echo (json_encode(
		array(
			"notification" => "Post added!"
		)
	));
	
}

function getlatestposts() {
	include(_DOCROOT.'/templates/post-templates.php');
	$lastId = $_GET['lastid'];
	$sql = "SELECT * FROM posts WHERE id > ".$lastId." ORDER BY id DESC";
	$res = mysql_query($sql);
	ob_start();
	
	while ($row = mysql_fetch_assoc($res)) {
		template_post($row);
	}
	$messages = ob_get_contents();
	ob_end_clean();
	echo json_encode(array(
		'messages' => $messages
	));
}

function loadmore() {
	include(_DOCROOT.'/templates/post-templates.php');
	$firstId = $_GET['firstid'];
	
	$sql_a = "SELECT COUNT(*) AS totalMessages FROM posts WHERE id < ".$firstId." ORDER BY id DESC";
	$res_a = mysql_query($sql_a);
	$row_a = mysql_fetch_assoc($res_a);
	$numMore = $row_a['totalMessages'];
	
	$sql = "SELECT * FROM posts WHERE id < ".$firstId." ORDER BY id DESC LIMIT 10";
	$res = mysql_query($sql);
	$numMessages = mysql_num_rows($res);
	ob_start();
	
	while ($row = mysql_fetch_assoc($res)) {
		template_post($row);
	}
	$messages = ob_get_contents();
	ob_end_clean();
	echo json_encode(array(
		'messages' => $messages,
		'messagesleft' => $numMessages,
		'nummore' => intval($numMore)
	));
}

?>