<!doctype html>
<html>
<head>
	<title><?php echo _TITLE;?></title>
	<meta charset="utf-8" />

<meta name="viewport" content="width=520, target-densitydpi=high-dpi" />
<meta http-equiv="Content-Type" content="application/vnd.wap.xhtml+xml; charset=utf-8" />
<meta name="HandheldFriendly" content="true" />
<meta name="apple-mobile-web-app-capable" content="yes" /> 

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