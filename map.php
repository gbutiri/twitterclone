<?php 
include ('config.php');
include (_DOCROOT.'/includes/pre-header.php');
include (_DOCROOT.'/includes/header.php');
include (_DOCROOT.'/includes/class.functions.php');
include (_DOCROOT.'/templates/post-templates.php');

showMap();

include (_DOCROOT.'/includes/footer.php');
$db->close();

?>