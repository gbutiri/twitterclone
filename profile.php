<?php 
include ('config.php');
include (_DOCROOT.'/includes/pre-header.php');
include (_DOCROOT.'/includes/header.php');
include(_DOCROOT.'/templates/post-templates.php');
$profile_un = $_GET['un'];

?>
<h1><?php echo $profile_un; ?></h1>
<ul id="tweets" data-username="<?php echo $profile_un; ?>"></ul>
<button id="load-more">Încărcați mai multe mesaje (<span><?php echo $numMore-10; ?></span>)</button>
<?php

include (_DOCROOT.'/includes/footer.php');
$db->close();

?>