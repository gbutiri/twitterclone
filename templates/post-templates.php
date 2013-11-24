<?php 

function template_post($row) {
	$timezone = $_GET['timezone'];
	?>
	<li id="post_<?php echo $row['id']; ?>">
		<a href="/<?php echo $row['poster']; ?>" class="poster"><?php echo $row['poster']; ?></a>
		<div class="content"><?php echo $row['details']; ?></div>
		<div class="time"><?php echo date("D, M jS @ g:i a",strtotime($row['dtm'].$timezone."hours")); ?></div>
	</li>
	<?php
}

?>