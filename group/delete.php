<?php
/**
 * Delete group
 *
 * @copyright &copy; 2008 The Open University
 * @author s.marshall AT open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package groups
 */

require_once('../config.php');
require_once('lib.php');

// Get and check parameters
$courseid = required_param('courseid', PARAM_INT);
$groupids = required_param('groups', PARAM_SEQUENCE);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

$PAGE->set_url('/group/delete.php', array('courseid'=>$courseid,'groups'=>$groupids));

// Make sure course is OK and user has access to manage groups
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourseid');
}
require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $course->id);
require_capability('moodle/course:managegroups', $context);

// Make sure all groups are OK and belong to course
$groupidarray = explode(',',$groupids);
$groupnames = array();
foreach($groupidarray as $groupid) {
    if (!$group = $DB->get_record('groups', array('id' => $groupid))) {
        print_error('invalidgroupid');
    }
    if ($courseid != $group->courseid) {
        print_error('groupunknown', '', '', $group->courseid);
    }
    $groupnames[] = format_string($group->name);
}

$returnurl='index.php?id='.$course->id;

if(count($groupidarray)==0) {
    print_error('errorselectsome','group',$returnurl);
}

if ($confirm && data_submitted()) {
    if (!confirm_sesskey() ) {
        print_error('confirmsesskeybad','error',$returnurl);
    }

    foreach($groupidarray as $groupid) {
        groups_delete_group($groupid);
    }

    redirect($returnurl);
} else {
    $PAGE->set_title(get_string('deleteselectedgroup', 'group'));
    $PAGE->set_heading($course->fullname . ': '. get_string('deleteselectedgroup', 'group'));
    echo $OUTPUT->header();
    $optionsyes = array('courseid'=>$courseid, 'groups'=>$groupids, 'sesskey'=>sesskey(), 'confirm'=>1);
    $optionsno = array('id'=>$courseid);
    if(count($groupnames)==1) {
        $message=get_string('deletegroupconfirm', 'group', $groupnames[0]);
    } else {
        $message=get_string('deletegroupsconfirm', 'group').'<ul>';
        foreach($groupnames as $groupname) {
            $message.='<li>'.$groupname.'</li>';
        }
        $message.='</ul>';
    }
    $formcontinue = new single_button(new moodle_url('delete.php', $optionsyes), get_string('yes'), 'post');
    $formcancel = new single_button(new moodle_url('index.php', $optionsno), get_string('no'), 'get');
    echo $OUTPUT->confirm($message, $formcontinue, $formcancel);
    echo $OUTPUT->footer();
}
