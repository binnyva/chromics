<?php
include('../common.php');

if(!isset($QUERY['comic'])) showMessage("Please select a comic", "../directory.php", "error");

$Comic->subscribe($QUERY['comic']);
$comic_name = $Comic->select('name')->find($QUERY['comic']);

showMessage("Subscribed to '$comic_name[name]' successfully.", "../directory.php");
