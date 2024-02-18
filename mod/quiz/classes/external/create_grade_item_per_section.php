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

use coding_exception;
use core_external\external_api;
use core_external\external_description;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use mod_quiz\quiz_attempt;
use mod_quiz\quiz_settings;
use moodle_exception;
use stdClass;

/**
 * For a quiz with no grade items yet, create a grade item for each section.
 *
 * And, assign the questions in each section to the corresponding grade item.
 *
 * The user must have the 'mod/quiz:manage' capability for the quiz.
 *
 * @package   mod_quiz
 * @copyright 2024 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class create_grade_item_per_section extends external_api {

    /**
     * Declare the method parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'quizid' => new external_value(PARAM_INT, 'The quiz to update slots for.'),
        ]);
    }

    /**
     * For a quiz with no grade items yet, create a grade item for each section.
     *
     * And, assign the questions in each section to the corresponding grade item.
     *
     * The user must have the 'mod/quiz:manage' capability for the quiz.
     *
     * @param int $quizid the id of the quiz to setup grade items for.
     */
    public static function execute(int $quizid): void {
        global $DB;

        [
            'quizid' => $quizid,
        ] = self::validate_parameters(self::execute_parameters(), [
            'quizid' => $quizid,
        ]);

        // Check the request is valid.
        $quizobj = quiz_settings::create($quizid);
        require_capability('mod/quiz:manage', $quizobj->get_context());
        self::validate_context($quizobj->get_context());

        $structure = $quizobj->get_structure();
        if ($structure->get_grade_items()) {
            throw new coding_exception('Cannot use create_grade_item_per_section for a quiz ' .
                'that already has grade items.');
        }

        $transaction = $DB->start_delegated_transaction();

        $gradeitemsids = [];
        foreach ($structure->get_sections() as $section) {
            $gradeitem = new stdClass();
            $gradeitem->quizid = $quizid;
            $gradeitem->name = $section->heading;
            $structure->create_grade_item($gradeitem);
            $gradeitemsids[$section->id] = $gradeitem->id;
        }

        foreach ($structure->get_slots() as $slot) {
            $structure->update_slot_grade_item($slot, $gradeitemsids[$slot->section->id]);
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
