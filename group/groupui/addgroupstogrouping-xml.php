<?php
/**
 * Adds an existing group to a grouping.
 *
 * @copyright &copy; 2006 The Open University
 * @author J.White AT open.ac.uk 
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package groups
 */
require_once('../../config.php');
require_once('../lib/lib.php');

@header('Content-Type: text/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="utf-8"?>';
echo '<groupsresponse>';

$courseid   = required_param('courseid', PARAM_INT);
$groupingid = required_param('groupingid', PARAM_INT);
$groups     = required_param('groups', PARAM_SEQUENCE); //TODO: check.

require_login($courseid);

if (confirm_sesskey() and isteacheredit($courseid)) {
    $groupids = explode(',', $groups); 

    if ($groupids != false) {
        foreach($groupids as $groupid) {
            $groupadded = groups_add_group_to_grouping($groupid, $groupingid);
            if (!$groupadded) {
                echo '<error>Failed to add group $groupid to grouping</error>';
            }
        } 
    }
}

echo '</groupsresponse>';
?>
