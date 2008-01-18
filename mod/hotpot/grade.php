<?php  // $Id$

    require_once("../../config.php");

    $id   = required_param('id', PARAM_INT);          // Course module ID

    if (! $cm = get_coursemodule_from_id('hotpot', $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $hotpot = get_record("hotpot", "id", $cm->instance)) {
        error("hotpot ID was incorrect");
    }

    if (! $course = get_record("course", "id", $hotpot->course)) {
        error("Course is misconfigured");
    }

    require_login($course->id, false, $cm);

    if (has_capability('mod/hotpot:grade', get_context_instance(CONTEXT_MODULE, $cm->id))) {
        redirect('report.php?id='.$cm->id);
    } else {
        redirect('view.php?id='.$cm->id);
    }

?>
