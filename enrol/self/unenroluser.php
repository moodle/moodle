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
 * Unenrol a user who was enrolled through a self enrolment.
 *
 * @package    enrol
 * @subpackage self
 * @copyright  2011 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once("$CFG->dirroot/enrol/locallib.php");
require_once("$CFG->dirroot/enrol/renderer.php");

$ueid    = required_param('ue', PARAM_INT); // user enrolment id
$filter  = optional_param('ifilter', 0, PARAM_INT);
$confirm = optional_param('confirm', false, PARAM_BOOL);

// Get the user enrolment object
$ue     = $DB->get_record('user_enrolments', array('id' => $ueid), '*', MUST_EXIST);
// Get the user for whom the enrolment is
$user   = $DB->get_record('user', array('id'=>$ue->userid), '*', MUST_EXIST);
// Get the course the enrolment is to
list($ctxsql, $ctxjoin) = context_instance_preload_sql('c.id', CONTEXT_COURSE, 'ctx');
$sql = "SELECT c.* $ctxsql
          FROM {course} c
     LEFT JOIN {enrol} e ON e.courseid = c.id
               $ctxjoin
         WHERE e.id = :enrolid";
$params = array('enrolid' => $ue->enrolid);
$course = $DB->get_record_sql($sql, $params, MUST_EXIST);
context_instance_preload($course);

// Make sure it's not the front page
if ($course->id == SITEID) {
    redirect(new moodle_url('/'));
}

// Obviously
require_login($course);
// Make sure the user can unenrol self enrolled users.
require_capability("enrol/self:unenrol", get_context_instance(CONTEXT_COURSE, $course->id));

// Get the enrolment manager for this course
$manager = new course_enrolment_manager($PAGE, $course, $filter);
// Get an enrolment users table object. Doign this will automatically retrieve the the URL params
// relating to table the user was viewing before coming here, and allows us to return the user to the
// exact page of the users screen they can from.
$table = new course_enrolment_users_table($manager, $PAGE);

// The URL of the enrolled users page for the course.
$usersurl = new moodle_url('/enrol/users.php', array('id' => $course->id));
// The URl to return the user too after this screen.
$returnurl = new moodle_url($usersurl, $manager->get_url_params()+$table->get_url_params());
// The URL of this page
$url = new moodle_url('/enrol/self/unenroluser.php', $returnurl->params());
$url->param('ue', $ueid);

$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
navigation_node::override_active_url($usersurl);

list($instance, $plugin) = $manager->get_user_enrolment_components($ue);
if (!$plugin->allow_unenrol($instance) || $instance->enrol != 'self' || !($plugin instanceof enrol_self_plugin)) {
    print_error('erroreditenrolment', 'enrol');
}

// If the unenrolment has been confirmed and the sesskey is valid unenrol the user.
if ($confirm && confirm_sesskey() && $manager->unenrol_user($ue)) {
    redirect($returnurl);
}

$yesurl = new moodle_url($PAGE->url, array('confirm'=>1, 'sesskey'=>sesskey()));
$message = get_string('unenroluser', 'enrol_self', array('user' => fullname($user, true), 'course' => format_string($course->fullname)));
$fullname = fullname($user);
$title = get_string('unenrol', 'enrol_self');

$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->navbar->add($title);
$PAGE->navbar->add($fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading($fullname);
echo $OUTPUT->confirm($message, $yesurl, $returnurl);
echo $OUTPUT->footer();