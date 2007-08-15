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
require_once('grouping_form.php');

/// get url variables
$courseid    = optional_param('courseid', PARAM_INT);
$id          = optional_param('id', 0, PARAM_INT);
$delete      = optional_param('delete', 0, PARAM_BOOL);
$confirm     = optional_param('confirm', 0, PARAM_BOOL);

if ($id) {
    if (!$grouping = get_record('groupings', 'id', $id)) {
        error('Group ID was incorrect');
    }
    if (empty($courseid)) {
        $courseid = $group->courseid;

    } else if ($courseid != $group->courseid) {
        error('Course ID was incorrect');
    }

    if (!$course = get_record('course', 'id', $courseid)) {
        error('Course ID was incorrect');
    }

} else {
    if (!$course = get_record('course', 'id', $courseid)) {
        error('Course ID was incorrect');
    }
    $grouping = new object();
    $grouping->courseid = $course->id;
}

require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $course->id);
require_capability('moodle/course:managegroups', $context);

$returnurl = $CFG->wwwroot.'/group/index.php?id='.$course->id;


if ($id and $delete) {
    if (!$confirm) {
        print_header(get_string('deleteselectedgrouping', 'group'), get_string('deleteselectedgroup', 'group'));
        $optionsyes = array('id'=>$id, 'delete'=>1, 'courseid'=>$courseid, 'sesskey'=>sesskey(), 'confirm'=>1);
        $optionsno  = array('id'=>$courseid);
        notice_yesno(get_string('deletegroupingconfirm', 'group', $group->name), 'grouping.php', 'index.php', $optionsyes, $optionsno, 'get', 'get');
        print_footer();
        die;

    } else if (confirm_sesskey()){
        if (groups_delete_grouping($id)) {
            // MDL-9983
            $eventdata = new object();
            $eventdata->group = $id;
            $eventdata->course = $courseid;
            events_trigger('grouping_deleted', $eventdata);
            redirect('index.php?id='.$course->id);
        } else {
            print_error('erroreditgrouping', 'group', $returnurl);
        }
    }
}

/// First create the form
$editform = new grouping_form();
$editform->set_data($grouping);

if ($editform->is_cancelled()) {
    redirect($returnurl);

} elseif ($data = $editform->get_data()) {
    $success = true;

    if ($data->id) {
        $data->timemodified = time();
        if (!update_record('groupings', $data)) {
            error('Error updating group');
        }

    } else {
        $data->timecreated = time();
        $data->timemodified = $data->timecreated;
        if (!$data->id = insert_record('groupings', $data)) {
            error('Error updating grouping');
        }
    }

    redirect($returnurl);

}

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
             "-> <a href=\"$returnurl\">$strgroups</a>".
             "-> $strheading", '', '', true, '', user_login_string($course, $USER));
print_heading($strheading);
$editform->display();
print_footer($course);

?>
