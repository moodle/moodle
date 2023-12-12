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
 * Web services relating to fetching of a rubric for the grading panel.
 *
 * @package    gradingform_rubric
 * @copyright  2019 Mathew May <mathew.solutions>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types = 1);

namespace gradingform_rubric\grades\grader\gradingpanel\external;

global $CFG;

use coding_exception;
use context;
use core_grades\component_gradeitem as gradeitem;
use core_grades\component_gradeitems;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use moodle_exception;
require_once($CFG->dirroot.'/grade/grading/form/rubric/lib.php');

/**
 * Web services relating to storing of a rubric for the grading panel.
 *
 * @package    gradingform_rubric
 * @copyright  2019 Mathew May <mathew.solutions>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class store extends external_api {

    /**
     * Describes the parameters for storing the grading panel for a simple grade.
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
            'notifyuser' => new external_value(
                PARAM_BOOL,
                'Wheteher to notify the user or not',
                VALUE_DEFAULT,
                false
            ),
            'formdata' => new external_value(
                PARAM_RAW,
                'The serialised form data representing the grade',
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
     * @param string $formdata
     * @param bool $notifyuser
     * @return array
     * @throws coding_exception
     * @throws moodle_exception
     * @since Moodle 3.8
     */
    public static function execute(string $component, int $contextid, string $itemname, int $gradeduserid, bool $notifyuser,
            string $formdata): array {
        global $USER;

        [
            'component' => $component,
            'contextid' => $contextid,
            'itemname' => $itemname,
            'gradeduserid' => $gradeduserid,
            'notifyuser' => $notifyuser,
            'formdata' => $formdata,
        ] = self::validate_parameters(self::execute_parameters(), [
            'component' => $component,
            'contextid' => $contextid,
            'itemname' => $itemname,
            'gradeduserid' => $gradeduserid,
            'notifyuser' => $notifyuser,
            'formdata' => $formdata,
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

        // Validate that this gradeitem is actually enabled.
        if (!$gradeitem->is_grading_enabled()) {
            throw new moodle_exception("Grading is not enabled for {$itemname} in this context");
        }

        // Fetch the record for the graded user.
        $gradeduser = \core_user::get_user($gradeduserid);

        // Require that this user can save grades.
        $gradeitem->require_user_can_grade($gradeduser, $USER);

        if (RUBRIC !== $gradeitem->get_advanced_grading_method()) {
            throw new moodle_exception(
                "The {$itemname} item in {$component}/{$contextid} is not configured for advanced grading with a rubric"
            );
        }

        // Parse the serialised string into an object.
        $data = [];
        parse_str($formdata, $data);

        // Grade.
        $gradeitem->store_grade_from_formdata($gradeduser, $USER, (object) $data);

        // Notify.
        if ($notifyuser) {
            // Send notification.
            $gradeitem->send_student_notification($gradeduser, $USER);
        }

        // Fetch the updated grade back out.
        $grade = $gradeitem->get_grade_for_user($gradeduser, $USER);

        return fetch::get_fetch_data($gradeitem, $gradeduser);
    }

    /**
     * Describes the data returned from the external function.
     *
     * @return external_single_structure
     * @since Moodle 3.8
     */
    public static function execute_returns(): external_single_structure {
        return fetch::execute_returns();
    }
}
