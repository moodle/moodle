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
 * User searching requests.
 *
 * @package    gradereport_history
 * @copyright  2013 NetSpot Pty Ltd (https://www.netspot.com.au)
 * @author     Adam Olley <adam.olley@netspot.com.au>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require_once('../../../config.php');
require_once($CFG->dirroot.'/grade/report/history/lib.php');

$id = required_param('id', PARAM_INT); // Course id.
$search = optional_param('search', '', PARAM_RAW);
$page = optional_param('page', 0, PARAM_INT);

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
$context = context_course::instance($course->id, MUST_EXIST);

if ($course->id == SITEID) {
    throw new moodle_exception('invalidcourse');
}

require_sesskey();
require_login($course);

$PAGE->set_context($context);

echo $OUTPUT->header();

$outcome = new stdClass();
$outcome->success = true;
$outcome->response = new stdClass();
$outcome->error = '';

$report = new grade_report_history($course->id, null, $context);
$users = $report->load_users($search, $page, 25);
$outcome->response = array('users' => $users);
$outcome->response['totalusers'] = count($users);

$extrafields = get_extra_user_fields($context);
$useroptions = array();
// User is not enrolled, either link to site profile or do not link at all.
if (has_capability('moodle/user:viewdetails', context_system::instance())) {
    $useroptions['courseid'] = SITEID;
} else {
    $useroptions['link'] = false;
}
foreach ($outcome->response['users'] as &$user) {
    $user->userid = $user->id;
    $user->picture = $OUTPUT->user_picture($user, $useroptions);
    $user->fullname = fullname($user);
    $fieldvalues = array();
    foreach ($extrafields as $field) {
        $fieldvalues[] = s($user->{$field});
        unset($user->{$field});
    }
    $user->extrafields = implode(', ', $fieldvalues);
    unset($user->id);
}
// Chrome will display users in the order of the array keys, so we need
// to ensure that the results ordered array keys. Fortunately, the JavaScript
// does not care what the array keys are. It uses user.id where necessary.
$outcome->response['users'] = array_values($outcome->response['users']);
$outcome->success = true;

echo json_encode($outcome);

echo $OUTPUT->footer();
