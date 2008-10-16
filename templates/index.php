<?php
if(isset($QUERY['comic'])) { // Viewing a single Comic...
	$comic_details = $comic_strips[0];
	
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
} else {

	if(i($_REQUEST, 'show') == 'all') { ?>
<strong>Show All</strong>, <a href="index.php?show=unread">Show Unread</a><br />
<?php } else { ?>
<a href="index.php?show=all">Show All</a>, <strong>Show Unread</strong><br />
<?php
	}
}
?>

<div id="strips-area">
<?php include('templates/get_comics.php'); ?>
</div>

