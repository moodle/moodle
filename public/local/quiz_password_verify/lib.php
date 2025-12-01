<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Inject JS via navigation extension (fallback for hooks)
 *
 * @param global_navigation $nav
 */
function local_quiz_password_verify_extend_navigation(global_navigation $nav) {
    global $PAGE, $DB;

    // Run on quiz attempt, summary, view, and course pages
    // Use strpos to match sub-types (e.g. course-view-topics)
    if (strpos($PAGE->pagetype, 'mod-quiz-attempt') !== 0 && 
        strpos($PAGE->pagetype, 'mod-quiz-summary') !== 0 &&
        strpos($PAGE->pagetype, 'mod-quiz-view') !== 0 &&
        strpos($PAGE->pagetype, 'course-view') !== 0) {
        return;
    }

    // Get attempt ID
    $attemptid = optional_param('attempt', 0, PARAM_INT);

    // Inject JS
    $PAGE->requires->strings_for_js(array(
        'verifyyouridentity', 
        'enteryourpassword', 
        'passwordhelp', 
        'verify', 
        'incorrectpassword', 
        'passwordverified'
    ), 'local_quiz_password_verify');
    $PAGE->requires->js_call_amd('local_quiz_password_verify/verify', 'init', array($attemptid));
}
