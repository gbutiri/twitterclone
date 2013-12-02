<?php 
include ('config.php');
include (_DOCROOT.'/includes/pre-header.php');
include (_DOCROOT.'/includes/header.php');
include (_DOCROOT.'/includes/class.functions.php');
include (_DOCROOT.'/templates/post-templates.php');
?>
<div class="main-billboard">
Conectați-vă cu prieteni Români!
</div>



<?php showMap(); ?>


<ul class="logintabs" id="logintabs">
	<li><a href="#" data-click="tweets" class="active">Ceau.ro</a></li>
	<li><a href="#" data-click="signin-form">Conectare</a></li>
	<li><a href="#" data-click="signup-form">Înregistrare</a></li>
</ul>
<div id="loginforms" class="loginforms">
	<div class="tab-contents" id="tweets"></div>
	<form method="POST" id="signin-form" class="tab-contents registration-forms" action="/ajax/registration.php?action=trylogin">
		<label>Nume de utilizator:</label>
		<div><input type="text" name="signin-username" id="signin-username" /></div>
		<label>Parolă:</label>
		<div><input type="password" name="signin-password" id="signin-password" /></div>
		<?php if (true) { ?>
		<div>
			<span><input type="checkbox" id="remember" name="remember" value="true" />Tine-ma minte</span>
		</div>
		<?php } ?>
		<div><button id="signin-button" />Conectați-vă</button></div>
		<div>
			<a href="#" id="forgot-password">Mi-am uitat parola</a>
		</div>
	</form>
	<form method="POST" id="signup-form" class="tab-contents registration-forms" action="/ajax/registration.php?action=trysignup">
		<label>E-mail:</label>
		<div><input type="text" name="signup-email" id="signup-email" /></div>
		<label>Nume de utilizator:</label>
		<div><input type="text" name="signup-username" id="signup-username" /></div>
		<label>Parolă:</label>
		<div><input type="password" name="signup-password" id="signup-password" /></div>
		<div><button type="submit" id="signup-button" />Înregistrați-vă</button></div>
	</form>
</div>
<?php
include (_DOCROOT.'/includes/footer.php');
$db->close();
?>
