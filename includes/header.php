<!doctype html>
<html>
<head>
	<title><?php echo _TITLE;?></title>
	<meta charset="utf-8" />

	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" /> <!--320-->

	<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="/css/main.css" />
	<link rel="shortcut icon" href="/favicon.ico" />
	  
	<script src="http://code.jquery.com/jquery-2.0.3.min.js"></script>
	<script src="/js/main.js"></script>
</head>
<body>
<div class="page-wrapper">
<nav>
	<a class="logo" href="/">ceau<span>.ro</span></a>
	<?php if (_USERNAME != '') { ?>
	<a id="logout-link" href="/logout">Ieșire</a>
	<?php } ?>
	<span class="username"><?php echo _USERNAME; ?></span>
</nav>