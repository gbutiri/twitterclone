<?php 
include ('config.php');
include (_DOCROOT.'/includes/header.php');
include(_DOCROOT.'/templates/post-templates.php');
?>
<form method="POST" id="write-form" action="/">
	<div class="write-container">
		<textarea class="autosize" autocomplete="off" id="write-area" name="writearea" place-holder="What's on your mind?">What's on your mind?</textarea>
	</div>
	<button id="post-button">Post</button>
</form>

<ul id="tweets"></ul>
<button id="load-more">Load More (<span><?php echo $numMore-10; ?></span>)</button>
<?php
include (_DOCROOT.'/includes/footer.php');

?>