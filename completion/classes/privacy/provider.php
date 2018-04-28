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
 * Privacy class for requesting user data.
 *
 * @package    core_completion
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_completion\privacy;

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\transform;
use \core_privacy\local\request\contextlist;

require_once($CFG->dirroot . '/comment/lib.php');

/**
 * Privacy class for requesting user data.
 *
 * @package    core_completion
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements \core_privacy\local\metadata\provider, \core_privacy\local\request\subsystem\plugin_provider {

    /**
     * Returns meta data about this system.
     *
     * @param   collection $collection The initialised collection to add items to.
     * @return  collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) {
        $collection->add_database_table('course_completions', [
                'userid' => 'privacy:metadata:userid',
                'course' => 'privacy:metadata:course',
                'timeenrolled' => 'privacy:metadata:timeenrolled',
                'timestarted' => 'privacy:metadata:timestarted',
                'timecompleted' => 'privacy:metadata:timecompleted',
                'reaggregate' => 'privacy:metadata:reaggregate'
            ], 'privacy:metadata:coursesummary');
        $collection->add_database_table('course_modules_completion', [
                'userid' => 'privacy:metadata:userid',
                'coursemoduleid' => 'privacy:metadata:coursemoduleid',
                'completionstate' => 'privacy:metadata:completionstate',
                'viewed' => 'privacy:metadata:viewed',
                'overrideby' => 'privacy:metadata:overrideby',
                'timemodified' => 'privacy:metadata:timemodified'
            ], 'privacy:metadata:coursemodulesummary');
        $collection->add_database_table('course_completion_crit_compl', [
                'userid' => 'privacy:metadata:userid',
                'course' => 'privacy:metadata:course',
                'gradefinal' => 'privacy:metadata:gradefinal',
                'unenroled' => 'privacy:metadata:unenroled',
                'timecompleted' => 'privacy:metadata:timecompleted'
            ], 'privacy:metadata:coursecompletedsummary');
        return $collection;
    }

    /**
     * Get join sql to retrieve courses the user is in.
     *
     * @param  int $userid The user ID
     * @param  string $prefix A unique prefix for these joins.
     * @param  string $joinfield A field to join these tables to. Joins to course ID.
     * @return array The join, where, and params for this join.
     */
    public static function get_course_completion_join_sql($userid, $prefix, $joinfield) {
        $cccalias = "{$prefix}_ccc"; // Course completion criteria.
        $cmcalias = "{$prefix}_cmc"; // Course modules completion.
        $ccccalias = "{$prefix}_cccc"; // Course completion criteria completion.

        $join = "JOIN {course_completion_criteria} {$cccalias} ON {$joinfield} = {$cccalias}.course
             LEFT JOIN {course_modules_completion} {$cmcalias} ON {$cccalias}.moduleinstance = {$cmcalias}.coursemoduleid
             LEFT JOIN {course_completion_crit_compl} {$ccccalias} ON {$ccccalias}.criteriaid = {$cccalias}.id";
        $where = "{$cmcalias}.userid = :{$prefix}_moduleuserid OR {$ccccalias}.userid = :{$prefix}_courseuserid";
        $params = ["{$prefix}_moduleuserid" => $userid, "{$prefix}_courseuserid" => $userid];

        return [$join, $where, $params];
    }

    /**
     * Returns activity completion information about a user.
     *
     * @param  \stdClass $user The user to return information about.
     * @param  \stdClass $course The course the user is in.
     * @param  \stdClass $cm Course module information.
     * @return \stdClass Activity completion information.
     */
    public static function get_activity_completion_info(\stdClass $user, \stdClass $course, $cm) {
        $completioninfo = new \completion_info($course);
        $completion = $completioninfo->is_enabled($cm);
        return ($completion != COMPLETION_TRACKING_NONE) ? $completioninfo->get_data($cm, true, $user->id) : new \stdClass();
    }

    /**
     * Returns course completion information for a user.
     *
     * @param  \stdClass $user The user that we are getting completion information for.
     * @param  \stdClass $course The course we are interested in.
     * @return \stdClass Course completion information.
     */
    public static function get_course_completion_info(\stdClass $user, \stdClass $course) {
        $completioninfo = new \completion_info($course);
        $completion = $completioninfo->is_enabled();

        if ($completion == COMPLETION_ENABLED) {

            $coursecomplete = $completioninfo->is_course_complete($user->id);
            $criteriacomplete = $completioninfo->count_course_user_data($user->id);
            $ccompletion = new \completion_completion(['userid' => $user->id, 'course' => $course->id]);

            $status = ($coursecomplete) ? get_string('complete') : '';
            $status = (!$criteriacomplete && !$ccompletion->timestarted) ? get_string('notyetstarted', 'completion') :
                    get_string('inprogress', 'completion');

            $completions = $completioninfo->get_completions($user->id);
            $overall = get_string('nocriteriaset', 'completion');
            if (!empty($completions)) {
                if ($completioninfo->get_aggregation_method() == COMPLETION_AGGREGATION_ALL) {
                    $overall = get_string('criteriarequiredall', 'completion');
                } else {
                    $overall = get_string('criteriarequiredany', 'completion');
                }
            }

            $coursecompletiondata = [
                'status' => $status,
                'required' => $overall,
            ];

            $coursecompletiondata['criteria'] = array_map(function($completion) use ($completioninfo) {
                $criteria = $completion->get_criteria();
                $aggregation = $completioninfo->get_aggregation_method($criteria->criteriatype);
                $required = ($aggregation == COMPLETION_AGGREGATION_ALL) ? get_string('all', 'completion') :
                        get_string('any', 'completion');
                $data = [
                    'required' => $required,
                    'completed' => transform::yesno($completion->is_complete()),
                    'timecompleted' => isset($completion->timecompleted) ? transform::datetime($completion->timecompleted) : ''
                ];
                $details = $criteria->get_details($completion);
                $data = array_merge($data, $details);
                return $data;
            }, $completions);
            return $coursecompletiondata;
        }
    }

    /**
     * Delete completion information for users.
     *
     * @param \stdClass $user The user. If provided will delete completion information for just this user. Else all users.
     * @param int $courseid The course id. Provide this if you want course completion and activity completion deleted.
     * @param int $cmid The course module id. Provide this if you only want activity completion deleted.
     */
    public static function delete_completion(\stdClass $user = null, $courseid = null, $cmid = null) {
        global $DB;

        if (isset($cmid)) {
            $params = (isset($user)) ? ['userid' => $user->id, 'coursemoduleid' => $cmid] : ['coursemoduleid' => $cmid];
            // Only delete the record for course modules completion.
            $DB->delete_records('course_modules_completion', $params);
            return;
        }

        if (isset($courseid)) {

            $usersql = isset($user) ? 'AND cmc.userid = :userid' : '';
            $params = isset($user) ? ['course' => $courseid, 'userid' => $user->id] : ['course' => $courseid];

            // Find records relating to course modules.
            $sql = "SELECT cmc.id
                      FROM {course_completion_criteria} ccc
                      JOIN {course_modules_completion} cmc ON ccc.moduleinstance = cmc.coursemoduleid
                     WHERE ccc.course = :course $usersql";
            $recordids = $DB->get_records_sql($sql, $params);
            $ids = array_keys($recordids);
            if (!empty($ids)) {
                list($deletesql, $deleteparams) = $DB->get_in_or_equal($ids);
                $deletesql = 'id ' . $deletesql;
                $DB->delete_records_select('course_modules_completion', $deletesql, $deleteparams);
            }
            $DB->delete_records('course_completion_crit_compl', $params);
            $DB->delete_records('course_completions', $params);
        }
    }
}
