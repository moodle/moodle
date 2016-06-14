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
     * Describes the parameters for get_grades_table.
     *
     * @return external_external_function_parameters
     * @since Moodle 2.9
     */
    public static function get_grades_table_parameters() {
        return new external_function_parameters (
            array(
                'courseid' => new external_value(PARAM_INT, 'Course Id', VALUE_REQUIRED),
                'userid'   => new external_value(PARAM_INT, 'Return grades only for this user (optional)', VALUE_DEFAULT, 0)
            )
        );
    }

    /**
     * Returns a list of grades tables for users in a course.
     *
     * @param int $courseid Course Id
     * @param int $userid   Only this user (optional)
     *
     * @return array the grades tables
     * @since Moodle 2.9
     */
    public static function get_grades_table($courseid, $userid = 0) {
        global $CFG, $USER;

        $warnings = array();

        // Validate the parameter.
        $params = self::validate_parameters(self::get_grades_table_parameters(),
            array(
                'courseid' => $courseid,
                'userid' => $userid)
            );

        // Compact/extract functions are not recommended.
        $courseid = $params['courseid'];
        $userid   = $params['userid'];

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
                'courseid' => $courseid,
                'userid' => $userid)
            );

        $tables = array();

        // Just one user.
        if ($user) {
            $report = new grade_report_user($courseid, $gpr, $context, $userid);
            $report->fill_table();

            $tables[] = array(
                'courseid'      => $courseid,
                'userid'        => $user->id,
                'userfullname'  => fullname($user),
                'maxdepth'      => $report->maxdepth,
                'tabledata'     => $report->tabledata
            );

        } else {
            $defaultgradeshowactiveenrol = !empty($CFG->grade_report_showonlyactiveenrol);
            $showonlyactiveenrol = get_user_preferences('grade_report_showonlyactiveenrol', $defaultgradeshowactiveenrol);
            $showonlyactiveenrol = $showonlyactiveenrol || !has_capability('moodle/course:viewsuspendedusers', $context);

            $gui = new graded_users_iterator($course);
            $gui->require_active_enrolment($showonlyactiveenrol);
            $gui->init();

            while ($userdata = $gui->next_user()) {
                $currentuser = $userdata->user;
                $report = new grade_report_user($courseid, $gpr, $context, $currentuser->id);
                $report->fill_table();

                $tables[] = array(
                    'courseid'      => $courseid,
                    'userid'        => $currentuser->id,
                    'userfullname'  => fullname($currentuser),
                    'maxdepth'      => $report->maxdepth,
                    'tabledata'     => $report->tabledata
                );
            }
            $gui->close();
        }

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
                'userid' => new external_value(PARAM_INT, 'id of the user, 0 means current user', VALUE_DEFAULT, 0)
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
}
