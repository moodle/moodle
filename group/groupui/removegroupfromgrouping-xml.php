<?php
/**********************************************
 * Removes a specified group from a specified grouping
 * (but does not delete the group)
 **********************************************/

require_once('../../config.php');
require_once('../lib/lib.php');

@header('Content-Type: text/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="utf-8"?>';
echo '<groupsresponse>';

$groupid    = required_param('groupid', PARAM_INT);
$groupingid = required_param('groupingid', PARAM_INT);
$courseid   = required_param('courseid', PARAM_INT);

require_login($courseid);

if (confirm_sesskey() and isteacheredit($courseid)) {
    $groupingremoved = groups_remove_group_from_grouping($groupid, $groupingid);
    if (!$groupingremoved) {
        echo '<error>Failed to remove group from grouping</error>';
    }
}

echo '</groupsresponse>';
?>
