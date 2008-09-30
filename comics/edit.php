<?php
include('../common.php');
include('_form.php');

if(isset($QUERY['action']) and $QUERY['action']=='Edit') {
	if($Comic->edit($QUERY['id'], $QUERY['name'], $QUERY['feed'], $QUERY['url'], $QUERY['description'], $QUERY['type'], $QUERY['fetch_regexp'], $QUERY['title_match_regexp'], $QUERY['last_downloaded_on'])) {
		showMessage("Comic updated successfully",'index.php?comic='.$QUERY['id']);
	}
} else {
	$data = $Comic->find($QUERY['id']);
	render();
}
