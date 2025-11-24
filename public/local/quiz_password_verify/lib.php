<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Library functions for quiz password verification
 *
 * @package    local_quiz_password_verify
 * @copyright  2024
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Hook to inject JavaScript into quiz pages
 *
 * @param moodle_page $page
 */
function local_quiz_password_verify_before_footer() {
    global $PAGE, $DB;
    
    // Only run on quiz attempt pages
    if ($PAGE->pagetype !== 'mod-quiz-attempt') {
        return;
    }
    
    // Get the attempt ID from the URL
    $attemptid = optional_param('attempt', 0, PARAM_INT);
    
    if (!$attemptid) {
        return;
    }
    
    // Verify the attempt exists
    $attempt = $DB->get_record('quiz_attempts', array('id' => $attemptid));
    
    if (!$attempt) {
        return;
    }
    
    // Check if already verified in this session
    if (isset($_SESSION['quiz_password_verified_' . $attemptid])) {
        return;
    }
    
    // Inject the JavaScript
    $PAGE->requires->js_call_amd('local_quiz_password_verify/verify', 'init', array($attemptid));
}
