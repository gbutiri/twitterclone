<?php 
include ('config.php');
include (_DOCROOT.'/includes/class.db.php');
include (_DOCROOT.'/includes/class.functions.php');

$action = isset($_GET['action']) ? $_GET['action'] : '';

$db = new DB();

$db->open();
call_user_func($action);
$db->close();

function makeuserfolders() {
	$f = new Functions();

	$sql = "SELECT username FROM signup";
	$res = mysql_query($sql);
	while ($row = mysql_fetch_assoc($res)) {
		$f->makeUserFolder($row['username']);
	}
	echo "User Folders Created!";
}

?>