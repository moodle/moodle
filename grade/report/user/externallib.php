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
 * External grade report user API
 *
 * @package    gradereport_user
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");


/**
 * External grade report API implementation
 *
 * @package    gradereport_user
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @category   external
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gradereport_user_external extends external_api {


    /**
     * Validate access permissions to the report
     *
     * @param  int  $courseid the courseid
     * @param  int  $userid   the user id to retrieve data from
     * @param  int $groupid   the group id
     * @return array with the parameters cleaned and other required information
     * @since  Moodle 3.2
     */
    protected static function check_report_access($courseid, $userid, $groupid = 0) {
        global $USER;

        // Validate the parameter.
        $params = self::validate_parameters(self::get_grades_table_parameters(),
            array(
                'courseid' => $courseid,
                'userid' => $userid,
                'groupid' => $groupid,
            )
        );

        // Compact/extract functions are not recommended.
        $courseid = $params['courseid'];
        $userid   = $params['userid'];
        $groupid  = $params['groupid'];

        // Function get_course internally throws an exception if the course doesn't exist.
        $course = get_course($courseid);

        $context = context_course::instance($courseid);
        self::validate_context($context);

        // Specific capabilities.
        require_capability('gradereport/user:view', $context);

        $user = null;

        if (empty($userid)) {
            require_capability('moodle/grade:viewall', $context);
        } else {
            $user = core_user::get_user($userid, '*', MUST_EXIST);
            core_user::require_active_user($user);
            // Check if we can view the user group (if any).
            // When userid == 0, we are retrieving all the users, we'll check then if a groupid is required.
            if (!groups_user_groups_visible($course, $user->id)) {
                throw new moodle_exception('notingroup');
            }
        }

        $access = false;

        if (has_capability('moodle/grade:viewall', $context)) {
            // Can view all course grades.
            $access = true;
        } else if ($userid == $USER->id and has_capability('moodle/grade:view', $context) and $course->showgrades) {
            // View own grades.
            $access = true;
        }

        if (!$access) {
            throw new moodle_exception('nopermissiontoviewgrades', 'error');
        }

        if (!empty($groupid)) {
            // Determine is the group is visible to user.
            if (!groups_group_visible($groupid, $course)) {
                throw new moodle_exception('notingroup');
            }
        } else {
            // Check to see if groups are being used here.
            if ($groupmode = groups_get_course_groupmode($course)) {
                $groupid = groups_get_course_group($course);
                // Determine is the group is visible to user (this is particullary for the group 0).
                if (!groups_group_visible($groupid, $course)) {
                    throw new moodle_exception('notingroup');
                }
            } else {
                $groupid = 0;
            }
        }

        return array($params, $course, $context, $user, $groupid);
    }

    /**
     * Get the report data
     * @param  stdClass $course  course object
     * @param  stdClass $context context object
     * @param  stdClass $user    user object (it can be null for all the users)
     * @param  int $userid       the user to retrieve data from, 0 for all
     * @param  int $groupid      the group id to filter
     * @param  bool $tabledata   whether to get the table data (true) or the gradeitemdata
     * @return array data and possible warnings
     * @since  Moodle 3.2
     */
    protected static function get_report_data($course, $context, $user, $userid, $groupid, $tabledata = true) {
        global $CFG;

        $warnings = array();
        // Require files here to save some memory in case validation fails.
        require_once($CFG->dirroot . '/group/lib.php');
        require_once($CFG->libdir  . '/gradelib.php');
        require_once($CFG->dirroot . '/grade/lib.php');
        require_once($CFG->dirroot . '/grade/report/user/lib.php');

        // Force regrade to update items marked as 'needupdate'.
        grade_regrade_final_grades($course->id);

        $gpr = new grade_plugin_return(
            array(
                'type' => 'report',
                'plugin' => 'user',
                'courseid' => $course->id,
                'userid' => $userid)
            );

        $reportdata = array();

        // Just one user.
        if ($user) {
            $report = new grade_report_user($course->id, $gpr, $context, $userid);
            $report->fill_table();

            $gradeuserdata = array(
                'courseid'      => $course->id,
                'userid'        => $user->id,
                'userfullname'  => fullname($user),
                'maxdepth'      => $report->maxdepth,
            );
            if ($tabledata) {
                $gradeuserdata['tabledata'] = $report->tabledata;
            } else {
                $gradeuserdata['gradeitems'] = $report->gradeitemsdata;
            }
            $reportdata[] = $gradeuserdata;
        } else {
            $defaultgradeshowactiveenrol = !empty($CFG->grade_report_showonlyactiveenrol);
            $showonlyactiveenrol = get_user_preferences('grade_report_showonlyactiveenrol', $defaultgradeshowactiveenrol);
            $showonlyactiveenrol = $showonlyactiveenrol || !has_capability('moodle/course:viewsuspendedusers', $context);

            $gui = new graded_users_iterator($course, null, $groupid);
            $gui->require_active_enrolment($showonlyactiveenrol);
            $gui->init();

            while ($userdata = $gui->next_user()) {
                $currentuser = $userdata->user;
                $report = new grade_report_user($course->id, $gpr, $context, $currentuser->id);
                $report->fill_table();

                $gradeuserdata = array(
                    'courseid'      => $course->id,
                    'userid'        => $currentuser->id,
                    'userfullname'  => fullname($currentuser),
                    'maxdepth'      => $report->maxdepth,
                );
                if ($tabledata) {
                    $gradeuserdata['tabledata'] = $report->tabledata;
                } else {
                    $gradeuserdata['gradeitems'] = $report->gradeitemsdata;
                }
                $reportdata[] = $gradeuserdata;
            }
            $gui->close();
        }
        return array($reportdata, $warnings);
    }

    /**
     * Describes the parameters for get_grades_table.
     *
     * @return external_external_function_parameters
     * @since Moodle 2.9
     */
    public static function get_grades_table_parameters() {
        return new external_function_parameters (
            array(
                'courseid' => new external_value(PARAM_INT, 'Course Id', VALUE_REQUIRED),
                'userid'   => new external_value(PARAM_INT, 'Return grades only for this user (optional)', VALUE_DEFAULT, 0),
                'groupid'  => new external_value(PARAM_INT, 'Get users from this group only', VALUE_DEFAULT, 0)
            )
        );
    }

    /**
     * Returns a list of grades tables for users in a course.
     *
     * @param int $courseid Course Id
     * @param int $userid   Only this user (optional)
     * @param int $groupid  Get users from this group only
     *
     * @return array the grades tables
     * @since Moodle 2.9
     */
    public static function get_grades_table($courseid, $userid = 0, $groupid = 0) {
        global $CFG, $USER;

        list($params, $course, $context, $user, $groupid) = self::check_report_access($courseid, $userid, $groupid);
        $userid   = $params['userid'];

        // We pass userid because it can be still 0.
        list($tables, $warnings) = self::get_report_data($course, $context, $user, $userid, $groupid);

        $result = array();
        $result['tables'] = $tables;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Creates a table column structure
     *
     * @return array
     * @since  Moodle 2.9
     */
    private static function grades_table_column() {
        return array (
            'class'   => new external_value(PARAM_RAW, 'class'),
            'content' => new external_value(PARAM_RAW, 'cell content'),
            'headers' => new external_value(PARAM_RAW, 'headers')
        );
    }

    /**
     * Describes tget_grades_table return value.
     *
     * @return external_single_structure
     * @since Moodle 2.9
     */
    public static function get_grades_table_returns() {
        return new external_single_structure(
            array(
                'tables' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'courseid' => new external_value(PARAM_INT, 'course id'),
                            'userid'   => new external_value(PARAM_INT, 'user id'),
                            'userfullname' => new external_value(PARAM_TEXT, 'user fullname'),
                            'maxdepth'   => new external_value(PARAM_INT, 'table max depth (needed for printing it)'),
                            'tabledata' => new external_multiple_structure(
                                new external_single_structure(
                                    array(
                                        'itemname' => new external_single_structure(
                                            array (
                                                'class' => new external_value(PARAM_RAW, 'class'),
                                                'colspan' => new external_value(PARAM_INT, 'col span'),
                                                'content'  => new external_value(PARAM_RAW, 'cell content'),
                                                'celltype'  => new external_value(PARAM_RAW, 'cell type'),
                                                'id'  => new external_value(PARAM_ALPHANUMEXT, 'id')
                                            ), 'The item returned data', VALUE_OPTIONAL
                                        ),
                                        'leader' => new external_single_structure(
                                            array (
                                                'class' => new external_value(PARAM_RAW, 'class'),
                                                'rowspan' => new external_value(PARAM_INT, 'row span')
                                            ), 'The item returned data', VALUE_OPTIONAL
                                        ),
                                        'weight' => new external_single_structure(
                                            self::grades_table_column(), 'weight column', VALUE_OPTIONAL
                                        ),
                                        'grade' => new external_single_structure(
                                            self::grades_table_column(), 'grade column', VALUE_OPTIONAL
                                        ),
                                        'range' => new external_single_structure(
                                            self::grades_table_column(), 'range column', VALUE_OPTIONAL
                                        ),
                                        'percentage' => new external_single_structure(
                                            self::grades_table_column(), 'percentage column', VALUE_OPTIONAL
                                        ),
                                        'lettergrade' => new external_single_structure(
                                            self::grades_table_column(), 'lettergrade column', VALUE_OPTIONAL
                                        ),
                                        'rank' => new external_single_structure(
                                            self::grades_table_column(), 'rank column', VALUE_OPTIONAL
                                        ),
                                        'average' => new external_single_structure(
                                            self::grades_table_column(), 'average column', VALUE_OPTIONAL
                                        ),
                                        'feedback' => new external_single_structure(
                                            self::grades_table_column(), 'feedback column', VALUE_OPTIONAL
                                        ),
                                        'contributiontocoursetotal' => new external_single_structure(
                                            self::grades_table_column(), 'contributiontocoursetotal column', VALUE_OPTIONAL
                                        ),
                                    ), 'table'
                                )
                            )
                        )
                    )
                ),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.9
     */
    public static function view_grade_report_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'id of the course'),
                'userid' => new external_value(PARAM_INT, 'id of the user, 0 means current user', VALUE_DEFAULT, 0),
            )
        );
    }

    /**
     * Trigger the user report events, do the same that the web interface view of the report
     *
     * @param int $courseid id of course
     * @param int $userid id of the user the report belongs to
     * @return array of warnings and status result
     * @since Moodle 2.9
     * @throws moodle_exception
     */
    public static function view_grade_report($courseid, $userid = 0) {
        global $CFG, $USER;
        require_once($CFG->dirroot . "/grade/lib.php");
        require_once($CFG->dirroot . "/grade/report/user/lib.php");

        $params = self::validate_parameters(self::view_grade_report_parameters(),
                                            array(
                                                'courseid' => $courseid,
                                                'userid' => $userid
                                            ));

        $warnings = array();

        $course = get_course($params['courseid']);

        $context = context_course::instance($course->id);
        self::validate_context($context);

        $userid = $params['userid'];
        if (empty($userid)) {
            $userid = $USER->id;
        } else {
            $user = core_user::get_user($userid, '*', MUST_EXIST);
            core_user::require_active_user($user);
        }

        $access = false;

        if (has_capability('moodle/grade:viewall', $context)) {
            // Can view all course grades (any user).
            $access = true;
        } else if ($userid == $USER->id and has_capability('moodle/grade:view', $context) and $course->showgrades) {
            // View own grades.
            $access = true;
        }

        if (!$access) {
            throw new moodle_exception('nopermissiontoviewgrades', 'error');
        }

        // Create a report instance. We don't need the gpr second parameter.
        $report = new grade_report_user($course->id, null, $context, $userid);
        $report->viewed();

        $result = array();
        $result['status'] = true;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.9
     */
    public static function view_grade_report_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Describes the parameters for get_grade_items.
     *
     * @return external_external_function_parameters
     * @since Moodle 3.2
     */
    public static function get_grade_items_parameters() {
        return self::get_grades_table_parameters();
    }

    /**
     * Returns the complete list of grade items for users in a course.
     *
     * @param int $courseid Course Id
     * @param int $userid   Only this user (optional)
     * @param int $groupid  Get users from this group only
     *
     * @return array the grades tables
     * @since Moodle 3.2
     */
    public static function get_grade_items($courseid, $userid = 0, $groupid = 0) {
        global $CFG, $USER;

        list($params, $course, $context, $user, $groupid) = self::check_report_access($courseid, $userid, $groupid);
        $userid   = $params['userid'];

        // We pass userid because it can be still 0.
        list($gradeitems, $warnings) = self::get_report_data($course, $context, $user, $userid, $groupid, false);

        foreach ($gradeitems as $gradeitem) {
            if (isset($gradeitem['feedback']) and isset($gradeitem['feedbackformat'])) {
                list($gradeitem['feedback'], $gradeitem['feedbackformat']) =
                    external_format_text($gradeitem['feedback'], $gradeitem['feedbackformat'], $context->id);
            }
        }

        $result = array();
        $result['usergrades'] = $gradeitems;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes tget_grade_items return value.
     *
     * @return external_single_structure
     * @since Moodle 3.2
     */
    public static function get_grade_items_returns() {
        return new external_single_structure(
            array(
                'usergrades' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'courseid' => new external_value(PARAM_INT, 'course id'),
                            'userid'   => new external_value(PARAM_INT, 'user id'),
                            'userfullname' => new external_value(PARAM_TEXT, 'user fullname'),
                            'maxdepth'   => new external_value(PARAM_INT, 'table max depth (needed for printing it)'),
                            'gradeitems' => new external_multiple_structure(
                                new external_single_structure(
                                    array(
                                        'id' => new external_value(PARAM_INT, 'Grade item id'),
                                        'itemtype' => new external_value(PARAM_ALPHA, 'Grade item type'),
                                        'itemmodule' => new external_value(PARAM_PLUGIN, 'Grade item module'),
                                        'iteminstance' => new external_value(PARAM_INT, 'Grade item instance'),
                                        'itemnumber' => new external_value(PARAM_INT, 'Grade item item number'),
                                        'categoryid' => new external_value(PARAM_INT, 'Grade item category id'),
                                        'outcomeid' => new external_value(PARAM_INT, 'Outcome id'),
                                        'scaleid' => new external_value(PARAM_INT, 'Scale id'),
                                        'cmid' => new external_value(PARAM_INT, 'Course module id (if type mod)', VALUE_OPTIONAL),
                                        'weightraw' => new external_value(PARAM_FLOAT, 'Weight raw', VALUE_OPTIONAL),
                                        'weightformatted' => new external_value(PARAM_NOTAGS, 'Weight', VALUE_OPTIONAL),
                                        'status' => new external_value(PARAM_ALPHA, 'Status', VALUE_OPTIONAL),
                                        'graderaw' => new external_value(PARAM_FLOAT, 'Grade raw', VALUE_OPTIONAL),
                                        'gradedatesubmitted' => new external_value(PARAM_INT, 'Grade submit date', VALUE_OPTIONAL),
                                        'gradedategraded' => new external_value(PARAM_INT, 'Grade graded date', VALUE_OPTIONAL),
                                        'gradehiddenbydate' => new external_value(PARAM_BOOL, 'Grade hidden by date?', VALUE_OPTIONAL),
                                        'gradeneedsupdate' => new external_value(PARAM_BOOL, 'Grade needs update?', VALUE_OPTIONAL),
                                        'gradeishidden' => new external_value(PARAM_BOOL, 'Grade is hidden?', VALUE_OPTIONAL),
                                        'gradeformatted' => new external_value(PARAM_NOTAGS, 'The grade formatted', VALUE_OPTIONAL),
                                        'grademin' => new external_value(PARAM_FLOAT, 'Grade min', VALUE_OPTIONAL),
                                        'grademax' => new external_value(PARAM_FLOAT, 'Grade max', VALUE_OPTIONAL),
                                        'rangeformatted' => new external_value(PARAM_NOTAGS, 'Range formatted', VALUE_OPTIONAL),
                                        'percentageformatted' => new external_value(PARAM_NOTAGS, 'Percentage', VALUE_OPTIONAL),
                                        'lettergradeformatted' => new external_value(PARAM_NOTAGS, 'Letter grade', VALUE_OPTIONAL),
                                        'rank' => new external_value(PARAM_INT, 'Rank in the course', VALUE_OPTIONAL),
                                        'numusers' => new external_value(PARAM_INT, 'Num users in course', VALUE_OPTIONAL),
                                        'averageformatted' => new external_value(PARAM_NOTAGS, 'Grade average', VALUE_OPTIONAL),
                                        'feedback' => new external_value(PARAM_RAW, 'Grade feedback', VALUE_OPTIONAL),
                                        'feedbackformat' => new external_format_value('feedback'),
                                    ), 'Grade items'
                                )
                            )
                        )
                    )
                ),
                'warnings' => new external_warnings()
            )
        );
    }
}
