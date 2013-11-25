<?php 
include (_DOCROOT.'/includes/class.functions.php');

function template_post($row) {
	$timezone = $_GET['timezone'];
	$f = new Functions();
	$like = $f->getLike($row['id']);
	$likes = $f->getLikes($row['id']);
	?>
	<li id="post_<?php echo $row['id']; ?>">
		<div class="avatar"><img src="<?php echo $f->userLink($row['poster']); ?>/photos/<?php echo $row['mainimgid']; ?>_small.jpg" /></div>
		<a href="/<?php echo $row['poster']; ?>" class="poster"><?php echo $row['poster']; ?></a> <?php if ($row['location'] != '') { ?><span class="location">( <?php echo $row['location']; ?> )</span><?php } ?>
		<div class="content"><?php echo $row['details']; ?></div>
		
		<div class="like-bar">
			<?php if ($like == 0) { ?>
			<a href="#" data-id="<?php echo $row['id']; ?>" class="like-button" href="#">Îmi place</a>
			<?php } else { ?>
			<a href="#" data-id="<?php echo $row['id']; ?>" class="unlike-button" href="#">Nu-mi place</a>
			<?php } ?>
			(<span class="like-count"><?php echo $likes; ?></span>)
		</div>
		
		<div class="time"><?php echo date("D, M jS @ g:i a",strtotime($row['dtm'].$timezone."hours")); ?></div>
	</li>
	<?php
}

?>