<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Completely unenrol a user from a course.
 *
 * Please note when unenrolling a user all of their grades are removed as well,
 * most ppl actually expect enrolments to be suspended only...
 *
 * @package    core_enrol
 * @copyright  2011 Petr skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../config.php');
require_once("$CFG->dirroot/enrol/locallib.php");
require_once("$CFG->dirroot/enrol/renderer.php");

$ueid    = required_param('ue', PARAM_INT); // user enrolment id
$confirm = optional_param('confirm', false, PARAM_BOOL);
$filter  = optional_param('ifilter', 0, PARAM_INT);

$ue = $DB->get_record('user_enrolments', array('id' => $ueid), '*', MUST_EXIST);
$user = $DB->get_record('user', array('id'=>$ue->userid), '*', MUST_EXIST);
$instance = $DB->get_record('enrol', array('id'=>$ue->enrolid), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id'=>$instance->courseid), '*', MUST_EXIST);

$context = context_course::instance($course->id);

// set up PAGE url first!
$PAGE->set_url('/enrol/unenroluser.php', array('ue'=>$ueid, 'ifilter'=>$filter));

require_login($course);

if (!enrol_is_enabled($instance->enrol)) {
    print_error('erroreditenrolment', 'enrol');
}

$plugin = enrol_get_plugin($instance->enrol);

if (!$plugin->allow_unenrol_user($instance, $ue) or !has_capability("enrol/$instance->enrol:unenrol", $context)) {
    print_error('erroreditenrolment', 'enrol');
}

$manager = new course_enrolment_manager($PAGE, $course, $filter);
$table = new course_enrolment_users_table($manager, $PAGE);

$usersurl = new moodle_url('/user/index.php', array('id' => $course->id));

$PAGE->set_pagelayout('admin');
navigation_node::override_active_url($usersurl);

// If the unenrolment has been confirmed and the sesskey is valid unenrol the user.
if ($confirm && confirm_sesskey()) {
    $plugin->unenrol_user($instance, $ue->userid);
    redirect($usersurl);
}

$yesurl = new moodle_url($PAGE->url, array('confirm'=>1, 'sesskey'=>sesskey()));
$message = get_string('unenrolconfirm', 'core_enrol',
    [
        'user' => fullname($user, true),
        'course' => format_string($course->fullname),
        'enrolinstancename' => $plugin->get_instance_name($instance)
    ]
);
$fullname = fullname($user);
$title = get_string('unenrol', 'core_enrol');

$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->navbar->add($title);
$PAGE->navbar->add($fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading($fullname);
echo $OUTPUT->confirm($message, $yesurl, $usersurl);
echo $OUTPUT->footer();
