<?php showComicPager(); ?>

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
