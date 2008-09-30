<?php
include('../common.php');

if(isset($QUERY['id']) and is_numeric($QUERY['id'])) {
	$Comic->remove($QUERY['id']);

	showMessage("Comic deleted successfully",'index.php');
}
