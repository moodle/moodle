<?php  // $Id$

    require_once("../../../../config.php");
    require_once("../../lib.php");
 
    $id     = required_param('id', PARAM_INT);    // Course Module ID
    $userid = required_param('userid', PARAM_INT);    // Course Module ID

    if (!empty($CFG->forcelogin)) {
        require_login();
    }

    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $assignment = get_record("assignment", "id", $cm->instance)) {
        error("assignment ID was incorrect");
    }

    if (! $course = get_record("course", "id", $assignment->course)) {
        error("Course is misconfigured");
    }

    if (! $user = get_record("user", "id", $userid)) {
        error("User is misconfigured");
    }

    require_login($course->id, false, $cm);

    if (!isteacher($course->id)) {
        error("Only teachers can look at this page");
    }

    require ("$CFG->dirroot/mod/assignment/type/$assignment->assignmenttype/assignment.class.php");
    $assignmentclass = "assignment_$assignment->assignmenttype";
    $assignmentinstance = new $assignmentclass($cm->id, $assignment, $cm, $course);

    if ($submission = $assignmentinstance->get_submission($userid)) {
        print_header(fullname($user,true).': '.$assignment->name);
        print_simple_box(format_text($submission->data1, $submission->data2), 'center', '100%');
        close_window_button();
        print_footer('none');
    } else {
        print_string('emptysubmission', 'assignment');
    }

?>
