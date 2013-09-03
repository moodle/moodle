<?php

require_once("../../config.php");
require_once("lib.php");
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->libdir . '/plagiarismlib.php');

$id = optional_param('id', 0, PARAM_INT);  // Course Module ID
$a  = optional_param('a', 0, PARAM_INT);   // Assignment ID

$url = new moodle_url('/mod/assignment/view.php');
if ($id) {
    if (! $cm = get_coursemodule_from_id('assignment', $id)) {
        print_error('invalidcoursemodule');
    }

    if (! $assignment = $DB->get_record("assignment", array("id"=>$cm->instance))) {
        print_error('invalidid', 'assignment');
    }

    if (! $course = $DB->get_record("course", array("id"=>$assignment->course))) {
        print_error('coursemisconf', 'assignment');
    }
    $url->param('id', $id);
} else {
    if (!$assignment = $DB->get_record("assignment", array("id"=>$a))) {
        print_error('invalidid', 'assignment');
    }
    if (! $course = $DB->get_record("course", array("id"=>$assignment->course))) {
        print_error('coursemisconf', 'assignment');
    }
    if (! $cm = get_coursemodule_from_instance("assignment", $assignment->id, $course->id)) {
        print_error('invalidcoursemodule');
    }
    $url->param('a', $a);
}

$PAGE->set_url($url);
require_login($course, true, $cm);

$PAGE->requires->js('/mod/assignment/assignment.js');

$classfile = "$CFG->dirroot/mod/assignment/type/$assignment->assignmenttype/assignment.class.php";
if (!file_exists($classfile)) {
    throw new moodle_exception('unsupportedsubplugin', 'assignment', new moodle_url('/course/view.php', array('id' => $course->id)), $assignment->assignmenttype);
}
require ($classfile);
$assignmentclass = "assignment_$assignment->assignmenttype";
$assignmentinstance = new $assignmentclass($cm->id, $assignment, $cm, $course);

/// Mark as viewed
$completion=new completion_info($course);
$completion->set_module_viewed($cm);

$assignmentinstance->view();   // Actually display the assignment!