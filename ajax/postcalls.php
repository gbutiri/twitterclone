<?php
include ($_SERVER['DOCUMENT_ROOT'].'/config.php');
include (_DOCROOT.'/includes/pre-header.php');
include (_DOCROOT.'/includes/class.functions.php');

$action = isset($_GET['action']) ? $_GET['action'] : '';
$db = new DB();
$db->open();
call_user_func($action);
$db->close();

function loadinitialmessages() {
	include(_DOCROOT.'/templates/post-templates.php');
	
	$username = $_GET['username'];
	$sql_where = "";
	if ($username != '') {
		$sql_where = " WHERE poster = '".$username."' ";
	}
	
	$sql_a = "SELECT COUNT(*) AS totalMessages FROM posts ".$sql_where." ORDER BY id DESC";
	$res_a = mysql_query($sql_a);
	$row_a = mysql_fetch_assoc($res_a);
	$numMore = $row_a['totalMessages'];
	
	$sql = "SELECT s.location, s.mainimgid, p.* FROM posts p INNER JOIN signup s ON s.username = p.poster ".$sql_where."  ORDER BY id DESC LIMIT 10";
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

	$username = $_GET['username'];
	$sql_where = "";
	if ($username != '') {
		$sql_where = " AND poster = '".$username."' ";
	}

	$lastId = $_GET['lastid'];
	$sql = "SELECT s.location, s.mainimgid, p.* FROM posts p INNER JOIN signup s ON s.username = p.poster WHERE id > ".$lastId." ".$sql_where." ORDER BY id DESC";
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
		'count' => $numMessages
	));
}

function loadmore() {
	include(_DOCROOT.'/templates/post-templates.php');
	$firstId = $_GET['firstid'];
	
	$username = $_GET['username'];
	$sql_where = "";
	if ($username != '') {
		$sql_where = " AND poster = '".$username."' ";
	}
	
	$sql_a = "SELECT COUNT(*) AS totalMessages FROM posts WHERE id < ".$firstId." ".$sql_where." ORDER BY id DESC";
	$res_a = mysql_query($sql_a);
	$row_a = mysql_fetch_assoc($res_a);
	$numMore = $row_a['totalMessages'];
	
	$sql = "SELECT s.location, s.mainimgid, p.* FROM posts p INNER JOIN signup s ON s.username = p.poster WHERE id < ".$firstId." ".$sql_where." ORDER BY id DESC LIMIT 10";
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