<?php
require('../common.php');

$has_read = $sql->getOne("SELECT id FROM StripUser WHERE strip_id=$QUERY[strip] AND user_id=$_SESSION[user_id]");

if(!$has_read and $QUERY['action'] == 'mark_as_read') { //If the user has not already read it, 
	$sql->execQuery("INSERT INTO StripUser(user_id, strip_id,read_on) VALUES('$_SESSION[user_id]', '$QUERY[strip]', NOW())"); // Mark it as read.

} else if($has_read and $QUERY['action'] == 'mark_as_unread') {
	$sql->execQuery("DELETE FROM StripUser WHERE id=$has_read"); // Remove the read mark
}

print '{"success": true, "failure": false}';