<?php  // $Id$

    require_once("../../config.php");

    $id   = required_param('id', PARAM_INT);          // Course module ID

    if (! $cm = get_coursemodule_from_id('quiz', $id)) {
        error('Course Module ID was incorrect');
    }

    if (! $quiz = get_record('quiz', 'id', $cm->instance)) {
        error('quiz ID was incorrect');
    }

    if (! $course = get_record('course', 'id', $quiz->course)) {
        error('Course is misconfigured');
    }

    require_login($course->id, false, $cm);

    if (has_capability('mod/quiz:viewreports', get_context_instance(CONTEXT_MODULE, $cm->id))) {
        redirect('report.php?id='.$cm->id);
    } else {
        redirect('view.php?id='.$cm->id);
    }

?>
