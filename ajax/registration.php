<?php
include ($_SERVER['DOCUMENT_ROOT'].'/config.php');
include (_DOCROOT.'/includes/class.db.php');

$action = isset($_GET['action']) ? $_GET['action'] : '';
$db = new DB();
$db->open();
call_user_func($action);
$db->close();

function trylogin() {

	$username = $_POST['signin-username'];
	$password = $_POST['signin-password'];

	$sql_un="SELECT COUNT(username) AS usercount, username FROM signup WHERE (username = '".$username."' OR email = '".$username."') AND password = '".md5($password)."' ";
	$res_un=mysql_query($sql_un);
	$row_un = mysql_fetch_array($res_un);
	
	$posterFound = false;
	if ($row_un['usercount'] > 0) {
		$posterFound = true;
	}
		
	if (!$posterFound) {
		$retArray = array(
			'error' => true,
			'message' => 'Username, email or password incorrect.'
		);
	} else {
		$token = md5($row_un['username']);
		$salt = md5(time());
		$_SESSION['fbclone_username'] = $row_un['username'];
		$_SESSION['fbclone_token'] = $token;
		$_SESSION['fbclone_salt'] = $salt;
		if (isset($_POST['remember']) && $_POST['remember']) {
			setcookie("fbclone_username",$row_un['username'],time()+(3600*24*365*10));
			setcookie("fbclone_token",$token,time()+(3600*24*30));
			setcookie("fbclone_salt",$salt,time()+(3600*24*30));
		} else {
			setcookie("fbclone_username","",time()-3600,'/');
			setcookie("fbclone_token","",time()-3600,'/');
			setcookie("fbclone_salt","",time()-3600,'/');
		}
		$sql="UPDATE signup SET lastlogin = ".date("Y-m-d H:i:s").", token='".$token."', salt='".$salt."' WHERE username = '"._USERNAME."'";
		mysql_query($sql);
		$retArray = array(
			'error' => false,
			'message' => 'Username, email or password incorrect.'
		);
	}
	
	echo json_encode($retArray);
}

function trysignup() {
	$token = md5($_POST['signup-username']);
	$salt = md5(time());
	
	$sql_un="SELECT username FROM signup WHERE username = '".$_POST['signup-username']."' ";
	$res_un=mysql_query($sql_un);
	
	$sql_em="SELECT COUNT(username) AS emailcount FROM signup WHERE email = '".$_POST['signup-email']."' ";
	$res_em=mysql_query($sql_em);
	$row_em = mysql_fetch_array($res_em);
	
	$err = '';
	$strict = false;
	$regex = $strict? 
	'/^([.0-9a-z_-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i' : 
	'/^([*+!.&#$¦\'\\%\/0-9a-z^_`{}=?~:-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i' ; 

	if (mysql_num_rows($res_un) > 0) {
		$err.="<div>Username not available. Use a different username.</div>";
	}
	if ($row_em['emailcount'] > 0) {
		$err.="<div>Email already registered. Use a different email address.</div>";
	/*
	} 
	if (!preg_match('/^[a-zA-Z0-9]+$/i', $_POST['signup-fname']) || strlen(trim($_POST['signup-fname'])) < 2) {
		$err="<div>First name too short. Only use letters. Must be at least 2 letters long.</div>";
	} 
	if (!preg_match('/^[a-zA-Z0-9]+$/i', $_POST['signup-lname']) || strlen(trim($_POST['signup-lname'])) < 2) {
		$err="<div>Last name too short. Only use letters. Must be at least 2 letters long.</div>";
	*/
	}
	if (strlen(trim($_POST['signup-username'])) < 6) {
		$err.="<div>Username too short.</div>";
	} 
	if (!preg_match('/^[a-zA-Z0-9]+$/i', $_POST['signup-username'])) {	 
		$err.="<div>Only use letters and numbers in the username field.</div>";
	} 
	if (!preg_match($regex, trim($_POST['signup-email']))) {
		$err.="<div>Incorrect email address format. Use 'user@domain.com'</div>";
	/*
	} 
	if ($_POST['repeat_email'] != $_POST['signup-email']) {
		$err="<div>Emails must match.</div>";
	} 
	if ($zip_status != "OK") {
		$err="<div>A valid postal code, <br>(or city, state / country combination)<br>is required.</div>";
	*/
	} 
	if( strlen($_POST['signup-password']) < 6 ) {
		$err .= "<div>Password too short. Must be at least 6 characters.</div>";
	} 
	if( strlen($_POST['signup-password']) > 20 ) {
		$err .= "<div>Password too long. Must be no longer than 20 characters.</div>";
	} 
	if( !preg_match("#[0-9]+#", $_POST['signup-password']) ) {
		$err .= "<div>Password must include at least one number!</div>";
	} 
	if( !preg_match("#[a-zA-Z]+#", $_POST['signup-password']) ) {
		$err .= "<div>Password must include at least one letter!</div>";
	/*
	} 
	if (!checkValidDate($_POST['month'],$_POST['day'],$_POST['year'])) {
		$err="<div>Incorrect date. Must be at least 13 years old to join.</div>";
	*/
	}
	if ($err != '') {
		$retArray = array(
			"error" => true,
			"message" => $err
		);
	} else {
		$sql="INSERT INTO signup (
			`username`,
			`email`,
			`password`,
			`added`,
			`lastlogin`,
			`salt`,
			`token`
		) VALUES (
			'".trim($_POST['signup-username'])."',
			'".trim($_POST['signup-email'])."',
			'".md5($_POST['signup-password'])."',
			'".date("Y-m-d H:i:s")."',
			'".date("Y-m-d H:i:s")."',
			'".$salt."',
			'".$token."'
		)";
		//echo($sql);
		mysql_query($sql);

		$_SESSION['fbclone_username'] = $_POST['signup-username'];
		$_SESSION['fbclone_token'] = $token;
		$_SESSION['fbclone_salt'] = $salt;

		if (isset($_POST['remember']) && $_POST['remember']) {
			setcookie("fbclone_username",$row_un['username'],time()+(3600*24*365*10));
			setcookie("fbclone_token",$token,time()+(3600*24*30));
			setcookie("fbclone_salt",$salt,time()+(3600*24*30));
		} else {
			setcookie("fbclone_username","",time()-3600,'/');
			setcookie("fbclone_token","",time()-3600,'/');
			setcookie("fbclone_salt","",time()-3600,'/');
		}
		$retArray = array(
			"error" => false
		);

	}
	echo json_encode($retArray);
}

