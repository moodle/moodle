<?php
/**
 * Create grouping OR edit grouping settings.
 *
 * @copyright &copy; 2006 The Open University
 * @author N.D.Freear AT open.ac.uk
 * @author J.White AT open.ac.uk 
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package groups
 */
require_once('../config.php');
require_once('lib.php');
require_once($CFG->libdir.'/moodlelib.php');
require_once('grouping_edit_form.php');

$courseid   = required_param('courseid', PARAM_INT);         
$id = optional_param('id', false, PARAM_INT);

$delete = optional_param('delete', false, PARAM_BOOL);

// Get the course information so we can print the header and
// check the course id is valid
$course = groups_get_course_info($courseid);
if (! $course) {
    $success = false;
    print_error('invalidcourse'); //'The course ID is invalid'
}
if (GROUP_NOT_IN_GROUPING == $id) {
    print_error('errornotingroupingedit', 'group', groups_home_url($courseid), get_string('notingrouping', 'group'));
}

/// basic access control checks
if ($id) {
    if (!$grouping = get_record('groups_groupings', 'id', $id)) {
        error('Grouping ID was incorrect');
    } 
    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    require_capability('moodle/course:managegroups', $context);
}
    
/// First create the form
$editform = new grouping_edit_form('grouping.php', compact('grouping', 'courseid'));

/// Override defaults if group is set
if (!empty($grouping)) {
    $editform->set_data($grouping);
}

if ($editform->is_cancelled()) {
    redirect(groups_home_url($courseid, false, $id, false));
} elseif ($data = $editform->get_data()) {
    $success = true;
    
    // preprocess data
    if ($delete) {
        if ($success = groups_delete_grouping($id)) {
            redirect(groups_home_url($course->id));
        } else {
            print_error('erroreditgrouping', 'group', groups_home_url($course->id));
        }
    } elseif (empty($grouping)) { // New grouping
        if (!$id = groups_create_grouping($course->id, $data)) {
            print_error('erroreditgrouping');
        } else {
            $success = (bool)$id;
            $data->id = $id;
        }
    } else { // Updating grouping
        if (!groups_update_grouping($data, $course->id)) {
            print_error('groupingnotupdated');
        }
    }
    
    if ($success) {
        redirect(groups_home_url($courseid, false, $id, false));
    } else {
        print_error('erroreditgrouping', 'group', groups_home_url($courseid));
    }

} else { // Prepare and output form
    $strgroups = get_string('groups');
    $strparticipants = get_string('participants');
    
    if ($id) {
        $strheading = get_string('editgroupingsettings', 'group');
    } else {
        $strheading = get_string('creategrouping', 'group');
    }
    print_header("$course->shortname: ". $strheading,
                 $course->fullname, 
                 "<a href=\"$CFG->wwwroot/course/view.php?id=$courseid\">$course->shortname</a> ".
                 "-> <a href=\"$CFG->wwwroot/user/index.php?id=$courseid\">$strparticipants</a> ".
                 '-> <a href="' .format_string(groups_home_url($courseid, false, $id, false)) . "\">$strgroups</a>".
                 "-> $strheading", '', '', true, '', user_login_string($course, $USER));
    print_heading($strheading);
    $editform->display();
    print_footer($course);
}
?>
