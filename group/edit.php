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
$id          = optional_param('id', false, PARAM_INT);         
$groupingid  = optional_param('grouping', false, PARAM_INT);
$newgrouping = optional_param('newgrouping', false, PARAM_INT);
$courseid    = required_param('courseid', PARAM_INT);

$delete = optional_param('delete', false, PARAM_BOOL);

/// Course must be valid 
if (!$course = get_record('course', 'id', $courseid)) {
    error('Course ID was incorrect');
}

/// Delete action should not be called without a group id
if ($delete && !$id) {
    error(get_string('errorinvalidgroup'));
}

/// basic access control checks
if ($id) {
    if (!$group = get_record('groups', 'id', $id)) {
        error('Group ID was incorrect');
    } 
    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    require_capability('moodle/course:managegroups', $context);
}

/// First create the form
$editform = new group_edit_form('edit.php', compact('group', 'groupingid', 'newgrouping', 'group', 'courseid'));

/// Override defaults if group is set
if (!empty($group)) {
    $editform->set_data($group);
}

if ($editform->is_cancelled()) {
    redirect(groups_home_url($courseid, $id, $groupingid, false));
} elseif ($data = $editform->get_data()) {
    $success = true;
        
    // preprocess data
    if ($delete) {
        if ($success = groups_delete_group($id)) {
            redirect(groups_home_url($course->id, null, $groupingid, false));
        } else {
            print_error('erroreditgroup', 'group', groups_home_url($course->id));
        }
    } elseif (empty($group)) { // New group
        if (!$group = groups_create_group($course->id)) {
            print_error('erroreditgroup');
        } else {
            $success = (bool)$id = groups_create_group($course->id);
        }
    } elseif ($groupingid != $newgrouping) { // Moving group to new grouping
        if ($groupingid != GROUP_NOT_IN_GROUPING) {
            $success = $success && groups_remove_group_from_grouping($id, $groupingid);
        } 
    } else { // Updating group
        if (!groups_update_group($data, $course->id)) {
            print_error('groupnotupdated');
        }
    }
    
    // Handle file upload
    if ($success) {
        if ($editform->save_files()) {
        } else {
            $success = false;
        }
    }
    $success = $success && groups_set_group_settings($id, $groupsettings);

    if ($success) {
        redirect(groups_home_url($course->id, $id, $groupingid, false));
    } else {
        print_error('erroreditgroup', 'group', groups_home_url($course->id));
    }
} else { // Prepare and output form
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
                 "-> $strgroups", '', '', true, '', user_login_string($course, $USER));
    
    print_heading($strheading);
    echo '<div id="grouppicture">';
    if ($id) {
        print_group_picture($group, $course->id);
    }
    echo '</div>';
    $editform->display();
    print_footer($course);
}
?>