function logout () {
	$_SESSION['fbclone_username'] = "";
	$_SESSION['fbclone_token'] = "";
	$_SESSION['fbclone_salt'] = "";
	$_SESSION = array();

	setcookie("fbclone_username","",time()-3600,'/');
	setcookie("fbclone_token","",time()-3600,'/');
	setcookie("fbclone_salt","",time()-3600,'/');
	
	echo json_encode(array(
		"error" => false
	));
}

function checkEmail($email, $strict = false) {
	$regex = $strict? 
		'/^([.0-9a-z_-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i' : 
		'/^([*+!.&#$¦\'\\%\/0-9a-z^_`{}=?~:-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i' ; 
	if (preg_match($regex, trim($email), $matches)) { 
		return array($matches[1], $matches[2]); 
	} else { 
		return false; 
	}
}

function checkPassword ($pwd) {
	$error="";
	if( strlen($pwd) < 6 ) {$error = "Password too short. Must be at least 6 characters.";}
	elseif( strlen($pwd) > 20 ) {$error = "Password too long. Must be no longer than 20 characters.";}
	elseif( !preg_match("#[0-9]+#", $pwd) ) {$error = "Password must include at least one number!";}
	elseif( !preg_match("#[a-zA-Z]+#", $pwd) ) {$error = "Password must include at least one letter!";}
	//elseif( !preg_match("#[a-z]+#", $pwd) ) {$error = "Password must include at least one lowercase letter!";}
	//elseif( !preg_match("#[A-Z]+#", $pwd) ) {$error = "Password must include at least one uppercase letter!";}
	//elseif( !preg_match("#\W+#", $pwd) ) {$error = "Password must include at least one symbol!";}
	if($error!=""){
		return $error;
	} else {
		return "";
	}
}

function checkUsername ($username) {
	$error="";
	if( strlen($username) < 6 ) {$error = "Username too short. Must be at least 6 characters.";}
	elseif( strlen($username) > 20 ) {$error = "Username too long. Must be no longer than 20 characters.";}
	elseif( !preg_match("#[0-9a-zA-Z_\-]+#", $username) ) {$error = "Username can only include letters, numbers, dash ( - ), and underscore ( _ )!";}
	elseif( preg_match("#\W+#", $username) && !preg_match("#[-]+#", $username) ) {$error = "Username can only include letters, numbers, and underscore ( _ )!";}
	if($error!=""){
		return $error;
	} else {
		return "";
	}
}

function checkValidDate($month,$day,$year) {
	$year_diff = date("Y") - $year;
	if ($year_diff < 13) {return false;}
	if ($year_diff > 13) {return true;}
	if ($year_diff == 13) {
		$month_diff = date("n") - $month;
		if ($month_diff<0) {return false;}
		if ($month_diff>0) {return true;}
		if ($month_diff == 0) {
			$day_diff = date("j") - $day;
			if ($day_diff < -1) {return false;}else{return true;}
		}
	}
}

?>