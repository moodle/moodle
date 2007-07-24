<?php  // $Id$

require_once '../../config.php';
require_once $CFG->dirroot.'/grade/lib.php';

$courseid = required_param('id', PARAM_INT);
$action   = required_param('action', PARAM_ALPHA);
$eid      = required_param('eid', PARAM_ALPHANUM);

/// Make sure they can even access this course
if (!$course = get_record('course', 'id', $courseid)) {
    print_error('nocourseid');
}
require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $course->id);

// default return url
$gpr = new grade_plugin_return();
$returnurl = $gpr->get_return_url($CFG->wwwroot.'/grade/edit/tree.php?id='.$course->id);

// get the grading tree object
$gtree = new grade_tree($courseid, false, false);

// what are we working with?
if (!$element = $gtree->locate_element($eid)) {
    error('Incorrect element id!', $returnurl);
}
$object = $element['object'];


switch ($action) {
    case 'hide':
        if ($eid and confirm_sesskey()) {
            if (!has_capability('moodle/grade:manage', $context) and !has_capability('moodle/grade:hide', $context)) {
                error('No permission to hide!', $returnurl);
            }
            $object->set_hidden(1);
        }
        break;

    case 'show':
        if ($eid and confirm_sesskey()) {
            if (!has_capability('moodle/grade:manage', $context) and !has_capability('moodle/grade:hide', $context)) {
                error('No permission to show!', $returnurl);
            }
            $object->set_hidden(0);
        }
        break;

    case 'lock':
        if ($eid and confirm_sesskey()) {
            if (!has_capability('moodle/grade:manage', $context) and !has_capability('moodle/grade:lock', $context)) {
                error('No permission to lock!', $returnurl);
            }
            $object->set_locked(1);
        }
        break;

    case 'unlock':
        if ($eid and confirm_sesskey()) {
            if (!has_capability('moodle/grade:manage', $context) and !has_capability('moodle/grade:unlock', $context)) {
                error('No permission to unlock!', $returnurl);
            }
            $object->set_locked(0);
        }
        break;
}

redirect($returnurl);
//redirect($returnurl, 'debug delay', 5);

?>