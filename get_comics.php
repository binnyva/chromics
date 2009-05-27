<?php
require_once('common.php');

$query = ''; //Yes, query is made my concatinating strings is this case. Not ideal, but it works

// Get name and id of all user subscribed feeds.
$users_subscribtions = array();
$users_subscribtions = $sql->getById("SELECT Comic.id,ComicTerm.comic_name AS name, Comic.type FROM Comic INNER JOIN ComicTerm
				ON ComicTerm.comic_id=Comic.id WHERE ComicTerm.user_id=$_SESSION[user_id]");

if(isset($QUERY['comic'])) { //If viewing a single comic...
	$query = "SELECT Strip.id, Strip.name, Strip.url, Strip.contents, Strip.image_url, Strip.added_on, DATE_FORMAT(Strip.added_on,'$config[time_format]') AS added_on_formated,
				Comic.name as comic_name, Comic.id as comic_id, Comic.type, Comic.url, Comic.description,
				IF(StripUser.id IS NULL, 0, 1) AS read_status
				FROM  ((Comic INNER JOIN Strip ON Comic.id=Strip.comic_id) 
						LEFT JOIN StripUser ON Strip.id=StripUser.strip_id AND StripUser.user_id=$_SESSION[user_id])
				WHERE Comic.id=$QUERY[comic]";
	
} else { // View the whole subscribed list.
	$query = "SELECT Strip.id, Strip.name, Strip.url, Strip.contents, Strip.image_url, Strip.added_on, DATE_FORMAT(Strip.added_on,'$config[time_format]') AS added_on_formated,
				ComicTerm.comic_name, ComicTerm.comic_id, IF(StripUser.id IS NULL, 0, 1) AS read_status
				FROM ((ComicTerm INNER JOIN Strip ON ComicTerm.comic_id=Strip.comic_id) 
						LEFT JOIN StripUser ON Strip.id=StripUser.strip_id AND StripUser.user_id=$_SESSION[user_id])
				WHERE ComicTerm.user_id=$_SESSION[user_id]";
}
if(i($QUERY, 'show') != 'all') $query .= ' AND StripUser.id IS NULL'; // Show only the unread comics.

//Set the order - the latest will show up at top by default.
$order = 'ASC';
if(isset($QUERY['order']) and strtolower($QUERY['order']) == 'desc') $order = 'DESC';

if(isset($QUERY['added_after'])) { //Auto insertion paging - show only the elements added after the given time(or before - if the order is asc)
	if($order == 'ASC') $query .= " AND Strip.added_on>'$QUERY[added_after]'";
	else $query .= " AND Strip.added_on<'$QUERY[added_after]'";
}

$query .= " ORDER BY Strip.added_on $order";
$comic_strips = $sql->getAll($query);
$total_unread_comics = $sql->fetchNumRows();

// Don't render if the file is included
if(i($QUERY,'ajax')) {
	require('templates/get_comics.php');
}
