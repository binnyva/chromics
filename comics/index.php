<?php
include('../common.php');

$search = '';
if(i($QUERY,'search')) {
	$keywords = array_unique(preg_split("/[\s,\.\-]+/", $QUERY['search'])); //Split the search query to its individual keywords
	$stop_words = array('I','a','about','an','are','as','at','be','by','for','from','how','in','is','it','la','of','on','or','that','the','this','to','was','what','when','where','who','will','with','the');
	$keywords = array_diff($keywords, $stop_words); //We don't need the frequently used words in the keywords
	
	if(!$keywords) showMessage('Please be more specific in your query','index.php','search-status');
	
	//Make the necessary query fragments
	$name = array();
	foreach($keywords as $key) {
		$name[] = "name LIKE '%$key%'";
	}
	$search = ' AND ' . implode(' AND ', $name);
}

$pager = new SqlPager("SELECT Comic.id, IF(ComicTerm.comic_name,ComicTerm.comic_name,Comic.name) AS name, Comic.url, 
		IF(ComicTerm.user_id=$_SESSION[user_id],1,0) AS subscribed
	FROM Comic LEFT JOIN ComicTerm ON Comic.id=ComicTerm.comic_id 
	WHERE Comic.status='1' $search
	ORDER BY subscribed DESC, Comic.name", 20);
$comics = $pager->getPage();

render(); 
