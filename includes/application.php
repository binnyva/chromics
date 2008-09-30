<?php
$_SESSION['user_id'] = 1;

function checkUser() {
	global $config;
	if(!isset($_SESSION['user_id']) or !$_SESSION['user_id']) 
		showMessage("Please login to use this feature", $config['site_url'] . 'user/login.php', "error");
}
