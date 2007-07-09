<?php
/**
 * Create group OR edit group settings.
 *
 * @copyright &copy; 2006 The Open University
 * @author N.D.Freear AT open.ac.uk
 * @author J.White AT open.ac.uk 
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package groups
 */
/// include libraries
require_once('../config.php');
require_once($CFG->libdir.'/moodlelib.php');
require_once($CFG->libdir.'/uploadlib.php');
require_once('lib.php');
require_once('edit_form.php');

/// get url variables
$courseid    = required_param('courseid', PARAM_INT);
$id          = optional_param('id', false, PARAM_INT);         
$groupingid  = optional_param('grouping', false, PARAM_INT);
$newgrouping = optional_param('newgrouping', false, PARAM_INT);
$delete      = optional_param('delete', 0, PARAM_BOOL);
$confirm     = optional_param('confirm', 0, PARAM_BOOL);

if (empty($CFG->enablegroupings)) {
    // NO GROUPINGS YET!
    $groupingid = GROUP_NOT_IN_GROUPING;
}

/// Course must be valid 
if (!$course = get_record('course', 'id', $courseid)) {
    error('Course ID was incorrect');
}

/// Delete action should not be called without a group id
if ($delete && !$id) {
    error(get_string('errorinvalidgroup'));
}

if ($delete && !$confirm) {
    print_header(get_string('deleteselectedgroup', 'group'), get_string('deleteselectedgroup', 'group'));
    $optionsyes = array('id'=>$id, 'delete'=>1, 'courseid'=>$courseid, 'sesskey'=>sesskey(), 'confirm'=>1);
    $optionsno  = array('id'=>$courseid);
    if (!$group = get_record('groups', 'id', $id)) {
        error('Group ID was incorrect');
    } 
    notice_yesno(get_string('deletegroupconfirm', 'group', $group->name), 'edit.php', 'index.php', $optionsyes, $optionsno, 'post', 'get');
    print_footer();
    die;
}

/// basic access control checks
if ($id) {
    if (!$group = get_record('groups', 'id', $id)) {
        error('Group ID was incorrect');
    } 
    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    require_capability('moodle/course:managegroups', $context);
    
    // If group given but no groupingid, retrieve grouping id
    if (empty($groupingid)) {
        $groupings = groups_get_groupings_for_group($id);
        if (empty($groupings)) {
            $groupingid = -1;
        } else {
            $groupingid = $groupings[0];
        }
    } 
}

/// First create the form
$editform = new group_edit_form('edit.php', compact('group', 'groupingid', 'newgrouping', 'group', 'courseid'));

/// Override defaults if group is set
if (!empty($group)) {
    $editform->set_data($group);
}

// Process delete action
if ($delete) {
    if (!confirm_sesskey()) {
        error('Sesskey error');
    }
    if (groups_delete_group($id)) {
        // MDL-9983
        events_trigger('group_deleted', $id);
        redirect(groups_home_url($course->id, null, $groupingid, false));
    } else {
        print_error('erroreditgroup', 'group', groups_home_url($course->id));
    }
}

$error = null;

if ($editform->is_cancelled()) {
    redirect(groups_home_url($courseid, $id, $groupingid, false));
} elseif ($data = $editform->get_data()) {
    $success = true;
    // preprocess data
    if (empty($group)) { // New group
        // First check if this group name doesn't already exist
        if (groups_group_name_exists($courseid, $data->name)) {
            $error = get_string('groupnameexists', 'group', $data->name);
            $success = false;
        } elseif (!$id = groups_create_group($course->id, $data)) {
            print_error('erroreditgroup');
        } else {
            $success = (bool)$id;
            $data->id = $id;
            if ($groupingid) {
                $success = $success && groups_add_group_to_grouping($id, $groupingid);
            }
            // MDL-9983
            if ($success) {
                events_trigger('group_created', $data);
            }
        }      
    } elseif ($groupingid != $newgrouping) { // Moving group to new grouping
        $success = $success && groups_remove_group_from_grouping($id, $groupingid);
        $success = $success && groups_add_group_to_grouping($id, $newgrouping);
    } else { // Updating group
        $group = groups_get_group($data->id);
        if (groups_group_name_exists($courseid, $data->name) && $group->name != $data->name) {
            $error = get_string('groupnameexists', 'group', $data->name);
            $success = false;
        } elseif (!groups_update_group($data, $course->id)) {
            print_error('groupnotupdated');
        }
        // MDL-9983
        if ($success) {
            events_trigger('group_updated', $data);
        }
    }
    // Handle file upload
    if ($success) {
        require_once("$CFG->libdir/gdlib.php");
        if (save_profile_image($id, $editform->_upload_manager, 'groups')) {
            $data->picture = 1;
            $success = $success && groups_update_group($data, $course->id); 
        } 
    }

    if ($success) {
        redirect(groups_home_url($course->id, $id, $groupingid, false));
    } elseif (empty($error)) {
        print_error('erroreditgroup', 'group', groups_home_url($course->id));
    }
} 
$strgroups = get_string('groups');
$strparticipants = get_string('participants');

if ($id) {
    $strheading = get_string('editgroupsettings', 'group');
} else {
    $strheading = get_string('creategroup', 'group');
}
print_header("$course->shortname: ". $strheading,
             $course->fullname, 
             "<a href=\"$CFG->wwwroot/course/view.php?id=$courseid\">$course->shortname</a> ".
             "-> <a href=\"$CFG->wwwroot/user/index.php?id=$courseid\">$strparticipants</a> ".
             '-> <a href="' .format_string(groups_home_url($courseid, $id, $groupingid, false)) . "\">$strgroups</a>".
             "-> $strheading", '', '', true, '', user_login_string($course, $USER));

print_heading($strheading);

if ($error) {
    notify($error);
}

echo '<div id="grouppicture">';
if ($id) {
    print_group_picture($group, $course->id);
}
echo '</div>';
$editform->display();
print_footer($course);
?>
