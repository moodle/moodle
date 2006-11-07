<?php
/**********************************************
 * Adds an existing group to a grouping
 **********************************************/
 
header("Content-Type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
echo '<groupsresponse>';

include_once('../../../config.php');
include('../lib/lib.php');

$groupingid = required_param('groupingid', PARAM_INT);
$groups = required_param('groups');
$courseid = required_param('courseid', PARAM_INT);

require_login($courseid);
	
if (confirm_sesskey() and isteacheredit($courseid)) {
	$groupids = explode(',', $groups); 
	
	if ($groupids != false) {
		foreach($groupids as $groupid) {
			$groupadded = groups_add_group_to_grouping($groupid, $groupingid);
			if (!$groupadded) {
				echo '<error>Failed to add group $groupid to grouping</error>';
			}
		} 
	}
}

echo '</groupsresponse>';
?>
