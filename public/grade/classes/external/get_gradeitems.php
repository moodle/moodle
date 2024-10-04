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

namespace core_grades\external;

defined('MOODLE_INTERNAL') || die;

use context_course;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;
use core_external\restricted_context_exception;
use grade_item;

require_once($CFG->libdir . '/gradelib.php');

/**
 * External grade get gradeitems API implementation
 *
 * @package    core_grades
 * @copyright  2023 Mathew May <mathew.solutions>
 * @category   external
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_gradeitems extends external_api {

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters (
            [
                'courseid' => new external_value(PARAM_INT, 'Course ID', VALUE_REQUIRED)
            ]
        );
    }

    /**
     * Given a course ID find the grading objects and return their names & IDs.
     *
     * @param int $courseid
     * @return array
     * @throws restricted_context_exception
     * @throws \invalid_parameter_exception
     */
    public static function execute(int $courseid): array {
        $params = self::validate_parameters(
            self::execute_parameters(),
            [
                'courseid' => $courseid
            ]
        );

        $warnings = [];
        $context = context_course::instance($params['courseid']);
        parent::validate_context($context);

        $allgradeitems = grade_item::fetch_all(['courseid' => $params['courseid']]);
        $gradeitems = array_filter($allgradeitems, function($item) {
            $item->itemname = $item->get_name();
            $item->category = $item->get_parent_category()->get_name();
            return $item->gradetype != GRADE_TYPE_NONE && !$item->is_category_item() && !$item->is_course_item();
        });

        return [
            'gradeItems' => $gradeitems,
            'warnings' => $warnings,
        ];
    }

    /**
     * Returns description of what gradeitems fetch should return.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'gradeItems' => new external_multiple_structure(
                new external_single_structure([
                    'id' => new external_value(PARAM_ALPHANUM, 'An ID for the grade item', VALUE_REQUIRED),
                    'itemname' => new external_value(PARAM_RAW, 'The full name of the grade item', VALUE_REQUIRED),
                    'category' => new external_value(PARAM_TEXT, 'The grade category of the grade item', VALUE_OPTIONAL),
                ])
            ),
            'warnings' => new external_warnings(),
        ]);
    }
}
