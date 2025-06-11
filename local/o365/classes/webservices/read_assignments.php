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
 * Get a list of assignments in one or more courses.
 *
 * @package local_o365
 * @author  2012 Paul Charsley, modified slightly 2017 James McQuillan
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  2012 Paul Charsley
 */

namespace local_o365\webservices;

use assign;
use context_course;
use context_module;
use moodle_exception;

defined('MOODLE_INTERNAL') || die();

use core_external\external_api;
use core_external\external_format_value;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;
use moodle_url;

global $CFG;

require_once($CFG->dirroot . '/course/modlib.php');
require_once($CFG->dirroot . '/user/externallib.php');
require_once($CFG->dirroot . '/mod/assign/locallib.php');

/**
 * Get a list of assignments in one or more courses.
 */
class read_assignments extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function assignments_read_parameters() {
        return new external_function_parameters([
            'courseids' => new external_multiple_structure(
                new external_value(PARAM_INT, 'course id, empty for retrieving all the courses where the user is enroled in'),
                '0 or more course ids',
                VALUE_DEFAULT,
                []
            ),
            'assignmentids' => new external_multiple_structure(
                new external_value(PARAM_INT,
                    'assignment id, empty for retrieving all assignments for courses the user is enroled in'),
                '0 or more assignment ids',
                VALUE_DEFAULT,
                []
            ),
            'capabilities' => new external_multiple_structure(
                new external_value(PARAM_CAPABILITY, 'capability'),
                'list of capabilities used to filter courses',
                VALUE_DEFAULT,
                []
            ),
            'includenotenrolledcourses' => new external_value(
                PARAM_BOOL,
                'whether to return courses that the user can see even if is not enroled in. This requires the parameter ' .
                'courseids to not be empty.',
                VALUE_DEFAULT,
                false
            ),
        ]);
    }

    /**
     * Returns an array of courses the user is enrolled, and for each course all of the assignments that the user can
     * view within that course.
     *
     * @param array $courseids An optional array of course ids. If provided only assignments within the given course
     * will be returned. If the user is not enrolled in or can't view a given course a warning will be generated and returned.
     * @param array $assignmentids An optional array of assignment ids.
     * @param array $capabilities An array of additional capability checks you wish to be made on the course context.
     * @param bool $includenotenrolledcourses Wheter to return courses that the user can see even if is not enroled in.
     * This requires the parameter $courseids to not be empty.
     * @return array An array of courses and warnings.
     * @since  Moodle 2.4
     */
    public static function assignments_read($courseids = [], $assignmentids = [], $capabilities = [],
        $includenotenrolledcourses = false) {
        global $USER, $DB;

        $params = self::validate_parameters(
            self::assignments_read_parameters(),
            [
                'courseids' => $courseids,
                'assignmentids' => $assignmentids,
                'capabilities' => $capabilities,
                'includenotenrolledcourses' => $includenotenrolledcourses,
            ]
        );

        $assignmentids = array_flip($params['assignmentids']);
        $warnings = [];
        $courses = [];
        $fields = 'sortorder,shortname,fullname,timemodified';

        // If the courseids list is empty, we return only the courses where the user is enrolled in.
        if (empty($params['courseids'])) {
            $courses = enrol_get_users_courses($USER->id, true, $fields);
            $courseids = array_keys($courses);
        } else if ($includenotenrolledcourses) {
            // In this case, we don't have to check here for enrolmnents. Maybe the user can see the course even if is not enrolled.
            $courseids = $params['courseids'];
        } else {
            // We need to check for enrolments.
            $mycourses = enrol_get_users_courses($USER->id, true, $fields);
            $mycourseids = array_keys($mycourses);

            foreach ($params['courseids'] as $courseid) {
                if (!in_array($courseid, $mycourseids)) {
                    unset($courses[$courseid]);
                    $warnings[] = [
                        'item' => 'course',
                        'itemid' => $courseid,
                        'warningcode' => '2',
                        'message' => 'User is not enrolled or does not have requested capability',
                    ];
                } else {
                    $courses[$courseid] = $mycourses[$courseid];
                }
            }
            $courseids = array_keys($courses);
        }

        foreach ($courseids as $cid) {

            try {
                $context = context_course::instance($cid);
                self::validate_context($context);

                // Check if this course was already loaded (by enrol_get_users_courses).
                if (!isset($courses[$cid])) {
                    $courses[$cid] = get_course($cid);
                }
            } catch (moodle_exception $e) {
                unset($courses[$cid]);
                $warnings[] = [
                    'item' => 'course',
                    'itemid' => $cid,
                    'warningcode' => '1',
                    'message' => 'No access rights in course context ' . $e->getMessage(),
                ];
                continue;
            }
            if (count($params['capabilities']) > 0 && !has_all_capabilities($params['capabilities'], $context)) {
                unset($courses[$cid]);
            }
        }
        $extrafields = 'm.id as assignmentid, ' .
            'm.course, ' .
            'm.nosubmissions, ' .
            'm.submissiondrafts, ' .
            'm.sendnotifications, ' .
            'm.sendlatenotifications, ' .
            'm.sendstudentnotifications, ' .
            'm.duedate, ' .
            'm.allowsubmissionsfromdate, ' .
            'm.grade, ' .
            'm.timemodified, ' .
            'm.completionsubmit, ' .
            'm.cutoffdate, ' .
            'm.teamsubmission, ' .
            'm.requireallteammemberssubmit, ' .
            'm.teamsubmissiongroupingid, ' .
            'm.blindmarking, ' .
            'm.revealidentities, ' .
            'm.attemptreopenmethod, ' .
            'm.maxattempts, ' .
            'm.markingworkflow, ' .
            'm.markingallocation, ' .
            'm.requiresubmissionstatement, ' .
            'm.intro, ' .
            'm.introformat';
        $coursearray = [];

        foreach ($courses as $id => $course) {
            $assignmentarray = [];
            // Get a list of assignments for the course.
            if ($modules = get_coursemodules_in_course('assign', $courses[$id]->id, $extrafields)) {
                foreach ($modules as $module) {

                    // Check assignment ID filter.
                    if (!empty($assignmentids) && !isset($assignmentids[$module->assignmentid])) {
                        continue;
                    }

                    $context = context_module::instance($module->id);
                    try {
                        self::validate_context($context);
                        require_capability('mod/assign:view', $context);
                    } catch (moodle_exception $e) {
                        $warnings[] = [
                            'item' => 'module',
                            'itemid' => $module->id,
                            'warningcode' => '1',
                            'message' => 'No access rights in module context',
                        ];
                        continue;
                    }
                    $configrecords = $DB->get_recordset('assign_plugin_config', ['assignment' => $module->assignmentid]);
                    $configarray = [];
                    foreach ($configrecords as $configrecord) {
                        $configarray[] = [
                            'id' => $configrecord->id,
                            'assignment' => $configrecord->assignment,
                            'plugin' => $configrecord->plugin,
                            'subtype' => $configrecord->subtype,
                            'name' => $configrecord->name,
                            'value' => $configrecord->value,
                        ];
                    }
                    $configrecords->close();

                    $assignment = [
                        'id' => $module->assignmentid,
                        'cmid' => $module->id,
                        'course' => $module->course,
                        'name' => $module->name,
                        'nosubmissions' => $module->nosubmissions,
                        'submissiondrafts' => $module->submissiondrafts,
                        'sendnotifications' => $module->sendnotifications,
                        'sendlatenotifications' => $module->sendlatenotifications,
                        'sendstudentnotifications' => $module->sendstudentnotifications,
                        'duedate' => $module->duedate,
                        'allowsubmissionsfromdate' => $module->allowsubmissionsfromdate,
                        'grade' => $module->grade,
                        'timemodified' => $module->timemodified,
                        'completionsubmit' => $module->completionsubmit,
                        'cutoffdate' => $module->cutoffdate,
                        'teamsubmission' => $module->teamsubmission,
                        'requireallteammemberssubmit' => $module->requireallteammemberssubmit,
                        'teamsubmissiongroupingid' => $module->teamsubmissiongroupingid,
                        'blindmarking' => $module->blindmarking,
                        'revealidentities' => $module->revealidentities,
                        'attemptreopenmethod' => $module->attemptreopenmethod,
                        'maxattempts' => $module->maxattempts,
                        'markingworkflow' => $module->markingworkflow,
                        'markingallocation' => $module->markingallocation,
                        'requiresubmissionstatement' => $module->requiresubmissionstatement,
                        'configs' => $configarray,
                    ];

                    // Return or not intro and file attachments depending on the plugin settings.
                    $assign = new assign($context, null, null);

                    if ($assign->show_intro()) {

                        [$assignment['intro'], $assignment['introformat']] = external_format_text($module->intro,
                            $module->introformat, $context->id, 'mod_assign', 'intro', null);

                        $fs = get_file_storage();
                        if ($files = $fs->get_area_files($context->id, 'mod_assign', ASSIGN_INTROATTACHMENT_FILEAREA,
                            0, 'timemodified', false)) {

                            $assignment['introattachments'] = [];
                            foreach ($files as $file) {
                                $filename = $file->get_filename();

                                $assignment['introattachments'][] = [
                                    'filename' => $filename,
                                    'mimetype' => $file->get_mimetype(),
                                    'fileurl' => moodle_url::make_webservice_pluginfile_url(
                                        $context->id, 'mod_assign', ASSIGN_INTROATTACHMENT_FILEAREA, 0, '/', $filename)->out(false),
                                ];
                            }
                        }
                    }

                    $assignmentarray[] = $assignment;
                }
            }
            $coursearray[] = [
                'id' => $courses[$id]->id,
                'fullname' => $courses[$id]->fullname,
                'shortname' => $courses[$id]->shortname,
                'timemodified' => $courses[$id]->timemodified,
                'assignments' => $assignmentarray,
            ];
        }

        $result = [
            'courses' => $coursearray,
            'warnings' => $warnings,
        ];

        return $result;
    }

    /**
     * Creates an assignment external_single_structure
     *
     * @return external_single_structure
     * @since Moodle 2.4
     */
    private static function get_assignments_assignment_structure() {
        return new external_single_structure(
            [
                'id' => new external_value(PARAM_INT, 'assignment id'),
                'cmid' => new external_value(PARAM_INT, 'course module id'),
                'course' => new external_value(PARAM_INT, 'course id'),
                'name' => new external_value(PARAM_TEXT, 'assignment name'),
                'nosubmissions' => new external_value(PARAM_INT, 'no submissions'),
                'submissiondrafts' => new external_value(PARAM_INT, 'submissions drafts'),
                'sendnotifications' => new external_value(PARAM_INT, 'send notifications'),
                'sendlatenotifications' => new external_value(PARAM_INT, 'send notifications'),
                'sendstudentnotifications' => new external_value(PARAM_INT, 'send student notifications (default)'),
                'duedate' => new external_value(PARAM_INT, 'assignment due date'),
                'allowsubmissionsfromdate' => new external_value(PARAM_INT, 'allow submissions from date'),
                'grade' => new external_value(PARAM_INT, 'grade type'),
                'timemodified' => new external_value(PARAM_INT, 'last time assignment was modified'),
                'completionsubmit' => new external_value(PARAM_INT, 'if enabled, set activity as complete following submission'),
                'cutoffdate' => new external_value(PARAM_INT, 'date after which submission is not accepted without an extension'),
                'teamsubmission' => new external_value(PARAM_INT, 'if enabled, students submit as a team'),
                'requireallteammemberssubmit' => new external_value(PARAM_INT, 'if enabled, all team members must submit'),
                'teamsubmissiongroupingid' => new external_value(PARAM_INT, 'the grouping id for the team submission groups'),
                'blindmarking' => new external_value(PARAM_INT, 'if enabled, hide identities until reveal identities actioned'),
                'revealidentities' => new external_value(PARAM_INT, 'show identities for a blind marking assignment'),
                'attemptreopenmethod' => new external_value(PARAM_TEXT, 'method used to control opening new attempts'),
                'maxattempts' => new external_value(PARAM_INT, 'maximum number of attempts allowed'),
                'markingworkflow' => new external_value(PARAM_INT, 'enable marking workflow'),
                'markingallocation' => new external_value(PARAM_INT, 'enable marking allocation'),
                'requiresubmissionstatement' => new external_value(PARAM_INT, 'student must accept submission statement'),
                'configs' => new external_multiple_structure(self::get_assignments_config_structure(), 'configuration settings'),
                'intro' => new external_value(PARAM_RAW,
                    'assignment intro, not allways returned because it deppends on the activity configuration', VALUE_OPTIONAL),
                'introformat' => new external_format_value('intro', VALUE_OPTIONAL),
                'introattachments' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'filename' => new external_value(PARAM_FILE, 'file name'),
                            'mimetype' => new external_value(PARAM_RAW, 'mime type'),
                            'fileurl' => new external_value(PARAM_URL, 'file download url'),
                        ]
                    ), 'intro attachments files', VALUE_OPTIONAL
                ),
            ], 'assignment information object');
    }

    /**
     * Creates an assign_plugin_config external_single_structure
     *
     * @return external_single_structure
     * @since Moodle 2.4
     */
    private static function get_assignments_config_structure() {
        return new external_single_structure(
            [
                'id' => new external_value(PARAM_INT, 'assign_plugin_config id'),
                'assignment' => new external_value(PARAM_INT, 'assignment id'),
                'plugin' => new external_value(PARAM_TEXT, 'plugin'),
                'subtype' => new external_value(PARAM_TEXT, 'subtype'),
                'name' => new external_value(PARAM_TEXT, 'name'),
                'value' => new external_value(PARAM_TEXT, 'value'),
            ], 'assignment configuration object'
        );
    }

    /**
     * Creates a course external_single_structure
     *
     * @return external_single_structure
     * @since Moodle 2.4
     */
    private static function get_assignments_course_structure() {
        return new external_single_structure(
            [
                'id' => new external_value(PARAM_INT, 'course id'),
                'fullname' => new external_value(PARAM_TEXT, 'course full name'),
                'shortname' => new external_value(PARAM_TEXT, 'course short name'),
                'timemodified' => new external_value(PARAM_INT, 'last time modified'),
                'assignments' => new external_multiple_structure(self::get_assignments_assignment_structure(), 'assignment info'),
            ], 'course information object'
        );
    }

    /**
     * Describes the return value for get_assignments
     *
     * @return external_single_structure
     * @since Moodle 2.4
     */
    public static function assignments_read_returns() {
        return new external_single_structure(
            [
                'courses' => new external_multiple_structure(self::get_assignments_course_structure(), 'list of courses'),
                'warnings' => new external_warnings('item can be \'course\' (errorcode 1 or 2) or \'module\' (errorcode 1)',
                    'When item is a course then itemid is a course id. When the item is a module then itemid is a module id',
                    'errorcode can be 1 (no access rights) or 2 (not enrolled or no permissions)'),
            ]
        );
    }
}
