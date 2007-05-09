<?php
/**********************************************
 * Fetches the settings of a grouping and returns 
 * them in an XML format
 **********************************************/

require_once('../../config.php');
require_once('../lib/lib.php');

@header('Content-Type: text/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="utf-8"?>';
echo '<groupsresponse>';

$courseid   = required_param('courseid', PARAM_INT);
$groupingid = required_param('groupingid', PARAM_INT);

require_login($courseid);

if (confirm_sesskey() and isteacheredit($courseid)) {
    $groupingsettings = groups_get_grouping_settings($groupingid);
    echo '<name>'.$groupingsettings->name.'</name>';

    if ($groupingsettings->viewowngroup) {
        echo '<viewowngroup>true</viewowngroup>';
    } else {
        echo '<viewowngroup>false</viewowngroup>';
    }

    if ($groupingsettings->viewallgroupsmembers) {
        echo '<viewallgroupsmembers>true</viewallgroupsmembers>';
    } else {
        echo '<viewallgroupsmembers>false</viewallgroupsmembers>';
    }

    if ($groupingsettings->viewallgroupsactivities) {
        echo '<viewallgroupsactivities>true</viewallgroupsactivities>';
    } else {
        echo '<viewallgroupsactivities>false</viewallgroupsactivities>';
    }

    if ($groupingsettings->teachersgroupmark) {
        echo '<teachersgroupmark>true</teachersgroupmark>';
    } else {
        echo '<teachersgroupmark>false</teachersgroupmark>';
    }

    if ($groupingsettings->teachersgroupview) {
        echo '<teachersgroupview>true</teachersgroupview>';
    } else {
        echo '<teachersgroupview>false</teachersgroupview>';
    }

    if ($groupingsettings->teachersoverride) {
        echo '<teachersoverride>true</teachersoverride>';
    } else {
        echo '<teachersoverride>false</teachersoverride>';
    }

}

echo '</groupsresponse>';
?>
