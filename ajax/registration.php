<?php
include ($_SERVER['DOCUMENT_ROOT'].'/config.php');
include (_DOCROOT.'/includes/class.db.php');

$action = isset($_GET['action']) ? $_GET['action'] : '';
$db = new DB();
$db->open();
call_user_func($action);
$db->close();

function trylogin() {

	
	echo json_encode(array(
		'messages' => 'asdf',
		'messagesleft' => 'asdf',
		'nummore' => 'asdf'
	));
}


?>