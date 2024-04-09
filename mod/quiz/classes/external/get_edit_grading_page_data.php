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
use Exception;
use html_writer;
use mod_quiz\output\edit_grading_page;
use mod_quiz\quiz_attempt;
use mod_quiz\quiz_settings;
use moodle_exception;

/**
 * Web service to get the data required o re-render the Quiz grading setup page.
 *
 * The use must have the 'mod/quiz:manage' capability.
 *
 * @package    mod_quiz
 * @copyright  2023 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_edit_grading_page_data extends external_api {

    /**
     * Declare the method parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'quizid' => new external_value(PARAM_INT, 'The quiz for which to return the data.'),
        ]);
    }

    /**
     * Check a quiz attempt state, and return a confirmation message method implementation.
     *
     * @param int $quizid the quiz for which to return the data.
     * @return string a suitable confirmation message (HTML), if the attempt is suitable to be reopened.
     * @throws Exception an appropriate exception if the attempt cannot be reopened now.
     */
    public static function execute(int $quizid): string {
        global $PAGE;

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
        $editpage = new edit_grading_page($structure);
        return json_encode($editpage->export_for_template($PAGE->get_renderer('core')));
    }

    /**
     * Define the webservice response.
     *
     * @return external_description
     */
    public static function execute_returns(): external_description {
        return new external_value(PARAM_RAW, 'JSON-encoded data required to render the mod_quiz/edit_grading_page template.');
    }
}
