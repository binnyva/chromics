<?php
include("common.php");

$single_comic = '';
if(isset($QUERY['comic'])) $single_comic = " AND Comic.id=$QUERY[comic]";
$comic_strips = $sql->getAll("SELECT Strip.id, Strip.name, Strip.url, Strip.contents, Strip.image_url,  "
					. " ComicTerm.comic_name, ComicTerm.comic_id, Comic.type "
					. " FROM  ComicTerm INNER JOIN Strip INNER JOIN Comic ON Strip.comic_id=Comic.id AND ComicTerm.comic_id=Comic.id"
					. " WHERE ComicTerm.user_id=$_SESSION[user_id] $single_comic "
					. " ORDER BY Strip.added_on DESC LIMIT 0, 25");

render();
