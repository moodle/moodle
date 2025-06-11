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
 * Web service method to update the properties of one or more slots in a quiz.
 *
 * The user must have the 'mod/quiz:manage' capability for the quiz.
 *
 * All the properties that can be set are optional. Only the ones passed are changed.
 * The full properties of the updated slot are returned.
 *
 * @package    mod_quiz
 * @copyright  2023 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class update_slots extends external_api {

    /**
     * Declare the method parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'quizid' => new external_value(PARAM_INT, 'The quiz to update slots for.'),
            'slots' => new external_multiple_structure(
                new external_single_structure([
                    'id' => new external_value(PARAM_INT, 'id of the slot'),
                    'displaynumber' => new external_value(
                        PARAM_TEXT,
                        'If passed, new customised question number. Empty string to clear customisation. ' .
                            'Null, or not specified, to leave unchanged.',
                        VALUE_OPTIONAL
                    ),
                    'requireprevious' => new external_value(
                        PARAM_BOOL,
                        'Whether to make this slot dependent on the previous one. Null, or not specified, to leave unchanged.',
                        VALUE_OPTIONAL
                    ),
                    'maxmark' => new external_value(
                        PARAM_FLOAT,
                        'Mark that this questions is out of. Null, or not specified, to leave unchanged.',
                        VALUE_OPTIONAL
                    ),
                    'quizgradeitemid' => new external_value(
                        PARAM_INT,
                        'For quizzes with multiple grades, which grade this slot contributes to (quiz_grade_id). ' .
                            '0 to set to nothing. Null, or not specified, to leave unchanged.',
                        VALUE_OPTIONAL
                    ),
                ])
            ),
        ]);
    }

    /**
     * Update the properties of one or more slots in a quiz.
     *
     * @param int $quizid the id of the quiz to update slots in.
     * @param array $slotsdata list of slots update. Must have properties id, any any other properties to change.
     */
    public static function execute(int $quizid, array $slotsdata): void {
        global $DB;

        [
            'quizid' => $quizid,
            'slots' => $slotsdata,
        ] = self::validate_parameters(self::execute_parameters(), [
            'quizid' => $quizid,
            'slots' => $slotsdata,
        ]);

        // Check the request is valid.
        $quizobj = quiz_settings::create($quizid);
        require_capability('mod/quiz:manage', $quizobj->get_context());
        self::validate_context($quizobj->get_context());

        $transaction = $DB->start_delegated_transaction();

        $structure = $quizobj->get_structure();
        $gradingsetupchanged = false;
        foreach ($slotsdata as $slotdata) {
            // Check this slot exists in this quiz.
            $slot = $structure->get_slot_by_id($slotdata['id']);

            if (isset($slotdata['displaynumber'])) {
                $structure->update_slot_display_number($slot->id, $slotdata['displaynumber']);
            }
            if (isset($slotdata['requireprevious'])) {
                $structure->update_question_dependency($slot->id, $slotdata['requireprevious']);
            }
            if (isset($slotdata['maxmark'])) {
                $gradingsetupchanged = $structure->update_slot_maxmark($slot, $slotdata['maxmark'])
                        || $gradingsetupchanged;
            }
            if (array_key_exists('quizgradeitemid', $slotdata)) {
                $gradingsetupchanged = $structure->update_slot_grade_item($slot, $slotdata['quizgradeitemid'])
                        || $gradingsetupchanged;
            }
        }

        // If the grade setup has canged, recompute things.
        if ($gradingsetupchanged) {
            $gradecalculator = $quizobj->get_grade_calculator();
            quiz_delete_previews($quizobj->get_quiz());
            $gradecalculator->recompute_quiz_sumgrades();
            $gradecalculator->recompute_all_attempt_sumgrades();
            $gradecalculator->recompute_all_final_grades();
            quiz_update_grades($quizobj->get_quiz(), 0, true);
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
