<?php  // $Id$

    require_once("../../config.php");

    $id   = required_param('id', PARAM_INT);          // Course module ID

    if (! $cm = get_coursemodule_from_id('lesson', $id)) {
        print_error("Course Module ID was incorrect");
    }

    if (! $lesson = $DB->get_record("lesson", array("id" => $cm->instance))) {
        print_error("lesson ID was incorrect");
    }

    if (! $course = $DB->get_record("course", array("id" => $lesson->course))) {
        print_error("Course is misconfigured");
    }

    require_login($course->id, false, $cm);

    if (has_capability('mod/lesson:grade', get_context_instance(CONTEXT_MODULE, $cm->id))) {
        redirect('report.php?id='.$cm->id);
    } else {
        redirect('view.php?id='.$cm->id);
    }

?>
