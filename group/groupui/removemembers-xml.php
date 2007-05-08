<?php
/**********************************************
 * Takes a groupid and comma-separated list of 
 * userids, and removes each of those userids 
 * from the specified group
 **********************************************/

require_once('../../config.php');
require_once('../lib/lib.php');

@header('Content-Type: text/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="utf-8"?>';
echo '<groupsresponse>';

$groupid  = required_param('groupid', PARAM_INT);
$users    = required_param('users', PARAM_SEQUENCE);
$courseid = required_param('courseid', PARAM_INT);

require_login($courseid);

if (confirm_sesskey() and isteacheredit($courseid)) {
    // Change the comma-separated string of the userids into an array of the userids
    $userids = explode(',', $users); 
    if ($userids != false) {
        // Remove each user in turn from the group. 
        foreach($userids as $userid) {
            $useradded = groups_remove_member($groupid, $userid);
            if (!$useradded) {
                echo "<error>Failed to adduser $userid</error>";
            }
        }
    }
}


echo '</groupsresponse>';

?>
