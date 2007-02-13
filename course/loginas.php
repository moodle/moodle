<?php // $Id$
      // Allows a teacher/admin to login as another user (in stealth mode)

    require_once("../config.php");
    require_once("lib.php");

/// Reset user back to their real self if needed
    $return   = optional_param('return', 0, PARAM_BOOL);   // return to the page we came from

    if (!empty($USER->realuser)) {
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

        if ($return and isset($_SERVER["HTTP_REFERER"])) { // That's all we wanted to do, so let's go back
            redirect($_SERVER["HTTP_REFERER"]);
        } else {
            redirect($CFG->wwwroot);
        }
    }


///-------------------------------------
/// We are trying to log in as this user in the first place

    $id       = required_param('id', PARAM_INT);           // course id
    $userid   = required_param('user', PARAM_INT);         // login as this user

    if (!$site = get_site()) {
        error("Site isn't defined!");
    }

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID was incorrect");
    }

/// User must be logged in

    if ($course->id == SITEID) {
        require_login();
        $context = get_context_instance(CONTEXT_SYSTEM, SITEID);
    } else {
        require_login($course->id);
        $context = get_context_instance(CONTEXT_COURSE, $course->id);
        if (!has_capability('moodle/course:view', $context, $userid, false)) {
            error('This user is not in this course!');
        }
        if (has_capability('moodle/site:doanything', $context, $userid, false)) {
            print_error('nologinas');
        }
    }

/// User must have permissions

    require_capability('moodle/user:loginas', $context);


/// Remember current timeaccess settings for later

    if (isset($USER->timeaccess)) {
        $SESSION->oldtimeaccess = $USER->timeaccess;
    }

/// Login as this user and return to course home page.

    $oldfullname = fullname($USER, true);
    $olduserid   = $USER->id;

    $USER = get_complete_user_data('id', $userid);    // Create the new USER object with all details
    $USER->realuser = $olduserid;

    load_user_capability('', $context); // load this user's capabilities for this context only

    if (isset($SESSION->currentgroup)) {    // Remember current cache setting for later
        $SESSION->oldcurrentgroup = $SESSION->currentgroup;
        unset($SESSION->currentgroup);
    }

    $newfullname = fullname($USER, true);

    add_to_log($course->id, "course", "loginas", "../user/view.php?id=$course->id&amp;user=$userid", "$oldfullname -> $newfullname");

    $strloginas    = get_string('loginas');
    $strloggedinas = get_string('loggedinas', '', $newfullname);

    print_header_simple($strloggedinas, '', $strloggedinas, '', '', true, '&nbsp;', navmenu($course));
    notice($strloggedinas, "$CFG->wwwroot/course/view.php?id=$course->id");


?>
