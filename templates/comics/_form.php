<form action="" method="post" id="comic-form">

<?php
if($action == "Add") { // The fetch part is for new 
$html->buildInput('feed', 'Comic Feed', 'text', ''); ?>
<label>&nbsp;</label><input type="button" id="fetch_details" value="Fetch Details" /><br />

<?php 
} else {
$html->buildInput("feed", "Feed", "text", i($data,"feed"));
}

print "<div id='details'>";

$html->buildInput("name", "Title", "text", i($data,"name"));
$html->buildInput("url", "Site Url", "text", i($data,"url"));
$html->buildInput("description", "Description", "textarea", i($data,"description"));
$html->buildInput("type", "Type", "select", i($data,"type"), array("options"=>$type_list));
$html->buildInput("fetch_regexp", "Fetch Regexp", "text", i($data,"fetch_regexp"));
$html->buildInput("title_match_regexp", "Title Match Regexp", "text", i($data,"title_match_regexp"));
$html->buildInput("update_frequency", "Update Frequency in Days", "text", i($data,"update_frequency", 1));

if($action == "Edit") { ?>
<input type="hidden" name='id' value="<?=$data['id']?>" />
<?php } ?>
<input name="action" value="<?=$action?>" type='submit' />
</div>

</form> 
