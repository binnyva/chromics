<?php
require("../common.php");
require_once('../models/Fetcher.php');

if($argc) {
	$Fetcher->getComics(array_slice($argv,1));
}

$Fetcher->fetchComics();
$sql->disconnect();
