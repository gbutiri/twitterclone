<?php 
include ($_SERVER['DOCUMENT_ROOT'].'/config.php');
include (_DOCROOT.'/includes/class.db.php');

$action = isset($_GET['action']) ? $_GET['action'] : '';
$db = new DB();
$db->open();
call_user_func($action);
$db->close();


function verify() {
	//var_dump($_GET);
	$sql = "SELECT username, email FROM signup WHERE email = '".$_GET['email']."' AND verifytoken = '".$_GET['verifytoken']."'";
	$res = mysql_query($sql);
	$row = mysql_fetch_assoc($res);
	
	if (mysql_num_rows($res) > 0) {
		$sql_u = "UPDATE signup SET emailverified = 1, verifytoken = '' WHERE username = '".$row['username']."' AND email = '".$row['email']."' AND verifytoken =  '".$_GET['verifytoken']."';";
		$res_u = mysql_query($sql_u);
		$_SESSION['fbclone_username'] = $row['username'];
	}
	header('location: /');
}

function emailunverified() {
	?>
	<p>Your email is not verified. Please check your email and verify.</p>
	<p>Click <a href="/notifications.php?action=verifyemailagain">here</a> to send a verification again.</p>
	<?php
}

function verifyemailagain() {
	
	?>
	<form method="post" action="/notifications.php?action=sendverifyemail">
		Email or Username: <input name="signup-email" type="text">
		<button>Send Verification</button>
	</form>
	<?php
}

function sendverifyemail() {
	
	$sql = "SELECT COUNT(*) AS usercount, username, email FROM signup 
			WHERE username = '".trim($_POST['signup-email'])."' 
			OR email = '".trim($_POST['signup-email'])."';";
	$res = mysql_query($sql);
	$row = mysql_fetch_assoc($res);
	if ($row['usercount'] > 0) {
		$verifyToken = md5(time());
		
		$sql_t = "UPDATE signup SET verifytoken = '".$verifyToken."' 
			WHERE username = '".$row['username']."'";
			//var_dump($sql_t);
		$res_t = mysql_query($sql_t);
		
		//$to = trim("MovieMaker713@gmail.com");
		$to = $row['email'];
		$subject = "Inregistrarea cu ceau.ro";
		$message = 'Apasati <a href="'._SITE.'/botifications.php?action=verify&email='.$to.'&verifytoken='.$verifyToken.'">aici</a> sau copiati linkul acesta '._SITE.'/notifications.php?action=verify&email='.$to.'&verifytoken='.$verifyToken.' ca sa verificati contul de pe <a href="'._SITE.'/">ceau.ro</a>';
		$headers  = 'From: george@actingshowcase.com' . "\r\n" .
			'Reply-To: george@actingshowcase.com' . "\r\n" .
			'MIME-Version: 1.0' . "\r\n" .
			'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
			'X-Mailer: PHP/' . phpversion();
			
		if (mail($to,$subject,$message,$headers)) {
			echo "Email Verification has been sent!";
		} else {
			echo "Unable to send mail.";
		}
	} else {
		echo "Username or Email does not exist in our database.";
	}
	verifyemailagain();
}
?>