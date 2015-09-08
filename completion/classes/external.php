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
 * Completion external API
 *
 * @package    core_completion
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 2.9
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");
require_once("$CFG->libdir/completionlib.php");

/**
 * Completion external functions
 *
 * @package    core_completion
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 2.9
 */
class core_completion_external extends external_api {

    /**
     * Describes the parameters for update_activity_completion_status_manually.
     *
     * @return external_external_function_parameters
     * @since Moodle 2.9
     */
    public static function update_activity_completion_status_manually_parameters() {
        return new external_function_parameters (
            array(
                'cmid' => new external_value(PARAM_INT, 'course module id'),
                'completed' => new external_value(PARAM_BOOL, 'activity completed or not'),
            )
        );
    }

    /**
     * Update completion status for the current user in an activity, only for activities with manual tracking.
     * @param  int $cmid      Course module id
     * @param  bool $completed Activity completed or not
     * @return array            Result and possible warnings
     * @since Moodle 2.9
     * @throws moodle_exception
     */
    public static function update_activity_completion_status_manually($cmid,  $completed) {

        // Validate and normalize parameters.
        $params = self::validate_parameters(self::update_activity_completion_status_manually_parameters(),
            array('cmid' => $cmid, 'completed' => $completed));
        $cmid = $params['cmid'];
        $completed = $params['completed'];

        $warnings = array();

        $context = context_module::instance($cmid);
        self::validate_context($context);

        list($course, $cm) = get_course_and_cm_from_cmid($cmid);

        // Set up completion object and check it is enabled.
        $completion = new completion_info($course);
        if (!$completion->is_enabled()) {
            throw new moodle_exception('completionnotenabled', 'completion');
        }

        // Check completion state is manual.
        if ($cm->completion != COMPLETION_TRACKING_MANUAL) {
            throw new moodle_exception('cannotmanualctrack', 'error');
        }

        $targetstate = ($completed) ? COMPLETION_COMPLETE : COMPLETION_INCOMPLETE;
        $completion->update_state($cm, $targetstate);

        $result = array();
        $result['status'] = true;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the update_activity_completion_status_manually return value.
     *
     * @return external_single_structure
     * @since Moodle 2.9
     */
    public static function update_activity_completion_status_manually_returns() {

        return new external_single_structure(
            array(
                'status'    => new external_value(PARAM_BOOL, 'status, true if success'),
                'warnings'  => new external_warnings(),
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.9
     */
    public static function get_activities_completion_status_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'Course ID'),
                'userid'   => new external_value(PARAM_INT, 'User ID'),
            )
        );
    }

