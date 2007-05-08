<?php
/**********************************************
 * Adds a new grouping
 **********************************************/

require_once('../../config.php');
require_once('../lib/lib.php');

@header('Content-Type: text/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="utf-8"?>';
echo '<groupsresponse>';

$groupingid = required_param('groupingid', PARAM_INT);
$courseid   = required_param('courseid', PARAM_INT);

$groupingsettings->viewowngroup = required_param('viewowngroup', PARAM_INT); //TODO: PARAM_BOOL ??
$groupingsettings->viewallgroupsmembers = required_param('viewallgroupsmembers', PARAM_INT);
$groupingsettings->viewallgroupsactivities = required_param('viewallgroupsactivities', PARAM_INT);
$groupingsettings->teachersgroupmark = required_param('teachersgroupmark', PARAM_INT);
$groupingsettings->teachersgroupview = required_param('teachersgroupview', PARAM_INT);
$groupingsettings->teachersoverride = required_param('teachersoverride', PARAM_INT);

require_login($courseid);

if (confirm_sesskey() and isteacheredit($courseid)) {
    $groupingid = groups_set_grouping_settings($groupingid, $groupingsettings);
}

echo '</groupsresponse>';
?>
