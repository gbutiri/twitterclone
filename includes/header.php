<!doctype html>
<html>
<head>
	<title><?php echo _TITLE;?></title>
	<meta charset="utf-8" />

	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" /> <!--320-->

	<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="/css/main.css" />
	<link rel="shortcut icon" href="/favicon.ico" />
	<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet" />
	<script src="http://code.jquery.com/jquery-2.0.3.min.js"></script>
	<script src="http://maps.googleapis.com/maps/api/js?v=3&amp;sensor=false&amp;language=ro"></script>
	<script type="text/javascript" src="/js/markerclusterer_packed.js"></script>
	<script src="/js/main.js"></script>
</head>
<body>
<?php 
$servers = array('127.0.0.1','4.30.56.71','68.5.250.174');
//echo($_SERVER['REMOTE_ADDR']);
if (!in_array($_SERVER['REMOTE_ADDR'],$servers)) {
?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-2397724-41', 'ceau.ro');
  ga('send', 'pageview');
</script>
<?php
}
?>
<div class="page-wrapper">
<nav>
	<img src="/img/ceauro-logo.png" alt="ceau.ro - un loc unde te poti conecta cu ai tai prieteni" />
	<a class="logo" href="/">acasa</a>
	<?php if (_USERNAME != '') { ?>
	<a class="logo" href="/map.html">harta</a>
	<a class="followers" href="/followers.html">urmaritii</a>
	<a id="logout-link" href="/logout">ieșire</a>
	<a href="/<?php echo _USERNAME; ?>" class="username"><?php echo _USERNAME; ?></a>
	<?php } ?>
</nav>