<?php
// Allows a teacher/admin to login as another user (in stealth mode)

require_once('../config.php');
require_once('lib.php');

$id       = optional_param('id', SITEID, PARAM_INT);   // course id
$redirect = optional_param('redirect', 0, PARAM_BOOL);

$url = new moodle_url('/course/loginas.php', array('id'=>$id));
$PAGE->set_url($url);

/// Reset user back to their real self if needed, for security reasons you need to log out and log in again
if (session_is_loggedinas()) {
    require_sesskey();
    require_logout();

    // We can not set wanted URL here because the session is closed.
    redirect(new moodle_url($url, array('redirect'=>1)));
}

if ($redirect) {
    if ($id and $id != SITEID) {
        $SESSION->wantsurl = "$CFG->wwwroot/course/view.php?id=".$id;
    } else {
        $SESSION->wantsurl = "$CFG->wwwroot/";
    }

    redirect(get_login_url());
}

///-------------------------------------
/// We are trying to log in as this user in the first place

$userid = required_param('user', PARAM_INT);         // login as this user

require_sesskey();
$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);

/// User must be logged in

$systemcontext = context_system::instance();
$coursecontext = context_course::instance($course->id);

require_login();

if (has_capability('moodle/user:loginas', $systemcontext)) {
    if (is_siteadmin($userid)) {
        print_error('nologinas');
    }
    $context = $systemcontext;
    $PAGE->set_context($context);
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
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add($strloggedinas);
notice($strloggedinas, "$CFG->wwwroot/course/view.php?id=$course->id");