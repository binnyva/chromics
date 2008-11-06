<?php
if(isset($QUERY['comic'])) { // Viewing a single Comic...
	$comic_details = $sql->getAssoc("SELECT id as comic_id, name, description, url, feed FROM Comic WHERE id='$QUERY[comic]'");
	
	$subscribed = false;
	if(isset($users_subscribtions[$comic_details['comic_id']])) { //If this comic is on the current users subscribed list,
		$comic_details['comic_name'] = $users_subscribtions[$comic_details['comic_id']]['name']; //Get the user given name for the feed.
		$subscribed = true;
	}
	?>
<div id="comic-details">
<h3><a href="<?=$comic_details['url']?>"><?=$comic_details['comic_name']?></a></h3>
<?php 
if($comic_details['description']) print "<p>$comic_details[description]</p>";

if($subscribed) { ?>
<p class="with-icon ok">You have subscibed this comic. <a href="<?=getLink("comics/subscribe.php",
	array("action"=>"unsubscribe","comic"=>$comic_details['comic_id']), true) 
	?>" class="with-icon delete ajaxify confirm" title="Unsubscribe '<?=$comic_details['name']?>'">Unsubscibe?</a></p>
<?php } else { ?>
<p><a href="<?=getLink("comics/subscribe.php", array("action"=>"subscribe", "comic"=>$comic_details['comic_id']), true)
	?>" class="with-icon add ajaxify" title="Subscribe to '<?=$comic_details['name']?>'?">Subscibe to this comic?</a></p>
<?php } ?>


</div>
	<?php
} 

if(i($_REQUEST, 'show') == 'all') { ?>
<strong>Show All</strong>, <a href="<?=getLink("index.php", array('show'=>'unread'), true)?>">Show Unread</a><br />
<?php } else { ?>
<a href="<?=getLink("index.php", array('show'=>'unread'), true)?>">Show All</a>, <strong>Show Unread(<?=$total_unread_comics?>)</strong><br />
<?php
}


if(count($comic_strips)) { // Show this only if there are strips
	if($order == 'DESC') { ?>
<strong>Show Latest First</strong>, <a href="<?=getLink("index.php", array('order'=>'asc'), true)?>">Show Oldest First</a><br />
<?php } else { ?>
<a href="<?=getLink("index.php", array('order'=>'desc'), true)?>">Show Latest First</a>, <strong>Show Oldest First</strong><br />
<?php
	}
}
?>

<div id="strips-area">
<?php include('templates/get_comics.php'); ?>
</div>

