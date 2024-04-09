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
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use mod_quiz\quiz_attempt;
use mod_quiz\quiz_settings;
use moodle_exception;

/**
 * Web service method to delete quiz grade items.
 *
 * The user must have the 'mod/quiz:manage' capability for the quiz.
 *
 * The grade items to be deleted must all belong to the same quiz,
 * and must not be referred to by any slot.
 *
 * @package   mod_quiz
 * @copyright 2023 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class delete_grade_items extends external_api {

    /**
     * Declare the method parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'quizid' => new external_value(PARAM_INT, 'The quiz to update slots for.'),
            'quizgradeitems' => new external_multiple_structure(
                new external_single_structure([
                    'id' => new external_value(PARAM_INT, 'id of the quiz grade item'),
                ])
            ),
        ]);
    }

    /**
     * Delete quiz grade items, if they are unused.
     *
     * @param int $quizid the id of the quiz from which to dlete grade items.
     * @param array $gradeitems list of grade items to delete. (They must belong to this quiz.)
     */
    public static function execute(int $quizid, array $gradeitems): void {
        global $DB;

        [
            'quizid' => $quizid,
            'quizgradeitems' => $gradeitems,
        ] = self::validate_parameters(self::execute_parameters(), [
            'quizid' => $quizid,
            'quizgradeitems' => $gradeitems,
        ]);

        // Check the request is valid.
        $quizobj = quiz_settings::create($quizid);
        require_capability('mod/quiz:manage', $quizobj->get_context());
        self::validate_context($quizobj->get_context());

        $transaction = $DB->start_delegated_transaction();

        $structure = $quizobj->get_structure();
        foreach ($gradeitems as $gradeitemdata) {
            $structure->delete_grade_item($gradeitemdata['id']);
        }

        $transaction->allow_commit();
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
