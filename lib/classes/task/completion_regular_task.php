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
 * A scheduled task.
 *
 * @package    core
 * @copyright  2015 Josh Willcock
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\task;

/**
 * Simple task to run the regular completion cron.
 * @copyright  2015 Josh Willcock
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class completion_regular_task extends scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('taskcompletionregular', 'admin');
    }

    /**
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {
        global $CFG, $COMPLETION_CRITERIA_TYPES, $DB;

        if ($CFG->enablecompletion) {
            require_once($CFG->libdir . "/completionlib.php");

            // Process each criteria type.
            foreach ($COMPLETION_CRITERIA_TYPES as $type) {
                $object = 'completion_criteria_' . $type;
                require_once($CFG->dirroot . '/completion/criteria/' . $object . '.php');

                $class = new $object();
                // Run the criteria type's cron method, if it has one.
                if (method_exists($class, 'cron')) {
                    if (debugging()) {
                        mtrace('Running '.$object.'->cron()');
                    }
                    $class->cron();
                }
            }

            if (debugging()) {
                mtrace('Aggregating completions');
            }

            // Save time started.
            $timestarted = time();

            // Grab all criteria and their associated criteria completions.
            $sql = 'SELECT DISTINCT c.id AS course, cr.id AS criteriaid, crc.userid AS userid,
                                    cr.criteriatype AS criteriatype, cc.timecompleted AS timecompleted
                      FROM {course_completion_criteria} cr
                INNER JOIN {course} c ON cr.course = c.id
                INNER JOIN {course_completions} crc ON crc.course = c.id
                 LEFT JOIN {course_completion_crit_compl} cc ON cc.criteriaid = cr.id AND crc.userid = cc.userid
                     WHERE c.enablecompletion = 1
                       AND crc.timecompleted IS NULL
                       AND crc.reaggregate > 0
                       AND crc.reaggregate < :timestarted
                  ORDER BY course, userid';
            $rs = $DB->get_recordset_sql($sql, ['timestarted' => $timestarted]);

            // Check if result is empty.
            if (!$rs->valid()) {
                $rs->close();
                return;
            }

            $currentuser = null;
            $currentcourse = null;
            $completions = [];
            while (1) {
                // Grab records for current user/course.
                foreach ($rs as $record) {
                    // If we are still grabbing the same users completions.
                    if ($record->userid === $currentuser && $record->course === $currentcourse) {
                        $completions[$record->criteriaid] = $record;
                    } else {
                        break;
                    }
                }

                // Aggregate.
                if (!empty($completions)) {
                    if (debugging()) {
                        mtrace('Aggregating completions for user ' . $currentuser . ' in course ' . $currentcourse);
                    }

                    // Get course info object.
                    $info = new \completion_info((object)['id' => $currentcourse]);

                    // Setup aggregation.
                    $overall = $info->get_aggregation_method();
                    $activity = $info->get_aggregation_method(COMPLETION_CRITERIA_TYPE_ACTIVITY);
                    $prerequisite = $info->get_aggregation_method(COMPLETION_CRITERIA_TYPE_COURSE);
                    $role = $info->get_aggregation_method(COMPLETION_CRITERIA_TYPE_ROLE);

                    $overallstatus = null;
                    $activitystatus = null;
                    $prerequisitestatus = null;
                    $rolestatus = null;

                    // Get latest timecompleted.
                    $timecompleted = null;

                    // Check each of the criteria.
                    foreach ($completions as $params) {
                        $timecompleted = max($timecompleted, $params->timecompleted);
                        $completion = new \completion_criteria_completion((array)$params, false);

                        // Handle aggregation special cases.
                        if ($params->criteriatype == COMPLETION_CRITERIA_TYPE_ACTIVITY) {
                            completion_cron_aggregate($activity, $completion->is_complete(), $activitystatus);
                        } else if ($params->criteriatype == COMPLETION_CRITERIA_TYPE_COURSE) {
                            completion_cron_aggregate($prerequisite, $completion->is_complete(), $prerequisitestatus);
                        } else if ($params->criteriatype == COMPLETION_CRITERIA_TYPE_ROLE) {
                            completion_cron_aggregate($role, $completion->is_complete(), $rolestatus);
                        } else {
                            completion_cron_aggregate($overall, $completion->is_complete(), $overallstatus);
                        }
                    }

                    // Include role criteria aggregation in overall aggregation.
                    if ($rolestatus !== null) {
                        completion_cron_aggregate($overall, $rolestatus, $overallstatus);
                    }

                    // Include activity criteria aggregation in overall aggregation.
                    if ($activitystatus !== null) {
                        completion_cron_aggregate($overall, $activitystatus, $overallstatus);
                    }

                    // Include prerequisite criteria aggregation in overall aggregation.
                    if ($prerequisitestatus !== null) {
                        completion_cron_aggregate($overall, $prerequisitestatus, $overallstatus);
                    }

                    // If aggregation status is true, mark course complete for user.
                    if ($overallstatus) {
                        if (debugging()) {
                            mtrace('Marking complete');
                        }

                        $ccompletion = new \completion_completion([
                            'course' => $params->course,
                            'userid' => $params->userid
                        ]);
                        $ccompletion->mark_complete($timecompleted);
                    }
                }

                // If this is the end of the recordset, break the loop.
                if (!$rs->valid()) {
                    $rs->close();
                    break;
                }

                // New/next user, update user details, reset completions.
                $currentuser = $record->userid;
                $currentcourse = $record->course;
                $completions = [];
                $completions[$record->criteriaid] = $record;
            }

            // Mark all users as aggregated.
            $sql = "UPDATE {course_completions}
                       SET reaggregate = 0
                     WHERE reaggregate < :timestarted
                       AND reaggregate > 0";
            $DB->execute($sql, ['timestarted' => $timestarted]);
        }
    }

}
