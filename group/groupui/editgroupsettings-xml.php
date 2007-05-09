<?php
/**********************************************
 * Adds a new grouping
 **********************************************/
 
require_once('../../config.php');
require_once('../lib/lib.php');

@header('Content-Type: text/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="utf-8"?>';
echo '<groupsresponse>';

$groupid     = required_param('groupid', PARAM_INT);
$groupname   = required_param('groupname', PARAM_ALPHANUM);
$description = required_param('description', PARAM_ALPHANUM);
$enrolmentkey= required_param('enrolmentkey', PARAM_ALPHANUM);
$hidepicture = required_param('hidepicture', PARAM_INT); //TODO: PARAM_BOOL ??
$courseid    = required_param('courseid', PARAM_INT);

require_login($courseid);

if (confirm_sesskey() and isteacheredit($courseid)) {
    $groupsettings->name = $groupname;
    $groupsettings->description = $description;
    $groupsettings->enrolmentkey = $enrolmentkey;
    $groupsettings->hidepicture = $hidepicture;

    // Upload the group icon if there is one - note that we don't remove any previous icons
    require_once($CFG->libdir.'/uploadlib.php');
    $um = new upload_manager('groupicon', false, false, null, false, 0, true, true);
    if ($um->preprocess_files()) {
        require_once("$CFG->libdir/gdlib.php");
        if (save_profile_image($groupid, $um, 'groups')) {
            $groupsettings->picture = 1;
        } else {
            echo '<error>Failed to save group image</error>';
        }
    } else {
        $groupsettings->picture = 0;
    }

    $infoset = groups_set_group_settings($groupid, $groupsettings);
    if (!$infoset) {
        echo "<error>Failed to set new group settings</error>";
    } 
}

echo '</groupsresponse>';
?>
