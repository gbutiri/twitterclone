<?php 
include ('config.php');
include (_DOCROOT.'/includes/header.php');
?>
<form method="POST" id="signin-form" class="registration-forms" action="/ajax/registration.php?action=trylogin">
	<label>Nume de utilizator:</label>
	<div><input type="text" name="signin-username" id="signin-username" /></div>
	<label>Parola:</label>
	<div><input type="password" name="signin-password" id="signin-password" /></div>
	<div><button id="signin-button" />Conectați-vă</button></div>
</form>
<form method="POST" id="signup-form" class="registration-forms" action="/ajax/registration.php?action=trysignup">
	<label>E-mail:</label>
	<div><input type="text" name="signup-email" id="signup-email" /></div>
	<label>Nume de utilizator:</label>
	<div><input type="text" name="signup-username" id="signup-username" /></div>
	<label>Parola:</label>
	<div><input type="password" name="signup-password" id="signup-password" /></div>
	<div><button type="submit" id="signup-button" />Înregistrați-vă</button></div>
</form>
<?php
include (_DOCROOT.'/includes/footer.php');
?>
