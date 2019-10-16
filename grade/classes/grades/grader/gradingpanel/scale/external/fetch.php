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
 * Web service functions relating to scale grades and grading.
 *
 * @package    core_grades
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types = 1);

namespace core_grades\grades\grader\gradingpanel\scale\external;

use coding_exception;
use context;
use core_grades\component_gradeitem as gradeitem;
use core_grades\component_gradeitems;
use core_user;
use external_api;
use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use external_warnings;
use moodle_exception;
use required_capability_exception;
use stdClass;

/**
 * External grading panel scale API
 *
 * @package    core_grades
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class fetch extends external_api {

    /**
     * Describes the parameters for fetching the grading panel for a simple grade.
     *
     * @return external_function_parameters
     * @since Moodle 3.8
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters ([
            'component' => new external_value(
                PARAM_ALPHANUMEXT,
                'The name of the component',
                VALUE_REQUIRED
            ),
            'contextid' => new external_value(
                PARAM_INT,
                'The ID of the context being graded',
                VALUE_REQUIRED
            ),
            'itemname' => new external_value(
                PARAM_ALPHANUM,
                'The grade item itemname being graded',
                VALUE_REQUIRED
            ),
            'gradeduserid' => new external_value(
                PARAM_INT,
                'The ID of the user show',
                VALUE_REQUIRED
            ),
        ]);
    }

    /**
     * Fetch the data required to build a grading panel for a simple grade.
     *
     * @param string $component
     * @param int $contextid
     * @param string $itemname
     * @param int $gradeduserid
     * @return array
     * @throws \dml_exception
     * @throws \invalid_parameter_exception
     * @throws \restricted_context_exception
     * @throws coding_exception
     * @throws moodle_exception
     * @since Moodle 3.8
     */
    public static function execute(string $component, int $contextid, string $itemname, int $gradeduserid): array {
        global $USER;

        [
            'component' => $component,
            'contextid' => $contextid,
            'itemname' => $itemname,
            'gradeduserid' => $gradeduserid,
        ] = self::validate_parameters(self::execute_parameters(), [
            'component' => $component,
            'contextid' => $contextid,
            'itemname' => $itemname,
            'gradeduserid' => $gradeduserid,
        ]);

        // Validate the context.
        $context = context::instance_by_id($contextid);
        self::validate_context($context);

        // Validate that the supplied itemname is a gradable item.
        if (!component_gradeitems::is_valid_itemname($component, $itemname)) {
            throw new coding_exception("The '{$itemname}' item is not valid for the '{$component}' component");
        }

        // Fetch the gradeitem instance.
        $gradeitem = gradeitem::instance($component, $context, $itemname);

        if (!$gradeitem->is_using_scale()) {
            throw new moodle_exception("The {$itemname} item in {$component}/{$contextid} is not configured for grading with scales");
        }

        $gradeduser = \core_user::get_user($gradeduserid);

        $maxgrade = (int) $gradeitem->get_grade_item()->grademax;

        return self::get_fetch_data($gradeitem, $gradeduser, $maxgrade);
    }

    /**
     * Get the data to be fetched.
     *
     * @param gradeitem $gradeitem
     * @param stdClass $gradeduser
     * @param int $maxgrade
     * @return array
     */
    public static function get_fetch_data(gradeitem $gradeitem, stdClass $gradeduser, int $maxgrade): array {
        global $USER;

        $hasgrade = $gradeitem->user_has_grade($gradeduser);
        $grade = $gradeitem->get_grade_for_user($gradeduser, $USER);
        $currentgrade = (int) unformat_float($grade->grade);

        $menu = $gradeitem->get_grade_menu();
        $values = array_map(function($description, $value) use ($currentgrade) {
            return [
                'value' => $value,
                'title' => $description,
                'selected' => ($value == $currentgrade),
            ];
        }, $menu, array_keys($menu));

        return [
            'templatename' => 'core_grades/grades/grader/gradingpanel/scale',
            'hasgrade' => $hasgrade,
            'grade' => [
                'options' => $values,
                'usergrade' => $grade->grade,
                'maxgrade' => $maxgrade,
                'timecreated' => $grade->timecreated,
                'timemodified' => $grade->timemodified,
            ],
            'warnings' => [],
        ];
    }

    /**
     * Describes the data returned from the external function.
     *
     * @return external_single_structure
     * @since Moodle 3.8
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'templatename' => new external_value(PARAM_SAFEPATH, 'The template to use when rendering this data'),
            'hasgrade' => new external_value(PARAM_BOOL, 'Does the user have a grade?'),
            'grade' => new external_single_structure([
                'options' => new external_multiple_structure(
                    new external_single_structure([
                        'value' => new external_value(PARAM_FLOAT, 'The grade value'),
                        'title' => new external_value(PARAM_RAW, 'The description fo the option'),
                        'selected' => new external_value(PARAM_BOOL, 'Whether this item is currently selected'),
                    ]),
                    'The description of the grade option'
                ),
                'usergrade' => new external_value(PARAM_RAW, 'Current user grade'),
                'maxgrade' => new external_value(PARAM_RAW, 'Max possible grade'),
                'timecreated' => new external_value(PARAM_INT, 'The time that the grade was created'),
                'timemodified' => new external_value(PARAM_INT, 'The time that the grade was last updated'),
            ]),
            'warnings' => new external_warnings(),
        ]);
    }
}
