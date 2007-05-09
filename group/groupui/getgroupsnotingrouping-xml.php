<?php
/**********************************************
 * Gets the groups not in a grouping for a course
 * and returns them in an XML format
 **********************************************/

require_once('../lib/lib.php');
require_once('../../config.php');

@header('Content-Type: text/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="utf-8"?>';
echo '<groupsresponse>';

$groupingid = required_param('groupingid', PARAM_INT);
$courseid   = required_param('courseid', PARAM_INT);

require_login($courseid);

if (confirm_sesskey() and isteacheredit($courseid)) {
    $groupids = groups_get_groups_not_in_grouping($groupingid, $courseid);
    if ($groupids != false) {
        // Put the groupings into a hash and sort them
        foreach($groupids as $groupid) {
            $listgroups[$groupid] = groups_get_group_displayname($groupid);       
        }

        natcasesort($listgroups);

        // Print out the XML 
        echo "<option>";
        foreach($listgroups as $value=>$name) {
            echo "<name>$name</name>";
            echo "<value>$value</value>";
        }
        echo "</option>";
    }
}

echo '</groupsresponse>';
?>
