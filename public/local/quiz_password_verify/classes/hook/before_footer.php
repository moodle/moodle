<?php
namespace local_quiz_password_verify\hook;

use core\hook\output\before_footer as before_footer_hook;

defined('MOODLE_INTERNAL') || die();

class before_footer {
    /**
     * Callback to inject JavaScript into quiz pages
     *
     * @param before_footer_hook $hook
     */
    public static function callback(before_footer_hook $hook): void {
        global $PAGE, $DB;
        // die('HOOK FIRED: ' . $PAGE->pagetype); // Uncomment to test hook firing

        // Run on quiz attempt, summary, view, and course pages (for manual completion)
        // Use strpos to match sub-types (e.g. course-view-topics)
        if (strpos($PAGE->pagetype, 'mod-quiz-attempt') !== 0 && 
            strpos($PAGE->pagetype, 'mod-quiz-summary') !== 0 &&
            strpos($PAGE->pagetype, 'mod-quiz-view') !== 0 &&
            strpos($PAGE->pagetype, 'course-view') !== 0) {
            return;
        }

        // Get the attempt ID from the URL
        // Get the attempt ID from the URL (if available)
        $attemptid = optional_param('attempt', 0, PARAM_INT);

        // If we have an attempt ID, verify it exists
        if ($attemptid) {
            $attempt = $DB->get_record('quiz_attempts', array('id' => $attemptid));
            if (!$attempt) {
                return;
            }
            
            // Check if already verified in this session
            if (isset($_SESSION['quiz_password_verified_' . $attemptid])) {
                // We still inject JS because we might need it for summary page submission interception
                // But maybe we can skip? No, let's inject to be safe.
            }
        }

        // Inject the JavaScript
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
}
