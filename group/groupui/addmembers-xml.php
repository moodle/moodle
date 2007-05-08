<?php
/**********************************************
 * Adds users to a group
 **********************************************/

require_once('../../config.php');
require_once('../lib/lib.php');

@header('Content-Type: text/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="utf-8"?>';
echo '<groupsresponse>';

$courseid = required_param('courseid', PARAM_INT);
$groupid  = required_param('groupid', PARAM_INT);
$users    = required_param('users', PARAM_SEQUENCE);

require_login($courseid);

if (confirm_sesskey() and isteacheredit($courseid)) {
    $userids = explode(',', $users); 

    if ($userids != false) {
        foreach($userids as $userid) {
            $useradded = groups_add_member($groupid, $userid);
            if (!$useradded) {
                echo '<error>Failed to add user $userid to group</error>';
            }
        }
    }
}

echo '</groupsresponse>';
?>
