<?php
include('../common.php');

if(!isset($QUERY['comic'])) showMessage("Please select a comic", "comics/", "error");

$comic_name = $Comic->select('name')->find($QUERY['comic']);

if($QUERY['action'] == 'unsubscribe') {
	$Comic->unsubscribe($QUERY['comic']);
	showMessage("You have unsubscribed from '$comic_name[name]'", "comics/");
	
} else {
	$Comic->subscribe($QUERY['comic']);
	showMessage("Subscribed to '$comic_name[name]' successfully.", "comics/");
}

