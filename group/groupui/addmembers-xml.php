<?php
/**********************************************
 * Adds users to a group
 **********************************************/
 
header("Content-Type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
echo '<groupsresponse>';

include_once('../../../config.php');
include('../lib/lib.php');

$groupid = required_param('groupid', PARAM_INT);
$users = required_param('users');
$courseid = required_param('courseid', PARAM_INT);

require_login($courseid);
	
if (confirm_sesskey() and isteacheredit($courseid)) {
	$userids = explode(',', $users); 
	
	if ($userids != false) {
		foreach($userids as $userid) {
			$useradded = groups_add_member($userid, $groupid);
			if (!$useradded) {
				echo '<error>Failed to add user $userid to group</error>';
			}
		}
	}
}

echo '</groupsresponse>';
?>