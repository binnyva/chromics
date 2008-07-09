<h1>Add Comic</h1>

<form action="" method="post">
<?php $html->buildInput('feed', 'Comic Feed', 'text', ''); ?>
<label>&nbsp;</label><input type="button" id="fetch_details" value="Fetch Details" /><br />

<div id="details">
<?php $html->buildInput('name', 'Title', 'text', ''); ?>
<?php $html->buildInput('url', 'Site URL', 'text', ''); ?>
<?php $html->buildInput('description', 'Description', 'textarea', ''); ?>
<?php $html->buildInput('fetch_regexp', 'Image URL Regexp', 'text', ''); ?>
<?php $html->buildInput('title_match_regexp', 'Title Regexp', 'text', ''); ?>
<label for='type'>Type</label>
<?php $html->buildDropDownArray($types, "type"); ?><br />

<label>&nbsp;</label><input type="submit" name="action" value="Add" />
</div>

</form>