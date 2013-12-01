<?php 
include ('config.php');
include (_DOCROOT.'/includes/pre-header.php');
include (_DOCROOT.'/includes/header.php');
include (_DOCROOT.'/includes/class.functions.php');
include (_DOCROOT.'/templates/post-templates.php');

showMap();

?>
<ul id="tweets"></ul>
<button id="load-more">Încărcați mai multe mesaje (<span><?php echo $numMore-10; ?></span>)</button>
<?php

include (_DOCROOT.'/includes/footer.php');
$db->close();

?>