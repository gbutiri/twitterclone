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
	<a class="logo" href="/"><span class="logo-t">t</span><span class="logo-witter">witter</span><span class="logo-c">c</span><span class="logo-lone">lone</span></a>
	<?php if (_USERNAME != '') { ?>
	<a id="logout-link" href="/logout">Logout</a>
	<?php } ?>
	<span class="username"><?php echo _USERNAME; ?></span>
</nav>