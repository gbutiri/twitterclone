<?php 
include ('config.php');
include (_DOCROOT.'/includes/pre-header.php');
include (_DOCROOT.'/includes/header.php');
include (_DOCROOT.'/templates/post-templates.php');
?>
<form method="POST" id="write-form" action="/">
	<div class="write-container">
		<textarea class="autosize" autocomplete="off" id="write-area" name="writearea" place-holder="La ce va gândiți?">La ce va gândiți?</textarea>
		<a id="postimage" href="#"><i class="fa fa-camera"></i></a>
	</div>
	<button id="post-button">Publicați</button>
</form>

<ul id="tweets" data-type="follow"></ul>
<button id="load-more" data-type="follow">Încărcați mai multe mesaje (<span><?php echo $numMore-10; ?></span>)</button>
<!-- UPLOAD DEPENDENCIES -->
<script src="/js/mini-upload/jquery.knob.js"></script>

<!-- jQuery File Upload Dependencies -->
<script src="/js/mini-upload/jquery.ui.widget.js"></script>
<script src="/js/mini-upload/jquery.iframe-transport.js"></script>
<script src="/js/mini-upload/jquery.fileupload.js"></script>
<?php
include (_DOCROOT.'/includes/footer.php');
$db->close();

?>