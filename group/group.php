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
$courseid = optional_param('courseid', 0, PARAM_INT);
$id       = optional_param('id', 0, PARAM_INT);
$delete   = optional_param('delete', 0, PARAM_BOOL);
$confirm  = optional_param('confirm', 0, PARAM_BOOL);

// This script used to support group delete, but that has been moved. In case
// anyone still links to it, let's redirect to the new script.
if($delete) {
    redirect('delete.php?courseid='.$courseid.'&groups='.$id);
}

if ($id) {
    if (!$group = get_record('groups', 'id', $id)) {
        error('Group ID was incorrect');
    }
    $group->description = clean_text($group->description);
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

require_login($course);
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
            redirect('index.php?id='.$course->id);
        } else {
            print_error('erroreditgroup', 'group', $returnurl);
        }
    }
}

/// First create the form
$editform = new group_form();
$editform->set_data($group);

if ($editform->is_cancelled()) {
    redirect($returnurl);

} elseif ($data = $editform->get_data()) {

    if ($data->id) {
        if (!groups_update_group($data, $editform->_upload_manager)) {
            error('Error updating group');
        }
    } else {
        if (!$id = groups_create_group($data, $editform->_upload_manager)) {
            error('Error creating group');
        }
        $returnurl = $CFG->wwwroot.'/group/index.php?id='.$course->id.'&amp;group='.$id;
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


$navlinks = array(array('name'=>$strparticipants, 'link'=>$CFG->wwwroot.'/user/index.php?id='.$courseid, 'type'=>'misc'),
                  array('name'=>$strgroups, 'link'=>$CFG->wwwroot.'/group/index.php?id='.$courseid, 'type'=>'misc'),
                  array('name'=>$strheading, 'link'=>'', 'type'=>'misc'));
$navigation = build_navigation($navlinks);

/// Print header
print_header_simple($strgroups, ': '.$strgroups, $navigation, '', '', true, '', navmenu($course));

echo '<div id="grouppicture">';
if ($id) {
    print_group_picture($group, $course->id);
}
echo '</div>';
$editform->display();
print_footer($course);
?>
