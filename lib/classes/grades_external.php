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
 * Core grades external functions
 *
 * @package    core_grades
 * @copyright  2012 Andrew Davis
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.7
 */

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/gradelib.php");
require_once("$CFG->dirroot/grade/edit/tree/lib.php");
require_once("$CFG->dirroot/grade/querylib.php");

/**
 * core grades functions
 */
class core_grades_external extends external_api {
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.7
     */
    public static function update_grades_parameters() {
        return new external_function_parameters(
            array(
                'source' => new external_value(PARAM_TEXT, 'The source of the grade update'),
                'courseid' => new external_value(PARAM_INT, 'id of course'),
                'component' => new external_value(PARAM_COMPONENT, 'A component, for example mod_forum or mod_quiz'),
                'activityid' => new external_value(PARAM_INT, 'The activity ID'),
                'itemnumber' => new external_value(
                    PARAM_INT, 'grade item ID number for modules that have multiple grades. Typically this is 0.'),
                'grades' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'studentid' => new external_value(PARAM_INT, 'Student ID'),
                            'grade' => new external_value(PARAM_FLOAT, 'Student grade'),
                            'str_feedback' => new external_value(
                                PARAM_TEXT, 'A string representation of the feedback from the grader', VALUE_OPTIONAL),
                        )
                ), 'Any student grades to alter', VALUE_DEFAULT, array()),
                'itemdetails' => new external_single_structure(
                    array(
                        'itemname' => new external_value(
                            PARAM_ALPHANUMEXT, 'The grade item name', VALUE_OPTIONAL),
                        'idnumber' => new external_value(
                            PARAM_INT, 'Arbitrary ID provided by the module responsible for the grade item', VALUE_OPTIONAL),
                        'gradetype' => new external_value(
                            PARAM_INT, 'The type of grade (0 = none, 1 = value, 2 = scale, 3 = text)', VALUE_OPTIONAL),
                        'grademax' => new external_value(
                            PARAM_FLOAT, 'Maximum grade allowed', VALUE_OPTIONAL),
                        'grademin' => new external_value(
                            PARAM_FLOAT, 'Minimum grade allowed', VALUE_OPTIONAL),
                        'scaleid' => new external_value(
                            PARAM_INT, 'The ID of the custom scale being is used', VALUE_OPTIONAL),
                        'multfactor' => new external_value(
                            PARAM_FLOAT, 'Multiply all grades by this number', VALUE_OPTIONAL),
                        'plusfactor' => new external_value(
                            PARAM_FLOAT, 'Add this to all grades', VALUE_OPTIONAL),
                        'deleted' => new external_value(
                            PARAM_BOOL, 'True if the grade item should be deleted', VALUE_OPTIONAL),
                        'hidden' => new external_value(
                            PARAM_BOOL, 'True if the grade item is hidden', VALUE_OPTIONAL),
                    ), 'Any grade item settings to alter', VALUE_DEFAULT, array()
                )
            )
        );
    }

    /**
     * Update a grade item and, optionally, student grades
     *
     * @param  string $source       The source of the grade update
     * @param  int $courseid        The course id
     * @param  string $component    Component name
     * @param  int $activityid      The activity id
     * @param  int $itemnumber      The item number
     * @param  array  $grades      Array of grades
     * @param  array  $itemdetails Array of item details
     * @return int                  A status flag
     * @since Moodle 2.7
     */
    public static function update_grades($source, $courseid, $component, $activityid,
        $itemnumber, $grades = array(), $itemdetails = array()) {
        global $CFG;

        $params = self::validate_parameters(
            self::update_grades_parameters(),
            array(
                'source' => $source,
                'courseid' => $courseid,
                'component' => $component,
                'activityid' => $activityid,
                'itemnumber' => $itemnumber,
                'grades' => $grades,
                'itemdetails' => $itemdetails
            )
        );

        list($itemtype, $itemmodule) = normalize_component($params['component']);

        if (! $cm = get_coursemodule_from_id($itemmodule, $activityid)) {
            throw new moodle_exception('invalidcoursemodule');
        }
        $iteminstance = $cm->instance;

        $coursecontext = context_course::instance($params['courseid']);

        try {
            self::validate_context($coursecontext);
        } catch (Exception $e) {
            $exceptionparam = new stdClass();
            $exceptionparam->message = $e->getMessage();
            $exceptionparam->courseid = $params['courseid'];
            throw new moodle_exception('errorcoursecontextnotvalid' , 'webservice', '', $exceptionparam);
        }

        $hidinggrades = false;
        $editinggradeitem = false;
        $editinggrades = false;

        $gradestructure = array();
        foreach ($grades as $grade) {
            $editinggrades = true;
            $gradestructure[ $grade['studentid'] ] = array('userid' => $grade['studentid'], 'rawgrade' => $grade['grade']);
        }
        if (!empty($params['itemdetails'])) {
            if (isset($params['itemdetails']['hidden'])) {
                $hidinggrades = true;
            } else {
                $editinggradeitem = true;
            }
        }

        if ($editinggradeitem && !has_capability('moodle/grade:manage', $coursecontext)) {
            throw new moodle_exception('nopermissiontoviewgrades', 'error', '', null,
                'moodle/grade:manage required to edit grade information');
        }
        if ($hidinggrades && !has_capability('moodle/grade:hide', $coursecontext) &&
            !has_capability('moodle/grade:hide', $coursecontext)) {
            throw new moodle_exception('nopermissiontoviewgrades', 'error', '', null,
                'moodle/grade:hide required to hide grade items');
        }
        if ($editinggrades && !has_capability('moodle/grade:edit', $coursecontext)) {
            throw new moodle_exception('nopermissiontoviewgrades', 'error', '', null,
                'moodle/grade:edit required to edit grades');
        }

        return grade_update($params['source'], $params['courseid'], $itemtype,
            $itemmodule, $iteminstance, $itemnumber, $gradestructure, $params['itemdetails'], true);
    }

    /**
     * Returns description of method result value
     *
     * @return \core_external\external_description
     * @since Moodle 2.7
     */
    public static function update_grades_returns() {
        return new external_value(
            PARAM_INT,
            'A value like ' . GRADE_UPDATE_OK . ' => OK, ' . GRADE_UPDATE_FAILED . ' => FAILED
            as defined in lib/grade/constants.php'
        );
    }
}
