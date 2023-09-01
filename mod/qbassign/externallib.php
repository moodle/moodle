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
 * External qbassign API
 *
 * @package    mod_qbassign
 * @since      Moodle 2.4
 * @copyright  2012 Paul Charsley
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");
require_once("$CFG->libdir/moodlelib.php");
require_once("$CFG->libdir/enrollib.php");
require_once("$CFG->dirroot/user/externallib.php");
require_once("$CFG->dirroot/mod/qbassign/lib.php");
require_once("$CFG->dirroot/mod/qbassign/locallib.php");
require_once("$CFG->dirroot/mod/quiz/lib.php");
/**
 * qbassign functions
 * @copyright 2012 Paul Charsley
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_qbassign_external extends \mod_qbassign\external\external_api {

    /**
     * Describes the parameters for get_grades
     * @return external_function_parameters
     * @since  Moodle 2.4
     */
    public static function get_grades_parameters() {
        return new external_function_parameters(
            array(
                'qbassignmentids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'qbassignment id'),
                    '1 or more qbassignment ids',
                    VALUE_REQUIRED),
                'since' => new external_value(PARAM_INT,
                          'timestamp, only return records where timemodified >= since',
                          VALUE_DEFAULT, 0)
            )
        );
    }

    /**
     * Returns grade information from qbassign_grades for the requested qbassignment ids
     * @param int[] $qbassignmentids
     * @param int $since only return records with timemodified >= since
     * @return array of grade records for each requested qbassignment
     * @since  Moodle 2.4
     */
    public static function get_grades($qbassignmentids, $since = 0) {
        global $DB;
        $params = self::validate_parameters(self::get_grades_parameters(),
                        array('qbassignmentids' => $qbassignmentids,
                              'since' => $since));

        $qbassignments = array();
        $warnings = array();
        $requestedqbassignmentids = $params['qbassignmentids'];

        // Check the user is allowed to get the grades for the qbassignments requested.
        $placeholders = array();
        list($sqlqbassignmentids, $placeholders) = $DB->get_in_or_equal($requestedqbassignmentids, SQL_PARAMS_NAMED);
        $sql = "SELECT cm.id, cm.instance FROM {course_modules} cm JOIN {modules} md ON md.id = cm.module ".
               "WHERE md.name = :modname AND cm.instance ".$sqlqbassignmentids;
        $placeholders['modname'] = 'qbassign';
        $cms = $DB->get_records_sql($sql, $placeholders);
        foreach ($cms as $cm) {
            try {
                $context = context_module::instance($cm->id);
                self::validate_context($context);
                $qbassign = new qbassign($context, null, null);
                $qbassign->require_view_grades();
            } catch (Exception $e) {
                $requestedqbassignmentids = array_diff($requestedqbassignmentids, array($cm->instance));
                $warning = array();
                $warning['item'] = 'qbassignment';
                $warning['itemid'] = $cm->instance;
                $warning['warningcode'] = '1';
                $warning['message'] = 'No access rights in module context';
                $warnings[] = $warning;
            }
        }

        // Create the query and populate an array of grade records from the recordset results.
        if (count ($requestedqbassignmentids) > 0) {
            $placeholders = array();
            list($inorequalsql, $placeholders) = $DB->get_in_or_equal($requestedqbassignmentids, SQL_PARAMS_NAMED);

            $sql = "SELECT ag.id,
                           ag.qbassignment,
                           ag.userid,
                           ag.timecreated,
                           ag.timemodified,
                           ag.grader,
                           ag.grade,
                           ag.attemptnumber
                      FROM {qbassign_grades} ag, {qbassign_submission} s
                     WHERE s.qbassignment $inorequalsql
                       AND s.userid = ag.userid
                       AND s.latest = 1
                       AND s.attemptnumber = ag.attemptnumber
                       AND ag.timemodified  >= :since
                       AND ag.qbassignment = s.qbassignment
                  ORDER BY ag.qbassignment, ag.id";

            $placeholders['since'] = $params['since'];
            $rs = $DB->get_recordset_sql($sql, $placeholders);
            $currentqbassignmentid = null;
            $qbassignment = null;
            foreach ($rs as $rd) {
                $grade = array();
                $grade['id'] = $rd->id;
                $grade['userid'] = $rd->userid;
                $grade['timecreated'] = $rd->timecreated;
                $grade['timemodified'] = $rd->timemodified;
                $grade['grader'] = $rd->grader;
                $grade['attemptnumber'] = $rd->attemptnumber;
                $grade['grade'] = (string)$rd->grade;

                if (is_null($currentqbassignmentid) || ($rd->qbassignment != $currentqbassignmentid )) {
                    if (!is_null($qbassignment)) {
                        $qbassignments[] = $qbassignment;
                    }
                    $qbassignment = array();
                    $qbassignment['qbassignmentid'] = $rd->qbassignment;
                    $qbassignment['grades'] = array();
                    $requestedqbassignmentids = array_diff($requestedqbassignmentids, array($rd->qbassignment));
                }
                $qbassignment['grades'][] = $grade;

                $currentqbassignmentid = $rd->qbassignment;
            }
            if (!is_null($qbassignment)) {
                $qbassignments[] = $qbassignment;
            }
            $rs->close();
        }
        foreach ($requestedqbassignmentids as $qbassignmentid) {
            $warning = array();
            $warning['item'] = 'qbassignment';
            $warning['itemid'] = $qbassignmentid;
            $warning['warningcode'] = '3';
            $warning['message'] = 'No grades found';
            $warnings[] = $warning;
        }

        $result = array();
        $result['qbassignments'] = $qbassignments;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Creates a grade single structure.
     *
     * @return external_single_structure a grade single structure.
     * @since  Moodle 3.1
     */
    private static function get_grade_structure($required = VALUE_REQUIRED) {
        return new external_single_structure(
            array(
                'id'                => new external_value(PARAM_INT, 'grade id'),
                'qbassignment'        => new external_value(PARAM_INT, 'qbassignment id', VALUE_OPTIONAL),
                'userid'            => new external_value(PARAM_INT, 'student id'),
                'attemptnumber'     => new external_value(PARAM_INT, 'attempt number'),
                'timecreated'       => new external_value(PARAM_INT, 'grade creation time'),
                'timemodified'      => new external_value(PARAM_INT, 'grade last modified time'),
                'grader'            => new external_value(PARAM_INT, 'grader, -1 if grader is hidden'),
                'grade'             => new external_value(PARAM_TEXT, 'grade'),
                'gradefordisplay'   => new external_value(PARAM_RAW, 'grade rendered into a format suitable for display',
                                                            VALUE_OPTIONAL),
            ), 'grade information', $required
        );
    }

    /**
     * Creates an qbassign_grades external_single_structure
     * @return external_single_structure
     * @since  Moodle 2.4
     */
    private static function qbassign_grades() {
        return new external_single_structure(
            array (
                'qbassignmentid'  => new external_value(PARAM_INT, 'qbassignment id'),
                'grades'        => new external_multiple_structure(self::get_grade_structure())
            )
        );
    }

    /**
     * Describes the get_grades return value
     * @return external_single_structure
     * @since  Moodle 2.4
     */
    public static function get_grades_returns() {
        return new external_single_structure(
            array(
                'qbassignments' => new external_multiple_structure(self::qbassign_grades(), 'list of qbassignment grade information'),
                'warnings'      => new external_warnings('item is always \'qbassignment\'',
                    'when errorcode is 3 then itemid is an qbassignment id. When errorcode is 1, itemid is a course module id',
                    'errorcode can be 3 (no grades found) or 1 (no permission to get grades)')
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since  Moodle 2.4
     */
    public static function get_qbassignments_parameters() {
        return new external_function_parameters(
            array(
                'courseids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'course id, empty for retrieving all the courses where the user is enroled in'),
                    '0 or more course ids',
                    VALUE_DEFAULT, array()
                ),
                'capabilities'  => new external_multiple_structure(
                    new external_value(PARAM_CAPABILITY, 'capability'),
                    'list of capabilities used to filter courses',
                    VALUE_DEFAULT, array()
                ),
                'includenotenrolledcourses' => new external_value(PARAM_BOOL, 'whether to return courses that the user can see
                                                                    even if is not enroled in. This requires the parameter courseids
                                                                    to not be empty.', VALUE_DEFAULT, false)
            )
        );
    }

    /**
     * Returns an array of courses the user is enrolled, and for each course all of the qbassignments that the user can
     * view within that course.
     *
     * @param array $courseids An optional array of course ids. If provided only qbassignments within the given course
     * will be returned. If the user is not enrolled in or can't view a given course a warning will be generated and returned.
     * @param array $capabilities An array of additional capability checks you wish to be made on the course context.
     * @param bool $includenotenrolledcourses Wheter to return courses that the user can see even if is not enroled in.
     * This requires the parameter $courseids to not be empty.
     * @return An array of courses and warnings.
     * @since  Moodle 2.4
     */
    public static function get_qbassignments($courseids = array(), $capabilities = array(), $includenotenrolledcourses = false) {
        global $USER, $DB, $CFG;

        $params = self::validate_parameters(
            self::get_qbassignments_parameters(),
            array(
                'courseids' => $courseids,
                'capabilities' => $capabilities,
                'includenotenrolledcourses' => $includenotenrolledcourses
            )
        );

        $warnings = array();
        $courses = array();
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
                    $warnings[] = array(
                        'item' => 'course',
                        'itemid' => $courseid,
                        'warningcode' => '2',
                        'message' => 'User is not enrolled or does not have requested capability'
                    );
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
                $courses[$cid]->contextid = $context->id;
            } catch (Exception $e) {
                unset($courses[$cid]);
                $warnings[] = array(
                    'item' => 'course',
                    'itemid' => $cid,
                    'warningcode' => '1',
                    'message' => 'No access rights in course context '.$e->getMessage()
                );
                continue;
            }
            if (count($params['capabilities']) > 0 && !has_all_capabilities($params['capabilities'], $context)) {
                unset($courses[$cid]);
            }
        }
        $extrafields='m.id as qbassignmentid, ' .
                     'm.course, ' .
                     'm.nosubmissions, ' .
                     'm.submissiondrafts, ' .
                     'm.sendnotifications, '.
                     'm.sendlatenotifications, ' .
                     'm.sendstudentnotifications, ' .
                     'm.duedate, ' .
                     'm.allowsubmissionsfromdate, '.
                     'm.grade, ' .
                     'm.timemodified, '.
                     'm.completionsubmit, ' .
                     'm.cutoffdate, ' .
                     'm.gradingduedate, ' .
                     'm.teamsubmission, ' .
                     'm.requireallteammemberssubmit, '.
                     'm.teamsubmissiongroupingid, ' .
                     'm.blindmarking, ' .
                     'm.hidegrader, ' .
                     'm.revealidentities, ' .
                     'm.attemptreopenmethod, '.
                     'm.maxattempts, ' .
                     'm.markingworkflow, ' .
                     'm.markingallocation, ' .
                     'm.requiresubmissionstatement, '.
                     'm.preventsubmissionnotingroup, '.
                     'm.intro, '.
                     'm.introformat,' .
                     'm.activity,' .
                     'm.activityformat,' .
                     'm.timelimit,' .
                     'm.submissionattachments';
        $coursearray = array();
        foreach ($courses as $id => $course) {
            $qbassignmentarray = array();
            // Get a list of qbassignments for the course.
            if ($modules = get_coursemodules_in_course('qbassign', $courses[$id]->id, $extrafields)) {
                foreach ($modules as $module) {
                    $context = context_module::instance($module->id);
                    try {
                        self::validate_context($context);
                        require_capability('mod/qbassign:view', $context);
                    } catch (Exception $e) {
                        $warnings[] = array(
                            'item' => 'module',
                            'itemid' => $module->id,
                            'warningcode' => '1',
                            'message' => 'No access rights in module context'
                        );
                        continue;
                    }

                    $qbassign = new qbassign($context, null, null);
                    // Update qbassign with override information.
                    $qbassign->update_effective_access($USER->id);

                    // Get configurations for only enabled plugins.
                    $plugins = $qbassign->get_submission_plugins();
                    $plugins = array_merge($plugins, $qbassign->get_feedback_plugins());

                    $configarray = array();
                    foreach ($plugins as $plugin) {
                        if ($plugin->is_enabled() && $plugin->is_visible()) {
                            $configrecords = $plugin->get_config_for_external();
                            foreach ($configrecords as $name => $value) {
                                $configarray[] = array(
                                    'plugin' => $plugin->get_type(),
                                    'subtype' => $plugin->get_subtype(),
                                    'name' => $name,
                                    'value' => $value
                                );
                            }
                        }
                    }

                    $qbassignment = array(
                        'id' => $module->qbassignmentid,
                        'cmid' => $module->id,
                        'course' => $module->course,
                        'name' => external_format_string($module->name, $context),
                        'nosubmissions' => $module->nosubmissions,
                        'submissiondrafts' => $module->submissiondrafts,
                        'sendnotifications' => $module->sendnotifications,
                        'sendlatenotifications' => $module->sendlatenotifications,
                        'sendstudentnotifications' => $module->sendstudentnotifications,
                        'duedate' => $qbassign->get_instance()->duedate,
                        'allowsubmissionsfromdate' => $qbassign->get_instance()->allowsubmissionsfromdate,
                        'grade' => $module->grade,
                        'timemodified' => $module->timemodified,
                        'completionsubmit' => $module->completionsubmit,
                        'cutoffdate' => $qbassign->get_instance()->cutoffdate,
                        'gradingduedate' => $qbassign->get_instance()->gradingduedate,
                        'teamsubmission' => $module->teamsubmission,
                        'requireallteammemberssubmit' => $module->requireallteammemberssubmit,
                        'teamsubmissiongroupingid' => $module->teamsubmissiongroupingid,
                        'blindmarking' => $module->blindmarking,
                        'hidegrader' => $module->hidegrader,
                        'revealidentities' => $module->revealidentities,
                        'attemptreopenmethod' => $module->attemptreopenmethod,
                        'maxattempts' => $module->maxattempts,
                        'markingworkflow' => $module->markingworkflow,
                        'markingallocation' => $module->markingallocation,
                        'requiresubmissionstatement' => $module->requiresubmissionstatement,
                        'preventsubmissionnotingroup' => $module->preventsubmissionnotingroup,
                        'timelimit' => $module->timelimit,
                        'submissionattachments' => $module->submissionattachments,
                        'configs' => $configarray
                    );

                    // Return or not intro and file attachments depending on the plugin settings.
                    if ($qbassign->show_intro()) {
                        $options = array('noclean' => true);
                        list($qbassignment['intro'], $qbassignment['introformat']) =
                            external_format_text($module->intro, $module->introformat, $context->id, 'mod_qbassign', 'intro', null,
                                $options);
                        $qbassignment['introfiles'] = external_util::get_area_files($context->id, 'mod_qbassign', 'intro', false,
                                                                                    false);
                        if ($qbassign->should_provide_intro_attachments($USER->id)) {
                            $qbassignment['introattachments'] = external_util::get_area_files($context->id, 'mod_qbassign',
                                qbassign_INTROATTACHMENT_FILEAREA, 0);
                        }
                    }

                    if ($module->requiresubmissionstatement) {
                        // Submission statement is required, return the submission statement value.
                        $adminconfig = get_config('qbassign');
                        // Single submission.
                        if (!$module->teamsubmission) {
                            list($qbassignment['submissionstatement'], $qbassignment['submissionstatementformat']) =
                                external_format_text($adminconfig->submissionstatement, FORMAT_MOODLE, $context->id,
                                    'mod_qbassign', '', 0);
                        } else { // Team submission.
                            // One user can submit for the whole team.
                            if (!empty($adminconfig->submissionstatementteamsubmission) && !$module->requireallteammemberssubmit) {
                                list($qbassignment['submissionstatement'], $qbassignment['submissionstatementformat']) =
                                    external_format_text($adminconfig->submissionstatementteamsubmission,
                                        FORMAT_MOODLE, $context->id, 'mod_qbassign', '', 0);
                            } else if (!empty($adminconfig->submissionstatementteamsubmissionallsubmit) &&
                                $module->requireallteammemberssubmit) {
                                // All team members must submit.
                                list($qbassignment['submissionstatement'], $qbassignment['submissionstatementformat']) =
                                    external_format_text($adminconfig->submissionstatementteamsubmissionallsubmit,
                                        FORMAT_MOODLE, $context->id, 'mod_qbassign', '', 0);
                            }
                        }
                    }

                    if ($module->activity && $qbassign->submissions_open($USER->id, true)) {
                        list($qbassignment['activity'], $qbassignment['activityformat']) = external_format_text($module->activity,
                                $module->activityformat, $context->id, 'mod_qbassign', qbassign_ACTIVITYATTACHMENT_FILEAREA, 0);
                        $qbassignment['activityattachments'] = external_util::get_area_files($context->id, 'mod_qbassign',
                            qbassign_ACTIVITYATTACHMENT_FILEAREA, 0);
                    }

                    $qbassignmentarray[] = $qbassignment;
                }
            }
            $coursearray[]= array(
                'id' => $courses[$id]->id,
                'fullname' => external_format_string($courses[$id]->fullname, $course->contextid),
                'shortname' => external_format_string($courses[$id]->shortname, $course->contextid),
                'timemodified' => $courses[$id]->timemodified,
                'qbassignments' => $qbassignmentarray
            );
        }

        $result = array(
            'courses' => $coursearray,
            'warnings' => $warnings
        );
        return $result;
    }

    /**
     * Creates an qbassignment external_single_structure
     *
     * @return external_single_structure
     * @since Moodle 2.4
     */
    private static function get_qbassignments_qbassignment_structure() {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'qbassignment id'),
                'cmid' => new external_value(PARAM_INT, 'course module id'),
                'course' => new external_value(PARAM_INT, 'course id'),
                'name' => new external_value(PARAM_RAW, 'qbassignment name'),
                'nosubmissions' => new external_value(PARAM_INT, 'no submissions'),
                'submissiondrafts' => new external_value(PARAM_INT, 'submissions drafts'),
                'sendnotifications' => new external_value(PARAM_INT, 'send notifications'),
                'sendlatenotifications' => new external_value(PARAM_INT, 'send notifications'),
                'sendstudentnotifications' => new external_value(PARAM_INT, 'send student notifications (default)'),
                'duedate' => new external_value(PARAM_INT, 'qbassignment due date'),
                'allowsubmissionsfromdate' => new external_value(PARAM_INT, 'allow submissions from date'),
                'grade' => new external_value(PARAM_INT, 'grade type'),
                'timemodified' => new external_value(PARAM_INT, 'last time qbassignment was modified'),
                'completionsubmit' => new external_value(PARAM_INT, 'if enabled, set activity as complete following submission'),
                'cutoffdate' => new external_value(PARAM_INT, 'date after which submission is not accepted without an extension'),
                'gradingduedate' => new external_value(PARAM_INT, 'the expected date for marking the submissions'),
                'teamsubmission' => new external_value(PARAM_INT, 'if enabled, students submit as a team'),
                'requireallteammemberssubmit' => new external_value(PARAM_INT, 'if enabled, all team members must submit'),
                'teamsubmissiongroupingid' => new external_value(PARAM_INT, 'the grouping id for the team submission groups'),
                'blindmarking' => new external_value(PARAM_INT, 'if enabled, hide identities until reveal identities actioned'),
                'hidegrader' => new external_value(PARAM_INT, 'If enabled, hide grader to student'),
                'revealidentities' => new external_value(PARAM_INT, 'show identities for a blind marking qbassignment'),
                'attemptreopenmethod' => new external_value(PARAM_TEXT, 'method used to control opening new attempts'),
                'maxattempts' => new external_value(PARAM_INT, 'maximum number of attempts allowed'),
                'markingworkflow' => new external_value(PARAM_INT, 'enable marking workflow'),
                'markingallocation' => new external_value(PARAM_INT, 'enable marking allocation'),
                'requiresubmissionstatement' => new external_value(PARAM_INT, 'student must accept submission statement'),
                'preventsubmissionnotingroup' => new external_value(PARAM_INT, 'Prevent submission not in group', VALUE_OPTIONAL),
                'submissionstatement' => new external_value(PARAM_RAW, 'Submission statement formatted.', VALUE_OPTIONAL),
                'submissionstatementformat' => new external_format_value('submissionstatement', VALUE_OPTIONAL),
                'configs' => new external_multiple_structure(self::get_qbassignments_config_structure(), 'configuration settings'),
                'intro' => new external_value(PARAM_RAW,
                    'qbassignment intro, not allways returned because it deppends on the activity configuration', VALUE_OPTIONAL),
                'introformat' => new external_format_value('intro', VALUE_OPTIONAL),
                'introfiles' => new external_files('Files in the introduction text', VALUE_OPTIONAL),
                'introattachments' => new external_files('intro attachments files', VALUE_OPTIONAL),
                'activity' => new external_value(PARAM_RAW, 'Description of activity', VALUE_OPTIONAL),
                'activityformat' => new external_format_value('activity', VALUE_OPTIONAL),
                'activityattachments' => new external_files('Files from activity field', VALUE_OPTIONAL),
                'timelimit' => new external_value(PARAM_INT, 'Time limit to complete assigment', VALUE_OPTIONAL),
                'submissionattachments' => new external_value(PARAM_INT,
                    'Flag to only show files during submission', VALUE_OPTIONAL),
            ), 'qbassignment information object');
    }

    /**
     * Creates an qbassign_plugin_config external_single_structure
     *
     * @return external_single_structure
     * @since Moodle 2.4
     */
    private static function get_qbassignments_config_structure() {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'qbassign_plugin_config id', VALUE_OPTIONAL),
                'qbassignment' => new external_value(PARAM_INT, 'qbassignment id', VALUE_OPTIONAL),
                'plugin' => new external_value(PARAM_TEXT, 'plugin'),
                'subtype' => new external_value(PARAM_TEXT, 'subtype'),
                'name' => new external_value(PARAM_TEXT, 'name'),
                'value' => new external_value(PARAM_TEXT, 'value')
            ), 'qbassignment configuration object'
        );
    }

    /**
     * Creates a course external_single_structure
     *
     * @return external_single_structure
     * @since Moodle 2.4
     */
    private static function get_qbassignments_course_structure() {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'course id'),
                'fullname' => new external_value(PARAM_RAW, 'course full name'),
                'shortname' => new external_value(PARAM_RAW, 'course short name'),
                'timemodified' => new external_value(PARAM_INT, 'last time modified'),
                'qbassignments' => new external_multiple_structure(self::get_qbassignments_qbassignment_structure(), 'qbassignment info')
              ), 'course information object'
        );
    }

    /**
     * Describes the return value for get_qbassignments
     *
     * @return external_single_structure
     * @since Moodle 2.4
     */
    public static function get_qbassignments_returns() {
        return new external_single_structure(
            array(
                'courses' => new external_multiple_structure(self::get_qbassignments_course_structure(), 'list of courses'),
                'warnings'  => new external_warnings('item can be \'course\' (errorcode 1 or 2) or \'module\' (errorcode 1)',
                    'When item is a course then itemid is a course id. When the item is a module then itemid is a module id',
                    'errorcode can be 1 (no access rights) or 2 (not enrolled or no permissions)')
            )
        );
    }

    /**
     * Return information (files and text fields) for the given plugins in the qbassignment.
     *
     * @param  qbassign $qbassign the qbassignment object
     * @param  array $qbassignplugins array of qbassignment plugins (submission or feedback)
     * @param  stdClass $item the item object (submission or grade)
     * @return array an array containing the plugins returned information
     */
    private static function get_plugins_data($qbassign, $qbassignplugins, $item) {
        global $CFG;

        $plugins = array();
        $fs = get_file_storage();

        foreach ($qbassignplugins as $qbassignplugin) {

            if (!$qbassignplugin->is_enabled() or !$qbassignplugin->is_visible()) {
                continue;
            }

            $plugin = array(
                'name' => $qbassignplugin->get_name(),
                'type' => $qbassignplugin->get_type()
            );
            // Subtype is 'qbassignsubmission', type is currently 'file' or 'onlinetex'.
            $component = $qbassignplugin->get_subtype().'_'.$qbassignplugin->get_type();

            $fileareas = $qbassignplugin->get_file_areas();
            foreach ($fileareas as $filearea => $name) {
                $fileareainfo = array('area' => $filearea);

                $fileareainfo['files'] = external_util::get_area_files(
                    $qbassign->get_context()->id,
                    $component,
                    $filearea,
                    $item->id
                );

                $plugin['fileareas'][] = $fileareainfo;
            }

            $editorfields = $qbassignplugin->get_editor_fields();
            foreach ($editorfields as $name => $description) {
                $editorfieldinfo = array(
                    'name' => $name,
                    'description' => $description,
                    'text' => $qbassignplugin->get_editor_text($name, $item->id),
                    'format' => $qbassignplugin->get_editor_format($name, $item->id)
                );

                // Now format the text.
                foreach ($fileareas as $filearea => $name) {
                    list($editorfieldinfo['text'], $editorfieldinfo['format']) = external_format_text(
                        $editorfieldinfo['text'], $editorfieldinfo['format'], $qbassign->get_context()->id,
                        $component, $filearea, $item->id);
                }

                $plugin['editorfields'][] = $editorfieldinfo;
            }
            $plugins[] = $plugin;
        }
        return $plugins;
    }

    /**
     * Describes the parameters for get_submissions
     *
     * @return external_function_parameters
     * @since Moodle 2.5
     */
    public static function get_submissions_parameters() {
        return new external_function_parameters(
            array(
                'qbassignmentids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'qbassignment id'),
                    '1 or more qbassignment ids',
                    VALUE_REQUIRED),
                'status' => new external_value(PARAM_ALPHA, 'status', VALUE_DEFAULT, ''),
                'since' => new external_value(PARAM_INT, 'submitted since', VALUE_DEFAULT, 0),
                'before' => new external_value(PARAM_INT, 'submitted before', VALUE_DEFAULT, 0)
            )
        );
    }

    /**
     * Returns submissions for the requested qbassignment ids
     *
     * @param int[] $qbassignmentids
     * @param string $status only return submissions with this status
     * @param int $since only return submissions with timemodified >= since
     * @param int $before only return submissions with timemodified <= before
     * @return array of submissions for each requested qbassignment
     * @since Moodle 2.5
     */
    public static function get_submissions($qbassignmentids, $status = '', $since = 0, $before = 0) {
        global $DB, $CFG;

        $params = self::validate_parameters(self::get_submissions_parameters(),
                        array('qbassignmentids' => $qbassignmentids,
                              'status' => $status,
                              'since' => $since,
                              'before' => $before));

        $warnings = array();
        $qbassignments = array();

        // Check the user is allowed to get the submissions for the qbassignments requested.
        $placeholders = array();
        list($inorequalsql, $placeholders) = $DB->get_in_or_equal($params['qbassignmentids'], SQL_PARAMS_NAMED);
        $sql = "SELECT cm.id, cm.instance FROM {course_modules} cm JOIN {modules} md ON md.id = cm.module ".
               "WHERE md.name = :modname AND cm.instance ".$inorequalsql;
        $placeholders['modname'] = 'qbassign';
        $cms = $DB->get_records_sql($sql, $placeholders);
        $qbassigns = array();
        foreach ($cms as $cm) {
            try {
                $context = context_module::instance($cm->id);
                self::validate_context($context);
                $qbassign = new qbassign($context, null, null);
                $qbassign->require_view_grades();
                $qbassigns[] = $qbassign;
            } catch (Exception $e) {
                $warnings[] = array(
                    'item' => 'qbassignment',
                    'itemid' => $cm->instance,
                    'warningcode' => '1',
                    'message' => 'No access rights in module context'
                );
            }
        }

        foreach ($qbassigns as $qbassign) {
            $submissions = array();
            $placeholders = array('qbassignid1' => $qbassign->get_instance()->id,
                                  'qbassignid2' => $qbassign->get_instance()->id);

            $submissionmaxattempt = 'SELECT mxs.userid, mxs.groupid, MAX(mxs.attemptnumber) AS maxattempt
                                     FROM {qbassign_submission} mxs
                                     WHERE mxs.qbassignment = :qbassignid1 GROUP BY mxs.userid, mxs.groupid';

            $sql = "SELECT mas.id, mas.qbassignment,mas.userid,".
                   "mas.timecreated,mas.timemodified,mas.timestarted,mas.status,mas.groupid,mas.attemptnumber ".
                   "FROM {qbassign_submission} mas ".
                   "JOIN ( " . $submissionmaxattempt . " ) smx ON mas.userid = smx.userid ".
                   "AND mas.groupid = smx.groupid ".
                   "WHERE mas.qbassignment = :qbassignid2 AND mas.attemptnumber = smx.maxattempt";

            if (!empty($params['status'])) {
                $placeholders['status'] = $params['status'];
                $sql = $sql." AND mas.status = :status";
            }
            if (!empty($params['before'])) {
                $placeholders['since'] = $params['since'];
                $placeholders['before'] = $params['before'];
                $sql = $sql." AND mas.timemodified BETWEEN :since AND :before";
            } else {
                $placeholders['since'] = $params['since'];
                $sql = $sql." AND mas.timemodified >= :since";
            }

            $submissionrecords = $DB->get_records_sql($sql, $placeholders);

            if (!empty($submissionrecords)) {
                $submissionplugins = $qbassign->get_submission_plugins();
                foreach ($submissionrecords as $submissionrecord) {
                    $submission = array(
                        'id' => $submissionrecord->id,
                        'userid' => $submissionrecord->userid,
                        'timecreated' => $submissionrecord->timecreated,
                        'timemodified' => $submissionrecord->timemodified,
                        'timestarted' => $submissionrecord->timestarted,
                        'status' => $submissionrecord->status,
                        'attemptnumber' => $submissionrecord->attemptnumber,
                        'groupid' => $submissionrecord->groupid,
                        'plugins' => self::get_plugins_data($qbassign, $submissionplugins, $submissionrecord),
                        'gradingstatus' => $qbassign->get_grading_status($submissionrecord->userid)
                    );

                    if (($qbassign->get_instance()->teamsubmission
                        && $qbassign->can_view_group_submission($submissionrecord->groupid))
                        || (!$qbassign->get_instance()->teamsubmission
                        && $qbassign->can_view_submission($submissionrecord->userid))
                    ) {
                        $submissions[] = $submission;
                    }
                }
            } else {
                $warnings[] = array(
                    'item' => 'module',
                    'itemid' => $qbassign->get_instance()->id,
                    'warningcode' => '3',
                    'message' => 'No submissions found'
                );
            }

            $qbassignments[] = array(
                'qbassignmentid' => $qbassign->get_instance()->id,
                'submissions' => $submissions
            );

        }

        $result = array(
            'qbassignments' => $qbassignments,
            'warnings' => $warnings
        );
        return $result;
    }

    /**
     * Creates an qbassignment plugin structure.
     *
     * @return external_single_structure the plugin structure
     */
    private static function get_plugin_structure() {
        return new external_single_structure(
            array(
                'type' => new external_value(PARAM_TEXT, 'submission plugin type'),
                'name' => new external_value(PARAM_TEXT, 'submission plugin name'),
                'fileareas' => new external_multiple_structure(
                    new external_single_structure(
                        array (
                            'area' => new external_value (PARAM_TEXT, 'file area'),
                            'files' => new external_files('files', VALUE_OPTIONAL),
                        )
                    ), 'fileareas', VALUE_OPTIONAL
                ),
                'editorfields' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'name' => new external_value(PARAM_TEXT, 'field name'),
                            'description' => new external_value(PARAM_RAW, 'field description'),
                            'text' => new external_value (PARAM_RAW, 'field value'),
                            'format' => new external_format_value ('text')
                        )
                    )
                    , 'editorfields', VALUE_OPTIONAL
                )
            )
        );
    }

    /**
     * Creates a submission structure.
     *
     * @return external_single_structure the submission structure
     */
    private static function get_submission_structure($required = VALUE_REQUIRED) {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'submission id'),
                'userid' => new external_value(PARAM_INT, 'student id'),
                'attemptnumber' => new external_value(PARAM_INT, 'attempt number'),
                'timecreated' => new external_value(PARAM_INT, 'submission creation time'),
                'timemodified' => new external_value(PARAM_INT, 'submission last modified time'),
                'timestarted' => new external_value(PARAM_INT, 'submission start time', VALUE_OPTIONAL),
                'status' => new external_value(PARAM_TEXT, 'submission status'),
                'groupid' => new external_value(PARAM_INT, 'group id'),
                'qbassignment' => new external_value(PARAM_INT, 'qbassignment id', VALUE_OPTIONAL),
                'latest' => new external_value(PARAM_INT, 'latest attempt', VALUE_OPTIONAL),
                'plugins' => new external_multiple_structure(self::get_plugin_structure(), 'plugins', VALUE_OPTIONAL),
                'gradingstatus' => new external_value(PARAM_ALPHANUMEXT, 'Grading status.', VALUE_OPTIONAL),
            ), 'submission info', $required
        );
    }

    /**
     * Creates an qbassign_submissions external_single_structure
     *
     * @return external_single_structure
     * @since Moodle 2.5
     */
    private static function get_submissions_structure() {
        return new external_single_structure(
            array (
                'qbassignmentid' => new external_value(PARAM_INT, 'qbassignment id'),
                'submissions' => new external_multiple_structure(self::get_submission_structure())
            )
        );
    }

    /**
     * Describes the get_submissions return value
     *
     * @return external_single_structure
     * @since Moodle 2.5
     */
    public static function get_submissions_returns() {
        return new external_single_structure(
            array(
                'qbassignments' => new external_multiple_structure(self::get_submissions_structure(), 'qbassignment submissions'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Describes the parameters for set_user_flags
     * @return external_function_parameters
     * @since  Moodle 2.6
     */
    public static function set_user_flags_parameters() {
        return new external_function_parameters(
            array(
                'qbassignmentid'    => new external_value(PARAM_INT, 'qbassignment id'),
                'userflags' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'userid'           => new external_value(PARAM_INT, 'student id'),
                            'locked'           => new external_value(PARAM_INT, 'locked', VALUE_OPTIONAL),
                            'mailed'           => new external_value(PARAM_INT, 'mailed', VALUE_OPTIONAL),
                            'extensionduedate' => new external_value(PARAM_INT, 'extension due date', VALUE_OPTIONAL),
                            'workflowstate'    => new external_value(PARAM_ALPHA, 'marking workflow state', VALUE_OPTIONAL),
                            'allocatedmarker'  => new external_value(PARAM_INT, 'allocated marker', VALUE_OPTIONAL)
                        )
                    )
                )
            )
        );
    }

    /**
     * Create or update user_flags records
     *
     * @param int $qbassignmentid the qbassignment for which the userflags are created or updated
     * @param array $userflags  An array of userflags to create or update
     * @return array containing success or failure information for each record
     * @since Moodle 2.6
     */
    public static function set_user_flags($qbassignmentid, $userflags = array()) {
        global $CFG, $DB;

        $params = self::validate_parameters(self::set_user_flags_parameters(),
                                            array('qbassignmentid' => $qbassignmentid,
                                                  'userflags' => $userflags));

        // Load qbassignment if it exists and if the user has the capability.
        list($qbassign, $course, $cm, $context) = self::validate_qbassign($params['qbassignmentid']);
        require_capability('mod/qbassign:grade', $context);

        $results = array();
        foreach ($params['userflags'] as $userflag) {
            $success = true;
            $result = array();

            $record = $qbassign->get_user_flags($userflag['userid'], false);
            if ($record) {
                if (isset($userflag['locked'])) {
                    $record->locked = $userflag['locked'];
                }
                if (isset($userflag['mailed'])) {
                    $record->mailed = $userflag['mailed'];
                }
                if (isset($userflag['extensionduedate'])) {
                    $record->extensionduedate = $userflag['extensionduedate'];
                }
                if (isset($userflag['workflowstate'])) {
                    $record->workflowstate = $userflag['workflowstate'];
                }
                if (isset($userflag['allocatedmarker'])) {
                    $record->allocatedmarker = $userflag['allocatedmarker'];
                }
                if ($qbassign->update_user_flags($record)) {
                    $result['id'] = $record->id;
                    $result['userid'] = $userflag['userid'];
                } else {
                    $result['id'] = $record->id;
                    $result['userid'] = $userflag['userid'];
                    $result['errormessage'] = 'Record created but values could not be set';
                }
            } else {
                $record = $qbassign->get_user_flags($userflag['userid'], true);
                $setfields = isset($userflag['locked'])
                             || isset($userflag['mailed'])
                             || isset($userflag['extensionduedate'])
                             || isset($userflag['workflowstate'])
                             || isset($userflag['allocatedmarker']);
                if ($record) {
                    if ($setfields) {
                        if (isset($userflag['locked'])) {
                            $record->locked = $userflag['locked'];
                        }
                        if (isset($userflag['mailed'])) {
                            $record->mailed = $userflag['mailed'];
                        }
                        if (isset($userflag['extensionduedate'])) {
                            $record->extensionduedate = $userflag['extensionduedate'];
                        }
                        if (isset($userflag['workflowstate'])) {
                            $record->workflowstate = $userflag['workflowstate'];
                        }
                        if (isset($userflag['allocatedmarker'])) {
                            $record->allocatedmarker = $userflag['allocatedmarker'];
                        }
                        if ($qbassign->update_user_flags($record)) {
                            $result['id'] = $record->id;
                            $result['userid'] = $userflag['userid'];
                        } else {
                            $result['id'] = $record->id;
                            $result['userid'] = $userflag['userid'];
                            $result['errormessage'] = 'Record created but values could not be set';
                        }
                    } else {
                        $result['id'] = $record->id;
                        $result['userid'] = $userflag['userid'];
                    }
                } else {
                    $result['id'] = -1;
                    $result['userid'] = $userflag['userid'];
                    $result['errormessage'] = 'Record could not be created';
                }
            }

            $results[] = $result;
        }
        return $results;
    }

    /**
     * Describes the set_user_flags return value
     * @return external_multiple_structure
     * @since  Moodle 2.6
     */
    public static function set_user_flags_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'id of record if successful, -1 for failure'),
                    'userid' => new external_value(PARAM_INT, 'userid of record'),
                    'errormessage' => new external_value(PARAM_TEXT, 'Failure error message', VALUE_OPTIONAL)
                )
            )
        );
    }

    /**
     * Describes the parameters for get_user_flags
     * @return external_function_parameters
     * @since  Moodle 2.6
     */
    public static function get_user_flags_parameters() {
        return new external_function_parameters(
            array(
                'qbassignmentids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'qbassignment id'),
                    '1 or more qbassignment ids',
                    VALUE_REQUIRED)
            )
        );
    }

    /**
     * Returns user flag information from qbassign_user_flags for the requested qbassignment ids
     * @param int[] $qbassignmentids
     * @return array of user flag records for each requested qbassignment
     * @since  Moodle 2.6
     */
    public static function get_user_flags($qbassignmentids) {
        global $DB;
        $params = self::validate_parameters(self::get_user_flags_parameters(),
                        array('qbassignmentids' => $qbassignmentids));

        $qbassignments = array();
        $warnings = array();
        $requestedqbassignmentids = $params['qbassignmentids'];

        // Check the user is allowed to get the user flags for the qbassignments requested.
        $placeholders = array();
        list($sqlqbassignmentids, $placeholders) = $DB->get_in_or_equal($requestedqbassignmentids, SQL_PARAMS_NAMED);
        $sql = "SELECT cm.id, cm.instance FROM {course_modules} cm JOIN {modules} md ON md.id = cm.module ".
               "WHERE md.name = :modname AND cm.instance ".$sqlqbassignmentids;
        $placeholders['modname'] = 'qbassign';
        $cms = $DB->get_records_sql($sql, $placeholders);
        foreach ($cms as $cm) {
            try {
                $context = context_module::instance($cm->id);
                self::validate_context($context);
                require_capability('mod/qbassign:grade', $context);
            } catch (Exception $e) {
                $requestedqbassignmentids = array_diff($requestedqbassignmentids, array($cm->instance));
                $warning = array();
                $warning['item'] = 'qbassignment';
                $warning['itemid'] = $cm->instance;
                $warning['warningcode'] = '1';
                $warning['message'] = 'No access rights in module context';
                $warnings[] = $warning;
            }
        }

        // Create the query and populate an array of qbassign_user_flags records from the recordset results.
        if (count ($requestedqbassignmentids) > 0) {
            $placeholders = array();
            list($inorequalsql, $placeholders) = $DB->get_in_or_equal($requestedqbassignmentids, SQL_PARAMS_NAMED);

            $sql = "SELECT auf.id,auf.qbassignment,auf.userid,auf.locked,auf.mailed,".
                   "auf.extensionduedate,auf.workflowstate,auf.allocatedmarker ".
                   "FROM {qbassign_user_flags} auf ".
                   "WHERE auf.qbassignment ".$inorequalsql.
                   " ORDER BY auf.qbassignment, auf.id";

            $rs = $DB->get_recordset_sql($sql, $placeholders);
            $currentqbassignmentid = null;
            $qbassignment = null;
            foreach ($rs as $rd) {
                $userflag = array();
                $userflag['id'] = $rd->id;
                $userflag['userid'] = $rd->userid;
                $userflag['locked'] = $rd->locked;
                $userflag['mailed'] = $rd->mailed;
                $userflag['extensionduedate'] = $rd->extensionduedate;
                $userflag['workflowstate'] = $rd->workflowstate;
                $userflag['allocatedmarker'] = $rd->allocatedmarker;

                if (is_null($currentqbassignmentid) || ($rd->qbassignment != $currentqbassignmentid )) {
                    if (!is_null($qbassignment)) {
                        $qbassignments[] = $qbassignment;
                    }
                    $qbassignment = array();
                    $qbassignment['qbassignmentid'] = $rd->qbassignment;
                    $qbassignment['userflags'] = array();
                    $requestedqbassignmentids = array_diff($requestedqbassignmentids, array($rd->qbassignment));
                }
                $qbassignment['userflags'][] = $userflag;

                $currentqbassignmentid = $rd->qbassignment;
            }
            if (!is_null($qbassignment)) {
                $qbassignments[] = $qbassignment;
            }
            $rs->close();

        }

        foreach ($requestedqbassignmentids as $qbassignmentid) {
            $warning = array();
            $warning['item'] = 'qbassignment';
            $warning['itemid'] = $qbassignmentid;
            $warning['warningcode'] = '3';
            $warning['message'] = 'No user flags found';
            $warnings[] = $warning;
        }

        $result = array();
        $result['qbassignments'] = $qbassignments;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Creates an qbassign_user_flags external_single_structure
     * @return external_single_structure
     * @since  Moodle 2.6
     */
    private static function qbassign_user_flags() {
        return new external_single_structure(
            array (
                'qbassignmentid'    => new external_value(PARAM_INT, 'qbassignment id'),
                'userflags'   => new external_multiple_structure(new external_single_structure(
                        array(
                            'id'               => new external_value(PARAM_INT, 'user flag id'),
                            'userid'           => new external_value(PARAM_INT, 'student id'),
                            'locked'           => new external_value(PARAM_INT, 'locked'),
                            'mailed'           => new external_value(PARAM_INT, 'mailed'),
                            'extensionduedate' => new external_value(PARAM_INT, 'extension due date'),
                            'workflowstate'    => new external_value(PARAM_ALPHA, 'marking workflow state', VALUE_OPTIONAL),
                            'allocatedmarker'  => new external_value(PARAM_INT, 'allocated marker')
                        )
                    )
                )
            )
        );
    }

    /**
     * Describes the get_user_flags return value
     * @return external_single_structure
     * @since  Moodle 2.6
     */
    public static function get_user_flags_returns() {
        return new external_single_structure(
            array(
                'qbassignments' => new external_multiple_structure(self::qbassign_user_flags(), 'list of qbassign user flag information'),
                'warnings'      => new external_warnings('item is always \'qbassignment\'',
                    'when errorcode is 3 then itemid is an qbassignment id. When errorcode is 1, itemid is a course module id',
                    'errorcode can be 3 (no user flags found) or 1 (no permission to get user flags)')
            )
        );
    }

    /**
     * Describes the parameters for get_user_mappings
     * @return external_function_parameters
     * @since  Moodle 2.6
     */
    public static function get_user_mappings_parameters() {
        return new external_function_parameters(
            array(
                'qbassignmentids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'qbassignment id'),
                    '1 or more qbassignment ids',
                    VALUE_REQUIRED)
            )
        );
    }

    /**
     * Returns user mapping information from qbassign_user_mapping for the requested qbassignment ids
     * @param int[] $qbassignmentids
     * @return array of user mapping records for each requested qbassignment
     * @since  Moodle 2.6
     */
    public static function get_user_mappings($qbassignmentids) {
        global $DB;
        $params = self::validate_parameters(self::get_user_mappings_parameters(),
                        array('qbassignmentids' => $qbassignmentids));

        $qbassignments = array();
        $warnings = array();
        $requestedqbassignmentids = $params['qbassignmentids'];

        // Check the user is allowed to get the mappings for the qbassignments requested.
        $placeholders = array();
        list($sqlqbassignmentids, $placeholders) = $DB->get_in_or_equal($requestedqbassignmentids, SQL_PARAMS_NAMED);
        $sql = "SELECT cm.id, cm.instance FROM {course_modules} cm JOIN {modules} md ON md.id = cm.module ".
               "WHERE md.name = :modname AND cm.instance ".$sqlqbassignmentids;
        $placeholders['modname'] = 'qbassign';
        $cms = $DB->get_records_sql($sql, $placeholders);
        foreach ($cms as $cm) {
            try {
                $context = context_module::instance($cm->id);
                self::validate_context($context);
                require_capability('mod/qbassign:revealidentities', $context);
            } catch (Exception $e) {
                $requestedqbassignmentids = array_diff($requestedqbassignmentids, array($cm->instance));
                $warning = array();
                $warning['item'] = 'qbassignment';
                $warning['itemid'] = $cm->instance;
                $warning['warningcode'] = '1';
                $warning['message'] = 'No access rights in module context';
                $warnings[] = $warning;
            }
        }

        // Create the query and populate an array of qbassign_user_mapping records from the recordset results.
        if (count ($requestedqbassignmentids) > 0) {
            $placeholders = array();
            list($inorequalsql, $placeholders) = $DB->get_in_or_equal($requestedqbassignmentids, SQL_PARAMS_NAMED);

            $sql = "SELECT aum.id,aum.qbassignment,aum.userid ".
                   "FROM {qbassign_user_mapping} aum ".
                   "WHERE aum.qbassignment ".$inorequalsql.
                   " ORDER BY aum.qbassignment, aum.id";

            $rs = $DB->get_recordset_sql($sql, $placeholders);
            $currentqbassignmentid = null;
            $qbassignment = null;
            foreach ($rs as $rd) {
                $mapping = array();
                $mapping['id'] = $rd->id;
                $mapping['userid'] = $rd->userid;

                if (is_null($currentqbassignmentid) || ($rd->qbassignment != $currentqbassignmentid )) {
                    if (!is_null($qbassignment)) {
                        $qbassignments[] = $qbassignment;
                    }
                    $qbassignment = array();
                    $qbassignment['qbassignmentid'] = $rd->qbassignment;
                    $qbassignment['mappings'] = array();
                    $requestedqbassignmentids = array_diff($requestedqbassignmentids, array($rd->qbassignment));
                }
                $qbassignment['mappings'][] = $mapping;

                $currentqbassignmentid = $rd->qbassignment;
            }
            if (!is_null($qbassignment)) {
                $qbassignments[] = $qbassignment;
            }
            $rs->close();

        }

        foreach ($requestedqbassignmentids as $qbassignmentid) {
            $warning = array();
            $warning['item'] = 'qbassignment';
            $warning['itemid'] = $qbassignmentid;
            $warning['warningcode'] = '3';
            $warning['message'] = 'No mappings found';
            $warnings[] = $warning;
        }

        $result = array();
        $result['qbassignments'] = $qbassignments;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Creates an qbassign_user_mappings external_single_structure
     * @return external_single_structure
     * @since  Moodle 2.6
     */
    private static function qbassign_user_mappings() {
        return new external_single_structure(
            array (
                'qbassignmentid'    => new external_value(PARAM_INT, 'qbassignment id'),
                'mappings'   => new external_multiple_structure(new external_single_structure(
                        array(
                            'id'     => new external_value(PARAM_INT, 'user mapping id'),
                            'userid' => new external_value(PARAM_INT, 'student id')
                        )
                    )
                )
            )
        );
    }

    /**
     * Describes the get_user_mappings return value
     * @return external_single_structure
     * @since  Moodle 2.6
     */
    public static function get_user_mappings_returns() {
        return new external_single_structure(
            array(
                'qbassignments' => new external_multiple_structure(self::qbassign_user_mappings(), 'list of qbassign user mapping data'),
                'warnings'      => new external_warnings('item is always \'qbassignment\'',
                    'when errorcode is 3 then itemid is an qbassignment id. When errorcode is 1, itemid is a course module id',
                    'errorcode can be 3 (no user mappings found) or 1 (no permission to get user mappings)')
            )
        );
    }

    /**
     * Describes the parameters for lock_submissions
     * @return external_function_parameters
     * @since  Moodle 2.6
     */
    public static function lock_submissions_parameters() {
        return new external_function_parameters(
            array(
                'qbassignmentid' => new external_value(PARAM_INT, 'The qbassignment id to operate on'),
                'userids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'user id'),
                    '1 or more user ids',
                    VALUE_REQUIRED),
            )
        );
    }

    /**
     * Locks (prevent updates to) submissions in this qbassignment.
     *
     * @param int $qbassignmentid The id of the qbassignment
     * @param array $userids Array of user ids to lock
     * @return array of warnings for each submission that could not be locked.
     * @since Moodle 2.6
     */
    public static function lock_submissions($qbassignmentid, $userids) {
        global $CFG;

        $params = self::validate_parameters(self::lock_submissions_parameters(),
                        array('qbassignmentid' => $qbassignmentid,
                              'userids' => $userids));

        list($qbassignment, $course, $cm, $context) = self::validate_qbassign($params['qbassignmentid']);

        $warnings = array();
        foreach ($params['userids'] as $userid) {
            if (!$qbassignment->lock_submission($userid)) {
                $detail = 'User id: ' . $userid . ', qbassignment id: ' . $params['qbassignmentid'];
                $warnings[] = self::generate_warning($params['qbassignmentid'],
                                                     'couldnotlock',
                                                     $detail);
            }
        }

        return $warnings;
    }

    /**
     * Describes the return value for lock_submissions
     *
     * @return external_single_structure
     * @since Moodle 2.6
     */
    public static function lock_submissions_returns() {
        return new external_warnings();
    }

    /**
     * Describes the parameters for revert_submissions_to_draft
     * @return external_function_parameters
     * @since  Moodle 2.6
     */
    public static function revert_submissions_to_draft_parameters() {
        return new external_function_parameters(
            array(
                'qbassignmentid' => new external_value(PARAM_INT, 'The qbassignment id to operate on'),
                'userids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'user id'),
                    '1 or more user ids',
                    VALUE_REQUIRED),
            )
        );
    }

    /**
     * Reverts a list of user submissions to draft for a single qbassignment.
     *
     * @param int $qbassignmentid The id of the qbassignment
     * @param array $userids Array of user ids to revert
     * @return array of warnings for each submission that could not be reverted.
     * @since Moodle 2.6
     */
    public static function revert_submissions_to_draft($qbassignmentid, $userids) {
        global $CFG;

        $params = self::validate_parameters(self::revert_submissions_to_draft_parameters(),
                        array('qbassignmentid' => $qbassignmentid,
                              'userids' => $userids));

        list($qbassignment, $course, $cm, $context) = self::validate_qbassign($params['qbassignmentid']);

        $warnings = array();
        foreach ($params['userids'] as $userid) {
            if (!$qbassignment->revert_to_draft($userid)) {
                $detail = 'User id: ' . $userid . ', qbassignment id: ' . $params['qbassignmentid'];
                $warnings[] = self::generate_warning($params['qbassignmentid'],
                                                     'couldnotrevert',
                                                     $detail);
            }
        }

        return $warnings;
    }

    /**
     * Describes the return value for revert_submissions_to_draft
     *
     * @return external_single_structure
     * @since Moodle 2.6
     */
    public static function revert_submissions_to_draft_returns() {
        return new external_warnings();
    }

    /**
     * Describes the parameters for unlock_submissions
     * @return external_function_parameters
     * @since  Moodle 2.6
     */
    public static function unlock_submissions_parameters() {
        return new external_function_parameters(
            array(
                'qbassignmentid' => new external_value(PARAM_INT, 'The qbassignment id to operate on'),
                'userids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'user id'),
                    '1 or more user ids',
                    VALUE_REQUIRED),
            )
        );
    }

    /**
     * Locks (prevent updates to) submissions in this qbassignment.
     *
     * @param int $qbassignmentid The id of the qbassignment
     * @param array $userids Array of user ids to lock
     * @return array of warnings for each submission that could not be locked.
     * @since Moodle 2.6
     */
    public static function unlock_submissions($qbassignmentid, $userids) {
        global $CFG;

        $params = self::validate_parameters(self::unlock_submissions_parameters(),
                        array('qbassignmentid' => $qbassignmentid,
                              'userids' => $userids));

        list($qbassignment, $course, $cm, $context) = self::validate_qbassign($params['qbassignmentid']);

        $warnings = array();
        foreach ($params['userids'] as $userid) {
            if (!$qbassignment->unlock_submission($userid)) {
                $detail = 'User id: ' . $userid . ', qbassignment id: ' . $params['qbassignmentid'];
                $warnings[] = self::generate_warning($params['qbassignmentid'],
                                                     'couldnotunlock',
                                                     $detail);
            }
        }

        return $warnings;
    }

    /**
     * Describes the return value for unlock_submissions
     *
     * @return external_single_structure
     * @since Moodle 2.6
     */
    public static function unlock_submissions_returns() {
        return new external_warnings();
    }

    /**
     * Describes the parameters for submit_grading_form webservice.
     * @return external_function_parameters
     * @since  Moodle 3.1
     */
    public static function submit_grading_form_parameters() {
        return new external_function_parameters(
            array(
                'qbassignmentid' => new external_value(PARAM_INT, 'The qbassignment id to operate on'),
                'userid' => new external_value(PARAM_INT, 'The user id the submission belongs to'),
                'jsonformdata' => new external_value(PARAM_RAW, 'The data from the grading form, encoded as a json array')
            )
        );
    }

    /**
     * Submit the logged in users qbassignment for grading.
     *
     * @param int $qbassignmentid The id of the qbassignment
     * @param int $userid The id of the user the submission belongs to.
     * @param string $jsonformdata The data from the form, encoded as a json array.
     * @return array of warnings to indicate any errors.
     * @since Moodle 3.1
     */
    public static function submit_grading_form($qbassignmentid, $userid, $jsonformdata) {
        global $CFG, $USER;

        require_once($CFG->dirroot . '/mod/qbassign/locallib.php');
        require_once($CFG->dirroot . '/mod/qbassign/gradeform.php');

        $params = self::validate_parameters(self::submit_grading_form_parameters(),
                                            array(
                                                'qbassignmentid' => $qbassignmentid,
                                                'userid' => $userid,
                                                'jsonformdata' => $jsonformdata
                                            ));

        list($qbassignment, $course, $cm, $context) = self::validate_qbassign($params['qbassignmentid']);

        $serialiseddata = json_decode($params['jsonformdata']);

        $data = array();
        parse_str($serialiseddata, $data);

        $warnings = array();

        $options = array(
            'userid' => $params['userid'],
            'attemptnumber' => $data['attemptnumber'],
            'rownum' => 0,
            'gradingpanel' => true
        );

        if (WS_SERVER) {
            // Assume form submission if coming from WS.
            $USER->ignoresesskey = true;
            $data['_qf__mod_qbassign_grade_form_'.$params['userid']] = 1;
        }

        $customdata = (object) $data;
        $formparams = array($qbassignment, $customdata, $options);

        // Data is injected into the form by the last param for the constructor.
        $mform = new mod_qbassign_grade_form(null, $formparams, 'post', '', null, true, $data);
        $validateddata = $mform->get_data();

        if ($validateddata) {
            $qbassignment->save_grade($params['userid'], $validateddata);
        } else {
            $warnings[] = self::generate_warning($params['qbassignmentid'],
                                                 'couldnotsavegrade',
                                                 'Form validation failed.');
        }


        return $warnings;
    }

    /**
     * Describes the return for submit_grading_form
     * @return external_function_parameters
     * @since  Moodle 3.1
     */
    public static function submit_grading_form_returns() {
        return new external_warnings();
    }

    /**
     * Describes the parameters for submit_for_grading
     * @return external_function_parameters
     * @since  Moodle 2.6
     */
    public static function submit_for_grading_parameters() {
        return new external_function_parameters(
            array(
                'qbassignmentid' => new external_value(PARAM_INT, 'The qbassignment id to operate on'),
                'acceptsubmissionstatement' => new external_value(PARAM_BOOL, 'Accept the qbassignment submission statement')
            )
        );
    }

    /**
     * Submit the logged in users qbassignment for grading.
     *
     * @param int $qbassignmentid The id of the qbassignment
     * @return array of warnings to indicate any errors.
     * @since Moodle 2.6
     */
    public static function submit_for_grading($qbassignmentid, $acceptsubmissionstatement) {
        global $CFG, $USER;

        $params = self::validate_parameters(self::submit_for_grading_parameters(),
                                            array('qbassignmentid' => $qbassignmentid,
                                                  'acceptsubmissionstatement' => $acceptsubmissionstatement));

        list($qbassignment, $course, $cm, $context) = self::validate_qbassign($params['qbassignmentid']);

        $warnings = array();
        $data = new stdClass();
        $data->submissionstatement = $params['acceptsubmissionstatement'];
        $notices = array();

        if (!$qbassignment->submit_for_grading($data, $notices)) {
            $detail = 'User id: ' . $USER->id . ', qbassignment id: ' . $params['qbassignmentid'] . ' Notices:' . implode(', ', $notices);
            $warnings[] = self::generate_warning($params['qbassignmentid'],
                                                 'couldnotsubmitforgrading',
                                                 $detail);
        }

        return $warnings;
    }

    /**
     * Describes the return value for submit_for_grading
     *
     * @return external_single_structure
     * @since Moodle 2.6
     */
    public static function submit_for_grading_returns() {
        return new external_warnings();
    }

    /**
     * Describes the parameters for save_user_extensions
     * @return external_function_parameters
     * @since  Moodle 2.6
     */
    public static function save_user_extensions_parameters() {
        return new external_function_parameters(
            array(
                'qbassignmentid' => new external_value(PARAM_INT, 'The qbassignment id to operate on'),
                'userids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'user id'),
                    '1 or more user ids',
                    VALUE_REQUIRED),
                'dates' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'dates'),
                    '1 or more extension dates (timestamp)',
                    VALUE_REQUIRED),
            )
        );
    }

    /**
     * Grant extension dates to students for an qbassignment.
     *
     * @param int $qbassignmentid The id of the qbassignment
     * @param array $userids Array of user ids to grant extensions to
     * @param array $dates Array of extension dates
     * @return array of warnings for each extension date that could not be granted
     * @since Moodle 2.6
     */
    public static function save_user_extensions($qbassignmentid, $userids, $dates) {
        global $CFG;

        $params = self::validate_parameters(self::save_user_extensions_parameters(),
                        array('qbassignmentid' => $qbassignmentid,
                              'userids' => $userids,
                              'dates' => $dates));

        if (count($params['userids']) != count($params['dates'])) {
            $detail = 'Length of userids and dates parameters differ.';
            $warnings[] = self::generate_warning($params['qbassignmentid'],
                                                 'invalidparameters',
                                                 $detail);

            return $warnings;
        }

        list($qbassignment, $course, $cm, $context) = self::validate_qbassign($params['qbassignmentid']);

        $warnings = array();
        foreach ($params['userids'] as $idx => $userid) {
            $duedate = $params['dates'][$idx];
            if (!$qbassignment->save_user_extension($userid, $duedate)) {
                $detail = 'User id: ' . $userid . ', qbassignment id: ' . $params['qbassignmentid'] . ', Extension date: ' . $duedate;
                $warnings[] = self::generate_warning($params['qbassignmentid'],
                                                     'couldnotgrantextensions',
                                                     $detail);
            }
        }

        return $warnings;
    }

    /**
     * Describes the return value for save_user_extensions
     *
     * @return external_single_structure
     * @since Moodle 2.6
     */
    public static function save_user_extensions_returns() {
        return new external_warnings();
    }

    /**
     * Describes the parameters for reveal_identities
     * @return external_function_parameters
     * @since  Moodle 2.6
     */
    public static function reveal_identities_parameters() {
        return new external_function_parameters(
            array(
                'qbassignmentid' => new external_value(PARAM_INT, 'The qbassignment id to operate on')
            )
        );
    }

    /**
     * Reveal the identities of anonymous students to markers for a single qbassignment.
     *
     * @param int $qbassignmentid The id of the qbassignment
     * @return array of warnings to indicate any errors.
     * @since Moodle 2.6
     */
    public static function reveal_identities($qbassignmentid) {
        global $CFG, $USER;

        $params = self::validate_parameters(self::reveal_identities_parameters(),
                                            array('qbassignmentid' => $qbassignmentid));

        list($qbassignment, $course, $cm, $context) = self::validate_qbassign($params['qbassignmentid']);

        $warnings = array();
        if (!$qbassignment->reveal_identities()) {
            $detail = 'User id: ' . $USER->id . ', qbassignment id: ' . $params['qbassignmentid'];
            $warnings[] = self::generate_warning($params['qbassignmentid'],
                                                 'couldnotrevealidentities',
                                                 $detail);
        }

        return $warnings;
    }

    /**
     * Describes the return value for reveal_identities
     *
     * @return external_single_structure
     * @since Moodle 2.6
     */
    public static function reveal_identities_returns() {
        return new external_warnings();
    }

    /**
     * Describes the parameters for save_submission
     * @return external_function_parameters
     * @since  Moodle 2.6
     */
    public static function save_submission_parameters() {
        global $CFG;
        $instance = new qbassign(null, null, null);
        $pluginsubmissionparams = array();

        foreach ($instance->get_submission_plugins() as $plugin) {
            if ($plugin->is_visible()) {
                $pluginparams = $plugin->get_external_parameters();
                if (!empty($pluginparams)) {
                    $pluginsubmissionparams = array_merge($pluginsubmissionparams, $pluginparams);
                }
            }
        }

        return new external_function_parameters(
            array(
                'qbassignmentid' => new external_value(PARAM_INT, 'The qbassignment id to operate on'),
                'plugindata' => new external_single_structure(
                    $pluginsubmissionparams
                )
            )
        );
    }

    /**
     * Save a student submission for a single qbassignment
     *
     * @param int $qbassignmentid The id of the qbassignment
     * @param array $plugindata - The submitted data for plugins
     * @return array of warnings to indicate any errors
     * @since Moodle 2.6
     */
    public static function save_submission($qbassignmentid, $plugindata) {
        global $CFG, $USER;

       /*$params = self::validate_parameters(self::save_submission_parameters(),
                                            array('qbassignmentid' => $qbassignmentid,
                                                  'plugindata' => $plugindata)); */
        $params['plugindata'] = $plugindata;
        list($qbassignment, $course, $cm, $context) = self::validate_qbassign($params['qbassignmentid']);

        $notices = array();

        $qbassignment->update_effective_access($USER->id);
        if (!$qbassignment->submissions_open($USER->id)) {
            $notices[] = get_string('duedatereached', 'qbassign');
        } else {
            $submissiondata = (object)$params['plugindata'];
            $qbassignment->save_submission($submissiondata, $notices);
        }

        $warnings = array();
        foreach ($notices as $notice) {
            $warnings[] = self::generate_warning($params['qbassignmentid'],
                                                 'couldnotsavesubmission',
                                                 $notice);
        }

        return $warnings;
    }

    /**
     * Describes the return value for save_submission
     *
     * @return external_single_structure
     * @since Moodle 2.6
     */
    public static function save_submission_returns() {
        return new external_warnings();
    }

    /**
     * Describes the parameters for save_grade
     * @return external_function_parameters
     * @since  Moodle 2.6
     */
    public static function save_grade_parameters() {
        global $CFG;
        require_once("$CFG->dirroot/grade/grading/lib.php");
        $instance = new qbassign(null, null, null);
        $pluginfeedbackparams = array();

        foreach ($instance->get_feedback_plugins() as $plugin) {
            if ($plugin->is_visible()) {
                $pluginparams = $plugin->get_external_parameters();
                if (!empty($pluginparams)) {
                    $pluginfeedbackparams = array_merge($pluginfeedbackparams, $pluginparams);
                }
            }
        }

        $advancedgradingdata = array();
        $methods = array_keys(grading_manager::available_methods(false));
        foreach ($methods as $method) {
            require_once($CFG->dirroot.'/grade/grading/form/'.$method.'/lib.php');
            $details  = call_user_func('gradingform_'.$method.'_controller::get_external_instance_filling_details');
            if (!empty($details)) {
                $items = array();
                foreach ($details as $key => $value) {
                    $value->required = VALUE_OPTIONAL;
                    unset($value->content->keys['id']);
                    $items[$key] = new external_multiple_structure (new external_single_structure(
                        array(
                            'criterionid' => new external_value(PARAM_INT, 'criterion id'),
                            'fillings' => $value
                        )
                    ));
                }
                $advancedgradingdata[$method] = new external_single_structure($items, 'items', VALUE_OPTIONAL);
            }
        }

        return new external_function_parameters(
            array(
                'qbassignmentid' => new external_value(PARAM_INT, 'The qbassignment id to operate on'),
                'userid' => new external_value(PARAM_INT, 'The student id to operate on'),
                'grade' => new external_value(PARAM_FLOAT, 'The new grade for this user. Ignored if advanced grading used'),
                'attemptnumber' => new external_value(PARAM_INT, 'The attempt number (-1 means latest attempt)'),
                'addattempt' => new external_value(PARAM_BOOL, 'Allow another attempt if the attempt reopen method is manual'),
                'workflowstate' => new external_value(PARAM_ALPHA, 'The next marking workflow state'),
                'applytoall' => new external_value(PARAM_BOOL, 'If true, this grade will be applied ' .
                                                               'to all members ' .
                                                               'of the group (for group qbassignments).'),
                'plugindata' => new external_single_structure($pluginfeedbackparams, 'plugin data', VALUE_DEFAULT, array()),
                'advancedgradingdata' => new external_single_structure($advancedgradingdata, 'advanced grading data',
                                                                       VALUE_DEFAULT, array())
            )
        );
    }

    /**
     * Save a student grade for a single qbassignment.
     *
     * @param int $qbassignmentid The id of the qbassignment
     * @param int $userid The id of the user
     * @param float $grade The grade (ignored if the qbassignment uses advanced grading)
     * @param int $attemptnumber The attempt number
     * @param bool $addattempt Allow another attempt
     * @param string $workflowstate New workflow state
     * @param bool $applytoall Apply the grade to all members of the group
     * @param array $plugindata Custom data used by plugins
     * @param array $advancedgradingdata Advanced grading data
     * @return null
     * @since Moodle 2.6
     */
    public static function save_grade($qbassignmentid,
                                      $userid,
                                      $grade,
                                      $attemptnumber,
                                      $addattempt,
                                      $workflowstate,
                                      $applytoall,
                                      $plugindata = array(),
                                      $advancedgradingdata = array()) {
        global $CFG, $USER;

        $params = self::validate_parameters(self::save_grade_parameters(),
                                            array('qbassignmentid' => $qbassignmentid,
                                                  'userid' => $userid,
                                                  'grade' => $grade,
                                                  'attemptnumber' => $attemptnumber,
                                                  'workflowstate' => $workflowstate,
                                                  'addattempt' => $addattempt,
                                                  'applytoall' => $applytoall,
                                                  'plugindata' => $plugindata,
                                                  'advancedgradingdata' => $advancedgradingdata));

        list($qbassignment, $course, $cm, $context) = self::validate_qbassign($params['qbassignmentid']);

        $gradedata = (object)$params['plugindata'];

        $gradedata->addattempt = $params['addattempt'];
        $gradedata->attemptnumber = $params['attemptnumber'];
        $gradedata->workflowstate = $params['workflowstate'];
        $gradedata->applytoall = $params['applytoall'];
        $gradedata->grade = $params['grade'];

        if (!empty($params['advancedgradingdata'])) {
            $advancedgrading = array();
            $criteria = reset($params['advancedgradingdata']);
            foreach ($criteria as $key => $criterion) {
                $details = array();
                foreach ($criterion as $value) {
                    foreach ($value['fillings'] as $filling) {
                        $details[$value['criterionid']] = $filling;
                    }
                }
                $advancedgrading[$key] = $details;
            }
            $gradedata->advancedgrading = $advancedgrading;
        }

        $qbassignment->save_grade($params['userid'], $gradedata);

        return null;
    }

    /**
     * Describes the return value for save_grade
     *
     * @return external_single_structure
     * @since Moodle 2.6
     */
    public static function save_grade_returns() {
        return null;
    }

    /**
     * Describes the parameters for save_grades
     * @return external_function_parameters
     * @since  Moodle 2.7
     */
    public static function save_grades_parameters() {
        global $CFG;
        require_once("$CFG->dirroot/grade/grading/lib.php");
        $instance = new qbassign(null, null, null);
        $pluginfeedbackparams = array();

        foreach ($instance->get_feedback_plugins() as $plugin) {
            if ($plugin->is_visible()) {
                $pluginparams = $plugin->get_external_parameters();
                if (!empty($pluginparams)) {
                    $pluginfeedbackparams = array_merge($pluginfeedbackparams, $pluginparams);
                }
            }
        }

        $advancedgradingdata = array();
        $methods = array_keys(grading_manager::available_methods(false));
        foreach ($methods as $method) {
            require_once($CFG->dirroot.'/grade/grading/form/'.$method.'/lib.php');
            $details  = call_user_func('gradingform_'.$method.'_controller::get_external_instance_filling_details');
            if (!empty($details)) {
                $items = array();
                foreach ($details as $key => $value) {
                    $value->required = VALUE_OPTIONAL;
                    unset($value->content->keys['id']);
                    $items[$key] = new external_multiple_structure (new external_single_structure(
                        array(
                            'criterionid' => new external_value(PARAM_INT, 'criterion id'),
                            'fillings' => $value
                        )
                    ));
                }
                $advancedgradingdata[$method] = new external_single_structure($items, 'items', VALUE_OPTIONAL);
            }
        }

        return new external_function_parameters(
            array(
                'qbassignmentid' => new external_value(PARAM_INT, 'The qbassignment id to operate on'),
                'applytoall' => new external_value(PARAM_BOOL, 'If true, this grade will be applied ' .
                                                               'to all members ' .
                                                               'of the group (for group qbassignments).'),
                'grades' => new external_multiple_structure(
                    new external_single_structure(
                        array (
                            'userid' => new external_value(PARAM_INT, 'The student id to operate on'),
                            'grade' => new external_value(PARAM_FLOAT, 'The new grade for this user. '.
                                                                       'Ignored if advanced grading used'),
                            'attemptnumber' => new external_value(PARAM_INT, 'The attempt number (-1 means latest attempt)'),
                            'addattempt' => new external_value(PARAM_BOOL, 'Allow another attempt if manual attempt reopen method'),
                            'workflowstate' => new external_value(PARAM_ALPHA, 'The next marking workflow state'),
                            'plugindata' => new external_single_structure($pluginfeedbackparams, 'plugin data',
                                                                          VALUE_DEFAULT, array()),
                            'advancedgradingdata' => new external_single_structure($advancedgradingdata, 'advanced grading data',
                                                                                   VALUE_DEFAULT, array())
                        )
                    )
                )
            )
        );
    }

    /**
     * Save multiple student grades for a single qbassignment.
     *
     * @param int $qbassignmentid The id of the qbassignment
     * @param boolean $applytoall If set to true and this is a team qbassignment,
     * apply the grade to all members of the group
     * @param array $grades grade data for one or more students that includes
     *                  userid - The id of the student being graded
     *                  grade - The grade (ignored if the qbassignment uses advanced grading)
     *                  attemptnumber - The attempt number
     *                  addattempt - Allow another attempt
     *                  workflowstate - New workflow state
     *                  plugindata - Custom data used by plugins
     *                  advancedgradingdata - Optional Advanced grading data
     * @throws invalid_parameter_exception if multiple grades are supplied for
     * a team qbassignment that has $applytoall set to true
     * @return null
     * @since Moodle 2.7
     */
    public static function save_grades($qbassignmentid, $applytoall, $grades) {
        global $CFG, $USER;

        $params = self::validate_parameters(self::save_grades_parameters(),
                                            array('qbassignmentid' => $qbassignmentid,
                                                  'applytoall' => $applytoall,
                                                  'grades' => $grades));

        list($qbassignment, $course, $cm, $context) = self::validate_qbassign($params['qbassignmentid']);

        if ($qbassignment->get_instance()->teamsubmission && $params['applytoall']) {
            // Check that only 1 user per submission group is provided.
            $groupids = array();
            foreach ($params['grades'] as $gradeinfo) {
                $group = $qbassignment->get_submission_group($gradeinfo['userid']);
                if (in_array($group->id, $groupids)) {
                    throw new invalid_parameter_exception('Multiple grades for the same team have been supplied '
                                                          .' this is not permitted when the applytoall flag is set');
                } else {
                    $groupids[] = $group->id;
                }
            }
        }

        foreach ($params['grades'] as $gradeinfo) {
            $gradedata = (object)$gradeinfo['plugindata'];
            $gradedata->addattempt = $gradeinfo['addattempt'];
            $gradedata->attemptnumber = $gradeinfo['attemptnumber'];
            $gradedata->workflowstate = $gradeinfo['workflowstate'];
            $gradedata->applytoall = $params['applytoall'];
            $gradedata->grade = $gradeinfo['grade'];

            if (!empty($gradeinfo['advancedgradingdata'])) {
                $advancedgrading = array();
                $criteria = reset($gradeinfo['advancedgradingdata']);
                foreach ($criteria as $key => $criterion) {
                    $details = array();
                    foreach ($criterion as $value) {
                        foreach ($value['fillings'] as $filling) {
                            $details[$value['criterionid']] = $filling;
                        }
                    }
                    $advancedgrading[$key] = $details;
                }
                $gradedata->advancedgrading = $advancedgrading;
            }
            $qbassignment->save_grade($gradeinfo['userid'], $gradedata);
        }

        return null;
    }

    /**
     * Describes the return value for save_grades
     *
     * @return external_single_structure
     * @since Moodle 2.7
     */
    public static function save_grades_returns() {
        return null;
    }

    /**
     * Describes the parameters for copy_previous_attempt
     * @return external_function_parameters
     * @since  Moodle 2.6
     */
    public static function copy_previous_attempt_parameters() {
        return new external_function_parameters(
            array(
                'qbassignmentid' => new external_value(PARAM_INT, 'The qbassignment id to operate on'),
            )
        );
    }

    /**
     * Copy a students previous attempt to a new attempt.
     *
     * @param int $qbassignmentid
     * @return array of warnings to indicate any errors.
     * @since Moodle 2.6
     */
    public static function copy_previous_attempt($qbassignmentid) {

        $params = self::validate_parameters(self::copy_previous_attempt_parameters(),
                                            array('qbassignmentid' => $qbassignmentid));

        list($qbassignment, $course, $cm, $context) = self::validate_qbassign($params['qbassignmentid']);

        $notices = array();

        $qbassignment->copy_previous_attempt($notices);

        $warnings = array();
        foreach ($notices as $notice) {
            $warnings[] = self::generate_warning($qbassignmentid,
                                                 'couldnotcopyprevioussubmission',
                                                 $notice);
        }

        return $warnings;
    }

    /**
     * Describes the return value for save_submission
     *
     * @return external_single_structure
     * @since Moodle 2.6
     */
    public static function copy_previous_attempt_returns() {
        return new external_warnings();
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function view_grading_table_parameters() {
        return new external_function_parameters(
            array(
                'qbassignid' => new external_value(PARAM_INT, 'qbassign instance id')
            )
        );
    }

    /**
     * Trigger the grading_table_viewed event.
     *
     * @param int $qbassignid the qbassign instance id
     * @return array of warnings and status result
     * @since Moodle 3.0
     * @throws moodle_exception
     */
    public static function view_grading_table($qbassignid) {

        $params = self::validate_parameters(self::view_grading_table_parameters(),
                                            array(
                                                'qbassignid' => $qbassignid
                                            ));
        $warnings = array();

        list($qbassign, $course, $cm, $context) = self::validate_qbassign($params['qbassignid']);

        $qbassign->require_view_grades();
        \mod_qbassign\event\grading_table_viewed::create_from_qbassign($qbassign)->trigger();

        $result = array();
        $result['status'] = true;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function view_grading_table_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Describes the parameters for view_submission_status.
     *
     * @return external_function_parameters
     * @since Moodle 3.1
     */
    public static function view_submission_status_parameters() {
        return new external_function_parameters (
            array(
                'qbassignid' => new external_value(PARAM_INT, 'qbassign instance id'),
            )
        );
    }

    /**
     * Trigger the submission status viewed event.
     *
     * @param int $qbassignid qbassign instance id
     * @return array of warnings and status result
     * @since Moodle 3.1
     */
    public static function view_submission_status($qbassignid) {

        $warnings = array();
        $params = array(
            'qbassignid' => $qbassignid,
        );
        $params = self::validate_parameters(self::view_submission_status_parameters(), $params);

        list($qbassign, $course, $cm, $context) = self::validate_qbassign($params['qbassignid']);

        \mod_qbassign\event\submission_status_viewed::create_from_qbassign($qbassign)->trigger();

        $result = array();
        $result['status'] = true;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the view_submission_status return value.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function view_submission_status_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for get_submission_status.
     *
     * @return external_function_parameters
     * @since Moodle 3.1
     */
    public static function get_submission_status_parameters() {
        return new external_function_parameters (
            array(
                'qbassignid' => new external_value(PARAM_INT, 'qbassignment instance id'),
                'userid' => new external_value(PARAM_INT, 'user id (empty for current user)', VALUE_DEFAULT, 0),
                'groupid' => new external_value(PARAM_INT, 'filter by users in group (used for generating the grading summary).
                    0 for all groups information, any other empty value will calculate currrent group.', VALUE_DEFAULT, 0),
            )
        );
    }

    /**
     * Returns information about an qbassignment submission status for a given user.
     *
     * @param int $qbassignid qbassignment instance id
     * @param int $userid user id (empty for current user)
     * @param int $groupid filter by users in group id (used for generating the grading summary). Use 0 for all groups information.
     * @return array of warnings and grading, status, feedback and previous attempts information
     * @since Moodle 3.1
     * @throws required_capability_exception
     */
    public static function get_submission_status($qbassignid, $userid = 0, $groupid = 0) {
        global $USER;

        $warnings = array();

        $params = array(
            'qbassignid' => $qbassignid,
            'userid' => $userid,
            'groupid' => $groupid,
        );
        $params = self::validate_parameters(self::get_submission_status_parameters(), $params);

        list($qbassign, $course, $cm, $context) = self::validate_qbassign($params['qbassignid']);

        // Default value for userid.
        if (empty($params['userid'])) {
            $params['userid'] = $USER->id;
        }
        $user = core_user::get_user($params['userid'], '*', MUST_EXIST);
        core_user::require_active_user($user);

        if (!$qbassign->can_view_submission($user->id)) {
            throw new required_capability_exception($context, 'mod/qbassign:viewgrades', 'nopermission', '');
        }

        $qbassign->update_effective_access($user->id);

        $gradingsummary = $lastattempt = $feedback = $previousattempts = null;

        // Get the renderable since it contais all the info we need.
        if (!empty($params['groupid'])) {
            $groupid = $params['groupid'];
            // Determine is the group is visible to user.
            if (!groups_group_visible($groupid, $course, $cm)) {
                throw new moodle_exception('notingroup');
            }
        } else {
            // A null group means that following functions will calculate the current group.
            // A groupid set to 0 means all groups.
            $groupid = ($params['groupid'] == 0) ? 0 : null;
        }
        if ($qbassign->can_view_grades($groupid)) {
            $gradingsummary = $qbassign->get_qbassign_grading_summary_renderable($groupid);
        }

        // Retrieve the rest of the renderable objects.
        if (has_capability('mod/qbassign:viewownsubmissionsummary', $context, $user, false)) {
            // The user can view the submission summary.
            $lastattempt = $qbassign->get_qbassign_submission_status_renderable($user, true);
        }

        $feedback = $qbassign->get_qbassign_feedback_status_renderable($user);

        $previousattempts = $qbassign->get_qbassign_attempt_history_renderable($user);

        // Now, build the result.
        $result = array();

        // First of all, grading summary, this is suitable for teachers/managers.
        if ($gradingsummary) {
            $result['gradingsummary'] = $gradingsummary;
        }
        // Show the grader's identity if 'Hide Grader' is disabled or has the 'Show Hidden Grader' capability.
        $showgradername = (has_capability('mod/qbassign:showhiddengrader', $context) or
            !$qbassign->is_hidden_grader());

        // Did we submit anything?
        if ($lastattempt) {
            $submissionplugins = $qbassign->get_submission_plugins();

            if (empty($lastattempt->submission)) {
                unset($lastattempt->submission);
            } else {
                $lastattempt->submission->plugins = self::get_plugins_data($qbassign, $submissionplugins, $lastattempt->submission);
            }

            if (empty($lastattempt->teamsubmission)) {
                unset($lastattempt->teamsubmission);
            } else {
                $lastattempt->teamsubmission->plugins = self::get_plugins_data($qbassign, $submissionplugins,
                                                                                $lastattempt->teamsubmission);
            }

            // We need to change the type of some of the structures retrieved from the renderable.
            if (!empty($lastattempt->submissiongroup)) {
                $lastattempt->submissiongroup = $lastattempt->submissiongroup->id;
            } else {
                unset($lastattempt->submissiongroup);
            }

            if (!empty($lastattempt->usergroups)) {
                $lastattempt->usergroups = array_keys($lastattempt->usergroups);
            }
            // We cannot use array_keys here.
            if (!empty($lastattempt->submissiongroupmemberswhoneedtosubmit)) {
                $lastattempt->submissiongroupmemberswhoneedtosubmit = array_map(
                                                                            function($e){
                                                                                return $e->id;
                                                                            },
                                                                            $lastattempt->submissiongroupmemberswhoneedtosubmit);
            }

            // Can edit its own submission?
            $lastattempt->caneditowner = has_capability('mod/qbassign:submit', $context, $user, false)
                && $qbassign->submissions_open($user->id) && $qbassign->is_any_submission_plugin_enabled();

            $result['lastattempt'] = $lastattempt;
        }

        // The feedback for our latest submission.
        if ($feedback) {
            if ($feedback->grade) {
                if (!$showgradername) {
                    $feedback->grade->grader = -1;
                }
                $feedbackplugins = $qbassign->get_feedback_plugins();
                $feedback->plugins = self::get_plugins_data($qbassign, $feedbackplugins, $feedback->grade);
            } else {
                unset($feedback->plugins);
                unset($feedback->grade);
            }

            $result['feedback'] = $feedback;
        }

        // Retrieve only previous attempts.
        if ($previousattempts and count($previousattempts->submissions) > 1) {
            // Don't show the last one because it is the current submission.
            array_pop($previousattempts->submissions);

            // Show newest to oldest.
            $previousattempts->submissions = array_reverse($previousattempts->submissions);

            foreach ($previousattempts->submissions as $i => $submission) {
                $attempt = array();

                $grade = null;
                foreach ($previousattempts->grades as $onegrade) {
                    if ($onegrade->attemptnumber == $submission->attemptnumber) {
                        $grade = $onegrade;
                        break;
                    }
                }

                $attempt['attemptnumber'] = $submission->attemptnumber;

                if ($submission) {
                    $submission->plugins = self::get_plugins_data($qbassign, $previousattempts->submissionplugins, $submission);
                    $attempt['submission'] = $submission;
                }

                if ($grade) {
                    // From object to id.
                    if (!$showgradername) {
                        $grade->grader = -1;
                    } else {
                        $grade->grader = $grade->grader->id;
                    }

                    $feedbackplugins = self::get_plugins_data($qbassign, $previousattempts->feedbackplugins, $grade);

                    $attempt['grade'] = $grade;
                    $attempt['feedbackplugins'] = $feedbackplugins;
                }
                $result['previousattempts'][] = $attempt;
            }
        }

        // Send back some qbassignment data as well.
        $instance = $qbassign->get_instance();
        $qbassignmentdata = [];
        $attachments = [];
        if ($qbassign->should_provide_intro_attachments($user->id)) {
            $attachments['intro'] = external_util::get_area_files($context->id, 'mod_qbassign',
                    qbassign_INTROATTACHMENT_FILEAREA, 0);
        }
        if ($instance->activity && ($lastattempt || $qbassign->submissions_open($user->id, true))) {
            list($qbassignmentdata['activity'], $qbassignmentdata['activityformat']) = external_format_text($instance->activity,
                $instance->activityformat, $context->id, 'mod_qbassign', qbassign_ACTIVITYATTACHMENT_FILEAREA, 0);
            $attachments['activity'] = external_util::get_area_files($context->id, 'mod_qbassign',
                qbassign_ACTIVITYATTACHMENT_FILEAREA, 0);
        }
        if (!empty($attachments)) {
            $qbassignmentdata['attachments'] = $attachments;
        }
        $result['qbassignmentdata'] = $qbassignmentdata;

        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the get_submission_status return value.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function get_submission_status_returns() {
        return new external_single_structure(
            array(
                'gradingsummary' => new external_single_structure(
                    array(
                        'participantcount' => new external_value(PARAM_INT, 'Number of users who can submit.'),
                        'submissiondraftscount' => new external_value(PARAM_INT, 'Number of submissions in draft status.'),
                        'submissiondraftscount' => new external_value(PARAM_INT, 'Number of submissions in draft status.'),
                        'submissionsenabled' => new external_value(PARAM_BOOL, 'Whether submissions are enabled or not.'),
                        'submissionssubmittedcount' => new external_value(PARAM_INT, 'Number of submissions in submitted status.'),
                        'submissionsneedgradingcount' => new external_value(PARAM_INT, 'Number of submissions that need grading.'),
                        'warnofungroupedusers' => new external_value(PARAM_ALPHA, 'Whether we need to warn people that there
                                                                        are users without groups (\'warningrequired\'), warn
                                                                        people there are users who will submit in the default
                                                                        group (\'warningoptional\') or no warning (\'\').'),
                    ), 'Grading information.', VALUE_OPTIONAL
                ),
                'lastattempt' => new external_single_structure(
                    array(
                        'submission' => self::get_submission_structure(VALUE_OPTIONAL),
                        'teamsubmission' => self::get_submission_structure(VALUE_OPTIONAL),
                        'submissiongroup' => new external_value(PARAM_INT, 'The submission group id (for group submissions only).',
                                                                VALUE_OPTIONAL),
                        'submissiongroupmemberswhoneedtosubmit' => new external_multiple_structure(
                            new external_value(PARAM_INT, 'USER id.'),
                            'List of users who still need to submit (for group submissions only).',
                            VALUE_OPTIONAL
                        ),
                        'submissionsenabled' => new external_value(PARAM_BOOL, 'Whether submissions are enabled or not.'),
                        'locked' => new external_value(PARAM_BOOL, 'Whether new submissions are locked.'),
                        'graded' => new external_value(PARAM_BOOL, 'Whether the submission is graded.'),
                        'canedit' => new external_value(PARAM_BOOL, 'Whether the user can edit the current submission.'),
                        'caneditowner' => new external_value(PARAM_BOOL, 'Whether the owner of the submission can edit it.'),
                        'cansubmit' => new external_value(PARAM_BOOL, 'Whether the user can submit.'),
                        'extensionduedate' => new external_value(PARAM_INT, 'Extension due date.'),
                        'timelimit' => new external_value(PARAM_INT, 'Time limit for submission.', VALUE_OPTIONAL),
                        'blindmarking' => new external_value(PARAM_BOOL, 'Whether blind marking is enabled.'),
                        'gradingstatus' => new external_value(PARAM_ALPHANUMEXT, 'Grading status.'),
                        'usergroups' => new external_multiple_structure(
                            new external_value(PARAM_INT, 'Group id.'), 'User groups in the course.'
                        ),
                    ), 'Last attempt information.', VALUE_OPTIONAL
                ),
                'feedback' => new external_single_structure(
                    array(
                        'grade' => self::get_grade_structure(VALUE_OPTIONAL),
                        'gradefordisplay' => new external_value(PARAM_RAW, 'Grade rendered into a format suitable for display.'),
                        'gradeddate' => new external_value(PARAM_INT, 'The date the user was graded.'),
                        'plugins' => new external_multiple_structure(self::get_plugin_structure(), 'Plugins info.', VALUE_OPTIONAL),
                    ), 'Feedback for the last attempt.', VALUE_OPTIONAL
                ),
                'previousattempts' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'attemptnumber' => new external_value(PARAM_INT, 'Attempt number.'),
                            'submission' => self::get_submission_structure(VALUE_OPTIONAL),
                            'grade' => self::get_grade_structure(VALUE_OPTIONAL),
                            'feedbackplugins' => new external_multiple_structure(self::get_plugin_structure(), 'Feedback info.',
                                                                                    VALUE_OPTIONAL),
                        )
                    ), 'List all the previous attempts did by the user.', VALUE_OPTIONAL
                ),
                'qbassignmentdata' => new external_single_structure([
                    'attachments' => new external_single_structure([
                        'intro' => new external_files('Intro attachments files', VALUE_OPTIONAL),
                        'activity' => new external_files('Activity attachments files', VALUE_OPTIONAL),
                    ], 'Intro and activity attachments', VALUE_OPTIONAL),
                    'activity' => new external_value(PARAM_RAW, 'Text of activity', VALUE_OPTIONAL),
                    'activityformat' => new external_format_value('activity', VALUE_OPTIONAL),
                ], 'Extra information about qbassignment', VALUE_OPTIONAL),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.1
     */
    public static function list_participants_parameters() {
        return new external_function_parameters(
            array(
                'qbassignid' => new external_value(PARAM_INT, 'qbassign instance id'),
                'groupid' => new external_value(PARAM_INT, 'group id'),
                'filter' => new external_value(PARAM_RAW, 'search string to filter the results'),
                'skip' => new external_value(PARAM_INT, 'number of records to skip', VALUE_DEFAULT, 0),
                'limit' => new external_value(PARAM_INT, 'maximum number of records to return', VALUE_DEFAULT, 0),
                'onlyids' => new external_value(PARAM_BOOL, 'Do not return all user fields', VALUE_DEFAULT, false),
                'includeenrolments' => new external_value(PARAM_BOOL, 'Do return courses where the user is enrolled',
                                                          VALUE_DEFAULT, true),
                'tablesort' => new external_value(PARAM_BOOL, 'Apply current user table sorting preferences.',
                                                          VALUE_DEFAULT, false)
            )
        );
    }

    /**
     * Retrieves the list of students to be graded for the qbassignment.
     *
     * @param int $qbassignid the qbassign instance id
     * @param int $groupid the current group id
     * @param string $filter search string to filter the results.
     * @param int $skip Number of records to skip
     * @param int $limit Maximum number of records to return
     * @param bool $onlyids Only return user ids.
     * @param bool $includeenrolments Return courses where the user is enrolled.
     * @param bool $tablesort Apply current user table sorting params from the grading table.
     * @return array of warnings and status result
     * @since Moodle 3.1
     * @throws moodle_exception
     */
    public static function list_participants($qbassignid, $groupid, $filter, $skip,
            $limit, $onlyids, $includeenrolments, $tablesort) {
        global $DB, $CFG;
        require_once($CFG->dirroot . "/mod/qbassign/locallib.php");
        require_once($CFG->dirroot . "/user/lib.php");
        require_once($CFG->libdir . '/grouplib.php');

        $params = self::validate_parameters(self::list_participants_parameters(),
                                            array(
                                                'qbassignid' => $qbassignid,
                                                'groupid' => $groupid,
                                                'filter' => $filter,
                                                'skip' => $skip,
                                                'limit' => $limit,
                                                'onlyids' => $onlyids,
                                                'includeenrolments' => $includeenrolments,
                                                'tablesort' => $tablesort
                                            ));
        $warnings = array();

        list($qbassign, $course, $cm, $context) = self::validate_qbassign($params['qbassignid']);

        require_capability('mod/qbassign:view', $context);

        $qbassign->require_view_grades();

        $participants = array();
        $coursegroups = [];
        if (groups_group_visible($params['groupid'], $course, $cm)) {
            $participants = $qbassign->list_participants_with_filter_status_and_group($params['groupid'], $params['tablesort']);
            $coursegroups = groups_get_all_groups($course->id);
        }

        $userfields = user_get_default_fields();
        if (!$params['includeenrolments']) {
            // Remove enrolled courses from users fields to be returned.
            $key = array_search('enrolledcourses', $userfields);
            if ($key !== false) {
                unset($userfields[$key]);
            } else {
                throw new moodle_exception('invaliduserfield', 'error', '', 'enrolledcourses');
            }
        }

        $result = array();
        $index = 0;
        foreach ($participants as $record) {
            // Preserve the fullname set by the qbassignment.
            $fullname = $record->fullname;
            $searchable = $fullname;
            $match = false;
            if (empty($filter)) {
                $match = true;
            } else {
                $filter = core_text::strtolower($filter);
                $value = core_text::strtolower($searchable);
                if (is_string($value) && (core_text::strpos($value, $filter) !== false)) {
                    $match = true;
                }
            }
            if ($match) {
                $index++;
                if ($index <= $params['skip']) {
                    continue;
                }
                if (($params['limit'] > 0) && (($index - $params['skip']) > $params['limit'])) {
                    break;
                }
                // Now we do the expensive lookup of user details because we completed the filtering.
                if (!$qbassign->is_blind_marking() && !$params['onlyids']) {
                    $userdetails = user_get_user_details($record, $course, $userfields);
                } else {
                    $userdetails = array('id' => $record->id);
                }
                $userdetails['fullname'] = $fullname;
                $userdetails['submitted'] = $record->submitted;
                $userdetails['requiregrading'] = $record->requiregrading;
                $userdetails['grantedextension'] = $record->grantedextension;
                $userdetails['submissionstatus'] = $record->submissionstatus;
                if (!empty($record->groupid)) {
                    $userdetails['groupid'] = $record->groupid;

                    if (!empty($coursegroups[$record->groupid])) {
                        // Format properly the group name.
                        $group = $coursegroups[$record->groupid];
                        $userdetails['groupname'] = format_string($group->name);
                    }
                }
                // Unique id is required for blind marking.
                $userdetails['recordid'] = -1;
                if (!empty($record->recordid)) {
                    $userdetails['recordid'] = $record->recordid;
                }

                $result[] = $userdetails;
            }
        }
        return $result;
    }

    /**
     * Returns the description of the results of the mod_qbassign_external::list_participants() method.
     *
     * @return external_description
     * @since Moodle 3.1
     */
    public static function list_participants_returns() {
        // Get user description.
        $userdesc = core_user_external::user_description();
        // List unneeded properties.
        $unneededproperties = [
            'auth', 'confirmed', 'lang', 'calendartype', 'theme', 'timezone', 'mailformat'
        ];
        // Remove unneeded properties for consistency with the previous version.
        foreach ($unneededproperties as $prop) {
            unset($userdesc->keys[$prop]);
        }

        // Override property attributes for consistency with the previous version.
        $userdesc->keys['fullname']->type = PARAM_NOTAGS;
        $userdesc->keys['profileimageurlsmall']->required = VALUE_OPTIONAL;
        $userdesc->keys['profileimageurl']->required = VALUE_OPTIONAL;
        $userdesc->keys['email']->desc = 'Email address';
        $userdesc->keys['idnumber']->desc = 'The idnumber of the user';
        $userdesc->keys['recordid'] = new external_value(PARAM_INT, 'record id');

        // Define other keys.
        $otherkeys = [
            'groups' => new external_multiple_structure(
                new external_single_structure(
                    [
                        'id' => new external_value(PARAM_INT, 'group id'),
                        'name' => new external_value(PARAM_RAW, 'group name'),
                        'description' => new external_value(PARAM_RAW, 'group description'),
                    ]
                ), 'user groups', VALUE_OPTIONAL
            ),
            'roles' => new external_multiple_structure(
                new external_single_structure(
                    [
                        'roleid' => new external_value(PARAM_INT, 'role id'),
                        'name' => new external_value(PARAM_RAW, 'role name'),
                        'shortname' => new external_value(PARAM_ALPHANUMEXT, 'role shortname'),
                        'sortorder' => new external_value(PARAM_INT, 'role sortorder')
                    ]
                ), 'user roles', VALUE_OPTIONAL
            ),
            'enrolledcourses' => new external_multiple_structure(
                new external_single_structure(
                    [
                        'id' => new external_value(PARAM_INT, 'Id of the course'),
                        'fullname' => new external_value(PARAM_RAW, 'Fullname of the course'),
                        'shortname' => new external_value(PARAM_RAW, 'Shortname of the course')
                    ]
                ), 'Courses where the user is enrolled - limited by which courses the user is able to see', VALUE_OPTIONAL
            ),
            'submitted' => new external_value(PARAM_BOOL, 'have they submitted their qbassignment'),
            'requiregrading' => new external_value(PARAM_BOOL, 'is their submission waiting for grading'),
            'grantedextension' => new external_value(PARAM_BOOL, 'have they been granted an extension'),
            'submissionstatus' => new external_value(PARAM_ALPHA, 'The submission status (new, draft, reopened or submitted).
                Empty when not submitted.', VALUE_OPTIONAL),
            'groupid' => new external_value(PARAM_INT, 'for group qbassignments this is the group id', VALUE_OPTIONAL),
            'groupname' => new external_value(PARAM_TEXT, 'for group qbassignments this is the group name', VALUE_OPTIONAL),
        ];

        // Merge keys.
        $userdesc->keys = array_merge($userdesc->keys, $otherkeys);
        return new external_multiple_structure($userdesc);
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.1
     */
    public static function get_participant_parameters() {
        return new external_function_parameters(
            array(
                'qbassignid' => new external_value(PARAM_INT, 'qbassign instance id'),
                'userid' => new external_value(PARAM_INT, 'user id'),
                'embeduser' => new external_value(PARAM_BOOL, 'user id', VALUE_DEFAULT, false),
            )
        );
    }

    /**
     * Get the user participating in the given qbassignment. An error with code 'usernotincourse'
     * is thrown is the user isn't a participant of the given qbassignment.
     *
     * @param int $qbassignid the qbassign instance id
     * @param int $userid the user id
     * @param bool $embeduser return user details (only applicable if not blind marking)
     * @return array of warnings and status result
     * @since Moodle 3.1
     * @throws moodle_exception
     */
    public static function get_participant($qbassignid, $userid, $embeduser) {
        global $DB, $CFG;
        require_once($CFG->dirroot . "/mod/qbassign/locallib.php");
        require_once($CFG->dirroot . "/user/lib.php");
        require_once($CFG->libdir . '/grouplib.php');

        $params = self::validate_parameters(self::get_participant_parameters(), array(
            'qbassignid' => $qbassignid,
            'userid' => $userid,
            'embeduser' => $embeduser
        ));

        list($qbassign, $course, $cm, $context) = self::validate_qbassign($params['qbassignid']);
        $qbassign->require_view_grades();

        $participant = $qbassign->get_participant($params['userid']);

        // Update qbassign with override information.
        $qbassign->update_effective_access($params['userid']);

        if (!$participant) {
            // No participant found so we can return early.
            throw new moodle_exception('usernotincourse');
        }

        $filtered = $qbassign->is_userid_filtered($userid);
        if (!$filtered) {
            // User is filtered out by user filters or table preferences.
            throw new moodle_exception('userisfilteredout');
        }

        $return = array(
            'id' => $participant->id,
            'fullname' => $participant->fullname,
            'submitted' => $participant->submitted,
            'requiregrading' => $participant->requiregrading,
            'grantedextension' => $participant->grantedextension,
            'submissionstatus' => $participant->submissionstatus,
            'blindmarking' => $qbassign->is_blind_marking(),
            'allowsubmissionsfromdate' => $qbassign->get_instance($userid)->allowsubmissionsfromdate,
            'duedate' => $qbassign->get_instance($userid)->duedate,
            'cutoffdate' => $qbassign->get_instance($userid)->cutoffdate,
            'duedatestr' => userdate($qbassign->get_instance($userid)->duedate, get_string('strftimedatetime', 'langconfig')),
        );

        if (!empty($participant->groupid)) {
            $return['groupid'] = $participant->groupid;

            if ($group = groups_get_group($participant->groupid)) {
                // Format properly the group name.
                $return['groupname'] = format_string($group->name);
            }
        }

        // Skip the expensive lookup of user detail if we're blind marking or the caller
        // hasn't asked for user details to be embedded.
        if (!$qbassign->is_blind_marking() && $embeduser) {
            if ($userdetails = user_get_user_details($participant, $course)) {
                $return['user'] = $userdetails;
            }
        }

        return $return;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.1
     */
    public static function get_participant_returns() {
        $userdescription = core_user_external::user_description();
        $userdescription->default = [];
        $userdescription->required = VALUE_OPTIONAL;

        return new external_single_structure(array(
            'id' => new external_value(PARAM_INT, 'ID of the user'),
            'fullname' => new external_value(PARAM_NOTAGS, 'The fullname of the user'),
            'submitted' => new external_value(PARAM_BOOL, 'have they submitted their qbassignment'),
            'requiregrading' => new external_value(PARAM_BOOL, 'is their submission waiting for grading'),
            'grantedextension' => new external_value(PARAM_BOOL, 'have they been granted an extension'),
            'blindmarking' => new external_value(PARAM_BOOL, 'is blind marking enabled for this qbassignment'),
            'allowsubmissionsfromdate' => new external_value(PARAM_INT, 'allowsubmissionsfromdate for the user'),
            'duedate' => new external_value(PARAM_INT, 'duedate for the user'),
            'cutoffdate' => new external_value(PARAM_INT, 'cutoffdate for the user'),
            'duedatestr' => new external_value(PARAM_TEXT, 'duedate for the user'),
            'groupid' => new external_value(PARAM_INT, 'for group qbassignments this is the group id', VALUE_OPTIONAL),
            'groupname' => new external_value(PARAM_TEXT, 'for group qbassignments this is the group name', VALUE_OPTIONAL),
            'submissionstatus' => new external_value(PARAM_ALPHA, 'The submission status (new, draft, reopened or submitted).
                Empty when not submitted.', VALUE_OPTIONAL),
            'user' => $userdescription,
        ));
    }

    /**
     * Describes the parameters for view_qbassign.
     *
     * @return external_function_parameters
     * @since Moodle 3.2
     */
    public static function view_qbassign_parameters() {
        return new external_function_parameters (
            array(
                'qbassignid' => new external_value(PARAM_INT, 'qbassign instance id'),
            )
        );
    }

    /**
     * Update the module completion status.
     *
     * @param int $qbassignid qbassign instance id
     * @return array of warnings and status result
     * @since Moodle 3.2
     */
    public static function view_qbassign($qbassignid) {
        $warnings = array();
        $params = array(
            'qbassignid' => $qbassignid,
        );
        $params = self::validate_parameters(self::view_qbassign_parameters(), $params);

        list($qbassign, $course, $cm, $context) = self::validate_qbassign($params['qbassignid']);

        $qbassign->set_module_viewed();

        $result = array();
        $result['status'] = true;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the view_qbassign return value.
     *
     * @return external_single_structure
     * @since Moodle 3.2
     */
    public static function view_qbassign_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'warnings' => new external_warnings(),
            )
        );
    }

   /**
     * List the Assignment module details.
     *
     * @param int $uniquefield qbassign unique field
     * @return array of warnings and status result
     * @since Moodle 3.2
     */

     public static function get_assignment_service_parameters()
     {
         return new external_function_parameters(
             array(
             'uniquefield' => new external_value(PARAM_TEXT, 'Unique Field')
          )
         );
     }
 
     public static function get_assignment_service($uniquefield)
     {        
         require_once('../../config.php');
         global $DB,$CFG,$USER,$CONTEXT;
        
         //Get activity unique field details       
         $get_assignmentdetails = $DB->get_record('qbassign', array('uid' => $uniquefield));
 
         if($get_assignmentdetails->id!='')
         { 
             $assignid = $get_assignmentdetails->id;
             $courseid = $get_assignmentdetails->course;
 
             //Get activity Module details
             $get_coursefield = $DB->get_record('course_modules', array('instance' => $assignid,'course' => $courseid));
             $moduleid = $get_coursefield->id;
 
             //Get assignment submission details
             $get_assignmentsubmission_details = $DB->get_record('qbassign_submission', array('userid' => $USER->id,'qbassignment'=>$get_assignmentdetails->id));
 
             $getonline_content = $DB->get_record('qbassignsubmission_onlinetex', array('submission' => $get_assignmentsubmission_details->id,'qbassignment'=>$get_assignmentdetails->id));
 
             //Get submission type details (file,onlinetex,codeblock)
             $sql = "SELECT * FROM {qbassign_plugin_config} WHERE qbassignment = :qbdetid AND subtype = :subtype ";
             $sql .= " AND name = :name AND value = :value ";
             $sql .= " AND (plugin = :type1 OR plugin = :type2 OR plugin = :type3)";
             $getpluginconfig = $DB->get_records_sql($sql,
             [
                 'qbdetid' => $get_assignmentdetails->id,
                 'subtype' => 'qbassignsubmission',
                 'name' => 'enabled',
                 'value' => '1',
                 'type1' => 'file',
                 'type2' => 'onlinetex',
                 'type3' => 'codeblock'
             ]
         );
             $countsql = count($getpluginconfig);
             if($countsql>0)
             { 
                 foreach($getpluginconfig as $config)
                 {
                     if($config->plugin=='onlinetex')
                     { 
                        $get_qbdetails = $DB->get_record('qbassign_plugin_config', array('qbassignment' => $get_assignmentdetails->id,'name' => 'wordlimit','plugin'=>'onlinetex'));
 
                        $submissintype = array(
                         'type'=> $config->plugin,
                         'wordlimit' => ($config->plugin=='onlinetex')?$get_qbdetails->value:''                    
                         ); 
                     }
                     if($config->plugin=='file')
                     {
                         $get_fbdetails = $DB->get_record('qbassign_plugin_config', array('qbassignment' => $get_assignmentdetails->id,'name' => 'maxfilesubmissions','plugin'=>'file'));
 
                         $get_fmbdetails = $DB->get_record('qbassign_plugin_config', array('qbassignment' => $get_assignmentdetails->id,'name' => 'maxsubmissionsizebytes','plugin'=>'file'));
 
                            $submissintype = array(
                             'type'=> $config->plugin,
                             'maxfileallowed' => ($config->plugin=='file')?$get_fbdetails->value:'',
                             'maxfilesize' => ($config->plugin=='file')?$get_fmbdetails->value:''                    
                             ); 
                     }
                 }
             }
             $context = context_course::instance($get_assignmentdetails->course);
             $roles = get_user_roles($context, $USER->id, true);
             $role = key($roles);
             $rolename = $roles[$role]->shortname;
 
             
             $userdetails = array(
                 'userid' => $USER->id,
                 'email' => $USER->email,
                 'username' => $USER->username,
                 'sesskey' => $USER->sesskey,
                 'role' => $rolename
             );
             $returnarray = array(
                 'course_id' => $get_assignmentdetails->course,            
                 'assignmentid' => $get_assignmentdetails->id,
                 'assignment_title' => $get_assignmentdetails->name,
                 'assignment_activitydesc' => $get_assignmentdetails->intro,
                 'duedate' => $get_assignmentdetails->duedate,
                 'allowsubmissionsfromdate' => $get_assignmentdetails->allowsubmissionsfromdate,
                 'assign_uniquefield' => $uniquefield,
                 'last_submitted_date' => $get_assignmentsubmission_details->timemodified,
                 'submission_id' => $get_assignmentsubmission_details->id,
                 'submission_status' => ($get_assignmentsubmission_details->status=='new')?0:1,
                 'studentsubmitted_content' => $getonline_content->onlinetex,
                 'submissiontypes' => $submissintype
             );
 
             $contextsystem = context_module::instance($moduleid);
             $checkenrol = is_enrolled($contextsystem, $USER, 'mod/assignment:submit');
             if($checkenrol)
             { 
                 $assign_updated = [                        
                         'message'=>'Assignment details',
                         'userdetails' => $userdetails,
                         'assignmentdetails' => $returnarray
                         ]; 
                 return $assign_updated;                      
             }            
             else
             { 
                 throw new moodle_exception('This user not enrolled', 'error');
             }
             
         }
         else
         { 
             throw new moodle_exception('Invalid assignment uniqueid', 'error');
         }
         
     }
 
     public static function get_assignment_service_returns()
     {
         return new external_single_structure(
                         array(
                         'message' => new external_value(PARAM_RAW, 'success'),
                         'userdetails' => new external_single_structure(
                                     array(
                                     'userid' => new external_value(PARAM_RAW, 'USER id',VALUE_OPTIONAL),
                                     'email' => new external_value(PARAM_RAW, 'User Email',VALUE_OPTIONAL),
                                     'username' => new external_value(PARAM_RAW, 'Username',VALUE_OPTIONAL),
                                     'sesskey' => new external_value(PARAM_RAW, 'Session Key',VALUE_OPTIONAL),
                                     'role' => new external_value(PARAM_RAW, 'User Role',VALUE_OPTIONAL)
                                     )
                                 ),
                                 'User Details', VALUE_OPTIONAL,
                         'assignmentdetails' => new external_single_structure(
                                     array(
                                     'course_id' => new external_value(PARAM_RAW, 'course id',VALUE_OPTIONAL),
                                     'assignmentid' => new external_value(PARAM_RAW, 'Assignment ID',VALUE_OPTIONAL),
                                     'assignment_title' => new external_value(PARAM_RAW, 'Assignment Name',VALUE_OPTIONAL),
                                     'assignment_activitydesc' => new external_value(PARAM_RAW, 'Assignment Question',VALUE_OPTIONAL),
                                     'duedate' => new external_value(PARAM_RAW, 'Last date',VALUE_OPTIONAL),
                                     'allowsubmissionsfromdate' => new external_value(PARAM_RAW, 'Start Submission date',VALUE_OPTIONAL),
                                     'assign_uniquefield' => new external_value(PARAM_RAW, 'Unique field',VALUE_OPTIONAL),
                                     'last_submitted_date' => new external_value(PARAM_RAW, 'Last Submitted date',VALUE_OPTIONAL),
                                     'submission_id' => new external_value(PARAM_INT, 'Submission ID',VALUE_OPTIONAL),
                                     'submission_status' => new external_value(PARAM_RAW, 'Submission Status (New,submitted)',VALUE_OPTIONAL),
                                     'studentsubmitted_content' => new external_value(PARAM_RAW, 'Submission Content',VALUE_OPTIONAL),
                                     'submissiontypes' => new external_single_structure(
                                         array(
                                          'type' => new external_value(PARAM_RAW, 'Submission Type (text,file,codblock)',VALUE_OPTIONAL),
                                          'wordlimit' =>new external_value(PARAM_RAW, 'Text Limit',VALUE_OPTIONAL)
                                         )
                                     ),
                                     'Submission Type Details', VALUE_OPTIONAL
                                     )
                                 ),
                                 'Assignment Details', VALUE_OPTIONAL
                         )
                 );
 
  
        
     }
 
     /**
      * Create Assignment module details.
      *
      * @param int $courseid qbassign Course id
      * @param text $uniquefield qbassign Unique field
      * @return array of warnings and status result
      * @since Moodle 3.2
      */
     public static function create_assignment_service_parameters()
     {
         return new external_function_parameters(
             array(
             'courseid' => new external_value(PARAM_INT, 'Course Id',VALUE_REQUIRED),
             'siteid' => new external_value(PARAM_INT, 'Site Id'),
             'chapterid' => new external_value(PARAM_INT, 'Section Id',VALUE_REQUIRED),
             'title' => new external_value(PARAM_TEXT, 'Assignment Name',VALUE_REQUIRED),
             'duedate' => new external_value(PARAM_TEXT, 'Due date',VALUE_OPTIONAL),
             'submissionfrom' => new external_value(PARAM_TEXT, 'Submission From',VALUE_OPTIONAL),
             'grade_duedate' => new external_value(PARAM_TEXT, 'Grade Due Date',VALUE_OPTIONAL),
             'grade' => new external_value(PARAM_TEXT, 'Grade',VALUE_REQUIRED),
             'question' => new external_value(PARAM_RAW, 'Description',VALUE_REQUIRED),
             'submission_type' => new external_value(PARAM_TEXT, 'Submission Type',VALUE_REQUIRED),
             'submissionstatus' => new external_value(PARAM_TEXT, 'Submission Status',VALUE_OPTIONAL),
             'online_text_limit' => new external_value(PARAM_TEXT, 'Word Limit',VALUE_OPTIONAL),            
             'uid' => new external_value(PARAM_TEXT, 'Unique Field',VALUE_REQUIRED),
             'maxfilesubmissions' => new external_value(PARAM_TEXT, 'max file submissions',VALUE_OPTIONAL),
             'filetypeslist' => new external_value(PARAM_TEXT, 'file Type',VALUE_OPTIONAL),
             'maxfilesubmissions_size' => new external_value(PARAM_TEXT, 'max file submissions Size',VALUE_OPTIONAL),
             )
         );
     }
 
    public static function create_assignment_service($courseid,$siteid,$chapterid,$title,$duedate,$submissionfrom,$grade_duedate,$grade,$question,$submission_type,$submissionstatus,$online_text_limit,$uid,$maxfilesubmissions,$filetypeslist,$maxfilesubmissions_size)
     { 
         global $DB,$CFG;
         $check_uniquefield = $DB->get_record('qbassign', array('uid' => $uid));
         if($check_uniquefield->id!='')
         {  
             //Update assignment details if unique field already present
             $check_coursemodulefield = $DB->get_record('course_modules', array('instance' => $check_uniquefield->id,'course'=>$courseid));
 
             //PASS our web service values to the lib file
             $formdata = (object) array(
             'name' => $title,
             'timemodified' => time(),
             'duedate' => strtotime($duedate),
             'course' => $courseid,
             'introformat'=>'1',
             'intro' => $question,
             'coursemodule' => $check_coursemodulefield->id,
             'submissiondrafts' =>0,
             'requiresubmissionstatement' =>0,
             'sendnotifications' => 0,
             'sendlatenotifications' =>0,
             'cutoffdate' => 0,
             'gradingduedate' => strtotime($grade_duedate),
             'allowsubmissionsfromdate' => strtotime($submissionfrom),
             'grade' =>$grade,
             'teamsubmission' =>0,
             'requireallteammemberssubmit' =>0,
             'blindmarking' => 0,
             'markingworkflow' =>0,
             'instance' => $check_uniquefield->id,
             'add' => 0,
             'update' => $check_coursemodulefield->id
             );
             $returnid = qbassign_update_instance($formdata,null);
 
             $wrdlmit = ($online_text_limit!='')?$online_text_limit:'0';
 
             if($submission_type == 'onlinetext')
             {
                 $sqlupdate = "UPDATE ".$CFG->prefix."qbassign_plugin_config SET value=1 WHERE plugin='onlinetex' AND subtype='qbassignsubmission' AND name='enabled' AND qbassignment=".$check_uniquefield->id;
                 $getpluginconfigtxt = $DB->execute($sqlupdate);
                 $submission_status = ($submissionstatus=='yes')?1:0;
 
                 $getwrd = $DB->get_record('qbassign_plugin_config', array('plugin' => 'onlinetex','subtype' => 'qbassignsubmission','name'=>'wordlimitenabled','qbassignment'=>$check_uniquefield->id));
                 if(isset($getwrd->id))
                 {
                     $sqlwrdupdate = "UPDATE ".$CFG->prefix."qbassign_plugin_config SET value=".$submission_status." WHERE plugin='onlinetex' AND subtype='qbassignsubmission' AND name='wordlimitenabled' AND qbassignment=".$check_uniquefield->id;
                     $updatewrd = $DB->execute($sqlwrdupdate);
                 }
                 else
                 {
                     $insertwrd =  array(
                     'qbassignment' => $check_uniquefield->id,
                     'plugin' => 'onlinetex',
                     'subtype' => 'qbassignsubmission',
                     'name' => 'wordlimitenabled',
                     'value' => $submission_status
                     );
                     $onlinetext_default = $DB->insert_record('qbassign_plugin_config', $insertwrd);
                 }
                 $getwrdlmt = $DB->get_record('qbassign_plugin_config', array('plugin' => 'onlinetex','subtype' => 'qbassignsubmission','name'=>'wordlimit','qbassignment'=>$check_uniquefield->id));
                 if(isset($getwrdlmt->id))
                 {
                     $sqlwrdmlmtupdate = "UPDATE ".$CFG->prefix."qbassign_plugin_config SET value=".$wrdlmit." WHERE plugin='onlinetex' AND subtype='qbassignsubmission' AND name='wordlimit' AND qbassignment=".$check_uniquefield->id;
                     $updatewrdlmt = $DB->execute($sqlwrdmlmtupdate);
                 }
                 else
                 {
                     $insertwrdlmt =  array(
                     'qbassignment' => $check_uniquefield->id,
                     'plugin' => 'onlinetex',
                     'subtype' => 'qbassignsubmission',
                     'name' => 'wordlimit',
                     'value' => $wrdlmit
                     );
                     $onlinetext_lmtdefault = $DB->insert_record('qbassign_plugin_config', $insertwrdlmt);
                 }
                                                           
             }   
             if($submission_type == 'onlinefile') 
             {
                 $submission_filestatus = ($submissionstatus=='yes')?1:0;
                 $getactive_online = $DB->get_record('qbassign_plugin_config', array('plugin' => 'file','subtype' => 'qbassignsubmission','name'=>'enabled','qbassignment'=>$check_uniquefield->id));
 
                 if(isset($getactive_online->id))
                 {                
                    $updateactivityonline = new stdClass();
                    $updateactivityonline->id = $getactive_online->id;
                    $updateactivityonline->value = $submission_filestatus;           
                    $onlinetext_default = $DB->update_record('qbassign_plugin_config', $updateactivityonline);
                 }
                 else
                 { 
                     $updateactivityonline =  array(
                     'qbassignment' => $returnid,
                     'plugin' => 'file',
                     'subtype' => 'qbassignsubmission',
                     'name' => 'enabled',
                     'value' => $submission_filestatus
                     );
                     $onlinetext_default = $DB->insert_record('qbassign_plugin_config', $updateactivityonline);
                 }
                 $getfilesub = $DB->get_record('qbassign_plugin_config', array('plugin' => 'file','subtype' => 'qbassignsubmission','name'=>'maxfilesubmissions','qbassignment'=>$check_uniquefield->id));
                 if(isset($getfilesub->id))
                 {
                     $DB->set_field('qbassign_plugin_config', 'value', $maxfilesubmissions, array('plugin'=>'file','subtype' => 'qbassignsubmission','name' => 'maxfilesubmissions','qbassignment' => $check_uniquefield->id));
                 }
                 else
                 {
                     $submissionfilelimit =  array(
                     'qbassignment' => $check_uniquefield->id,
                     'plugin' => 'file',
                     'subtype' => 'qbassignsubmission',
                     'name' => 'maxfilesubmissions',
                     'value' => $maxfilesubmissions
                     );
                     $onlinetext_flimit = $DB->insert_record('qbassign_plugin_config', $submissionfilelimit);
                 }
 
                 $getfiletype = $DB->get_record('qbassign_plugin_config', array('plugin' => 'file','subtype' => 'qbassignsubmission','name'=>'filetypeslist','qbassignment'=>$check_uniquefield->id));
                 if(isset($getfiletype->id))
                 {
                     $DB->set_field('qbassign_plugin_config', 'value', $filetypeslist, array('plugin'=>'file','subtype' => 'qbassignsubmission','name' => 'filetypeslist','qbassignment' => $check_uniquefield->id));
                 }
                 else
                 {
                     $submissionfiletype =  array(
                     'qbassignment' => $check_uniquefield->id,
                     'plugin' => 'file',
                     'subtype' => 'qbassignsubmission',
                     'name' => 'filetypeslist',
                     'value' => $filetypeslist
                     );
                     $onlinetext_tyflimit = $DB->insert_record('qbassign_plugin_config', $submissionfiletype);
                 }
 
                 $returnbytes = self::getbytevalue($maxfilesubmissions_size);
                 $getfilesize = $DB->get_record('qbassign_plugin_config', array('plugin' => 'file','subtype' => 'qbassignsubmission','name'=>'maxsubmissionsizebytes','qbassignment'=>$check_uniquefield->id));
                 if(isset($getfilesize->id))
                 {
                     $DB->set_field('qbassign_plugin_config', 'value', $returnbytes, array('plugin'=>'file','subtype' => 'qbassignsubmission','name' => 'maxsubmissionsizebytes','qbassignment' => $check_uniquefield->id));
                 }
                 else
                 {
                     $submissionfilebytetype =  array(
                     'qbassignment' => $check_uniquefield->id,
                     'plugin' => 'file',
                     'subtype' => 'qbassignsubmission',
                     'name' => 'maxsubmissionsizebytes',
                     'value' => $returnbytes
                     );
                     $onlinetext_tybyflimit = $DB->insert_record('qbassign_plugin_config', $submissionfilebytetype);
                 }
             } 
             if($submission_type == 'codeblock') 
             {
                 //CODE BLOCK
                 $submission_codestatus = ($submissioncodestatus=='yes')?1:0;
                 $getactive_online = $DB->get_record('qbassign_plugin_config', array('plugin' => 'codeblock','subtype' => 'qbassignsubmission','name'=>'enabled','qbassignment'=>$check_uniquefield->id));
                 if(isset($getactive_online))
                 {
                    $updateactivityonline = new stdClass();
                    $updateactivityonline->id = $getactive_online->id;
                    $updateactivityonline->value = $submission_codestatus;           
                    $onlinetext_default = $DB->update_record('qbassign_plugin_config', $updateactivityonline);
                 }
                 else
                 {
                     $updateactivityonline =  array(
                     'qbassignment' => $check_uniquefield->id,
                     'plugin' => 'codeblock',
                     'subtype' => 'qbassignsubmission',
                     'name' => 'enabled',
                     'value' => $submission_codestatus
                     );
                     $onlinetext_default = $DB->insert_record('qbassign_plugin_config', $updatesactivityonline);
                 }           
             } 
             
             $assign_updated = [                        
                         'message'=>'Successfuly updated assignment',
                         'assignment_id' =>$check_uniquefield->id,
                         'uniquefield' => $uid
                         ];
             return $assign_updated;
         }        
         else
         { 
             //Add assignment details if unique field not present
             $getcoursemoduleslist_courses = get_coursemodules_in_course('qbassign', $courseid, '');
             $getcoursemoduleslist_courses_last = end($getcoursemoduleslist_courses);
             $sections = $DB->get_record('course_sections', array('course'=>$courseid,'section' => $chapterid));
             $section_id = $sections->id;
             $sequence_column = $sections->sequence;
             
             $sequencing = array();
             $coursemodule = $getcoursemoduleslist_courses_last->id +1;
 
             //Get QBassign Module
             $get_modulelist = $DB->get_record('modules', array('name' => 'qbassign'));
 
             //INSERT instance into course modules
             $flags = array(
             'course' => $courseid,
             'module' => $get_modulelist->id,
             'instance' => '',
             'section' => $section_id,
             'added' => time()
             );       
             $course_insert_id = $DB->insert_record('course_modules', $flags);
             $updatedata = new stdClass();
             $updatedata->id = $section_id;
             if($sequence_column=='')
             {
                $updatedata->sequence = $course_insert_id;        
             }
             else
             {
                 $sequencing = explode(",",$sequence_column);
                 array_push($sequencing,$course_insert_id);
                 $updatedata->sequence = implode(',', $sequencing);
             }
             
             $updatedata->section = $chapterid;        
             $coursesectionupdate = $DB->update_record('course_sections', $updatedata); 
             $getcoursecontext = $DB->get_record('context', array('instanceid' => $courseid,'depth'=> 3,'contextlevel'=>50));
             $coursepath = $getcoursecontext->path;
             $recorder =  array(
                 'contextlevel' => 70, //CONTEXT_MODULE = 70,CONTEXT_SYSTEM = 10,CONTEXT_BLOCK = 80,COURSE = 50
                 'instanceid' => $course_insert_id,
                 'path' => $coursepath.'/',
                 'depth' => 4,
                 'locked' => 0
             );
             $coursecontextinsertid = $DB->insert_record('context', $recorder);
 
             $getcoursecontextpath = $DB->get_record('context', array('id' => $coursecontextinsertid,'depth' => 4));
 
             $updatecontextdata = new stdClass();
             $updatecontextdata->id = $coursecontextinsertid;
             $updatecontextdata->path = $getcoursecontextpath->path.$coursecontextinsertid; 
             $coursesectionupdate = $DB->update_record('context', $updatecontextdata);
             
             $gradeareas = array(
                 'contextid' =>$coursecontextinsertid,
                 'component' =>'mod_qbassign',
                 'areaname' =>'submissions'
             );
             $grading_areasupdate = $DB->insert_record('grading_areas', $gradeareas);
             //PASS our web service values to the lib file
             $formdata = (object) array(
                 'name' => $title,
                 'timemodified' => time(),
                 'duedate' => strtotime($duedate),
                 'course' => $courseid,
                 'introformat'=>'1',
                 'intro' => $question,
                 'coursemodule' => $course_insert_id,
                 'submissiondrafts' =>0,
                 'requiresubmissionstatement' =>0,
                 'sendnotifications' => 0,
                 'sendlatenotifications' =>0,
                 'cutoffdate' => 0,
                 'gradingduedate' => strtotime($grade_duedate),
                 'allowsubmissionsfromdate' => strtotime($submissionfrom),
                 'grade' =>$grade,
                 'teamsubmission' =>0,
                 'requireallteammemberssubmit' =>0,
                 'blindmarking' => 0,
                 'markingworkflow' =>0,
             );
             $returnid = qbassign_add_instance($formdata,null);
 
             //update assignment id into course modules
             $updatecoursemoduledata = new stdClass();
             $updatecoursemoduledata->id = $course_insert_id;
             $updatecoursemoduledata->instance = $returnid;
             $coursesectionupdate = $DB->update_record('course_modules', $updatecoursemoduledata);
 
             $wrdlmit = (isset($online_text_limit))?$online_text_limit:'';
 
             if($submission_type == 'onlinetext')
             {
                $sqlupdate = "UPDATE ".$CFG->prefix."qbassign_plugin_config SET value=1 WHERE plugin='onlinetex' AND subtype='qbassignsubmission' AND name='enabled' AND qbassignment=".$returnid;
                 $getpluginconfigtxt = $DB->execute($sqlupdate);
                 $submission_status = ($submissionstatus=='yes')?1:0;
                 $submissionlimit =  array(
                 'qbassignment' => $returnid,
                 'plugin' => 'onlinetex',
                 'subtype' => 'qbassignsubmission',
                 'name' => 'wordlimit',
                 'value' => $wrdlmit
                 );
                 $onlinetext_limit = $DB->insert_record('qbassign_plugin_config', $submissionlimit);
                 $submissionlimits =  array(
                 'qbassignment' => $returnid,
                 'plugin' => 'onlinetex',
                 'subtype' => 'qbassignsubmission',
                 'name' => 'wordlimitenabled',
                 'value' => $submission_status
                 );
                 $onlinetext_limiter = $DB->insert_record('qbassign_plugin_config', $submissionlimits);                                   
             }   
             if($submission_type == 'onlinefile') 
             {               
                 $submission_filestatus = ($submissionstatus=='yes')?1:0;
                 $getactive_online = $DB->get_record('qbassign_plugin_config', array('plugin' => 'file','subtype' => 'qbassignsubmission','name'=>'enabled','qbassignment'=>$returnid));
 
                 if(isset($getactive_online->id))
                 {                
                    $updateactivityonline = new stdClass();
                    $updateactivityonline->id = $getactive_online->id;
                    $updateactivityonline->value = $submission_filestatus;           
                    $onlinetext_default = $DB->update_record('qbassign_plugin_config', $updateactivityonline);
                 }
                 else
                 { 
                     $updateactivityonline =  array(
                     'qbassignment' => $returnid,
                     'plugin' => 'file',
                     'subtype' => 'qbassignsubmission',
                     'name' => 'enabled',
                     'value' => $submission_filestatus
                     );
                     $onlinetext_default = $DB->insert_record('qbassign_plugin_config', $updateactivityonline);
                 }
                
                 $submissionfilelimit =  array(
                 'qbassignment' => $returnid,
                 'plugin' => 'file',
                 'subtype' => 'qbassignsubmission',
                 'name' => 'maxfilesubmissions',
                 'value' => $maxfilesubmissions
                 );
                 $onlinetext_flimit = $DB->insert_record('qbassign_plugin_config', $submissionfilelimit);
 
                 $submissionfiletype =  array(
                 'qbassignment' => $returnid,
                 'plugin' => 'file',
                 'subtype' => 'qbassignsubmission',
                 'name' => 'filetypeslist',
                 'value' => $filetypeslist
                 );
                 $onlinetext_tyflimit = $DB->insert_record('qbassign_plugin_config', $submissionfiletype);
 
                 $returnbytes = self::getbytevalue($maxfilesubmissions_size);
 
                 $submissionfilebytetype =  array(
                 'qbassignment' => $returnid,
                 'plugin' => 'file',
                 'subtype' => 'qbassignsubmission',
                 'name' => 'maxsubmissionsizebytes',
                 'value' => $returnbytes
                 );
                 $onlinetext_tybyflimit = $DB->insert_record('qbassign_plugin_config', $submissionfilebytetype);
             } 
             if($submission_type == 'codeblock') 
             {
                 //CODE BLOCK
                 $submission_codestatus = ($submissioncodestatus=='yes')?1:0;
                 $getactive_online = $DB->get_record('qbassign_plugin_config', array('plugin' => 'codeblock','subtype' => 'qbassignsubmission','name'=>'enabled','qbassignment'=>$returnid));
                 if(isset($getactive_online))
                 {
                    $updateactivityonline = new stdClass();
                    $updateactivityonline->id = $getactive_online->id;
                    $updateactivityonline->value = $submission_codestatus;           
                    $onlinetext_default = $DB->update_record('qbassign_plugin_config', $updateactivityonline);
                 }
                 else
                 {
                     $updateactivityonline =  array(
                     'qbassignment' => $returnid,
                     'plugin' => 'codeblock',
                     'subtype' => 'qbassignsubmission',
                     'name' => 'enabled',
                     'value' => $submission_codestatus
                     );
                     $onlinetext_default = $DB->insert_record('qbassign_plugin_config', $updateactivityonline);
                 }           
             }
            
             $DB->set_field('qbassign', 'uid', $uid, array('id' => $returnid));
             $assign_updated = [                        
                             'message'=>'Successfully created assignment',
                             'assignment_id' =>$returnid,
                             'uniquefield' => $uid,
                             'cm_id' => $course_insert_id
                             ];
             return $assign_updated;
         }
     }
 
     public static function create_assignment_service_returns()
     {
         return new external_single_structure(
                 array(
                     'assignment_id' => new external_value(PARAM_TEXT, 'assignment id'),
                     'message'=> new external_value(PARAM_TEXT, 'success message'),
                     'uniquefield'=> new external_value(PARAM_TEXT, 'Unique Field'),
                     'cm_id' => new external_value(PARAM_INT, 'Course Module ID')
                 )
             );
     }
 
     public static function getbytevalue($val)
     {
         $bytearray = array('41943040'=>'40mb','20mb'=>'20971520','10485760'=>'10mb','5242880'=>'5mb','2097152'=>'2mb','1048576'=>'1mb','512000'=>'500kb','102400'=>'100kb','51200'=>'50kb','10240'=>'10kb');
         $byteval = array_search($val,$bytearray);
         return $byteval;
     }
 
     public static function getnameofmodule($name)
     {
         global $DB;
         $moduleName = $DB->get_record('modules', array('name'=>$name,'visible'=>1));
         return $moduleName;
     }
 
     /**
      * Save Assignment Submission module details.
      *
      * @param int $qbassignmentid qbassign id
      * @param text $plugindata_text qbassign Submission text 
      * @return array of warnings and status result
      * @since Moodle 3.2
      */
     public static function save_studentsubmission_parameters()
     {
         return new external_function_parameters(
             array(
             'qbassignmentid' => new external_value(PARAM_INT, 'Assignment Id',VALUE_REQUIRED),
             'plugindata_text' => new external_value(PARAM_RAW, 'Submission Text',VALUE_REQUIRED),
             'plugindata_format' => new external_value(PARAM_TEXT, 'Submission Format',VALUE_REQUIRED),
             'plugindata_type' => new external_value(PARAM_TEXT, 'Submission Type',VALUE_REQUIRED)
             )
         );
     }
 
     public static function save_studentsubmission($qbassignmentid,$plugindata_text,$plugindata_format,$plugindata_type)
     {
         require_once('../../config.php');
         global $DB,$CFG,$USER,$CONTEXT;
         $currentuser = $USER->id;
         if(empty($currentuser))
         {
             throw new moodle_exception('Required Login', 'error');
         }
         $assignid = $DB->get_record('qbassign', array('id' => $qbassignmentid));
         if(!empty($assignid))
         {
             //Get activity Module details
             $get_coursefield = $DB->get_record('course_modules', array('instance' => $qbassignmentid,'course' => $assignid->course));
             $moduleid = $get_coursefield->id;
 
             $contextsystem = context_module::instance($moduleid);
 
             $enrolledcandidates = get_enrolled_users($contextsystem, 'mod/assign:submit');
             $enrolstudents = array();
             foreach($enrolledcandidates as $enrol)
             {
                 $enrolstudents[] = $enrol->id;
             }
             if(!in_array($currentuser, $enrolstudents))
             {
                 throw new moodle_exception('student not enrolled', 'error');
             }
             else
             {
                 foreach($enrolledcandidates as $enrol)
                 {
                     if($currentuser==$enrol->id)
                     {                        
                         $get_currentuser_submission = $DB->get_record('qbassign_user_mapping', array('qbassignment' => $qbassignmentid,'userid' => $currentuser));
                         if($get_currentuser_submission->id!='')
                         { 
                             //do nothing
                             $DB->set_field('qbassign_user_mapping', 'userid', $currentuser, array('id' => $get_currentuser_submission->id,'qbassignment'=>$qbassignmentid));
                         }
                         else
                         {
                             $add_submissionlvl =  array(
                             'qbassignment' => $qbassignmentid,
                             'userid' => $enrol->id
                             );
                             $insertid = $DB->insert_record('qbassign_user_mapping', $add_submissionlvl);
                         }                    
                     }                    
                 }
             }
             $check_submission = $DB->get_record('qbassign_submission', array('qbassignment' => $qbassignmentid,'userid'=>$USER->id));
             if($check_submission->id=='')
             {                
                 $add_submission =  array(
                 'qbassignment' => $qbassignmentid,
                 'userid' => $USER->id,
                 'timecreated' => time(),
                 'timemodified' => time(),
                 'status' => 'submitted',
                 'latest' => 1
                 );
                 $insertid = $DB->insert_record('qbassign_submission', $add_submission);
                 $submissionID = $insertid;
                 if($plugindata_type =='onlinetext')
                 {
                     $add_textsubmission =  array(
                     'qbassignment' => $qbassignmentid,
                     'submission' => $insertid,
                     'onlinetex' => $plugindata_text,
                     'onlineformat' => $plugindata_format
                     );
                     $sub_insertid = $DB->insert_record('qbassignsubmission_onlinetex', $add_textsubmission);
                 }
             }
             else
             { 
                 $check_submissiontxt = $DB->get_record('qbassignsubmission_onlinetex', array('qbassignment' => $qbassignmentid,'submission'=>$check_submission->id)); 
                 if($check_submissiontxt->id=='') // remove submission from students
                 { 
                     $add_textsubmission =  array(
                     'qbassignment' => $qbassignmentid,
                     'submission' => $check_submission->id,
                     'onlinetex' => $plugindata_text,
                     'onlineformat' => $plugindata_format
                     );
                     $sub_insertid = $DB->insert_record('qbassignsubmission_onlinetex', $add_textsubmission);
 
                     $DB->set_field('qbassign_submission', 'status', 'submitted', array('userid' => $USER->id,'qbassignment'=>$qbassignmentid));
                     $DB->set_field('qbassign_submission', 'timemodified', time(), array('userid' => $USER->id,'qbassignment'=>$qbassignmentid));
                     $submissionID = $check_submission->id;
                 }   
                 else
                 { 
                     $DB->set_field('qbassign_submission', 'timemodified', time(), array('userid' => $USER->id,'qbassignment'=>$qbassignmentid));
                     $get_submission = $DB->get_record('qbassign_submission', array('qbassignment' => $qbassignmentid,'userid'=>$USER->id));
                    
                     $DB->set_field('qbassignsubmission_onlinetex', 'onlinetex', $plugindata_text, array('submission' => $get_submission->id,'qbassignment'=>$qbassignmentid));
 
                     $DB->set_field('qbassign_submission', 'status', 'submitted', array('userid' => $USER->id,'qbassignment'=>$qbassignmentid));
                     $submissionID = $check_submission->id;
                 } 
             }
             $save_updated = ['message'=>'sucess','submissionid'=>$submissionID]; 
             return $save_updated; 
         }
         else
         {
             throw new moodle_exception('Assignment ID Wrong', 'error');
         }
 
     }
 
     public static function save_studentsubmission_returns()
     {
         return new external_single_structure(
                 array(
                     'message'=> new external_value(PARAM_TEXT, 'success message'),
                     'submissionid'=> new external_value(PARAM_INT, 'Submission ID')
                 )
             );
     }
 
     /**
      * Create Quiz module details.
      *
      * @param int $courseid quiz Course id
      * @param text $uniquefield quiz Unique field
      * @return array of warnings and status result
      * @since Moodle 3.2
      */
 
     public static function quiz_addition_parameters()
     {
         return new external_function_parameters(
             array(
             'courseid' => new external_value(PARAM_INT, 'Course Id',VALUE_REQUIRED),
             'siteid' => new external_value(PARAM_INT, 'Site Id'),
             'chapterid' => new external_value(PARAM_INT, 'Section Id',VALUE_REQUIRED),
             'quiz_name' => new external_value(PARAM_TEXT, 'Assignment Name',VALUE_REQUIRED),
             'uniquefield' => new external_value(PARAM_TEXT, 'Unique Field',VALUE_REQUIRED),
             'description' => new external_value(PARAM_RAW, 'Description',VALUE_OPTIONAL),
             'questions' => new external_value(PARAM_TEXT, 'Questions',VALUE_OPTIONAL)
             )
         );
     }
 
     public static function quiz_addition($courseid,$siteid,$chapterid,$quiz_name,$uniquefield,$description,$questions)
     {
         global $DB,$CFG,$CONTEXT; 
         $check_uniquefield = $DB->get_record('quiz', array('uid'=>$uniquefield));
         if(isset($check_uniquefield->id))
         { 
             //Update assignment details if unique field already present
             $check_coursemodulefield = $DB->get_record('course_modules', array('instance' => $check_uniquefield->id,'course'=>$courseid));
             //PASS our web service values to the lib file
             $formdata = (object) array(
                 'name' => $quiz_name,                
                 'introformat'=>'1',
                 'quizpassword' => '',
                 'intro' => $description,
                 'coursemodule' => $check_coursemodulefield->id,
                 'course' => $courseid,
                 'add' => 0,
                 'instance' =>$check_uniquefield->id,
                 'update' => $check_coursemodulefield->id               
             );
             quiz_update_instance($formdata,'');
 
             $returnid = $check_uniquefield->id;
             $quizModule = self::getnameofmodule('quiz');
             $modl = $DB->get_record('course_modules', array('course' => $courseid,'instance'=>$check_uniquefield->id,'module'=>$quizModule->id));
             $mod_id = $modl->id;
 
             $contxtl = $DB->get_record('context', array('instanceid'=>$mod_id,'depth'=>4));
             $moduleid = $contxtl->id;
 
             // DELETE ALL the SLOTS AND REFERENCES for Update
             quiz_delete_references($check_uniquefield->id);
             $DB->delete_records('quiz_slots', array('quizid' => $check_uniquefield->id));
 
             //Question sections
             if(!empty($questions))
             { 
                 $exp_questions = explode(",",$questions);
                 $questonscount = count($exp_questions);
                 foreach($exp_questions as $eaches)
                 { 
                     $getquestion = $DB->get_record('question', array('name'=>trim($eaches),'parent'=>0));
                     if(isset($getquestion->id))
                     { 
                     
                         $questionID = $getquestion->id;
                         $question_mark = $getquestion->defaultmark;
 
                         $getquestion_versions = $DB->get_record('question_versions', array('questionid' => $questionID,'status'=>'ready'));
                         $questionbank_entry = $getquestion_versions->questionbankentryid;
                        
                         //INSERTION INTO SLOTS
                         $sql = "SELECT * FROM {quiz_slots} WHERE quizid = ? ORDER BY id DESC LIMIT 1";
                         $params[] = $returnid;
                         $getslots = $DB->get_records_sql($sql, $params); 
                         $countslot = count($getslots);
                         if($countslot>0)
                         {
                             foreach($getslots as $sql1)
                             {
                                 $slot = $sql1->slot + 1;
                             }
                         }
                         else
                         {   
                             $slot = 1;
                         }
                         
                         $insertslot = array(
                         'slot' => $slot,                
                         'quizid'=> $returnid,
                         'page' => $slot,
                         'requireprevious' => 0,
                         'maxmark' => $question_mark        
                         );
                         $insertslotid = $DB->insert_record('quiz_slots', $insertslot);
 
                         //INSERT INTO REFRENCES
                         $insert_reference = array(
                         'usingcontextid' => $moduleid,                
                         'component'=> 'mod_quiz',
                         'questionarea' => 'slot',
                         'itemid' => $insertslotid,
                         'questionbankentryid' => $questionbank_entry        
                         );
                         $insert_refid = $DB->insert_record('question_references', $insert_reference);
 
                         $slot ='';
                     }
                     else
                     {                        
                         return array('message'=>'Question Not found');
                     }
                 }
             }
             
             return array('message'=>'updated Success');
             
         }
         else
         {
             //Add assignment details if unique field not present
             $getcoursemoduleslist_courses = get_coursemodules_in_course('quiz', $courseid, '');
             $getcoursemoduleslist_courses_last = end($getcoursemoduleslist_courses);
             $sections = $DB->get_record('course_sections', array('course'=>$courseid,'section' => $chapterid));
             $section_id = $sections->id;
             $sequence_column = $sections->sequence;
             
             $sequencing = array();
             $coursemodule = $getcoursemoduleslist_courses_last->id +1;
 
             //Get QBassign Module
             $get_modulelist = $DB->get_record('modules', array('name' => 'quiz'));
 
             //INSERT instance into course modules
             $flags = array(
             'course' => $courseid,
             'module' => $get_modulelist->id,
             'instance' => '',
             'section' => $section_id,
             'added' => time()
             );       
             $course_insert_id = $DB->insert_record('course_modules', $flags);
 
             $updatedata = new stdClass();
             $updatedata->id = $section_id;
             if($sequence_column=='')
             {
                $updatedata->sequence = $course_insert_id;        
             }
             else
             {
                 $sequencing = explode(",",$sequence_column);
                 array_push($sequencing,$course_insert_id);
                 $updatedata->sequence = implode(',', $sequencing);
             }
             
             $updatedata->section = $chapterid;        
             $coursesectionupdate = $DB->update_record('course_sections', $updatedata);
 
 
             $getcoursecontext = $DB->get_record('context', array('instanceid' => $courseid,'depth'=> 3,'contextlevel'=>50));
             $coursepath = $getcoursecontext->path;
 
             $recorder =  array(
                 'contextlevel' => 70, //CONTEXT_MODULE = 70,CONTEXT_SYSTEM = 10,CONTEXT_BLOCK = 80
                 'instanceid' => $course_insert_id,
                 'path' => $coursepath.'/',
                 'depth' => 4,
                 'locked' => 0
             );
             $coursecontextinsertid = $DB->insert_record('context', $recorder);
 
             $getcoursecontextpath = $DB->get_record('context', array('id' => $coursecontextinsertid,'depth' => 4));
 
             $updatecontextdata = new stdClass();
             $updatecontextdata->id = $coursecontextinsertid;
             $updatecontextdata->path = $getcoursecontextpath->path.$coursecontextinsertid; 
             $coursesectionupdate = $DB->update_record('context', $updatecontextdata);
             //PASS our web service values to the lib file
             $formdata = (object) array(
                 'name' => $quiz_name,                
                 'introformat'=>'1',
                 'quizpassword' => '',
                 'intro' => $description,
                 'coursemodule' => $course_insert_id,
                 'course' => $courseid,
                 'preferredbehaviour' => 'deferredfeedback',
                 'overduehandling' => 'autosubmit'           
             );
             $returnid = quiz_add_instance($formdata); 
             $quizModule = self::getnameofmodule('quiz');
             $modl = $DB->get_record('course_modules', array('course' => $courseid,'instance'=>$returnid,'module'=>$quizModule->id));
             $mod_id = $modl->id;
            
             $DB->set_field('quiz', 'uid', $uniquefield, array('id' => $returnid));
 
             //update assignment id into course modules
             $updatecoursemoduledata = new stdClass();
             $updatecoursemoduledata->id = $course_insert_id;
             $updatecoursemoduledata->instance = $returnid;
             $coursesectionupdate = $DB->update_record('course_modules', $updatecoursemoduledata);
 
             //Question sections
             if(!empty($questions))
             { 
                 $exp_questions = explode(",",$questions);
                 $questonscount = count($exp_questions);
                 foreach($exp_questions as $eaches)
                 { 
                     $getquestion = $DB->get_record('question', array('name'=>trim($eaches),'parent'=>0));
                     if(isset($getquestion->id))
                     { 
                         $questionID = $getquestion->id;
                         $question_mark = $getquestion->defaultmark;
 
                         $getquestion_versions = $DB->get_record('question_versions', array('questionid' => $questionID,'status'=>'ready'));
                         $questionbank_entry = $getquestion_versions->questionbankentryid;
                        
                         //INSERTION INTO SLOTS
                         $sql = "SELECT * FROM {quiz_slots} WHERE quizid = ? ORDER BY id DESC LIMIT 1";
                         $params[] = $returnid;
                         $getslots = $DB->get_records_sql($sql, $params); 
                         $countslot = count($getslots);
                         if($countslot>0)
                         {
                             foreach($getslots as $sql1)
                             {
                                 $slot = $sql1->slot + 1;
                             }
                         }
                         else
                         {   
                             $slot = 1;
                         }
                         
                         $insertslot = array(
                         'slot' => $slot,                
                         'quizid'=> $returnid,
                         'page' => $slot,
                         'requireprevious' => 0,
                         'maxmark' => ($question_mark!='')?$question_mark:'0.000000'        
                         );
                         $insertslotid = $DB->insert_record('quiz_slots', $insertslot);
                         //$context = context_course::instance($courseid);
 
                         //INSERT INTO REFRENCES
                         $insert_reference = array(
                         'usingcontextid' => $coursecontextinsertid,                
                         'component'=> 'mod_quiz',
                         'questionarea' => 'slot',
                         'itemid' => $insertslotid,
                         'questionbankentryid' => $questionbank_entry        
                         );
                         $insert_refid = $DB->insert_record('question_references', $insert_reference);
 
                         $slot ='';
                     }
                     else
                     { 
                         return array('message'=>'Question Not found');
                     }
                 }
             }
             return array('message'=>'Added Success','cm_id' => $course_insert_id);
         }
     }
 
     public static function quiz_addition_returns()
     {
         return new external_single_structure(
                 array(
                     'message'=> new external_value(PARAM_TEXT, 'success message'),
                     'cm_id' => new external_value(PARAM_INT, 'Course Module ID')
                 )
             );
     }
 
     /**
      * Remove Student Submission module details.
      *
      * @param int $submissionid assignment submission id
      * @param text $assignmentid assignment id
      * @return array of warnings and status result
      * @since Moodle 3.2
      */
 
     public static function remove_submission_parameters()
     {
         return new external_function_parameters(
             array(
             'submissionid' => new external_value(PARAM_INT, 'Submission Id',VALUE_REQUIRED),
             'assignmentid' => new external_value(PARAM_INT, 'Assignment Id',VALUE_REQUIRED),
             'courseid' => new external_value(PARAM_INT, 'Course Id',VALUE_REQUIRED)
             )
         );
     }
 
     public static function remove_submission($submissionid,$assignmentid,$courseid)
     {
         global $DB,$USER,$CONTEXT;
         //Get activity Module details
         $get_coursefield = $DB->get_record('course_modules', array('instance' => $assignmentid,'course' => $courseid));
         if(isset($get_coursefield->id))
         {
             $moduleid = $get_coursefield->id;
             $contextsystem = context_module::instance($moduleid);
             $checkenrol = is_enrolled($contextsystem, $USER, 'mod/assignment:submit');
             if($checkenrol)
             {
                 $submissionid = $submissionid ? $submissionid : 0;
                 $get_submission = $DB->get_record('qbassign_submission', array('qbassignment' => $assignmentid,'userid'=>$USER->id,'id'=>$submissionid));
                 if(isset($get_submission->id))
                 {
                     if ($submissionid) {
                         $DB->delete_records('qbassignsubmission_onlinetex', array('submission' => $submissionid));
                         $DB->set_field('qbassign_submission', 'status', 'new', array('userid' => $USER->id,'id'=>$submissionid));
                         $remove_updated = ['message'=>'sucess']; 
                         return $remove_updated;
                     }
                     else
                     {
                         throw new moodle_exception('Submission error', 'error');
                     }
                 }
                 else
                 {
                     throw new moodle_exception('Invalid Submission ID', 'error');
                 }
             }
             else
             { 
                 throw new moodle_exception('This user not enrolled', 'error');
             }
         }
         else
         { 
             throw new moodle_exception('Invalid Assignment ID', 'error');
         }
     }
 
     public static function remove_submission_returns()
     {
         return new external_single_structure(
                 array(
                     'message'=> new external_value(PARAM_TEXT, 'success message')
                 )
             );
     }
}