    /**
     * Get Activities completion status
     *
     * @param int $courseid ID of the Course
     * @param int $userid ID of the User
     * @return array of activities progress and warnings
     * @throws moodle_exception
     * @since Moodle 2.9
     * @throws moodle_exception
     */
    public static function get_activities_completion_status($courseid, $userid) {
        global $CFG, $USER;
        require_once($CFG->libdir . '/grouplib.php');

        $warnings = array();
        $arrayparams = array(
            'courseid' => $courseid,
            'userid'   => $userid,
        );

        $params = self::validate_parameters(self::get_activities_completion_status_parameters(), $arrayparams);

        $course = get_course($params['courseid']);
        $user = core_user::get_user($params['userid'], 'id', MUST_EXIST);

        $context = context_course::instance($course->id);
        self::validate_context($context);

        // Check that current user have permissions to see this user's activities.
        if ($user->id != $USER->id) {
            require_capability('report/progress:view', $context);
            if (!groups_user_groups_visible($course, $user->id)) {
                // We are not in the same group!
                throw new moodle_exception('accessdenied', 'admin');
            }
        }

        $completion = new completion_info($course);
        $activities = $completion->get_activities();
        $progresses = $completion->get_progress_all();
        $userprogress = $progresses[$user->id];

        $results = array();
        foreach ($activities as $activity) {

            // Check if current user has visibility on this activity.
            if (!$activity->uservisible) {
                continue;
            }

            // Get progress information and state.
            if (array_key_exists($activity->id, $userprogress->progress)) {
                $thisprogress  = $userprogress->progress[$activity->id];
                $state         = $thisprogress->completionstate;
                $timecompleted = $thisprogress->timemodified;
            } else {
                $state = COMPLETION_INCOMPLETE;
                $timecompleted = 0;
            }

            $results[] = array(
                       'cmid'          => $activity->id,
                       'modname'       => $activity->modname,
                       'instance'      => $activity->instance,
                       'state'         => $state,
                       'timecompleted' => $timecompleted,
                       'tracking'      => $activity->completion
            );
        }

        $results = array(
            'statuses' => $results,
            'warnings' => $warnings
        );
        return $results;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.9
     */
    public static function get_activities_completion_status_returns() {
        return new external_single_structure(
            array(
                'statuses' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'cmid'          => new external_value(PARAM_INT, 'comment ID'),
                            'modname'       => new external_value(PARAM_PLUGIN, 'activity module name'),
                            'instance'      => new external_value(PARAM_INT, 'instance ID'),
                            'state'         => new external_value(PARAM_INT, 'completion state value:
                                                                    0 means incomplete, 1 complete,
                                                                    2 complete pass, 3 complete fail'),
                            'timecompleted' => new external_value(PARAM_INT, 'timestamp for completed activity'),
                            'tracking'      => new external_value(PARAM_INT, 'type of tracking:
                                                                    0 means none, 1 manual, 2 automatic'),
                        ), 'Activity'
                    ), 'List of activities status'
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
    public static function get_course_completion_status_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'Course ID'),
                'userid'   => new external_value(PARAM_INT, 'User ID'),
            )
        );
    }
    /**
     * Get Course completion status
     *
     * @param int $courseid ID of the Course
     * @param int $userid ID of the User
     * @return array of course completion status and warnings
     * @since Moodle 2.9
     * @throws moodle_exception
     */
    public static function get_course_completion_status($courseid, $userid) {
        global $CFG, $USER;
        require_once($CFG->libdir . '/grouplib.php');

        $warnings = array();
        $arrayparams = array(
            'courseid' => $courseid,
            'userid'   => $userid,
        );
        $params = self::validate_parameters(self::get_course_completion_status_parameters(), $arrayparams);

        $course = get_course($params['courseid']);
        $user = core_user::get_user($params['userid'], 'id', MUST_EXIST);
        $context = context_course::instance($course->id);
        self::validate_context($context);

        // Can current user see user's course completion status?
        // This check verifies if completion is enabled because $course is mandatory.
        if (!completion_can_view_data($user->id, $course)) {
            throw new moodle_exception('cannotviewreport');
        }

        // The previous function doesn't check groups.
        if ($user->id != $USER->id) {
            if (!groups_user_groups_visible($course, $user->id)) {
                // We are not in the same group!
                throw new moodle_exception('accessdenied', 'admin');
            }
        }

        $info = new completion_info($course);

        // Check this user is enroled.
        if (!$info->is_tracked_user($user->id)) {
            if ($USER->id == $user->id) {
                throw new moodle_exception('notenroled', 'completion');
            } else {
                throw new moodle_exception('usernotenroled', 'completion');
            }
        }

        $completions = $info->get_completions($user->id);
        if (empty($completions)) {
            throw new moodle_exception('nocriteriaset', 'completion');
        }

        // Load course completion.
        $completionparams = array(
            'userid' => $user->id,
            'course' => $course->id,
        );
        $ccompletion = new completion_completion($completionparams);

        $completionrows = array();
        // Loop through course criteria.
        foreach ($completions as $completion) {
            $criteria = $completion->get_criteria();

            $completionrow = array();
            $completionrow['type'] = $criteria->criteriatype;
            $completionrow['title'] = $criteria->get_title();
            $completionrow['status'] = $completion->get_status();
            $completionrow['complete'] = $completion->is_complete();
            $completionrow['timecompleted'] = $completion->timecompleted;
            $completionrow['details'] = $criteria->get_details($completion);
            $completionrows[] = $completionrow;
        }

        $result = array(
                  'completed'   => $info->is_course_complete($user->id),
                  'aggregation' => $info->get_aggregation_method(),
                  'completions' => $completionrows
        );

        $results = array(
            'completionstatus' => $result,
            'warnings' => $warnings
        );
        return $results;

    }
    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.9
     */
    public static function get_course_completion_status_returns() {
        return new external_single_structure(
            array(
                'completionstatus' => new external_single_structure(
                    array(
                        'completed'     => new external_value(PARAM_BOOL, 'true if the course is complete, false otherwise'),
                        'aggregation'   => new external_value(PARAM_INT, 'aggregation method 1 means all, 2 means any'),
                        'completions'   => new external_multiple_structure(
                            new external_single_structure(
                            array(
                                 'type'          => new external_value(PARAM_INT,   'Completion criteria type'),
                                 'title'         => new external_value(PARAM_TEXT,  'Completion criteria Title'),
                                 'status'        => new external_value(PARAM_NOTAGS, 'Completion status (Yes/No) a % or number'),
                                 'complete'      => new external_value(PARAM_BOOL,   'Completion status (true/false)'),
                                 'timecompleted' => new external_value(PARAM_INT,   'Timestamp for criteria completetion'),
                                 'details' => new external_single_structure(
                                     array(
                                         'type' => new external_value(PARAM_TEXT, 'Type description'),
                                         'criteria' => new external_value(PARAM_RAW, 'Criteria description'),
                                         'requirement' => new external_value(PARAM_TEXT, 'Requirement description'),
                                         'status' => new external_value(PARAM_RAW, 'Status description, can be anything'),
                                         ), 'details'),
                                 ), 'Completions'
                            ), ''
                         )
                    ), 'Course status'
                ),
                'warnings' => new external_warnings()
            ), 'Course completion status'
        );
    }

}
