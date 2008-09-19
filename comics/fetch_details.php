<?php
require('../common.php');
if(!isset($QUERY['feed'])) exit;

include('../models/Fetcher.php');

$feed_contents = load($QUERY['feed']);
$data = xml2array($feed_contents);
if(!isset($data['rss'])) {
	print '{"error":"Feed is not in RSS format"}';
	exit; 
}
$feed = $data['rss']['channel'];

//Find if the images are linked or embedded.
$type = 'linked';
$first_item = isset($feed['item'][0]) ? $feed['item'][0] : $feed['item'] ;
if(i($first_item, 'content:encoded')) $contents = i($first_item, 'content:encoded');
elseif(i($first_item, 'content')) $contents = i($first_item, 'content');
else $contents = i($first_item, 'description');
$image_url = $Fetcher->findFirstImage($contents);
if($image_url) $type = 'embedded';

$feed_details = array(
	'name'	=> $feed['title'],
	'url'	=> $feed['link'],
	'description' => $feed['description'],
	'feed'	=> $QUERY['feed'],
	'type'	=> $type,
);

print json_encode(array("success"=>$feed_details));
