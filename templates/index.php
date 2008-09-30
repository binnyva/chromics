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
}

showComicPager();
?>

<?php foreach($comic_strips as $strip) {
extract($strip);
?>
<div class="strip" id="strip-<?=$id?>">
<p class="time"><?=$added_on?></p>
<h3><a href="<?=$url?>"><?=$name?></a></h3>

<p>from <a href="index.php?comic=<?=$comic_id?>"><?=$comic_name?></a></p>

<?php 
if($type == 'embedded') print $contents;
else { ?>
<img src="<?=$image_url?>" alt="<?=$name?>" /><br />
<?=$contents?>
<?php } ?>

</div>
<?php } ?>

<?php showComicPager(); ?>
