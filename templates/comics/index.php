<h1>Comics</h1>

<form action="" method="post">
<label for="search">Search</label> <input type="text" name="search" id="search" />
<input type="submit" value="Go" name="action" />
</form>

<table>
<tr><th>Comic</th><th>Subscibe</th><th>Site</th><th colspan="2">Actions</th></tr>
<?php
$row = 0;
foreach($comics as $comic) {
	$class = ($row++ % 2) ? 'even' : 'odd';
	$id = $comic['id'];
?>
<tr class="<?=$class?>">
<td><a href="../index.php?comic=<?=$comic['id']?>&amp;order=asc"><?=$comic['name']?></a></td>

<td><?php if($comic['subscribed']) { ?>
<a href="<?=getLink("subscribe.php", array("action"=>"unsubscribe","comic"=>$comic['id']), true) ?>" class="with-icon ok ajaxify confirm" title="Unsubscribe from '<?=$comic['name']?>'">Subscibed</a>
<?php } else { ?>
<a href="<?=getLink("subscribe.php", array("action"=>"subscribe",  "comic"=>$comic['id']), true) ?>" class="with-icon add ajaxify" title="Subscribe to '<?=$comic['name']?>'?">Subscibe?</a>
<?php } ?></td>

<td><a href="<?=$comic['url']?>" class="with-icon site">Site</a></td>

<td class="action"><a class="icon edit" href="edit.php?id=<?=$id?>">Edit</a></td>
<td class="action"><a class="icon delete confirm" title="Delete '<?=$comic['name']?>'" href="delete.php?id=<?=$id?>">Delete</a></td></tr>
<?php } ?>
</table>
<?php showPager(); ?><br />


<a class="icon new" href="new.php">New Comic</a>