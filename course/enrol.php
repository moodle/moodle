<?PHP // $Id$
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
        $message = get_string('enrolmentnotyet', '', userdate($student->timestart));
        print_header();
        notice($message, $CFG->wwwroot);
    }

/// Check the submitted enrollment key if there is one

    if ($form = data_submitted()) {
        $enrol->check_entry($form, $course);
    }

    $enrol->print_entry($course);

/// Easy!

?>
