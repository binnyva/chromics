<?php
include('../common.php');

$pager = new SqlPager("SELECT Comic.id, IF(ComicTerm.comic_name,ComicTerm.comic_name,Comic.name) AS name, Comic.url, 
		IF(ComicTerm.user_id=$_SESSION[user_id],1,0) AS subscribed
	FROM Comic LEFT JOIN ComicTerm ON Comic.id=ComicTerm.comic_id 
	WHERE Comic.status='1'
	ORDER BY subscribed DESC, Comic.name", 20);
	
$comics = $pager->getPage();

render(); 
