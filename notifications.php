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
	include (_DOCROOT.'/includes/header.php');
	?>
	<p>Emailul dumneavoastra nu este verificat. Vă rugăm, căutați emailul pentru verificare.</p>
	<p>Apăsați <a href="/notifications.php?action=verifyemailagain">aici</a> să trimiteți un email de verificare.</p>
	<?php
	include (_DOCROOT.'/includes/footer.php');
}

function verifyemailagain() {
	include (_DOCROOT.'/includes/header.php');
	?>
	<form method="post" action="/notifications.php?action=sendverifyemail">
		Adresa de E-mail sau Nume de utilizator: <input name="signup-email" type="text">
		<button>Trimiteți Verification</button>
	</form>
	<?php
	include (_DOCROOT.'/includes/footer.php');
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
		$subject = "Înregistrarea cu ceau.ro";
		$message = 'Apăsați <a href="'._SITE.'/notifications.php?action=verify&email='.$to.'&verifytoken='.$verifyToken.'">aici</a> sau copiați linkul acesta '._SITE.'/notifications.php?action=verify&email='.$to.'&verifytoken='.$verifyToken.' ca să verificați contul de pe <a href="'._SITE.'/">ceau.ro</a>';
		$headers  = 'From: george@actingshowcase.com' . "\r\n" .
			'Reply-To: george@actingshowcase.com' . "\r\n" .
			'MIME-Version: 1.0' . "\r\n" .
			'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
			'X-Mailer: PHP/' . phpversion();
			
		if (mail($to,$subject,$message,$headers)) {
			echo "Verificare de Email a fost trimisă!";
		} else {
			echo "Emailul nu a fost putut trimis.";
		}
	} else {
		echo "Numele de utilizator sau Emailul nu se află cu noi.";
	}
	verifyemailagain();
}
?>