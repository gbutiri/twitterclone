<?php
include ($_SERVER['DOCUMENT_ROOT'].'/config.php');
include (_DOCROOT.'/includes/class.db.php');
include (_DOCROOT.'/includes/class.functions.php');

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
			'message' => 'Numele is parola sunt incorecte.'
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
		$sql="UPDATE signup SET lastlogin = ".date("Y-m-d H:i:s").", token='".$token."', salt='".$salt."' WHERE username = '".$row_un['username']."'";
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
		$err.="<div>Numele nu e valabil. Folositi alt nume.</div>";
	}
	if ($row_em['emailcount'] > 0) {
		$err.="<div>Emailul acesta deja exista. Folositi un email diferit.</div>";
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
		$err.="<div>Numele e prea scurt.</div>";
	} 
	if (!preg_match('/^[a-zA-Z0-9]+$/i', $_POST['signup-username'])) {	 
		$err.="<div>Folositi numai litere si numere pentru nume. Spatiu nu e valid.</div>";
	} 
	if (!preg_match($regex, trim($_POST['signup-email']))) {
		$err.="<div>Formatul emailului nu e corect. Folositi formatul 'nume@domeniu.com'</div>";
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
		$err .= "<div>Parola e prea scurta. Trebuie sa fie cel putin 6 cifre / litere.</div>";
	} 
	if( strlen($_POST['signup-password']) > 20 ) {
		$err .= "<div>Parola e prea lunga. Limita e 20 de cifre / litere.</div>";
	} 
	if( !preg_match("#[0-9]+#", $_POST['signup-password']) ) {
		$err .= "<div>Folositi cel putin o cifra pentru parola.</div>";
	} 
	if( !preg_match("#[a-zA-Z]+#", $_POST['signup-password']) ) {
		$err .= "<div>Folositi cel putin o litera pentru parola.</div>";
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
		
		$f = new Functions();
		$f->makeUserFolder(trim($_POST['signup-username']));
	
		$emailverified = 0;
		$verifyToken = md5(time());
		if (_EMAILVERIFY === false) {
			$emailverified = 1;
			$verifyToken = '';
		}
		$sql="INSERT INTO signup (
			`username`,
			`email`,
			`password`,
			`added`,
			`lastlogin`,
			`salt`,
			`token`,
			`emailverified`,
			`verifytoken`
		) VALUES (
			'".trim($_POST['signup-username'])."',
			'".trim($_POST['signup-email'])."',
			'".md5($_POST['signup-password'])."',
			'".date("Y-m-d H:i:s")."',
			'".date("Y-m-d H:i:s")."',
			'".$salt."',
			'".$token."',
			'".$emailverified."',
			'".$verifyToken."'
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
		
		if (_EMAILVERIFY) {
		
			$to = trim($_POST['signup-email']);
			//$to = trim("MovieMaker713@gmail.com");
			$subject = "Inregistrarea cu ceau.ro";
			$message = 'Apasati <a href="'._SITE.'/notifications.html?action=verify&email='.$to.'&verifytoken='.$verifyToken.'">aici</a> sau copiati linkul acesta '._SITE.'/notifications.html?action=verify&email='.$to.'&verifytoken='.$verifyToken.' ca sa verificati contul de pe <a href="'._SITE.'/">ceau.ro</a>';
			$headers  = 'From: george@ceau.ro' . "\r\n" .
				'Reply-To: george@ceau.ro' . "\r\n" .
				'MIME-Version: 1.0' . "\r\n" .
				'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
				'X-Mailer: PHP/' . phpversion();
			if (mail($to,$subject,$message,$headers)) {
				$retArray = array(
					"error" => false
				);
			} else {
				echo "Unable to send mail.";
				$retArray = array(
					"error" => true,
					"message" => "Unable to send mail"
				);
			}
		} else {
			$retArray = array(
				"error" => false
			);
		}

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
	if( strlen($username) < 6 ) {$error = "Numele e prea scurt. Folositi minim 6 litere / cifre.";}
	elseif( strlen($username) > 20 ) {$error = "Numele e prea lung. Folsiti maxim 20 litere / cifre.";}
	elseif( !preg_match("#[0-9a-zA-Z_\-]+#", $username) ) {$error = "Pentru nume, folisiti numai litere, cifre, liniuta ( - ), si subliniere ( _ ).";}
	elseif( preg_match("#\W+#", $username) && !preg_match("#[-]+#", $username) ) {$error = "Pentru nume, folisiti numai litere, cifre, liniuta ( - ), si subliniere ( _ ).";}
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