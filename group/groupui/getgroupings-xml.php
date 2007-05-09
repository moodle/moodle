<?php
/**********************************************
 * Fetches the settings of the groupings for a course
 * and returns them in an XML format
 **********************************************/
 
require_once('../../config.php');
require_once('../lib/lib.php');

@header('Content-Type: text/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="utf-8"?>';
echo '<groupsresponse>';

$courseid = required_param('courseid', PARAM_INT);

require_login($courseid);

if (confirm_sesskey() and isteacheredit($courseid)) {
    $groupingids = groups_get_groupings($courseid);
    if ($groupingids != false) {
        // Put the groupings into a hash and sort them
        foreach($groupingids as $groupingid) {
            $listgroupings[$groupingid] = groups_get_grouping_displayname($groupingid);       
        }
        natcasesort($listgroupings);

        // Print out the XML 
        echo '<option>';
        foreach($listgroupings as $value=>$name) {
            echo "<name>$name</name>";
            echo "<value>$value</value>";
        }
        echo '</option>';
    }
}

echo '</groupsresponse>';
?>



