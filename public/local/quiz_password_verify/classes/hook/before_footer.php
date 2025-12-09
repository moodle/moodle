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
 * Before footer hook definition for local_quiz_password_verify
 *
 * @package    local_quiz_password_verify
 * @copyright  2024 Author Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_quiz_password_verify\hook;

use core\hook\output\before_footer as before_footer_hook;



/**
 * Hook to before_footer
 *
 * @package    local_quiz_password_verify
 * @copyright  2024 Author Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class before_footer {
    /**
     * Callback to inject JavaScript into quiz pages
     *
     * @param before_footer_hook $hook The hook object
     */
    public static function callback(before_footer_hook $hook): void {
        unset($hook);
        global $PAGE, $DB;

        // Run on quiz attempt, summary, view, and course pages (for manual completion).
        // Use strpos to match sub-types (e.g. course-view-topics).
        if (
            strpos($PAGE->pagetype, 'mod-quiz-attempt') !== 0 &&
            strpos($PAGE->pagetype, 'mod-quiz-summary') !== 0 &&
            strpos($PAGE->pagetype, 'mod-quiz-view') !== 0 &&
            strpos($PAGE->pagetype, 'course-view') !== 0
        ) {
            return;
        }

        // Get the attempt ID from the URL (if available).
        $attemptid = optional_param('attempt', 0, PARAM_INT);

        // If we have an attempt ID, verify it exists.
        if ($attemptid) {
            $attempt = $DB->get_record('quiz_attempts', ['id' => $attemptid]);
            if (!$attempt) {
                return;
            }
        }

        // Inject the JavaScript.
        $PAGE->requires->strings_for_js([
            'verifyyouridentity',
            'enteryourpassword',
            'passwordhelp',
            'verify',
            'incorrectpassword',
            'passwordverified',
        ], 'local_quiz_password_verify');
        $PAGE->requires->js_call_amd('local_quiz_password_verify/verify', 'init', [$attemptid]);
    }
}
