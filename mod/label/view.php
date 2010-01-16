<?php

require_once("../../config.php");

$id = optional_param('id',0,PARAM_INT);    // Course Module ID, or
$l = optional_param('l',0,PARAM_INT);     // Label ID

if ($id) {
    $PAGE->set_url('/mod/label/index.php', array('id'=>$id));
    if (! $cm = get_coursemodule_from_id('label', $id)) {
        print_error('invalidcoursemodule');
    }

    if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
        print_error('coursemisconf');
    }

    if (! $label = $DB->get_record("label", array("id"=>$cm->instance))) {
        print_error('invalidcoursemodule');
    }

} else {
    $PAGE->set_url('/mod/label/index.php', array('l'=>$l));
    if (! $label = $DB->get_record("label", array("id"=>$l))) {
        print_error('invalidcoursemodule');
    }
    if (! $course = $DB->get_record("course", array("id"=>$label->course)) ){
        print_error('coursemisconf');
    }
    if (! $cm = get_coursemodule_from_instance("label", $label->id, $course->id)) {
        print_error('invalidcoursemodule');
    }
}

require_login($course, true, $cm);

redirect("$CFG->wwwroot/course/view.php?id=$course->id");


