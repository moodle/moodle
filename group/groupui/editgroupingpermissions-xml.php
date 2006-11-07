<?php
/**********************************************
 * Adds a new grouping
 **********************************************/
 
header("Content-Type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
echo '<groupsresponse>';

include_once('../../../config.php');
include('../lib/lib.php');
$groupingid = required_param('groupingid');
$courseid = required_param('courseid', PARAM_INT);

$groupingsettings->viewowngroup = required_param('viewowngroup');
$groupingsettings->viewallgroupsmembers = required_param('viewallgroupsmembers');
$groupingsettings->viewallgroupsactivities = required_param('viewallgroupsactivities');
$groupingsettings->teachersgroupmark = required_param('teachersgroupmark');
$groupingsettings->teachersgroupview = required_param('teachersgroupview');
$groupingsettings->teachersoverride = required_param('teachersoverride');

require_login($courseid);
	
if (confirm_sesskey() and isteacheredit($courseid)) {
	$groupingid = groups_set_grouping_settings($groupingid, $groupingsettings);
}

echo '</groupsresponse>';
?>