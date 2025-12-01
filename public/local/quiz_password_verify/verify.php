<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * AJAX endpoint to verify user password
 *
 * @package    local_quiz_password_verify
 * @copyright  2024
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/authlib.php');

    // Get parameters
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

        $quiz = null;

        if ($attemptid) {
            // Get the quiz attempt
            $attempt = $DB->get_record('quiz_attempts', array('id' => $attemptid), '*', MUST_EXIST);
            
            // Verify this is the user's attempt
            if ($attempt->userid != $USER->id) {
                throw new moodle_exception('invalidattempt', 'local_quiz_password_verify');
            }
            
            // Get the quiz
            $quiz = $DB->get_record('quiz', array('id' => $attempt->quiz), '*', MUST_EXIST);
        } else {
            // Get quiz from CMID
            $cm = get_coursemodule_from_id('quiz', $cmid, 0, false, MUST_EXIST);
            $quiz = $DB->get_record('quiz', array('id' => $cm->instance), '*', MUST_EXIST);
        }
        
        // Verify the password
        $user = $DB->get_record('user', array('id' => $USER->id), '*', MUST_EXIST);
        
        if (!validate_internal_user_password($user, $password)) {
            $response->message = get_string('incorrectpassword', 'local_quiz_password_verify');
            echo json_encode($response);
            exit;
        }
        
        // Log the verification
        $record = new stdClass();
        $record->quizid = $quiz->id;
        $record->userid = $USER->id;
        $record->attemptid = $attemptid ?: null; // Use null if 0
        $record->timeverified = time();
        $record->ipaddress = getremoteaddr();
        $record->useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $DB->insert_record('local_quiz_pwd_verify', $record);
        
        if ($attemptid) {
            // Mark this attempt as verified in session
            $_SESSION['quiz_password_verified_' . $attemptid] = true;
        } else {
            // Mark this CMID as verified in session
            $_SESSION['quiz_password_verified_cmid_' . $cmid] = true;
        }
        
        $response->success = true;
        $response->message = get_string('passwordverified', 'local_quiz_password_verify');
        
    } catch (Exception $e) {
        $response->success = false;
        $response->message = $e->getMessage();
    }

    echo json_encode($response);
