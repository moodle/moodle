<?php  // $Id$

    require("../../config.php");
    require_once("$CFG->dirroot/enrol/$CFG->enrol/enrol.php");

    require_variable($id);

    if (!$course = get_record("course", "id", $id)) {
        redirect($CFG->wwwroot);
    }

    require_login();

/// Refreshing enrolment data in the USER session
    $enrol = new enrolment_plugin();
    $enrol->get_student_courses($USER);

    if ($SESSION->wantsurl) {
        $destination = $SESSION->wantsurl;
        unset($SESSION->wantsurl);
    } else {
        $destination = "$CFG->wwwroot/course/view.php?id=$course->id";
    }
    
    if (isstudent($course->id) or isteacher($course->id)) {
        redirect($destination, get_string('paymentthanks', '', $course->fullname));

    } else {   /// Somehow they aren't enrolled yet!  :-(
        print_header();
        notice(get_string('paymentsorry', '', $course), $destination);
    }
    

?>
