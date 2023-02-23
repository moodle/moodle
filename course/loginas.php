<?php
// Allows a teacher/admin to login as another user (in stealth mode).

require_once('../config.php');
require_once('lib.php');

$id       = optional_param('id', SITEID, PARAM_INT);   // course id
$redirect = optional_param('redirect', 0, PARAM_BOOL);

$url = new moodle_url('/course/loginas.php', array('id'=>$id));
$PAGE->set_url($url);

// Reset user back to their real self if needed, for security reasons you need to log out and log in again.
if (\core\session\manager::is_loggedinas()) {
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

// Try log in as this user.
$userid = required_param('user', PARAM_INT);

require_sesskey();
$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);

// User must be logged in.

$systemcontext = context_system::instance();
$coursecontext = context_course::instance($course->id);

require_login();

if (has_capability('moodle/user:loginas', $systemcontext)) {
    if (is_siteadmin($userid)) {
        throw new \moodle_exception('nologinas');
    }
    $context = $systemcontext;
    $PAGE->set_context($context);
} else {
    require_login($course);
    require_capability('moodle/user:loginas', $coursecontext);
    if (is_siteadmin($userid)) {
        throw new \moodle_exception('nologinas');
    }
    if (!is_enrolled($coursecontext, $userid)) {
        throw new \moodle_exception('usernotincourse');
    }
    $context = $coursecontext;

    // Check if course has SEPARATEGROUPS and user is part of that group.
    if (groups_get_course_groupmode($course) == SEPARATEGROUPS &&
            !has_capability('moodle/site:accessallgroups', $context)) {
        $samegroup = false;
        if ($groups = groups_get_all_groups($course->id, $USER->id)) {
            foreach ($groups as $group) {
                if (groups_is_member($group->id, $userid)) {
                    $samegroup = true;
                    break;
                }
            }
        }
        if (!$samegroup) {
            throw new \moodle_exception('nologinas');
        }
    }
}

// Login as this user and return to course home page.
\core\session\manager::loginas($userid, $context);
// Add a notification to let the logged in as user know that all content will be force cleaned
// while in this session.
\core\notification::info(get_string('sessionforceclean', 'core'));
$newfullname = fullname($USER, true);

$strloginas    = get_string('loginas');
$strloggedinas = get_string('loggedinas', '', $newfullname);

$PAGE->set_title($strloggedinas);
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add($strloggedinas);
notice($strloggedinas, "$CFG->wwwroot/course/view.php?id=$course->id");