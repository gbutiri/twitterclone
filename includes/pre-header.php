<?php 
include (_DOCROOT.'/includes/class.db.php');
$strposlogin = strpos($_SERVER['SCRIPT_FILENAME'],'/login.php');
$strposabout = strpos($_SERVER['SCRIPT_FILENAME'],'/about.php');

if (_USERNAME == '' && $strposlogin === false && $strposabout === false) {
	header('location: /login.html');
}

define ('_TITLE',"ceau.ro - conectați-vă cu prieteni Români!");
$db = new DB();
$db->open();

// verify email verification
$sql_ev = "SELECT COUNT(*) AS usercount, emailverified FROM signup WHERE username = '"._USERNAME."'";
$res_ev = mysql_query($sql_ev);
//var_dump(mysql_num_rows($res_ev));
$row_ev = mysql_fetch_assoc($res_ev);

if ($row_ev['usercount'] > 0) {
	if ($row_ev['emailverified'] == 0) {
		header('location: /notifications.html?action=emailunverified');
	}
}

// check for cookie token and salt.
if (isset($_COOKIE['fbclone_username']) && $_COOKIE['fbclone_username'] != "") {
	$sql = "SELECT salt, token FROM signup WHERE username = '".$_COOKIE['fbclone_username']."'";
	$res = mysql_query($sql);
	$row_login = mysql_fetch_assoc($res);
	if (isset($_COOKIE['fbclone_token']) && $_COOKIE['fbclone_token'] == $row_login['token'] && isset($_COOKIE['fbclone_salt']) && $_COOKIE['fbclone_salt'] == $row_login['salt']) {
		$_SESSION['fbclone_username'] = $_COOKIE['fbclone_username'];
		$sql="UPDATE signup SET lastlogin = ".date("Y-m-d H:i:s")." WHERE username = '".$_SESSION['fbclone_username']."'";
		mysql_query($sql);
	} else {
		$_SESSION['fbclone_username'] = "";
		$_SESSION['fbclone_token'] = "";
		$_SESSION['fbclone_salt'] = "";
		$_SESSION = array();

		setcookie("fbclone_username","",time()-3600,'/');
		setcookie("fbclone_token","",time()-3600,'/');
		setcookie("fbclone_salt","",time()-3600,'/');
		
		header('location: /');
	}
}


?>
