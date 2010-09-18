<?php

require_once('../../config.php');
require_once('lib.php');

$id = optional_param('id', 0, PARAM_INT);  // Course module ID
$a  = optional_param('a', 0, PARAM_INT);   // Assignment ID

$url = new moodle_url('/mod/assignment/delete.php');
if ($id) {
    if (! $cm = get_coursemodule_from_id('assignment', $id)) {
        print_error('invalidcoursemodule');
    }

    if (! $assignment = $DB->get_record('assignment', array('id'=>$cm->instance))) {
        print_error('invalidid', 'assignment');
    }

    if (! $course = $DB->get_record('course', array('id'=>$assignment->course))) {
        print_error('coursemisconf', 'assignment');
    }
    $url->param('id', $id);
} else {
    if (!$assignment = $DB->get_record('assignment', array('id'=>$a))) {
        print_error('invalidcoursemodule');
    }
    if (! $course = $DB->get_record('course', array('id'=>$assignment->course))) {
        print_error('coursemisconf', 'assignment');
    }
    if (! $cm = get_coursemodule_from_instance('assignment', $assignment->id, $course->id)) {
        print_error('invalidcoursemodule');
    }
    $url->param('a', $a);
}

$PAGE->set_url($url);
require_login($course->id, false, $cm);

/// Load up the required assignment code
require($CFG->dirroot.'/mod/assignment/type/'.$assignment->assignmenttype.'/assignment.class.php');
$assignmentclass = 'assignment_'.$assignment->assignmenttype;
$assignmentinstance = new $assignmentclass($cm->id, $assignment, $cm, $course);

$assignmentinstance->delete();   // delete something