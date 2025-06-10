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

/**
 * Honorlock proctoring interface functions.
 *
 * @package    local_honorlockproctoring
 * @copyright  2023 Honorlock (https://honorlock.com/)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_honorlockproctoring\honorlock;

/**
 * const string to track if a quiz is honorlock enabled
 */
const HL_NO_EDIT = 'HL_NO_EDIT';

/**
 * Honorlock proctoring.
 *
 * @param global_navigation $navigation
 * @return void
 */
function local_honorlockproctoring_extend_navigation(global_navigation $navigation): void {
    global $PAGE;
    global $CFG;
    global $USER;
    global $DB;

    if ($PAGE->cm && $PAGE->cm->modname === 'quiz') {
        $quizid = $PAGE->cm->instance;
        $quiz = $DB->get_record('quiz', ['id' => $quizid]);
        $attempts = $DB->get_records('quiz_attempts', [
            'quiz' => $quizid,
            'userid' => $USER->id,
        ], 'attempt DESC');

         // Determine attempt data.
         $attemptid = 1;

        if (!empty($attempts)) {
            $attempt = current($attempts);

            if ($attempt->state === "inprogress") {
                // We are continuing an attempt.
                $attemptid = $attempt->attempt;
            } else {
                // We will be starting a new attempt shortly.
                // Moodle hasn't yet created the newest/next attempt at this point.
                // So we need to anticipate the next attempt id based on the latest attempt (increment by 1).
                $attemptid = $attempt->attempt + 1;
            }
        }

        if (is_honorlock_enabled_quiz($quiz)) {
            $pageurl = $PAGE->url->get_path();

            // Extension Check/Exam Instructions page.
            if (strpos($pageurl, "/mod/quiz/startattempt.php") !== false) {
                $honorlock = new honorlock();

                // Hit HL-API extension-check.
                $extensioncheckresult = $honorlock->extension_check();

                // Hit HL-API create session.
                $sessiondetails = [
                    'exam_taker_id' => $USER->id,
                    'exam_taker_email' => $USER->email,
                    'exam_taker_first_name' => $USER->firstname,
                    'exam_taker_last_name' => $USER->lastname,
                    'external_exam_id' => $quiz->id,
                    'exam_taker_attempt_id' => $attemptid,
                ];
                $sessioncreateresult = $honorlock->create_session($sessiondetails);

                // Hit HL-API get exam instructions.
                $examinstructions = $honorlock->get_exam_instructions($quiz->id);

                $PAGE->requires->js_call_amd('local_honorlockproctoring/honorlockproctoring', 'examAuth', [[
                    'extension_instruction_url' => $extensioncheckresult->iframe_src,
                    'quiz_password' => $quiz->password,
                    'session_details' => $sessioncreateresult,
                    'exam_instructions' => $examinstructions,
                    'exam_id' => $quiz->id,
                    'attempt_id' => $attemptid,
                    'exam_taker' => [
                        'id' => $USER->id,
                        'name' => $USER->firstname." ".$USER->lastname,
                    ],
                    'app_url' => $CFG->wwwroot,
                ]]);
            }

            // Exam Pre-submit/Summary Page.
            if ($PAGE->pagetype === "mod-quiz-summary") {
                $PAGE->requires->js_call_amd('local_honorlockproctoring/honorlockproctoring', 'quizSummary');
            }
        }
    }
}

/**
 * Check if quiz is honorlock enabled.
 *
 * @param object $quiz
 * @return bool
 */
function is_honorlock_enabled_quiz(object $quiz): bool {
    return strpos($quiz->password, HL_NO_EDIT) === 0;
}
