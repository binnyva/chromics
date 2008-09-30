<?php
require('../common.php');
include('_form.php');

$data = array();
if(isset($QUERY['name']) and $QUERY['name']) {
	if($id = $Comic->create($QUERY['name'], $QUERY['feed'], $QUERY['url'], $QUERY['description'], $QUERY['type'], $PARAM['fetch_regexp'], $PARAM['title_match_regexp'])) {
		$Comic->subscribe($id);
		$QUERY['success'] = "Comic '$QUERY[name]' added and subscribed to.";
	}
}

render();
