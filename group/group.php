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

require_once('../config.php');
require_once('lib.php');
require_once('group_form.php');

/// get url variables
$courseid    = optional_param('courseid', PARAM_INT);
$id          = optional_param('id', 0, PARAM_INT);
$delete      = optional_param('delete', 0, PARAM_BOOL);
$confirm     = optional_param('confirm', 0, PARAM_BOOL);

if ($id) {
    if (!$group = get_record('groups', 'id', $id)) {
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
    $group = new object();
    $group->courseid = $course->id;
}

$context = get_context_instance(CONTEXT_COURSE, $course->id);
require_capability('moodle/course:managegroups', $context);

$returnurl = $CFG->wwwroot.'/group/index.php?id='.$course->id.'&amp;group='.$id;

if ($id and $delete) {
    if (!$confirm) {
        print_header(get_string('deleteselectedgroup', 'group'), get_string('deleteselectedgroup', 'group'));
        $optionsyes = array('id'=>$id, 'delete'=>1, 'courseid'=>$courseid, 'sesskey'=>sesskey(), 'confirm'=>1);
        $optionsno  = array('id'=>$courseid);
        notice_yesno(get_string('deletegroupconfirm', 'group', $group->name), 'group.php', 'index.php', $optionsyes, $optionsno, 'get', 'get');
        print_footer();
        die;

    } else if (confirm_sesskey()){
        if (groups_delete_group($id)) {
            // MDL-9983
            $eventdata = new object();
            $eventdata->group = $id;
            $eventdata->course = $courseid;
            events_trigger('group_deleted', $eventdata);
            redirect('index.php?id='.$course->id);
        } else {
            print_error('erroreditgroup', 'group', $returnurl);
        }
    }
}

/// First create the form
$editform = new group_edit_form();
$editform->set_data($group);

if ($editform->is_cancelled()) {
    redirect($returnurl);

} elseif ($data = $editform->get_data()) {

    $result = false;
    if ($data->id) {
        if (!update_record('groups', $data)) {
            error('Error updating group');
        }
    } else {
        if (!$data->id = insert_record('groups', $data)) {
            error('Error updating group');
        }
    }

    //update image
    require_once("$CFG->libdir/gdlib.php");
    if (save_profile_image($data->id, $editform->_upload_manager, 'groups')) {
        $data->picture = 1;
        update_record('groups', $data);
    }

    redirect($returnurl);
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
             "-> <a href=\"$CFG->wwwroot/group/index.php?id=$courseid\">$strgroups</a>".
             "-> $strheading", '', '', true, '', user_login_string($course, $USER));

print_heading($strheading);

echo '<div id="grouppicture">';
if ($id) {
    print_group_picture($group, $course->id);
}
echo '</div>';
$editform->display();
print_footer($course);
?>
