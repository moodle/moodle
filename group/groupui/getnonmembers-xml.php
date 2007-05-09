<?php
/**********************************************
 * Gets the users registered for a course that
 * don't belong to a specified group and prints
 * their detailsin an XML format. 
 **********************************************/

require_once('../../config.php');
require_once('../lib/lib.php');

@header('Content-Type: text/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="utf-8"?>';
echo '<groupsresponse>';

$groupid    = required_param('groupid', PARAM_INT);
$groupingid = required_param('groupingid', PARAM_INT);
$courseid   = required_param('courseid', PARAM_INT);
$showall    = required_param('showall', PARAM_INT);

require_login($courseid);

if (confirm_sesskey() and isteacheredit($courseid)) {
    echo "$groupingid $groupid";
    if ($showall == 0) {
        $userids = groups_get_users_not_in_any_group_in_grouping($courseid,$groupingid, $groupid);
    } else {
        $userids = groups_get_users_not_in_group($courseid, $groupid);
    }

    if ($userids != false) {
        // Put the groupings into a hash and sorts them
        foreach($userids as $userid) {
            $listmembers[$userid] = groups_get_user_displayname($userid, $courseid);       
        }
        natcasesort($listmembers);


        // Print out the XML 
        echo "<option>";
        foreach($listmembers as $value=>$name) {
            echo "<name>$name</name>";
            echo "<value>$value</value>";
        }
        echo "</option>";
    }
}

echo '</groupsresponse>';
?>
