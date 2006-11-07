<?php
/**********************************************
 * Deletes a specified grouping
 **********************************************/
 
header("Content-Type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
echo '<groupsresponse>';

include_once("../../../config.php");
include("../lib/lib.php");

$groupingid = required_param('groupingid', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);

require_login($courseid);
	
if (confirm_sesskey() and isteacheredit($courseid)) {
	$groupingremoved = groups_delete_grouping($groupingid);
	if (!$groupingremoved) {
		echo '<error>Failed to delete grouping</error>';
	}
}

echo '</groupsresponse>';
?>
