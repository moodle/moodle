<?php // $Id$
      // Depending on the current enrolment method, this page 
      // presents the user with whatever they need to know when 
      // they try to enrol in a course.

    require_once("../config.php");
    require_once("lib.php");
    require_once("$CFG->dirroot/enrol/enrol.class.php");

    $id           = required_param('id', PARAM_INT);
    $loginasguest = optional_param('loginasguest', 0, PARAM_BOOL);

    require_login();

    if (! $course = get_record("course", "id", $id) ) {
        error("That's an invalid course id");
    }

    if (! $site = get_site()) {
        error("Could not find a site!");
    }

    check_for_restricted_user($USER->username);

/// Refreshing enrolment data in the USER session
    if (!($plugins = explode(',', $CFG->enrol_plugins_enabled))) {
        $plugins = array($CFG->enrol);
    }
    require_once($CFG->dirroot .'/enrol/enrol.class.php');
    foreach ($plugins as $p) {
        $enrol = enrolment_factory::factory($p);
        if (method_exists($enrol, 'get_student_courses')) {
            $enrol->get_student_courses($USER);
        }
        if (method_exists($enrol, 'get_teacher_courses')) {
            $enrol->get_teacher_courses($USER);
        }
        unset($enrol);
    }

    $enrol = enrolment_factory::factory($course->enrol);

/// Double check just in case they are actually enrolled already 
/// This might occur if they were enrolled during this session
/// also happens when course is unhidden after student logs in

    if ( !empty($USER->student[$course->id]) or !empty($USER->teacher[$course->id]) ) {

        if ($SESSION->wantsurl) {
            $destination = $SESSION->wantsurl;
            unset($SESSION->wantsurl);
        } else {
            $destination = "$CFG->wwwroot/course/view.php?id=$course->id";
        }

        redirect($destination);
    }

/// Check if the course is a meta course
/// moved here to fix bug 5734
    if ($course->metacourse) {
        print_header_simple();
        notice(get_string('coursenotaccessible'), "$CFG->wwwroot/index.php");
    }
    
/// Users can't enroll to site course
    if (!$course->category) {
        print_header_simple();
        notice(get_string('enrollfirst'), "$CFG->wwwroot/index.php");
    }

/// Double check just in case they are enrolled to start in the future 

    if ($student = get_record('user_students', 'userid', $USER->id, 'course', $course->id)) { 
        if ($course->enrolperiod and $student->timestart and ($student->timestart >= time())) {
            $message = get_string('enrolmentnotyet', '', userdate($student->timestart));
            print_header();
            notice($message, "$CFG->wwwroot/index.php");
        }
    }

/// Check if the course is enrollable
    if (!method_exists($enrol, 'print_entry')) {
        print_header_simple();
        notice(get_string('enrolmentnointernal'), "$CFG->wwwroot/index.php");
    }

    if (!$loginasguest and ($USER->username != 'guest') and(
            !$course->enrollable ||
            ($course->enrollable == 2 && $course->enrolstartdate > 0 && $course->enrolstartdate > time()) ||
            ($course->enrollable == 2 && $course->enrolenddate > 0 && $course->enrolenddate <= time())
            )) {
        print_header($course->shortname, $course->fullname, $course->shortname );
        notice(get_string('notenrollable'), "$CFG->wwwroot/index.php");
    }

/// Check the submitted enrollment key if there is one

    if ($form = data_submitted()) {
      //User is not enrolled in the course, wants to access course content
      //as a guest, and course setting allow unlimited guest access
      //
      //the original idea was to use "loginas" feature, but require_login() would have to be changed
      //and we would have to explain it to all users - it is now plain login action
      if ($loginasguest and !empty($CFG->guestloginbutton) and ($course->guest==1 or $course->guest==2)) {
        if (isset($SESSION->currentgroup)) {
            unset($SESSION->currentgroup);
        }
        $USER = get_complete_user_data('username', 'guest');    // get full guest user data
        add_to_log(SITEID, 'user', 'login', "view.php?id=$USER->id&course=".SITEID, $USER->id, 0, $USER->id);
        if ($SESSION->wantsurl) {
            $destination = $SESSION->wantsurl;
            unset($SESSION->wantsurl);
        } else {
            $destination = "$CFG->wwwroot/course/view.php?id=$course->id";
        }
        redirect($destination);
      }
      $enrol->check_entry($form, $course);
    }

    $enrol->print_entry($course);

/// Easy!

?>
