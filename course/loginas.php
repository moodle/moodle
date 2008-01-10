<?php // $Id$
      // Allows a teacher/admin to login as another user (in stealth mode)

    require_once('../config.php');
    require_once('lib.php');

/// Reset user back to their real self if needed
    $return = optional_param('return', 0, PARAM_BOOL);   // return to the page we came from

    if (!empty($USER->realuser)) {
        if (!confirm_sesskey()) {
            print_error('confirmsesskeybad');
        }

        $USER = get_complete_user_data('id', $USER->realuser);
        load_all_capabilities();   // load all this user's normal capabilities

        if (isset($SESSION->oldcurrentgroup)) {      // Restore previous "current group" cache.
            $SESSION->currentgroup = $SESSION->oldcurrentgroup;
            unset($SESSION->oldcurrentgroup);
        }
        if (isset($SESSION->oldtimeaccess)) {        // Restore previous timeaccess settings
            $USER->timeaccess = $SESSION->oldtimeaccess;
            unset($SESSION->oldtimeaccess);
        }
        if (isset($SESSION->grade_last_report)) {    // Restore grade defaults if any
            $USER->grade_last_report = $SESSION->grade_last_report;
            unset($SESSION->grade_last_report);
        }

        if ($return and isset($_SERVER["HTTP_REFERER"])) { // That's all we wanted to do, so let's go back
            redirect($_SERVER["HTTP_REFERER"]);
        } else {
            redirect($CFG->wwwroot);
        }
    }

///-------------------------------------
/// We are trying to log in as this user in the first place

    $id     = optional_param('id', SITEID, PARAM_INT);   // course id
    $userid = required_param('user', PARAM_INT);         // login as this user

    if (!confirm_sesskey()) {
        print_error('confirmsesskeybad');
    }

    if (! $course = get_record('course', 'id', $id)) {
        error("Course ID was incorrect");
    }

/// User must be logged in

    $systemcontext = get_context_instance(CONTEXT_SYSTEM);
    $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);

    require_login();

    if (has_capability('moodle/user:loginas', $systemcontext)) {
        if (has_capability('moodle/site:doanything', $systemcontext, $userid, false)) {
            print_error('nologinas');
        }
        $context = $systemcontext;
    } else {
        require_login($course);
        require_capability('moodle/user:loginas', $coursecontext);
        if (!has_capability('moodle/course:view', $coursecontext, $userid, false)) {
            error('This user is not in this course!');
        }
        if (has_capability('moodle/site:doanything', $coursecontext, $userid, false)) {
            print_error('nologinas');
        }
        $context = $coursecontext;
    }

/// Remember current timeaccess settings for later

    if (isset($USER->timeaccess)) {
        $SESSION->oldtimeaccess = $USER->timeaccess;
    }
    if (isset($USER->grade_last_report)) {
        $SESSION->grade_last_report = $USER->grade_last_report;
    }

/// Login as this user and return to course home page.

    $oldfullname = fullname($USER, true);
    $olduserid   = $USER->id;

/// Create the new USER object with all details and reload needed capabilitites
    $USER = get_complete_user_data('id', $userid);
    $USER->realuser = $olduserid;
    $USER->loginascontext = $context;
    check_enrolment_plugins($USER);
    load_all_capabilities();   // reload capabilities

    if (isset($SESSION->currentgroup)) {    // Remember current cache setting for later
        $SESSION->oldcurrentgroup = $SESSION->currentgroup;
        unset($SESSION->currentgroup);
    }

    $newfullname = fullname($USER, true);

    add_to_log($course->id, "course", "loginas", "../user/view.php?id=$course->id&amp;user=$userid", "$oldfullname -> $newfullname");

    $strloginas    = get_string('loginas');
    $strloggedinas = get_string('loggedinas', '', $newfullname);

    print_header_simple($strloggedinas, '', build_navigation(array(array('name'=>$strloggedinas, 'link'=>'','type'=>'misc'))),
            '', '', true, '&nbsp;', navmenu($course));
    notice($strloggedinas, "$CFG->wwwroot/course/view.php?id=$course->id");


?>
