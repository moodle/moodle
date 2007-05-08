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

$courseid = required_param('courseid', PARAM_INT);
$groupid  = required_param('groupid', PARAM_INT);

require_login($courseid);

if (confirm_sesskey() and isteacheredit($courseid)) {

    $groupsettings = groups_get_group_settings($groupid);
    if (!$groupsettings) {
        echo '<error>Failed to get group details</error>';
    } else {
        echo '<name>'.$groupsettings->name.'</name>';
        echo '<description>'.$groupsettings->description.'</description>';
        echo '<enrolmentkey>'.$groupsettings->enrolmentkey.'</enrolmentkey>';
        echo '<hidepicture>'.$groupsettings->hidepicture.'</hidepicture>';
        echo '<picture>'.$groupsettings->picture.'</picture>';
        echo '<lang>'.$groupinkfo->lang.'</lang>';
        echo '<theme>'.$groupsettings->theme.'</theme>';
    }
}

echo '</groupsresponse>';
?>
