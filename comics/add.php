<?php
require('../common.php');
$html = new HTML;

$types = array('linked'=>'Linked','embedded'=>'Embedded', );

if(isset($QUERY['name']) and $QUERY['name']) {
	if($id = $Comic->create($QUERY['name'], $QUERY['feed'], $QUERY['url'], $QUERY['description'], $QUERY['type'], $QUERY['fetch_regexp'], $QUERY['title_match_regexp'])) {
		$Comic->subscribe($id);
		$QUERY['success'] = "Comic '$QUERY[name]' added and subscribed to.";
	}
}
render();
