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
 * External grade report overview API
 *
 * @package    gradereport_overview
 * @copyright  2016 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->dirroot . '/grade/lib.php');
require_once($CFG->dirroot . '/grade/report/overview/lib.php');

/**
 * External grade overview report API implementation
 *
 * @package    gradereport_overview
 * @copyright  2016 Juan Leyva <juan@moodle.com>
 * @category   external
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gradereport_overview_external extends external_api {

    /**
     * Describes the parameters for get_course_grades.
     *
     * @return external_external_function_parameters
     * @since Moodle 3.2
     */
    public static function get_course_grades_parameters() {
        return new external_function_parameters (
            array(
                'userid' => new external_value(PARAM_INT, 'Get grades for this user (optional, default current)', VALUE_DEFAULT, 0)
            )
        );
    }

    /**
     * Get the given user courses final grades
     *
     * @param int $userid get grades for this user (optional, default current)
     *
     * @return array the grades tables
     * @since Moodle 3.2
     */
    public static function get_course_grades($userid = 0) {
        global $USER;

        $warnings = array();

        // Validate the parameter.
        $params = self::validate_parameters(self::get_course_grades_parameters(),
            array(
                'userid' => $userid
            )
        );

        $userid = $params['userid'];
        if (empty($userid)) {
            $userid = $USER->id;
        }

        $systemcontext = context_system::instance();
        self::validate_context($systemcontext);

        if ($USER->id != $userid) {
            // We must check if the current user can view other users grades.
            $user = core_user::get_user($userid, '*', MUST_EXIST);
            core_user::require_active_user($user);
            require_capability('moodle/grade:viewall', $systemcontext);
        }

        // We need the site course, and course context.
        $course = get_course(SITEID);
        $context = context_course::instance($course->id);

        // Force a regrade if required.
        grade_regrade_final_grades_if_required($course);
        // Get the course final grades now.
        $gpr = new grade_plugin_return(array('type' => 'report', 'plugin' => 'overview', 'courseid' => $course->id,
                                        'userid' => $userid));
        $report = new grade_report_overview($userid, $gpr, $context);
        $coursesgrades = $report->setup_courses_data(true);

        $grades = array();
        foreach ($coursesgrades as $coursegrade) {
            $gradeinfo = array(
                'courseid' => $coursegrade['course']->id,
                'grade' => grade_format_gradevalue($coursegrade['finalgrade'], $coursegrade['courseitem'], true),
                'rawgrade' => $coursegrade['finalgrade'],
            );
            if (isset($coursegrade['rank'])) {
                $gradeinfo['rank'] = $coursegrade['rank'];
            }
            $grades[] = $gradeinfo;
        }

        $result = array();
        $result['grades'] = $grades;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the get_course_grades return value.
     *
     * @return external_single_structure
     * @since Moodle 3.2
     */
    public static function get_course_grades_returns() {
        return new external_single_structure(
            array(
                'grades' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'courseid' => new external_value(PARAM_INT, 'Course id'),
                            'grade' => new external_value(PARAM_RAW, 'Grade formatted'),
                            'rawgrade' => new external_value(PARAM_RAW, 'Raw grade, not formatted'),
                            'rank' => new external_value(PARAM_INT, 'Your rank in the course', VALUE_OPTIONAL),
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
     * @since Moodle 3.2
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
     * @since Moodle 3.2
     * @throws moodle_exception
     */
    public static function view_grade_report($courseid, $userid = 0) {
        global $USER;

        $params = self::validate_parameters(self::view_grade_report_parameters(),
            array(
                'courseid' => $courseid,
                'userid' => $userid
            )
        );

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
        $systemcontext = context_system::instance();
        $personalcontext = context_user::instance($userid);

        $access = grade_report_overview::check_access($systemcontext, $context, $personalcontext, $course, $userid);

        if (!$access) {
            throw new moodle_exception('nopermissiontoviewgrades', 'error');
        }

        grade_report_overview::viewed($context, $course->id, $userid);

        $result = array();
        $result['status'] = true;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.2
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
