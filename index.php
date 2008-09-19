<?php
include("common.php");

function showComicPager() {
	global $comic_pager;
	print $comic_pager->getLink('previous');
	$comic_pager->printGoToDropDown();
	print $comic_pager->getLink('next');
}

$single_comic = '';
if(isset($QUERY['comic'])) $single_comic = " AND Comic.id=$QUERY[comic]";
$order = 'DESC';
if(isset($QUERY['order']) and strtolower($QUERY['order']) == 'asc') $order = 'ASC';

$comic_pager = new SqlPager("SELECT Strip.id, Strip.name, Strip.url, Strip.contents, Strip.image_url, DATE_FORMAT(Strip.added_on,'$config[time_format]') AS added_on, "
					. " ComicTerm.comic_name, ComicTerm.comic_id, Comic.type "
					. " FROM  ComicTerm INNER JOIN Strip INNER JOIN Comic ON Strip.comic_id=Comic.id AND ComicTerm.comic_id=Comic.id"
					. " WHERE ComicTerm.user_id=$_SESSION[user_id] $single_comic "
					. " ORDER BY Strip.added_on $order", 10);
$comic_strips = $comic_pager->getPage();

render();
