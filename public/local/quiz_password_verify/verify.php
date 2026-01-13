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
 * AJAX endpoint to verify user password
 *
 * @package    local_quiz_password_verify
 * @copyright  2024 Author Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/authlib.php');

// Get parameters.
$attemptid = optional_param('attemptid', 0, PARAM_INT);
$cmid = optional_param('cmid', 0, PARAM_INT);
$password = required_param('password', PARAM_RAW);

$response = new stdClass();
$response->success = false;

try {
    if (!$attemptid && !$cmid) {
        throw new moodle_exception('missingparam', 'local_quiz_password_verify');
    }

    require_login();
    require_sesskey();

    $activityid = null;
    $activitytype = null;

    if ($attemptid) {
        // Get the quiz attempt.
        $attempt = $DB->get_record('quiz_attempts', ['id' => $attemptid], '*', MUST_EXIST);

        // Verify this is the user's attempt.
        if ($attempt->userid != $USER->id) {
            throw new moodle_exception('invalidattempt', 'local_quiz_password_verify');
        }

        // Get the quiz.
        $quiz = $DB->get_record('quiz', ['id' => $attempt->quiz], '*', MUST_EXIST);
        $activityid = $quiz->id;
        $activitytype = 'quiz';
    } else {
        // Get activity from CMID - try to determine the type.
        $cm = $DB->get_record('course_modules', ['id' => $cmid], '*', MUST_EXIST);
        $module = $DB->get_record('modules', ['id' => $cm->module], '*', MUST_EXIST);
        
        $activitytype = $module->name;
        $activityid = $cm->instance;
        
        // For supported module types, get the instance record.
        if ($module->name == 'quiz') {
            $activity = $DB->get_record('quiz', ['id' => $cm->instance], '*', MUST_EXIST);
        } else if ($module->name == 'resource') {
            $activity = $DB->get_record('resource', ['id' => $cm->instance], '*', MUST_EXIST);
        } else {
            // For other module types, just use the cmid as reference.
            $activityid = $cmid;
        }
    }

    // Verify the password.
    $user = $DB->get_record('user', ['id' => $USER->id], '*', MUST_EXIST);

    if (!validate_internal_user_password($user, $password)) {
        $response->message = get_string('incorrectpassword', 'local_quiz_password_verify');
        echo json_encode($response);
        exit;
    }

    // Log the verification.
    // Note: We store activityid in the quizid column for backwards compatibility.
    $record = new stdClass();
    $record->quizid = $activityid;
    $record->userid = $USER->id;
    $record->attemptid = $attemptid ?: null; // Use null if 0.
    $record->timeverified = time();
    $record->ipaddress = getremoteaddr();
    $record->useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';

    $DB->insert_record('local_quiz_password_verify', $record);

    if ($attemptid) {
        // Mark this attempt as verified in session.
        $_SESSION['quiz_password_verified_' . $attemptid] = true;
    } else {
        // Mark this CMID as verified in session.
        $_SESSION['quiz_password_verified_cmid_' . $cmid] = true;
    }

    $response->success = true;
    $response->message = get_string('passwordverified', 'local_quiz_password_verify');
} catch (Exception $e) {
    $response->success = false;
    $response->message = $e->getMessage();
}

echo json_encode($response);

