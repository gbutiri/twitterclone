<?php 
include (_DOCROOT.'/includes/class.functions.php');

function template_post($row) {
	$timezone = $_GET['timezone'];
	$f = new Functions();
	?>
	<li id="post_<?php echo $row['id']; ?>">
		<div class="avatar"><img src="<?php echo $f->userLink($row['poster']); ?>/photos/<?php echo $row['mainimgid']; ?>_small.jpg" /></div>
		<a href="/<?php echo $row['poster']; ?>" class="poster"><?php echo $row['poster']; ?></a> <span class="location">( <?php echo $row['location']; ?> )</span>
		<div class="content"><?php echo $row['details']; ?></div>
		<div class="time"><?php echo date("D, M jS @ g:i a",strtotime($row['dtm'].$timezone."hours")); ?></div>
	</li>
	<?php
}

?>