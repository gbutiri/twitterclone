<?php 
include (_DOCROOT.'/includes/pre-header.php');
?>
<!doctype html>
<html>
<head>
	<title><?php echo _TITLE;?></title>
	<meta charset="utf-8" />
	<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="/css/main.css" />
	
	<script src="http://code.jquery.com/jquery-2.0.3.min.js"></script>
	<script src="/js/main.js"></script>
</head>
<body>
<div class="page-wrapper">
<nav>
	<?php if (_USERNAME != '') { ?>
	<a id="logout-link" href="/logout">Logout</a>
	<?php } ?>
</nav>