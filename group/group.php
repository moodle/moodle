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
    if (!$group = $DB->get_record('groups', array('id'=>$id))) {
        print_error('invalidgroupid');
    }
    $group->description = clean_text($group->description);
    if (empty($courseid)) {
        $courseid = $group->courseid;

    } else if ($courseid != $group->courseid) {
        print_error('invalidcourseid');
    }

    if (!$course = $DB->get_record('course', array('id'=>$courseid))) {
        print_error('invalidcourseid');
    }

} else {
    if (!$course = $DB->get_record('course', array('id'=>$courseid))) {
        print_error('invalidcourseid');
    }
    $group = new object();
    $group->courseid = $course->id;
}

if ($id !== 0) {
    $PAGE->set_url(new moodle_url($CFG->wwwroot.'/group/group.php', array('id'=>$id)));
} else {
    $PAGE->set_url(new moodle_url($CFG->wwwroot.'/group/group.php', array('courseid'=>$courseid)));
}

require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $course->id);
require_capability('moodle/course:managegroups', $context);

$returnurl = $CFG->wwwroot.'/group/index.php?id='.$course->id.'&amp;group='.$id;

if ($id and $delete) {
    if (!$confirm) {
        $PAGE->set_title(get_string('deleteselectedgroup', 'group'));
        $PAGE->set_heading(get_string('deleteselectedgroup', 'group'));
        echo $OUTPUT->header();
        $optionsyes = array('id'=>$id, 'delete'=>1, 'courseid'=>$courseid, 'sesskey'=>sesskey(), 'confirm'=>1);
        $optionsno  = array('id'=>$courseid);
        $formcontinue = html_form::make_button('group.php', $optionsyes, get_string('yes'), 'get');
        $formcancel = html_form::make_button($baseurl, $optionsno, get_string('no'), 'get');
        echo $OUTPUT->confirm(get_string('deletegroupconfirm', 'group', $group->name), $formcontinue, $formcancel);
        echo $OUTPUT->footer();
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
        groups_update_group($data, $editform);
    } else {
        $id = groups_create_group($data, $editform);
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

$PAGE->navbar->add($strparticipants, new moodle_url($CFG->wwwroot.'/user/index.php', array('id'=>$courseid)));
$PAGE->navbar->add($strgroups, new moodle_url($CFG->wwwroot.'/group/index.php', array('id'=>$courseid)));
$PAGE->navbar->add($strheading);

/// Print header
$PAGE->set_title($strgroups);
$PAGE->set_heading(': '.$strgroups);
echo $OUTPUT->header();
echo '<div id="grouppicture">';
if ($id) {
    print_group_picture($group, $course->id);
}
echo '</div>';
$editform->display();
echo $OUTPUT->footer();
