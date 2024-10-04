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
    if ($id && $id != SITEID) {
        $SESSION->wantsurl = "$CFG->wwwroot/course/view.php?id=".$id;
    } else {
        $SESSION->wantsurl = "$CFG->wwwroot/?redirect=1";
    }

    redirect(get_login_url());
}

// Try log in as this user.
$userid = required_param('user', PARAM_INT);

require_sesskey();
$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);

// User must be logged in.
require_login();

$user = $DB->get_record('user', ['id' => $userid]);

$context = \core\session\loginas_helper::get_context_user_can_login_as($USER, $user, $course);
if (empty($context)) {
    throw new moodle_exception('nologinas');
}

$PAGE->set_context($context);

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

if ($course->id != SITEID) {
    $returnurl = course_get_url($course);
} else {
    $returnurl = new moodle_url('/', ['redirect' => 1]);
}

notice($strloggedinas, $returnurl);
