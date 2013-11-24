<?php 
include ('config.php');
include (_DOCROOT.'/includes/pre-header.php');
include (_DOCROOT.'/includes/header.php');
include (_DOCROOT.'/templates/post-templates.php');
?>
<form method="POST" id="write-form" action="/">
	<div class="write-container">
		<textarea class="autosize" autocomplete="off" id="write-area" name="writearea" place-holder="La ce va gândiți?">La ce va gândiți?</textarea>
	</div>
	<button id="post-button">Publicați</button>
</form>

<ul id="tweets"></ul>
<button id="load-more">Încărcați mai multe mesaje (<span><?php echo $numMore-10; ?></span>)</button>
<?php
include (_DOCROOT.'/includes/footer.php');
$db->close();

?>