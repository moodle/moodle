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

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/grade/lib.php');

/**
 * Web service to return the grade tree structure for a given course.
 *
 * @package    core_grades
 * @copyright  2023 Mihail Geshoski <mihail@moodle.com>
 * @category   external
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_grade_tree extends external_api {

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
     * Given a course ID, return the grade tree structure for that course.
     *
     * @param int $courseid The course ID.
     * @return string JSON encoded data representing the course grade tree structure.
     */
    public static function execute(int $courseid): string {

        $params = self::validate_parameters(
            self::execute_parameters(),
            [
                'courseid' => $courseid
            ]
        );

        $context = \context_course::instance($params['courseid']);
        parent::validate_context($context);
        // Make sure that the user has the capability to view the full grade tree in the course.
        require_capability('moodle/grade:viewall', $context);
        // Get the course grade category.
        $coursegradecategory = \grade_category::fetch_course_category($params['courseid']);
        $coursegradetree = self::generate_course_grade_tree($coursegradecategory);

        return json_encode($coursegradetree);
    }

    /**
     * Describes the return structure.
     *
     * @return external_value
     */
    public static function execute_returns(): external_value {
        return new external_value(PARAM_RAW, 'JSON encoded data representing the course grade tree structure.');
    }

    /**
     * Recursively generates the course grade tree structure.
     *
     * @param \grade_category $gradecategory The course grade category.
     * @return array The course grade tree structure.
     */
    private static function generate_course_grade_tree(\grade_category $gradecategory): array {
        $gradecategorydata = [
            'id' => $gradecategory->id,
            'name' => $gradecategory->get_name(),
            'iscategory' => true,
            'haschildcategories' => false
        ];

        // Get the children of the grade category.
        if ($gradecategorychildren = $gradecategory->get_children()) {
            foreach ($gradecategorychildren as $child) {
                // If the child is a grade category, recursively generate the grade tree structure for that category.
                if ($child['object'] instanceof \grade_category) {
                    $gradecategorydata['haschildcategories'] = true;
                    $gradecategorydata['children'][] = self::generate_course_grade_tree($child['object']);
                } else { // Otherwise, add the grade item to the grade tree structure.
                    $gradecategorydata['children'][] = [
                        'id' => $child['object']->id,
                        'name' => $child['object']->get_name(),
                        'iscategory' => false,
                        'children' => null
                    ];
                }
            }
        } else { // If the grade category has no children, set the children property to null.
            $gradecategorydata['children'] = null;
        }

        return $gradecategorydata;
    }
}
