<?php

    require_once("../../config.php");

    $id   = required_param('id', PARAM_INT);          // Course module ID

    if (! $cm = get_coursemodule_from_id('scorm', $id)) {
        error('Course Module ID was incorrect');
    }

    if (! $scorm = get_record('scorm', 'id',$cm->instance)) {
        error('scorm ID was incorrect');
    }

    if (! $course = get_record('course','id', $scorm->course)) {
        error('Course is misconfigured');
    }

    require_login($course->id, false, $cm);

    if (has_capability('mod/scorm:viewreport', get_context_instance(CONTEXT_MODULE, $cm->id))) {
        redirect('report.php?id='.$cm->id);
    } else {
        redirect('view.php?id='.$cm->id);
    }
