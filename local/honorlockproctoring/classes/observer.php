<?php
// This file is part of the honorlockproctoring module for Moodle - http://moodle.org/
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
namespace local_honorlockproctoring;

/**
 * const string to track if a quiz is honorlock enabled
 */
const HL_NO_EDIT = 'HL_NO_EDIT';

/**
 * Honorlock proctoring observer.
 *
 * @package    local_honorlockproctoring
 * @copyright  2023 Honorlock (https://honorlock.com/)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class observer {
    /**
     * Handles a course quiz viewed event (Quiz Attempt List Page)
     * Calls appropriate frontent JS method viewQuiz()
     *
     * @param \mod_quiz\event\course_module_viewed $event
     * @return void
     */
    public static function quiz_viewed(\mod_quiz\event\course_module_viewed $event): void {
        global $PAGE, $SESSION;

        $data = $event->get_data();

        // Get Quiz ID.
        $quizid = $data['objectid'];

        // Check Honorlock Enabled.
        if (!static::is_honorlock_enabled_quiz($quizid)) {
            return;
        }

        // Clear out password checked for this quiz to make sure we always show the preflight page.
        // Since this is where we do HL auth/checks.
        unset($SESSION->passwordcheckedquizzes[$quizid]);

        $PAGE->requires->js_call_amd('local_honorlockproctoring/honorlockproctoring', 'viewQuiz');
    }

    /**
     * Handles a viewed quiz attempt.
     * Submits a sessionBegin/Continue API call for an in progress attempt.
     *
     * @param \mod_quiz\event\attempt_viewed $event
     * @return void
     */
    public static function quiz_attempt_viewed(\mod_quiz\event\attempt_viewed $event): void {
        global $DB, $PAGE;

        $data = $event->get_data();

        // Get attempt information.
        $attempt = $DB->get_record('quiz_attempts', [
            'id' => $data['objectid'],
        ], 'quiz, userid, attempt, state');

        // Check Honorlock Enabled.
        if (!static::is_honorlock_enabled_quiz($attempt->quiz)) {
            return;
        }

        // Make sure it's an in progress attempt and not just viewing a completed attempt.
        if ($attempt->state !== 'inprogress') {
            return;
        }

        // Get Honorlock instance.
        $honorlock = static::get_honorlock_instance();

        // Start/Continue Session.
        $honorlock->begin_session($attempt->userid, $attempt->quiz, $attempt->attempt);

        $PAGE->requires->js_call_amd('local_honorlockproctoring/honorlockproctoring', 'takeQuiz');
    }

    /**
     * Handles a submited quiz attempt.
     * Submits a session completed API call for the attempt.
     *
     * @param \mod_quiz\event\attempt_submitted $event
     */
    public static function quiz_attempt_submitted(\mod_quiz\event\attempt_submitted $event): void {
        global $DB;

        $data = $event->get_data();

        // Get attempt information.
        $attempt = $DB->get_record('quiz_attempts', [
            'id' => $data['objectid'],
        ], 'quiz, userid, attempt');

        // Check Honorlock Enabled.
        if (!static::is_honorlock_enabled_quiz($attempt->quiz)) {
            return;
        }

        // Get Honorlock instance.
        $honorlock = static::get_honorlock_instance();

        // End Session.
        $honorlock->end_session($attempt->userid, $attempt->quiz, $attempt->attempt);
    }

    /**
     * Checks if the provided quiz $id is Honorlock Enabled.
     *
     * @param int $quizid
     * @return bool
     */
    private static function is_honorlock_enabled_quiz(int $quizid): bool {
        global $DB;

        $quiz = $DB->get_record('quiz', [
            'id' => $quizid,
        ], 'password');

        return strpos($quiz->password, HL_NO_EDIT) === 0;
    }

    /**
     * Builds an instance of Honorlock using config params in HL Proctoring Settings.
     *
     * @return honorlock
     */
    private static function get_honorlock_instance(): honorlock {

        return new honorlock();
    }
}
