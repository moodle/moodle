<?php  // $Id$

    require_once("../../config.php");

    $id   = required_param('id', PARAM_INT);          // Course module ID

    if (! $cm = get_coursemodule_from_id('assignment', $id)) {
        print_error('invalidcoursemodule');
    }

    if (! $assignment = get_record("assignment", "id", $cm->instance)) {
        print_error('invalidid', 'assignment');
    }

    if (! $course = get_record("course", "id", $assignment->course)) {
        print_error('coursemisconf', 'assignment');
    }

    require_login($course->id, false, $cm);

    if (has_capability('mod/assignment:grade', get_context_instance(CONTEXT_MODULE, $cm->id))) {
        redirect('submissions.php?id='.$cm->id);
    } else {
        redirect('view.php?id='.$cm->id);
    }

?>
