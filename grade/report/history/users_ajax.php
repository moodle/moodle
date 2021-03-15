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

require_once(__DIR__ . '/../../../config.php');

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
require_capability('gradereport/history:view', $context);
require_capability('moodle/grade:viewall', $context);

$outcome = new stdClass();
$outcome->success = true;
$outcome->error = '';

$users = \gradereport_history\helper::get_users($context, $search, $page, 25);
$outcome->response = array('users' => array());
$outcome->response['totalusers'] = \gradereport_history\helper::get_users_count($context, $search);;

// TODO Does not support custom user profile fields (MDL-70456).
$extrafields = \core_user\fields::get_identity_fields($context, false);
$useroptions = array('link' => false, 'visibletoscreenreaders' => false);

// Format the user record.
foreach ($users as $user) {
    $newuser = new stdClass();
    $newuser->userid = $user->id;
    $newuser->picture = $OUTPUT->user_picture($user, $useroptions);
    $newuser->fullname = fullname($user);
    $fieldvalues = array();
    foreach ($extrafields as $field) {
        $fieldvalues[] = s($user->{$field});
    }
    $newuser->extrafields = implode(', ', $fieldvalues);
    $outcome->response['users'][] = $newuser;
}

$outcome->success = true;

echo $OUTPUT->header();
echo json_encode($outcome);
echo $OUTPUT->footer();
