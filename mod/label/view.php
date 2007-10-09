<?php  // $Id$

    require_once("../../config.php");

    $id = optional_param('id',0,PARAM_INT);    // Course Module ID, or
    $l = optional_param('l',0,PARAM_INT);     // Label ID

    if ($id) {
        if (! $cm = get_coursemodule_from_id('label', $id)) {
            error("Course Module ID was incorrect");
        }
    
        if (! $course = get_record("course", "id", $cm->course)) {
            error("Course is misconfigured");
        }
    
        if (! $label = get_record("label", "id", $cm->instance)) {
            error("Course module is incorrect");
        }

    } else {
        if (! $label = get_record("label", "id", $l)) {
            error("Course module is incorrect");
        }
        if (! $course = get_record("course", "id", $label->course)) {
            error("Course is misconfigured");
        }
        if (! $cm = get_coursemodule_from_instance("label", $label->id, $course->id)) {
            error("Course Module ID was incorrect");
        }
    }

    require_login($course->id);

    redirect("$CFG->wwwroot/course/view.php?id=$course->id");

?>
