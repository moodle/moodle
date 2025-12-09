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
 * Library functions for local_quiz_password_verify
 *
 * @package    local_quiz_password_verify
 * @copyright  2024 Author Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */



/**
 * Inject JS via navigation extension (fallback for hooks)
 *
 * @package local_quiz_password_verify
 * @param global_navigation $nav
 */
function local_quiz_password_verify_extend_navigation(global_navigation $nav) {
    // Prevent PHPMD "UnusedFormalParameter" warning by unsetting the variable.
    unset($nav);
    global $PAGE;

    // Run on quiz attempt, summary, view, and course pages.
    // Use strpos to match sub-types (e.g. course-view-topics).
    if (
        strpos($PAGE->pagetype, 'mod-quiz-attempt') !== 0 &&
        strpos($PAGE->pagetype, 'mod-quiz-summary') !== 0 &&
        strpos($PAGE->pagetype, 'mod-quiz-view') !== 0 &&
        strpos($PAGE->pagetype, 'course-view') !== 0
    ) {
        return;
    }

    // Get attempt ID.
    $attemptid = optional_param('attempt', 0, PARAM_INT);

    // Inject JS.
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
