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

namespace gradereport_singleview\external;

use context_course;
use core_course_external;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;
use grade_tree;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/course/externallib.php');
require_once($CFG->dirroot.'/grade/lib.php');

/**
 * External grade report singleview API
 *
 * @package    gradereport_singleview
 * @copyright  2022 Mathew May <mathew.solutions>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class singleview extends core_course_external {
    /**
     * Describes the parameters for get_users_for_course.
     *
     * @return external_function_parameters
     */
    public static function get_grade_items_for_search_widget_parameters(): external_function_parameters {
        return new external_function_parameters (
            [
                'courseid' => new external_value(PARAM_INT, 'Course Id', VALUE_REQUIRED)
            ]
        );
    }

    /**
     * Given a course ID find the
     *
     * @param int $courseid
     * @return array Users and warnings to pass back to the calling widget.
     * @throws coding_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @throws restricted_context_exception
     */
    protected static function get_grade_items_for_search_widget(int $courseid): array {
        global $PAGE, $USER, $CFG;

        $params = self::validate_parameters(
            self::get_grade_items_for_search_widget_parameters(),
            [
                'courseid' => $courseid,
            ]
        );

        $warnings = [];
        $coursecontext = context_course::instance($params['courseid']);
        parent::validate_context($coursecontext);

        $gtree = new grade_tree($params['courseid'], false, true, null, !$CFG->enableoutcomes);
        $gradeableitems = $gtree->get_items();

        $gradeitems = array_map(function ($gradeitem) use ($PAGE, $USER, $params) {
            $item = new \stdClass();
            $item->id = $gradeitem->id;
            $item->name = $gradeitem->get_name(true);

            return $item;
        }, $gradeableitems);

        return [
            'gradeitems' => $gradeitems,
            'warnings' => $warnings,
        ];
    }

    /**
     * Returns description of what the user search for the widget should return.
     *
     * @return external_single_structure
     */
    public static function get_grade_items_for_search_widget_returns(): external_single_structure {
        return new external_single_structure([
            'gradeitems' => new external_multiple_structure(
                new external_single_structure([
                    'id'    => new external_value(PARAM_INT, 'ID of the grade item', VALUE_OPTIONAL),
                    'name' => new external_value(PARAM_TEXT, 'The full name of the grade item', VALUE_OPTIONAL)
                ])
            ),
            'warnings' => new external_warnings(),
        ]);
    }
}
