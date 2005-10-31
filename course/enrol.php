<?php // $Id$
      // Depending on the current enrolment method, this page 
      // presents the user with whatever they need to know when 
      // they try to enrol in a course.

    require_once("../config.php");
    require_once("lib.php");
    require_once("$CFG->dirroot/enrol/$CFG->enrol/enrol.php");

    require_variable($id);

    require_login();

    if (! $course = get_record("course", "id", $id) ) {
        error("That's an invalid course id");
    }

    if (! $site = get_site()) {
        error("Could not find a site!");
    }

/// Check if the course is a meta course
    if ($course->metacourse) {
        print_header_simple();
        notice(get_string('coursenotaccessible'), $CFG->wwwroot);
    }

    check_for_restricted_user($USER->username);

    $enrol = new enrolment_plugin();

/// Refreshing enrolment data in the USER session
    $enrol->get_student_courses($USER);
    $enrol->get_teacher_courses($USER);


/// Double check just in case they are actually enrolled already 
/// This might occur if they were enrolled during this session

    if ( !empty($USER->student[$course->id]) or !empty($USER->teacher[$course->id]) ) {

        if ($SESSION->wantsurl) {
            $destination = $SESSION->wantsurl;
            unset($SESSION->wantsurl);
        } else {
            $destination = "$CFG->wwwroot/course/view.php?id=$course->id";
        }

        redirect($destination);
    }
    
/// Users can't enroll to site course
    if (!$course->category) {
        print_header_simple();
        notice(get_string('enrollfirst'), $CFG->wwwroot);
    }

/// Double check just in case they are enrolled to start in the future 

    if ($student = get_record('user_students', 'userid', $USER->id, 'course', $course->id)) { 
        if ($course->enrolperiod and $student->timestart and ($student->timestart >= time())) {
            $message = get_string('enrolmentnotyet', '', userdate($student->timestart));
            print_header();
            notice($message, $CFG->wwwroot);
        }
    }

/// Check the submitted enrollment key if there is one

    if ($form = data_submitted()) {
      //User is not enrolled in the course, wants to access course content
      //as a guest, and course setting allow unlimited guest access
      //Code cribbed from course/loginas.php
      if (isset($loginasguest) && ($course->guest==1)) {
        $realuser = $USER->id;
        $realname = fullname($USER, true);
        $USER = guest_user();
        $USER->loggedin = true;
        $USER->site = $CFG->wwwroot;
        $USER->realuser = $realuser;
        $USER->sessionIP = md5(getremoteaddr());   // Store the current IP in the session
        if (isset($SESSION->currentgroup)) {    // Remember current cache setting for later
            $SESSION->oldcurrentgroup = $SESSION->currentgroup;
            unset($SESSION->currentgroup);
        }
        $guest_name = fullname($USER, true);
        add_to_log($course->id, "course", "loginas", "../user/view.php?id=$course->id&$USER->id$", "$realname -> $guest_name");
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
