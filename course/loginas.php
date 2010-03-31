<?php
      // Allows a teacher/admin to login as another user (in stealth mode)

    require_once('../config.php');
    require_once('lib.php');

/// Reset user back to their real self if needed
    $return = optional_param('return', 0, PARAM_BOOL);   // return to the page we came from

    if (session_is_loggedinas()) {
        if (!confirm_sesskey()) {
            print_error('confirmsesskeybad');
        }

        session_unloginas();

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

    $url = new moodle_url('/course/loginas.php', array('user'=>$userid, 'sesskey'=>sesskey()));
    if ($id !== SITEID) {
        $url->param('id', $id);
    }
    $PAGE->set_url($url);

    if (!confirm_sesskey()) {
        print_error('confirmsesskeybad');
    }

    if (!$course = $DB->get_record('course', array('id'=>$id))) {
        print_error("invalidcourseid");
    }

/// User must be logged in

    $systemcontext = get_context_instance(CONTEXT_SYSTEM);
    $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);

    require_login();

    if (has_capability('moodle/user:loginas', $systemcontext)) {
        if (is_siteadmin($userid)) {
            print_error('nologinas');
        }
        $context = $systemcontext;
    } else {
        require_login($course);
        require_capability('moodle/user:loginas', $coursecontext);
        if (is_siteadmin($userid)) {
            print_error('nologinas');
        }
        if (!is_enrolled($coursecontext, $userid)) {
            print_error('usernotincourse');
        }
        $context = $coursecontext;
    }

/// Login as this user and return to course home page.
    $oldfullname = fullname($USER, true);
    session_loginas($userid, $context);
    $newfullname = fullname($USER, true);

    add_to_log($course->id, "course", "loginas", "../user/view.php?id=$course->id&amp;user=$userid", "$oldfullname -> $newfullname");

    $strloginas    = get_string('loginas');
    $strloggedinas = get_string('loggedinas', '', $newfullname);

    $PAGE->set_title($strloggedinas);
    $PAGE->navbar->add($strloggedinas);
    notice($strloggedinas, "$CFG->wwwroot/course/view.php?id=$course->id");



