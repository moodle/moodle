<?php
/**********************************************
 * Adds a new grouping
 **********************************************/
 
header("Content-Type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
echo '<groupsresponse>';

require_once('../../config.php');
require_once('../lib/lib.php');
$groupingid = required_param('groupingid');
$courseid = required_param('courseid', PARAM_INT);

$groupingsettings->name =required_param('groupingname');
$groupingsettings->description = required_param('description');

require_login($courseid);
	
if (confirm_sesskey() and isteacheredit($courseid)) {
	$groupingid = groups_set_grouping_settings($groupingid, $groupingsettings);
}

echo '</groupsresponse>';
?>