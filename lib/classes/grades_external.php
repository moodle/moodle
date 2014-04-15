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

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");
require_once("$CFG->libdir/gradelib.php");

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
    public static function get_grades_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'id of course'),
                'component' => new external_value(
                    PARAM_COMPONENT, 'A component, for example mod_forum or mod_quiz', VALUE_DEFAULT, ''),
                'activityid' => new external_value(PARAM_INT, 'The activity ID', VALUE_DEFAULT, null),
                'userids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'user ID'),
                    'An array of user IDs, leave empty to just retrieve grade item information', VALUE_DEFAULT, array()
                )
            )
        );
    }

    /**
     * Retrieve grade items and, optionally, student grades
     *
     * @param  int $courseid        Course id
     * @param  string $component    Component name
     * @param  int $activityid      Activity id
     * @param  array  $userids      Array of user ids
     * @return array                Array of grades
     * @since Moodle 2.7
     */
    public static function get_grades($courseid, $component = null, $activityid = null, $userids = array()) {
        global $CFG, $USER, $DB;

        $params = self::validate_parameters(self::get_grades_parameters(),
            array('courseid' => $courseid, 'component' => $component, 'activityid' => $activityid, 'userids' => $userids));

        $coursecontext = context_course::instance($params['courseid']);

        try {
            self::validate_context($coursecontext);
        } catch (Exception $e) {
            $exceptionparam = new stdClass();
            $exceptionparam->message = $e->getMessage();
            $exceptionparam->courseid = $params['courseid'];
            throw new moodle_exception('errorcoursecontextnotvalid' , 'webservice', '', $exceptionparam);
        }

        $course = $DB->get_record('course', array('id' => $params['courseid']), '*', MUST_EXIST);

        $access = false;
        if (has_capability('moodle/grade:viewall', $coursecontext)) {
            // Can view all user's grades in this course.
            $access = true;

        } else if ($course->showgrades && count($params['userids']) == 1) {
            // Course showgrades == students/parents can access grades.

            if ($params['userids'][0] == $USER->id and has_capability('moodle/grade:view', $coursecontext)) {
                // Student can view their own grades in this course.
                $access = true;

            } else if (has_capability('moodle/grade:viewall', context_user::instance($params['userids'][0]))) {
                // User can view the grades of this user. Parent most probably.
                $access = true;
            }
        }

        if (!$access) {
            throw new moodle_exception('nopermissiontoviewgrades', 'error');
        }

        $itemtype = null;
        $itemmodule = null;
        if (!empty($params['component'])) {
            list($itemtype, $itemmodule) = normalize_component($params['component']);
        }

        $cm = null;
        if (!empty($itemmodule) && !empty($activityid)) {
            if (! $cm = get_coursemodule_from_id($itemmodule, $activityid)) {
                throw new moodle_exception('invalidcoursemodule');
            }
        }

        $cminstanceid = null;
        if (!empty($cm)) {
            $cminstanceid = $cm->instance;
        }
        $grades = grade_get_grades($params['courseid'], $itemtype, $itemmodule, $cminstanceid, $params['userids']);

        $acitivityinstances = null;
        if (empty($cm)) {
            // If we're dealing with multiple activites load all the module info.
            $modinfo = get_fast_modinfo($params['courseid']);
            $acitivityinstances = $modinfo->get_instances();
        }

        foreach ($grades->items as $gradeitem) {
            if (!empty($cm)) {
                // If they only requested one activity we will already have the cm.
                $modulecm = $cm;
            } else if (!empty($gradeitem->itemmodule)) {
                $modulecm = $acitivityinstances[$gradeitem->itemmodule][$gradeitem->iteminstance];
            } else {
                // Course grade item.
                continue;
            }

            // Make student feedback ready for output.
            foreach ($gradeitem->grades as $studentgrade) {
                if (!empty($studentgrade->feedback)) {
                    list($studentgrade->feedback, $categoryinfo->feedbackformat) =
                        external_format_text($studentgrade->feedback, $studentgrade->feedbackformat,
                        $modulecm->id, $params['component'], 'feedback', null);
                }
            }
        }

        // Convert from objects to arrays so all web service clients are supported.
        // While we're doing that we also remove grades the current user can't see due to hiding.
        $gradesarray = array();
        $canviewhidden = has_capability('moodle/grade:viewhidden', context_course::instance($params['courseid']));

        $gradesarray['items'] = array();
        foreach ($grades->items as $gradeitem) {
            // Switch the stdClass instance for a grade item instance so we can call is_hidden() and use the ID.
            $gradeiteminstance = self::get_grade_item(
                $course->id, $gradeitem->itemtype, $gradeitem->itemmodule, $gradeitem->iteminstance, 0);
            if (!$canviewhidden && $gradeiteminstance->is_hidden()) {
                continue;
            }
            $gradeitemarray = (array)$gradeitem;
            $gradeitemarray['grades'] = array();

            if (!empty($gradeitem->grades)) {
                foreach ($gradeitem->grades as $studentid => $studentgrade) {
                    $gradegradeinstance = grade_grade::fetch(
                        array(
                            'userid' => $studentid,
                            'itemid' => $gradeiteminstance->id
                        )
                    );
                    if (!$canviewhidden && $gradegradeinstance->is_hidden()) {
                        continue;
                    }
                    $gradeitemarray['grades'][$studentid] = (array)$studentgrade;
                    // Add the student ID as some WS clients can't access the array key.
                    $gradeitemarray['grades'][$studentid]['userid'] = $studentid;
                }
            }

            // If they requested grades for multiple activities load the cm object now.
            $modulecm = $cm;
            if (empty($modulecm) && !empty($gradeiteminstance->itemmodule)) {
                $modulecm = $acitivityinstances[$gradeiteminstance->itemmodule][$gradeiteminstance->iteminstance];
            }
            if ($gradeiteminstance->itemtype == 'course') {
                $gradesarray['items']['course'] = $gradeitemarray;
                $gradesarray['items']['course']['activityid'] = 'course';
            } else {
                $gradesarray['items'][$modulecm->id] = $gradeitemarray;
                // Add the activity ID as some WS clients can't access the array key.
                $gradesarray['items'][$modulecm->id]['activityid'] = $modulecm->id;
            }
        }

        $gradesarray['outcomes'] = array();
        foreach ($grades->outcomes as $outcome) {
            $modulecm = $cm;
            if (empty($modulecm)) {
                $modulecm = $acitivityinstances[$outcome->itemmodule][$outcome->iteminstance];
            }
            $gradesarray['outcomes'][$modulecm->id] = (array)$outcome;
            $gradesarray['outcomes'][$modulecm->id]['activityid'] = $modulecm->id;

            $gradesarray['outcomes'][$modulecm->id]['grades'] = array();
            if (!empty($outcome->grades)) {
                foreach ($outcome->grades as $studentid => $studentgrade) {
                    if (!$canviewhidden) {
                        // Need to load the grade_grade object to check visibility.
                        $gradeiteminstance = self::get_grade_item(
                            $course->id, $outcome->itemtype, $outcome->itemmodule, $outcome->iteminstance, $outcome->itemnumber);
                        $gradegradeinstance = grade_grade::fetch(
                            array(
                                'userid' => $studentid,
                                'itemid' => $gradeiteminstance->id
                            )
                        );
                        // The grade grade may be legitimately missing if the student has no grade.
                        if (!empty($gradegradeinstance ) && $gradegradeinstance->is_hidden()) {
                            continue;
                        }
                    }
                    $gradesarray['outcomes'][$modulecm->id]['grades'][$studentid] = (array)$studentgrade;

                    // Add the student ID into the grade structure as some WS clients can't access the key.
                    $gradesarray['outcomes'][$modulecm->id]['grades'][$studentid]['userid'] = $studentid;
                }
            }
        }

        return $gradesarray;
    }

    /**
     * Get a grade item
     * @param  int $courseid        Course id
     * @param  string $itemtype     Item type
     * @param  string $itemmodule   Item module
     * @param  int $iteminstance    Item instance
     * @param  int $itemnumber      Item number
     * @return grade_item           A grade_item instance
     */
    private static function get_grade_item($courseid, $itemtype, $itemmodule = null, $iteminstance = null, $itemnumber = null) {
        $gradeiteminstance = null;
        if ($itemtype == 'course') {
            $gradeiteminstance = grade_item::fetch(array('courseid' => $courseid, 'itemtype' => $itemtype));
        } else {
            $gradeiteminstance = grade_item::fetch(
                array('courseid' => $courseid, 'itemtype' => $itemtype,
                    'itemmodule' => $itemmodule, 'iteminstance' => $iteminstance, 'itemnumber' => $itemnumber));
        }
        return $gradeiteminstance;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.7
     */
    public static function get_grades_returns() {
        return new external_single_structure(
            array(
                'items'  => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'activityid' => new external_value(
                                PARAM_ALPHANUM, 'The ID of the activity or "course" for the course grade item'),
                            'itemnumber'  => new external_value(PARAM_INT, 'Will be 0 unless the module has multiple grades'),
                            'scaleid' => new external_value(PARAM_INT, 'The ID of the custom scale or 0'),
                            'name' => new external_value(PARAM_RAW, 'The module name'),
                            'grademin' => new external_value(PARAM_FLOAT, 'Minimum grade'),
                            'grademax' => new external_value(PARAM_FLOAT, 'Maximum grade'),
                            'gradepass' => new external_value(PARAM_FLOAT, 'The passing grade threshold'),
                            'locked' => new external_value(PARAM_BOOL, 'Is the grade item locked?'),
                            'hidden' => new external_value(PARAM_BOOL, 'Is the grade item hidden?'),
                            'grades' => new external_multiple_structure(
                                new external_single_structure(
                                    array(
                                        'userid' => new external_value(
                                            PARAM_INT, 'Student ID'),
                                        'grade' => new external_value(
                                            PARAM_FLOAT, 'Student grade'),
                                        'locked' => new external_value(
                                            PARAM_BOOL, 'Is the student\'s grade locked?'),
                                        'hidden' => new external_value(
                                            PARAM_BOOL, 'Is the student\'s grade hidden?'),
                                        'overridden' => new external_value(
                                            PARAM_BOOL, 'Is the student\'s grade overridden?'),
                                        'feedback' => new external_value(
                                            PARAM_RAW, 'Feedback from the grader'),
                                        'feedbackformat' => new external_value(
                                            PARAM_INT, 'The format of the feedback'),
                                        'usermodified' => new external_value(
                                            PARAM_INT, 'The ID of the last user to modify this student grade'),
                                        'datesubmitted' => new external_value(
                                            PARAM_INT, 'A timestamp indicating when the student submitted the activity'),
                                        'dategraded' => new external_value(
                                            PARAM_INT, 'A timestamp indicating when the assignment was grades'),
                                        'str_grade' => new external_value(
                                            PARAM_RAW, 'A string representation of the grade'),
                                        'str_long_grade' => new external_value(
                                            PARAM_RAW, 'A nicely formatted string representation of the grade'),
                                        'str_feedback' => new external_value(
                                            PARAM_TEXT, 'A string representation of the feedback from the grader'),
                                    )
                                )
                            ),
                        )
                    )
                ),
                'outcomes'  => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'activityid' => new external_value(
                                PARAM_ALPHANUM, 'The ID of the activity or "course" for the course grade item'),
                            'itemnumber'  => new external_value(PARAM_INT, 'Will be 0 unless the module has multiple grades'),
                            'scaleid' => new external_value(PARAM_INT, 'The ID of the custom scale or 0'),
                            'name' => new external_value(PARAM_RAW, 'The module name'),
                            'locked' => new external_value(PARAM_BOOL, 'Is the grade item locked?'),
                            'hidden' => new external_value(PARAM_BOOL, 'Is the grade item hidden?'),
                            'grades' => new external_multiple_structure(
                                new external_single_structure(
                                    array(
                                        'userid' => new external_value(
                                            PARAM_INT, 'Student ID'),
                                        'grade' => new external_value(
                                            PARAM_FLOAT, 'Student grade'),
                                        'locked' => new external_value(
                                            PARAM_BOOL, 'Is the student\'s grade locked?'),
                                        'hidden' => new external_value(
                                            PARAM_BOOL, 'Is the student\'s grade hidden?'),
                                        'feedback' => new external_value(
                                            PARAM_RAW, 'Feedback from the grader'),
                                        'feedbackformat' => new external_value(
                                            PARAM_INT, 'The feedback format'),
                                        'usermodified' => new external_value(
                                            PARAM_INT, 'The ID of the last user to modify this student grade'),
                                        'str_grade' => new external_value(
                                            PARAM_RAW, 'A string representation of the grade'),
                                        'str_feedback' => new external_value(
                                            PARAM_TEXT, 'A string representation of the feedback from the grader'),
                                    )
                                )
                            ),
                        )
                    ), 'An array of outcomes associated with the grade items', VALUE_OPTIONAL
                )
            )
        );

    }

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
                ), 'Any student grades to alter', VALUE_OPTIONAL),
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
                    ), 'Any grade item settings to alter', VALUE_OPTIONAL
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
            $itemmodule, $iteminstance, $itemnumber, $gradestructure, $params['itemdetails']);
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
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
