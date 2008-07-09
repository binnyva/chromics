
<?php foreach($all_comics as $comic) {
extract($comic);
?>
<div class="strip" id="strip-<?=$id?>">
<h3><a href="<?=$url?>"><?=$name?></a></h3>

<?php 
if(in_array($id, $subscribed_comics)) { 
	print "Subscribed<br />";
} else { ?>
<a href="comics/subscribe.php?comic=<?=$id?>" class="ajaxify">Subscribe</a><br />
<?php } ?>
<a href="index.php?comic=<?=$id?>">Browse</a><br />

</div>

<?php } ?>
<br />
