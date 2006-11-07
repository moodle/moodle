<?php
/**********************************************
 * Deletes a group
 **********************************************/
header("Content-Type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
echo '<groupsresponse>';

require_once('../../config.php');
require_once('../lib/lib.php');

$groupid = required_param('groupid', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);

require_login($courseid);
	
if (confirm_sesskey() and isteacheredit($courseid)) {
	$groupremoved = groups_delete_group($groupid);
	
	if ($groupremoved == false) {
		echo "<error>Could not delete group $groupid</error>";
	} 
}

echo '</groupsresponse>';
?>
