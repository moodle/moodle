<?php
/**********************************************
 * Adds a new group to a grouping
 **********************************************/

require_once('../../config.php');
require_once('../lib/lib.php');

@header('Content-Type: text/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="utf-8"?>';
echo '<groupsresponse>';

$courseid   = required_param('courseid', PARAM_INT);
$groupingid = required_param('groupingid', PARAM_INT);

$groupsettings->name        = required_param('groupname', PARAM_ALPHANUM);
$groupsettings->description = required_param('description', PARAM_ALPHANUM);
$groupsettings->enrolmentkey= required_param('enrolmentkey', PARAM_ALPHANUM);
$groupsettings->hidepicture = optional_param('hidepicture', PARAM_INT);


require_login($courseid);

if (confirm_sesskey() and isteacheredit($courseid)) {
    $groupid = groups_create_group($courseid, $groupsettings);

    if (!$groupid) {
        echo '<error>Failed to create group</error>';
    } else {
        $groupadded = groups_add_group_to_grouping($groupid, $groupingid);

        if (!$groupadded) {
            echo '<error>Failed to add group to grouping</error>';
        } else {
            // Upload a picture file if there was one - note that we don't remove any previous icons
            require_once($CFG->libdir.'/uploadlib.php');
            $um = new upload_manager('newgroupicon', false, false, null, false, 0, true, true);
            if ($um->preprocess_files()) {
                require_once("$CFG->libdir/gdlib.php");
                if (save_profile_image($groupid, $um, 'groups')) {
                    $groupsettings->picture = 1;
                    $infoset = groups_set_group_settings($groupid, $groupsettings);
                    if (!$infoset) {
                        echo '<error>Failed to save the fact that the group image was uploaded</error>';
                    }
                } 
            }

            echo '<groupid>'.$groupid.'</groupid>';
        }
    }
}

echo '</groupsresponse>';
?>
