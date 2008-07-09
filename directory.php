<?php
include("common.php");

$all_comics = $sql->getById("SELECT Comic.id, Comic.name, Comic.url FROM Comic");
$subscribed_comics = $sql->getCol("SELECT comic_id FROM ComicTerm WHERE user_id='$_SESSION[user_id]'");

render();
