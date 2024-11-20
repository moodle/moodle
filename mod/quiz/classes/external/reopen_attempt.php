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

namespace mod_quiz\external;

use core_external\external_api;
use core_external\external_description;
use core_external\external_function_parameters;
use core_external\external_value;
use mod_quiz\quiz_attempt;
use moodle_exception;

/**
 * Web service method for re-opening a quiz attempt.
 *
 * The use must have the 'mod/quiz:reopenattempts' capability and the attempt
 * must (at least for now) be in the 'Never submitted' state (quiz_attempt::ABANDONED).
 *
 * @package    mod_quiz
 * @copyright  2023 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class reopen_attempt extends external_api {

    /**
     * Declare the method parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'attemptid' => new external_value(PARAM_INT, 'The id of the attempt to reopen'),
        ]);
    }

    /**
     * Re-opening a submitted attempt method implementation.
     *
     * @param int $attemptid the id of the attempt to reopen.
     */
    public static function execute(int $attemptid): void {
        ['attemptid' => $attemptid] = self::validate_parameters(
                self::execute_parameters(), ['attemptid' => $attemptid]);

        // Check the request is valid.
        $attemptobj = quiz_attempt::create($attemptid);
        require_capability('mod/quiz:reopenattempts', $attemptobj->get_context());
        self::validate_context($attemptobj->get_context());
        if ($attemptobj->get_state() != quiz_attempt::ABANDONED) {
            throw new moodle_exception('reopenattemptwrongstate', 'quiz', '',
                    ['attemptid' => $attemptid, 'state' => quiz_attempt_state_name($attemptobj->get_state())]);
        }

        // Re-open the attempt.
        $attemptobj->process_reopen_abandoned(time());
    }

    /**
     * Define the webservice response.
     *
     * @return external_description|null always null.
     */
    public static function execute_returns(): ?external_description {
        return null;
    }
}
