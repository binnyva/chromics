<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html><head>
<title><?=$title?></title>
<link href="<?=$abs?>css/style.css" rel="stylesheet" type="text/css" />
<link href="<?=$abs?>images/silk_theme.css" rel="stylesheet" type="text/css" />
<?=$css_includes?>
</head>
<body>
<div id="header">
<h1 id="logo"><a href="<?=$abs?>"><?=$title?></a></h1>

<div id="top-links">
<a href="<?=$abs?>comics/">All Comics</a> | <a href="<?=$abs?>comics/add.php">Add Comic</a>
</div>
</div>

<div id="content">
<div id="error-message" <?=($QUERY['error']) ? '':'style="display:none;"';?>><?php
	if(isset($PARAM['error'])) print strip_tags($PARAM['error']); //It comes from the URL
	else print $QUERY['error']; //Its set in the code(validation error or something.
?></div>
<div id="success-message" <?=($QUERY['success']) ? '':'style="display:none;"';?>><?=strip_tags(stripslashes($QUERY['success']))?></div>

<!-- Begin Content -->
<?php 
/////////////////////////////////// The Template file will appear here ////////////////////////////

include($GLOBALS['template']->template); 

/////////////////////////////////// The Template file will appear here ////////////////////////////
?>
<!-- End Content -->
</div>

<div id="footer">An <a href="http://www.bin-co.com/php/scripts/iframe/">iFrame</a> Application</div>

<script src="<?=$abs?>js/libraries/jsl.js" type="text/javascript"></script>
<script src="<?=$abs?>js/application.js" type="text/javascript"></script>
<?=$js_includes?>
</body>
</html>