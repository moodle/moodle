<?php  // $Id$

    require_once('../../config.php');

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

    require_login($course->id, false);

    // mod/hotpot/lib.php is required for the "hotpot_is_visible" function
    // the "require_once" should come after "require_login", to ensure language strings are correct 
    require_once($CFG->dirroot.'/mod/hotpot/lib.php');

    // check user can access this hotpot activity
    if (!hotpot_is_visible($cm)) {
        print_error("activityiscurrentlyhidden");
    }

    if (has_capability('mod/hotpot:grade', get_context_instance(CONTEXT_MODULE, $cm->id))) {
        redirect('report.php?id='.$cm->id);
    } else {
        redirect('view.php?id='.$cm->id);
    }

?>
