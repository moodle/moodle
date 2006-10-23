<?php  // $Id$

    require("../../config.php");
    require_once("$CFG->dirroot/enrol/paypal/enrol.php");

    $id = required_param('id', PARAM_INT);

    if (!$course = get_record("course", "id", $id)) {
        redirect($CFG->wwwroot);
    }

    if (! $context = get_context_instance(CONTEXT_COURSE, $course->id)) {
        redirect($CFG->wwwroot);
    }

    require_login();

/// Refreshing enrolment data in the USER session
    load_all_capabilities();

    if ($SESSION->wantsurl) {
        $destination = $SESSION->wantsurl;
        unset($SESSION->wantsurl);
    } else {
        $destination = "$CFG->wwwroot/course/view.php?id=$course->id";
    }
    
    if (has_capability('moodle/course:view', $context)) {
        redirect($destination, get_string('paymentthanks', '', $course->fullname));

    } else {   /// Somehow they aren't enrolled yet!  :-(
        print_header();
        notice(get_string('paymentsorry', '', $course), $destination);
    }

?>
