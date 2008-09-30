<?php
include("common.php");

function showComicPager() {
	global $comic_pager;
	print $comic_pager->getLink('previous');
	$comic_pager->printGoToDropDown();
	print $comic_pager->getLink('next');
}

$query = ''; //Yes, query is made my concating strings is this case. Not ideal, but it works

// Get name and id of all user subscribed feeds.
$users_subscribtions = array();
if(isset($_SESSION['user_id'])) {
	$users_subscribtions = $sql->getById("SELECT Comic.id,ComicTerm.comic_name AS name, Comic.type FROM Comic INNER JOIN ComicTerm "
			. " ON ComicTerm.comic_id=Comic.id WHERE ComicTerm.user_id=$_SESSION[user_id]");
}

if(isset($QUERY['comic'])) { //If viewing a single comic...
	$query = "SELECT Strip.id, Strip.name, Strip.url, Strip.contents, Strip.image_url, DATE_FORMAT(Strip.added_on,'$config[time_format]') AS added_on, "
			. " Comic.name as comic_name, Comic.id as comic_id, Comic.type, Comic.url, Comic.description "
			. " FROM  Strip INNER JOIN Comic ON Strip.comic_id=Comic.id WHERE Comic.id=$QUERY[comic]";
	
}
else if($_SESSION['user_id']) { //If the user is just browsing, show his subscribed comics.
	$query = "SELECT Strip.id, Strip.name, Strip.url, Strip.contents, Strip.image_url, DATE_FORMAT(Strip.added_on,'$config[time_format]') AS added_on, "
			. " ComicTerm.comic_name, ComicTerm.comic_id, Comic.type "
			. " FROM  ComicTerm INNER JOIN Strip INNER JOIN Comic ON Strip.comic_id=Comic.id AND ComicTerm.comic_id=Comic.id"
			. " WHERE ComicTerm.user_id=$_SESSION[user_id] "; 
}

//Set the order - the latest will show up at top by default.
$order = 'DESC';
if(isset($QUERY['order']) and strtolower($QUERY['order']) == 'asc') $order = 'ASC';
$query .= " ORDER BY Strip.added_on $order";

$comic_pager = new SqlPager($query, 10);
$comic_strips = $comic_pager->getPage();

render();
