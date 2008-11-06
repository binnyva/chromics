<?php 
if(!count($comic_strips)) {
	print "<p>No Comics Found</p>";
	
} else {

$show_comics = 10 > count($comic_strips) ? count($comic_strips) : 10;
for($i=0; $i<$show_comics; $i++) {
	unset($type);
	extract($comic_strips[$i]);
	if(!$id) continue; // No ID means no strip.
	
	$class = 'unread';
	if(isset($read_status) and $read_status) $class = 'read';
?>
<div class="strip <?=$class?>" id="strip-<?=$id?>">
<input type="hidden" name="added_on" value="<?=$added_on?>" />
<p class="time"><?=$added_on_formated?></p>
<h3><a href="<?=$url?>"><?=$name?></a></h3>

<p>from <a href="index.php?comic=<?=$comic_id?>"><?=$comic_name?></a></p>

<?php
if(!isset($type)) $type = $users_subscribtions[$comic_id]['type'];

if($type == 'embedded' and $contents) print $contents;
else { ?>
<img src="<?=$image_url?>" alt="<?=$name?>" /><br />
<?=$contents?>
<?php } ?>

</div>
<?php }
}
?>