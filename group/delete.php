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

// Make sure course is OK and user has access to manage groups
if (!$course = get_record('course', 'id', $courseid)) {
    error('Course ID was incorrect');
}
require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $course->id);
require_capability('moodle/course:managegroups', $context);

// Make sure all groups are OK and belong to course
$groupidarray = explode(',',$groupids);
$groupnames = array();
foreach($groupidarray as $groupid) {
    if (!$group = get_record('groups', 'id', $groupid)) {
        error('Group ID was incorrect');
    }
    if ($courseid != $group->courseid) {
        error('Group not on required course');
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
    begin_sql();
    foreach($groupidarray as $groupid) {
        if (!groups_delete_group($groupid)) {
            print_error('erroreditgroup', 'group', $returnurl);
        } 
    }
    commit_sql();
    redirect($returnurl);
} else {
    print_header(get_string('deleteselectedgroup', 'group'), get_string('deleteselectedgroup', 'group'));
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
    notice_yesno($message, 'delete.php', 'index.php', $optionsyes, $optionsno, 'post', 'get');
    print_footer();
}
?>
