<?php 
include ('config.php');
include (_DOCROOT.'/includes/pre-header.php');
include (_DOCROOT.'/includes/header.php');
include (_DOCROOT.'/templates/post-templates.php');
$profile_un = $_GET['un'];
$sql = "SELECT mainimgid, location FROM signup WHERE username = '".$profile_un."'";
$res = mysql_query($sql);
$row = mysql_fetch_assoc($res);
$f = new Functions();

?>
<div id="profile-avatar" class="avatar">
	<?php if ($profile_un == _USERNAME) { ?>
		<a href="#">
			<i class="fa fa-camera"></i>
			<img src="<?php echo $f->userLink($profile_un); ?>/photos/<?php echo $row['mainimgid']; ?>_medium.jpg" />
		</a>
	<?php } else { ?>
		<img src="<?php echo $f->userLink($profile_un); ?>/photos/<?php echo $row['mainimgid']; ?>_medium.jpg" />
	<?php } ?>
</div>
<h1><?php echo $profile_un; ?></h1>

<div>
	<?php if ($profile_un == _USERNAME) { ?>
		<form autocomplete="off" id="profile-form" action="/ajax/profile-calls.php?action=saveprofilefield">
			<input autocomplete="off" name="location" id="zipcode" type="text" data-fieldname="location" class="autosave <?php echo( ($row['location'] == '') ? '' : 'active'); ?>" place-holder="Localnicul" value="<?php echo( ($row['location'] == '') ? 'Localnicul' : $row['location']); ?>" />
		</form>
	<?php } else { ?>
		<?php echo $row['location']; ?>
	<?php } ?>
</div>

<ul id="tweets" data-username="<?php echo $profile_un; ?>"></ul>
<button id="load-more">Încărcați mai multe mesaje (<span><?php echo $numMore-10; ?></span>)</button>

<?php
if ($profile_un == _USERNAME) {
	?>
	<!-- UPLOAD DEPENDENCIES -->
	<script src="/js/mini-upload/jquery.knob.js"></script>

	<!-- jQuery File Upload Dependencies -->
	<script src="/js/mini-upload/jquery.ui.widget.js"></script>
	<script src="/js/mini-upload/jquery.iframe-transport.js"></script>
	<script src="/js/mini-upload/jquery.fileupload.js"></script>
	<?php
}
include (_DOCROOT.'/includes/footer.php');
$db->close();

?>