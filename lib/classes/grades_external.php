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
     * @deprecated Moodle 3.2 MDL-51373 - Please do not call this function any more.
     * @see gradereport_user_external::get_grade_items for a similar function
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
     * Returns student course total grade and grades for activities.
     * This function does not return category or manual items.
     * This function is suitable for managers or teachers not students.
     *
     * @param  int $courseid        Course id
     * @param  string $component    Component name
     * @param  int $activityid      Activity id
     * @param  array  $userids      Array of user ids
     * @return array                Array of grades
     * @since Moodle 2.7
     * @deprecated Moodle 3.2 MDL-51373 - Please do not call this function any more.
     * @see gradereport_user_external::get_grade_items for a similar function
     */
    public static function get_grades($courseid, $component = null, $activityid = null, $userids = array()) {
        global $CFG, $USER, $DB;

        $params = self::validate_parameters(self::get_grades_parameters(),
            array('courseid' => $courseid, 'component' => $component, 'activityid' => $activityid, 'userids' => $userids));

        $gradesarray = array(
            'items'     => array(),
            'outcomes'  => array()
        );

        $coursecontext = context_course::instance($params['courseid']);

        try {
            self::validate_context($coursecontext);
        } catch (Exception $e) {
            $exceptionparam = new stdClass();
            $exceptionparam->message = $e->getMessage();
            $exceptionparam->courseid = $params['courseid'];
            throw new moodle_exception('errorcoursecontextnotvalid' , 'webservice', '', $exceptionparam);
        }

        require_capability('moodle/grade:viewhidden', $coursecontext);

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
        $iteminstance = null;

        if (!empty($params['component'])) {
            list($itemtype, $itemmodule) = normalize_component($params['component']);
        }

        $cm = null;
        if (!empty($itemmodule) && !empty($params['activityid'])) {
            if (!$cm = get_coursemodule_from_id($itemmodule, $params['activityid'])) {
                throw new moodle_exception('invalidcoursemodule');
            }
            $iteminstance = $cm->instance;
        }

        // Load all the module info.
        $modinfo = get_fast_modinfo($params['courseid']);
        $activityinstances = $modinfo->get_instances();

        $gradeparams = array('courseid' => $params['courseid']);
        if (!empty($itemtype)) {
            $gradeparams['itemtype'] = $itemtype;
        }
        if (!empty($itemmodule)) {
            $gradeparams['itemmodule'] = $itemmodule;
        }
        if (!empty($iteminstance)) {
            $gradeparams['iteminstance'] = $iteminstance;
        }

        if ($activitygrades = grade_item::fetch_all($gradeparams)) {
            $canviewhidden = has_capability('moodle/grade:viewhidden', context_course::instance($params['courseid']));

            foreach ($activitygrades as $activitygrade) {

                if ($activitygrade->itemtype != 'course' and $activitygrade->itemtype != 'mod') {
                    // This function currently only supports course and mod grade items. Manual and category not supported.
                    continue;
                }

                $context = $coursecontext;

                if ($activitygrade->itemtype == 'course') {
                    $item = grade_get_course_grades($course->id, $params['userids']);
                    $item->itemnumber = 0;

                    $grades = new stdClass;
                    $grades->items = array($item);
                    $grades->outcomes = array();

                } else {
                    $cm = $activityinstances[$activitygrade->itemmodule][$activitygrade->iteminstance];
                    $instance = $cm->instance;
                    $context = context_module::instance($cm->id, IGNORE_MISSING);

                    $grades = grade_get_grades($params['courseid'], $activitygrade->itemtype,
                                                $activitygrade->itemmodule, $instance, $params['userids']);
                }

                // Convert from objects to arrays so all web service clients are supported.
                // While we're doing that we also remove grades the current user can't see due to hiding.
                foreach ($grades->items as $gradeitem) {
                    // Switch the stdClass instance for a grade item instance so we can call is_hidden() and use the ID.
                    $gradeiteminstance = self::get_grade_item(
                        $course->id, $activitygrade->itemtype, $activitygrade->itemmodule, $activitygrade->iteminstance, 0);
                    if (!$canviewhidden && $gradeiteminstance->is_hidden()) {
                        continue;
                    }

                    // Format mixed bool/integer parameters.
                    $gradeitem->hidden = (empty($gradeitem->hidden)) ? 0 : $gradeitem->hidden;
                    $gradeitem->locked = (empty($gradeitem->locked)) ? 0 : $gradeitem->locked;

                    $gradeitemarray = (array)$gradeitem;
                    $gradeitemarray['grades'] = array();

                    if (!empty($gradeitem->grades)) {
                        foreach ($gradeitem->grades as $studentid => $studentgrade) {
                            if (!$canviewhidden) {
                                // Need to load the grade_grade object to check visibility.
                                $gradegradeinstance = grade_grade::fetch(
                                    array(
                                        'userid' => $studentid,
                                        'itemid' => $gradeiteminstance->id
                                    )
                                );
                                // The grade grade may be legitimately missing if the student has no grade.
                                if (!empty($gradegradeinstance) && $gradegradeinstance->is_hidden()) {
                                    continue;
                                }
                            }

                            // Format mixed bool/integer parameters.
                            $studentgrade->hidden = (empty($studentgrade->hidden)) ? 0 : $studentgrade->hidden;
                            $studentgrade->locked = (empty($studentgrade->locked)) ? 0 : $studentgrade->locked;
                            $studentgrade->overridden = (empty($studentgrade->overridden)) ? 0 : $studentgrade->overridden;

                            if ($gradeiteminstance->itemtype != 'course' and !empty($studentgrade->feedback)) {
                                list($studentgrade->feedback, $studentgrade->feedbackformat) =
                                    external_format_text($studentgrade->feedback, $studentgrade->feedbackformat,
                                    $context->id, $params['component'], 'feedback', null);
                            }

                            $gradeitemarray['grades'][$studentid] = (array)$studentgrade;
                            // Add the student ID as some WS clients can't access the array key.
                            $gradeitemarray['grades'][$studentid]['userid'] = $studentid;
                        }
                    }

                    if ($gradeiteminstance->itemtype == 'course') {
                        $gradesarray['items']['course'] = $gradeitemarray;
                        $gradesarray['items']['course']['activityid'] = 'course';
                    } else {
                        $gradesarray['items'][$cm->id] = $gradeitemarray;
                        // Add the activity ID as some WS clients can't access the array key.
                        $gradesarray['items'][$cm->id]['activityid'] = $cm->id;
                    }
                }

                foreach ($grades->outcomes as $outcome) {
                    // Format mixed bool/integer parameters.
                    $outcome->hidden = (empty($outcome->hidden)) ? 0 : $outcome->hidden;
                    $outcome->locked = (empty($outcome->locked)) ? 0 : $outcome->locked;

                    $gradesarray['outcomes'][$cm->id] = (array)$outcome;
                    $gradesarray['outcomes'][$cm->id]['activityid'] = $cm->id;

                    $gradesarray['outcomes'][$cm->id]['grades'] = array();
                    if (!empty($outcome->grades)) {
                        foreach ($outcome->grades as $studentid => $studentgrade) {
                            if (!$canviewhidden) {
                                // Need to load the grade_grade object to check visibility.
                                $gradeiteminstance = self::get_grade_item($course->id, $activitygrade->itemtype,
                                                                           $activitygrade->itemmodule, $activitygrade->iteminstance,
                                                                           $activitygrade->itemnumber);
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

                            // Format mixed bool/integer parameters.
                            $studentgrade->hidden = (empty($studentgrade->hidden)) ? 0 : $studentgrade->hidden;
                            $studentgrade->locked = (empty($studentgrade->locked)) ? 0 : $studentgrade->locked;

                            if (!empty($studentgrade->feedback)) {
                                list($studentgrade->feedback, $studentgrade->feedbackformat) =
                                    external_format_text($studentgrade->feedback, $studentgrade->feedbackformat,
                                    $context->id, $params['component'], 'feedback', null);
                            }

                            $gradesarray['outcomes'][$cm->id]['grades'][$studentid] = (array)$studentgrade;

                            // Add the student ID into the grade structure as some WS clients can't access the key.
                            $gradesarray['outcomes'][$cm->id]['grades'][$studentid]['userid'] = $studentid;
                        }
                    }
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
     * @deprecated Moodle 3.2 MDL-51373 - Please do not call this function any more.
     * @see gradereport_user_external::get_grade_items for a similar function
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
     * @deprecated Moodle 3.2 MDL-51373 - Please do not call this function any more.
     * @see gradereport_user_external::get_grade_items for a similar function
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
                            'locked' => new external_value(PARAM_INT, '0 means not locked, > 1 is a date to lock until'),
                            'hidden' => new external_value(PARAM_INT, '0 means not hidden, > 1 is a date to hide until'),
                            'grades' => new external_multiple_structure(
                                new external_single_structure(
                                    array(
                                        'userid' => new external_value(
                                            PARAM_INT, 'Student ID'),
                                        'grade' => new external_value(
                                            PARAM_FLOAT, 'Student grade'),
                                        'locked' => new external_value(
                                            PARAM_INT, '0 means not locked, > 1 is a date to lock until'),
                                        'hidden' => new external_value(
                                            PARAM_INT, '0 means not hidden, 1 hidden, > 1 is a date to hide until'),
                                        'overridden' => new external_value(
                                            PARAM_INT, '0 means not overridden, > 1 means overridden'),
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
                                            PARAM_RAW, 'A formatted string representation of the feedback from the grader'),
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
                            'locked' => new external_value(PARAM_INT, '0 means not locked, > 1 is a date to lock until'),
                            'hidden' => new external_value(PARAM_INT, '0 means not hidden, > 1 is a date to hide until'),
                            'grades' => new external_multiple_structure(
                                new external_single_structure(
                                    array(
                                        'userid' => new external_value(
                                            PARAM_INT, 'Student ID'),
                                        'grade' => new external_value(
                                            PARAM_FLOAT, 'Student grade'),
                                        'locked' => new external_value(
                                            PARAM_INT, '0 means not locked, > 1 is a date to lock until'),
                                        'hidden' => new external_value(
                                            PARAM_INT, '0 means not hidden, 1 hidden, > 1 is a date to hide until'),
                                        'feedback' => new external_value(
                                            PARAM_RAW, 'Feedback from the grader'),
                                        'feedbackformat' => new external_value(
                                            PARAM_INT, 'The feedback format'),
                                        'usermodified' => new external_value(
                                            PARAM_INT, 'The ID of the last user to modify this student grade'),
                                        'str_grade' => new external_value(
                                            PARAM_RAW, 'A string representation of the grade'),
                                        'str_feedback' => new external_value(
                                            PARAM_RAW, 'A formatted string representation of the feedback from the grader'),
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
     * Marking the method as deprecated.
     *
     * @return bool
     */
    public static function get_grades_is_deprecated() {
        return true;
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

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.10
     */
    public static function create_gradecategory_parameters() {
        return new external_function_parameters(
            [
                'courseid' => new external_value(PARAM_INT, 'id of course', VALUE_REQUIRED),
                'fullname' => new external_value(PARAM_TEXT, 'fullname of category', VALUE_REQUIRED),
                'options' => new external_single_structure([
                    'aggregation' => new external_value(PARAM_INT, 'aggregation method', VALUE_OPTIONAL),
                    'aggregateonlygraded' => new external_value(PARAM_BOOL, 'exclude empty grades', VALUE_OPTIONAL),
                    'aggregateoutcomes' => new external_value(PARAM_BOOL, 'aggregate outcomes', VALUE_OPTIONAL),
                    'droplow' => new external_value(PARAM_INT, 'drop low grades', VALUE_OPTIONAL),
                    'itemname' => new external_value(PARAM_TEXT, 'the category total name', VALUE_OPTIONAL),
                    'iteminfo' => new external_value(PARAM_TEXT, 'the category iteminfo', VALUE_OPTIONAL),
                    'idnumber' => new external_value(PARAM_TEXT, 'the category idnumber', VALUE_OPTIONAL),
                    'gradetype' => new external_value(PARAM_INT, 'the grade type', VALUE_OPTIONAL),
                    'grademax' => new external_value(PARAM_INT, 'the grade max', VALUE_OPTIONAL),
                    'grademin' => new external_value(PARAM_INT, 'the grade min', VALUE_OPTIONAL),
                    'gradepass' => new external_value(PARAM_INT, 'the grade to pass', VALUE_OPTIONAL),
                    'display' => new external_value(PARAM_INT, 'the display type', VALUE_OPTIONAL),
                    'decimals' => new external_value(PARAM_INT, 'the decimal count', VALUE_OPTIONAL),
                    'hiddenuntil' => new external_value(PARAM_INT, 'grades hidden until', VALUE_OPTIONAL),
                    'locktime' => new external_value(PARAM_INT, 'lock grades after', VALUE_OPTIONAL),
                    'weightoverride' => new external_value(PARAM_BOOL, 'weight adjusted', VALUE_OPTIONAL),
                    'aggregationcoef2' => new external_value(PARAM_RAW, 'weight coefficient', VALUE_OPTIONAL),
                    'parentcategoryid' => new external_value(PARAM_INT, 'The parent category id', VALUE_OPTIONAL),
                    'parentcategoryidnumber' => new external_value(PARAM_TEXT, 'the parent category idnumber', VALUE_OPTIONAL),
                ], 'optional category data', VALUE_DEFAULT, [])
            ]
        );
    }

    /**
     * Creates a gradecategory inside of the specified course.
     *
     * @param int $courseid the courseid to create the gradecategory in.
     * @param string $fullname the fullname of the grade category to create.
     * @param array $options array of options to set.
     *
     * @return array array of created categoryid and warnings.
     */
    public static function create_gradecategory(int $courseid, string $fullname, array $options) {
        global $CFG, $DB;

        $params = self::validate_parameters(self::create_gradecategory_parameters(),
            ['courseid' => $courseid, 'fullname' => $fullname, 'options' => $options]);

        // Now params are validated, update the references.
        $courseid = $params['courseid'];
        $fullname = $params['fullname'];
        $options = $params['options'];

        // Check that the context and permissions are OK.
        $context = context_course::instance($courseid);
        self::validate_context($context);
        require_capability('moodle/grade:manage', $context);

        $defaultparentcat = new grade_category(['courseid' => $courseid, 'depth' => 1], true);

        // Setup default data so WS call needs to contain only data to set.
        // This is not done in the Parameters, so that the array of options can be optional.
        $data = [
            'fullname' => $fullname,
            'aggregation' => grade_get_setting($courseid, 'displaytype', $CFG->grade_displaytype),
            'aggregateonlygraded' => 1,
            'aggregateoutcomes' => 0,
            'droplow' => 0,
            'grade_item_itemname' => '',
            'grade_item_iteminfo' => '',
            'grade_item_idnumber' => '',
            'grade_item_gradetype' => GRADE_TYPE_VALUE,
            'grade_item_grademax' => 100,
            'grade_item_grademin' => 1,
            'grade_item_gradepass' => 1,
            'grade_item_display' => GRADE_DISPLAY_TYPE_DEFAULT,
            // Hack. This must be -2 to use the default setting.
            'grade_item_decimals' => -2,
            'grade_item_hiddenuntil' => 0,
            'grade_item_locktime' => 0,
            'grade_item_weightoverride' => 0,
            'grade_item_aggregationcoef2' => 0,
            'parentcategory' => $defaultparentcat->id
        ];

        // Most of the data items need boilerplate prepended. These are the exceptions.
        $ignorekeys = ['aggregation', 'aggregateonlygraded', 'aggregateoutcomes', 'droplow', 'parentcategoryid', 'parentcategoryidnumber'];
        foreach ($options as $key => $value) {
            if (!in_array($key, $ignorekeys)) {
                $fullkey = 'grade_item_' . $key;
                $data[$fullkey] = $value;
            } else {
                $data[$key] = $value;
            }
        }

        // Handle parent category special case.
        if (array_key_exists('parentcategoryid', $options) && $parentcat = $DB->get_record('grade_categories',
            ['id' => $options['parentcategoryid'], 'courseid' => $courseid])) {
            $data['parentcategory'] = $parentcat->id;
        } else if (array_key_exists('parentcategoryidnumber', $options) && $parentcatgradeitem = $DB->get_record('grade_items',
            ['itemtype' => 'category', 'idnumber' => $options['parentcategoryidnumber']], '*', IGNORE_MULTIPLE)) {
            if ($parentcat = $DB->get_record('grade_categories', ['courseid' => $courseid, 'id' => $parentcatgradeitem->iteminstance])) {
                $data['parentcategory'] = $parentcat->id;
            }
        }

        // Create new gradecategory item.
        $gradecategory = new grade_category(['courseid' => $courseid], false);
        $gradecategory->apply_default_settings();
        $gradecategory->apply_forced_settings();

        // Data Validation.
        if (array_key_exists('grade_item_gradetype', $data) and $data['grade_item_gradetype'] == GRADE_TYPE_SCALE) {
            if (empty($data['grade_item_scaleid'])) {
                $warnings[] = ['item' => 'scaleid', 'warningcode' => 'invalidscale',
                    'message' => get_string('missingscale', 'grades')];
            }
        }
        if (array_key_exists('grade_item_grademin', $data) and array_key_exists('grade_item_grademax', $data)) {
            if (($data['grade_item_grademax'] != 0 OR $data['grade_item_grademin'] != 0) AND
                ($data['grade_item_grademax'] == $data['grade_item_grademin'] OR
                $data['grade_item_grademax'] < $data['grade_item_grademin'])) {
                $warnings[] = ['item' => 'grademax', 'warningcode' => 'invalidgrade',
                    'message' => get_string('incorrectminmax', 'grades')];
            }
        }

        if (!empty($warnings)) {
            return ['categoryid' => null, 'warnings' => $warnings];
        }

        // Now call the update function with data. Transactioned so the gradebook isn't broken on bad data.
        try {
            $transaction = $DB->start_delegated_transaction();
            grade_edit_tree::update_gradecategory($gradecategory, (object) $data);
            $transaction->allow_commit();
        } catch (Exception $e) {
            // If the submitted data was broken for any reason.
            $warnings['database'] = $e->getMessage();
            $transaction->rollback();
            return ['warnings' => $warnings];
        }

        return['categoryid' => $gradecategory->id, 'warnings' => []];
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.10
     */
    public static function create_gradecategory_returns() {
        return new external_single_structure([
            'categoryid' => new external_value(PARAM_INT, 'The ID of the created category', VALUE_OPTIONAL),
            'warnings' => new external_warnings(),
        ]);
    }
}
