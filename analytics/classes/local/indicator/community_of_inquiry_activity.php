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
 * Community of inquire abstract indicator.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics\local\indicator;

defined('MOODLE_INTERNAL') || die();

/**
 * Community of inquire abstract indicator.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class community_of_inquiry_activity extends linear {

    protected $course = null;
    /**
     * TODO This should ideally be reused by cognitive depth and social breadth.
     *
     * @var array Array of logs by [contextid][userid]
     */
    protected $activitylogs = null;

    /**
     * @var array Array of grades by [contextid][userid]
     */
    protected $grades = null;

    /**
     * @const Constant cognitive indicator type.
     */
    const INDICATOR_COGNITIVE = "cognitve";

    /**
     * @const Constant social indicator type.
     */
    const INDICATOR_SOCIAL = "social";

    /**
     * TODO Automate this when merging into core.
     * @var string The activity name (e.g. assign or quiz)
     */
    abstract protected function get_activity_type();

    protected function get_cognitive_depth_level(\cm_info $cm) {
        throw new \coding_exception('Overwrite get_cognitive_depth_level method to set your activity potential cognitive ' .
            'depth level');
    }

    public static function required_sample_data() {
        // Only course because the indicator is valid even without students.
        return array('course');
    }

    protected final function any_log($contextid, $user) {
        if (empty($this->activitylogs[$contextid])) {
            return false;
        }

        // Someone interacted with the activity if there is no user or the user interacted with the
        // activity if there is a user.
        if (empty($user) ||
                (!empty($user) && !empty($this->activitylogs[$contextid][$user->id]))) {
            return true;
        }

        return false;
    }

    protected final function any_write_log($contextid, $user) {
        if (empty($this->activitylogs[$contextid])) {
            return false;
        }

        // No specific user, we look at all activity logs.
        $it = $this->activitylogs[$contextid];
        if ($user) {
            if (empty($this->activitylogs[$contextid][$user->id])) {
                return false;
            }
            $it = array($user->id => $this->activitylogs[$contextid][$user->id]);
        }
        foreach ($it as $logs) {
            foreach ($logs as $log) {
                if ($log->crud === 'c' || $log->crud === 'u') {
                    return true;
                }
            }
        }

        return false;
    }

    protected function any_feedback($action, \cm_info $cm, $contextid, $user) {
        if (empty($this->activitylogs[$contextid])) {
            return false;
        }

        if (empty($this->grades[$contextid]) && $this->feedback_check_grades()) {
            // If there are no grades there is no feedback.
            return false;
        }

        $it = $this->activitylogs[$contextid];
        if ($user) {
            if (empty($this->activitylogs[$contextid][$user->id])) {
                return false;
            }
            $it = array($user->id => $this->activitylogs[$contextid][$user->id]);
        }

        foreach ($this->activitylogs[$contextid] as $userid => $logs) {
            $methodname = 'feedback_' . $action;
            if ($this->{$methodname}($cm, $contextid, $userid)) {
                return true;
            }
            // If it wasn't viewed try with the next user.
        }
        return false;
    }

    /**
     * $cm is used for this method overrides.
     *
     * This function must be fast.
     *
     * @param \cm_info $cm
     * @param mixed $contextid
     * @param mixed $userid
     * @param int $after Timestamp, defaults to the graded date or false if we don't check the date.
     * @return bool
     */
    protected function feedback_viewed(\cm_info $cm, $contextid, $userid, $after = null) {
        return $this->feedback_post_action($cm, $contextid, $userid, $this->feedback_viewed_events(), $after);
    }

    protected function feedback_replied(\cm_info $cm, $contextid, $userid, $after = null) {
        return $this->feedback_post_action($cm, $contextid, $userid, $this->feedback_replied_events(), $after);
    }

    protected function feedback_submitted(\cm_info $cm, $contextid, $userid, $after = null) {
        return $this->feedback_post_action($cm, $contextid, $userid, $this->feedback_submitted_events(), $after);
    }

    protected function feedback_viewed_events() {
        throw new \coding_exception('Activities with a potential cognitive level that include viewing feedback should define ' .
            '"feedback_viewed_events" method or should override feedback_viewed method.');
    }

    protected function feedback_replied_events() {
        throw new \coding_exception('Activities with a potential cognitive level that include replying to feedback should define ' .
            '"feedback_replied_events" method or should override feedback_replied method.');
    }

    protected function feedback_submitted_events() {
        throw new \coding_exception('Activities with a potential cognitive level that include viewing feedback should define ' .
            '"feedback_submitted_events" method or should override feedback_submitted method.');
    }

    protected function feedback_post_action(\cm_info $cm, $contextid, $userid, $eventnames, $after = null) {
        if ($after === null) {
            if ($this->feedback_check_grades()) {
                if (!$after = $this->get_graded_date($contextid, $userid)) {
                    return false;
                }
            } else {
                $after = false;
            }
        }

        if (empty($this->activitylogs[$contextid][$userid])) {
            return false;
        }

        foreach ($eventnames as $eventname) {
            if (!$after) {
                if (!empty($this->activitylogs[$contextid][$userid][$eventname])) {
                    // If we don't care about when the feedback has been seen we consider this enough.
                    return true;
                }
            } else {
                if (empty($this->activitylogs[$contextid][$userid][$eventname])) {
                    continue;
                }
                $timestamps = $this->activitylogs[$contextid][$userid][$eventname]->timecreated;
                // Faster to start by the end.
                rsort($timestamps);
                foreach ($timestamps as $timestamp) {
                    if ($timestamp > $after) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * get_graded_date
     *
     * @param int $contextid
     * @param int $userid
     * @param bool $checkfeedback Check that the student was graded or check that feedback was given
     * @return int|false
     */
    protected function get_graded_date($contextid, $userid, $checkfeedback = false) {
        if (empty($this->grades[$contextid][$userid])) {
            return false;
        }
        foreach ($this->grades[$contextid][$userid] as $gradeitemid => $gradeitem) {

            // We check that either feedback or the grade is set.
            if (($checkfeedback && $gradeitem->feedback) || $gradeitem->grade) {

                // Grab the first graded date.
                if ($gradeitem->dategraded && (empty($after) || $gradeitem->dategraded < $after)) {
                    $after = $gradeitem->dategraded;
                }
            }
        }

        if (!isset($after)) {
            // False if there are no graded items.
            return false;
        }

        return $after;
    }

    protected function get_student_activities($sampleid, $tablename, $starttime, $endtime) {

        // May not be available.
        $user = $this->retrieve('user', $sampleid);

        if ($this->course === null) {
            // The indicator scope is a range, so all activities belong to the same course.
            $this->course = \core_analytics\course::instance($this->retrieve('course', $sampleid));
        }

        if ($this->activitylogs === null) {
            // Fetch all activity logs in each activity in the course, not restricted to a specific sample so we can cache it.

            $courseactivities = $this->course->get_all_activities($this->get_activity_type());

            // Null if no activities of this type in this course.
            if (empty($courseactivities)) {
                $this->activitylogs = false;
                return null;
            }
            $this->activitylogs = $this->fetch_activity_logs($courseactivities, $starttime, $endtime);
        }

        if ($this->grades === null) {
            $courseactivities = $this->course->get_all_activities($this->get_activity_type());
            $this->grades = $this->course->get_student_grades($courseactivities);
        }

        if ($cm = $this->retrieve('cm', $sampleid)) {
            // Samples are at cm level or below.
            $useractivities = array(\context_module::instance($cm->id)->id => $cm);
        } else {
            // All course activities.
            $useractivities = $this->course->get_activities($this->get_activity_type(), $starttime, $endtime, $user);
        }

        return $useractivities;
    }

    protected function fetch_activity_logs($activities, $starttime = false, $endtime = false) {
        global $DB;

        // Filter by context to use the db table index.
        list($contextsql, $contextparams) = $DB->get_in_or_equal(array_keys($activities), SQL_PARAMS_NAMED);

        // Keeping memory usage as low as possible by using recordsets and storing only 1 log
        // per contextid-userid-eventname + 1 timestamp for each of this combination records.
        $fields = 'eventname, crud, contextid, contextlevel, contextinstanceid, userid, courseid';
        $select = "contextid $contextsql AND timecreated > :starttime AND timecreated <= :endtime";
        $sql = "SELECT $fields, timecreated " .
            "FROM {logstore_standard_log} " .
            "WHERE $select " .
            "ORDER BY timecreated ASC";
        $params = $contextparams + array('starttime' => $starttime, 'endtime' => $endtime);
        $logs = $DB->get_recordset_sql($sql, $params);

        // Returs the logs organised by contextid, userid and eventname so it is easier to calculate activities data later.
        // At the same time we want to keep this array reasonably "not-massive".
        $processedlogs = array();
        foreach ($logs as $log) {
            if (!isset($processedlogs[$log->contextid])) {
                $processedlogs[$log->contextid] = array();
            }
            if (!isset($processedlogs[$log->contextid][$log->userid])) {
                $processedlogs[$log->contextid][$log->userid] = array();
            }

            // contextid and userid have already been used to index the logs, the next field to index by is eventname:
            // crud is unique per eventname, courseid is the same for all records and we append timecreated.
            if (!isset($processedlogs[$log->contextid][$log->userid][$log->eventname])) {
                $processedlogs[$log->contextid][$log->userid][$log->eventname] = $log;

                // We want timecreated attribute to be an array containing all user access times.
                $processedlogs[$log->contextid][$log->userid][$log->eventname]->timecreated = array(intval($log->timecreated));
            } else {
                // Add the event timecreated.
                $processedlogs[$log->contextid][$log->userid][$log->eventname]->timecreated[] = intval($log->timecreated);
            }
        }
        $logs->close();

        return $processedlogs;
    }

    /**
     * Whether grades should be checked or not when looking for feedback.
     *
     * @return void
     */
    protected function feedback_check_grades() {
        return true;
    }

    /**
     * cognitive_calculate_sample
     *
     * @param $sampleid
     * @param $tablename
     * @param bool $starttime
     * @param bool $endtime
     * @return float|int|null
     * @throws \coding_exception
     */
    protected function cognitive_calculate_sample($sampleid, $tablename, $starttime = false, $endtime = false) {

        // May not be available.
        $user = $this->retrieve('user', $sampleid);

        if (!$useractivities = $this->get_student_activities($sampleid, $tablename, $starttime, $endtime)) {
            // Null if no activities.
            return null;
        }

        $scoreperactivity = (self::get_max_value() - self::get_min_value()) / count($useractivities);

        $score = self::get_min_value();

        // Iterate through the module activities/resources which due date is part of this time range.
        foreach ($useractivities as $contextid => $cm) {

            $potentiallevel = $this->get_cognitive_depth_level($cm);
            if (!is_int($potentiallevel) || $potentiallevel > 5 || $potentiallevel < 1) {
                throw new \coding_exception('Activities\' potential level of engagement possible values go from 1 to 5.');
            }
            $scoreperlevel = $scoreperactivity / $potentiallevel;

            switch ($potentiallevel) {
                case 5:
                    // Cognitive level 4 is to comment on feedback.
                    if ($this->any_feedback('submitted', $cm, $contextid, $user)) {
                        $score += $scoreperlevel * 5;
                        break;
                    }
                // The user didn't reach the activity max cognitive depth, continue with level 2.

                case 4:
                    // Cognitive level 4 is to comment on feedback.
                    if ($this->any_feedback('replied', $cm, $contextid, $user)) {
                        $score += $scoreperlevel * 4;
                        break;
                    }
                // The user didn't reach the activity max cognitive depth, continue with level 2.

                case 3:
                    // Cognitive level 3 is to view feedback.

                    if ($this->any_feedback('viewed', $cm, $contextid, $user)) {
                        // Max score for level 3.
                        $score += $scoreperlevel * 3;
                        break;
                    }
                // The user didn't reach the activity max cognitive depth, continue with level 2.

                case 2:
                    // Cognitive depth level 2 is to submit content.

                    if ($this->any_write_log($contextid, $user)) {
                        $score += $scoreperlevel * 2;
                        break;
                    }
                // The user didn't reach the activity max cognitive depth, continue with level 1.

                case 1:
                    // Cognitive depth level 1 is just accessing the activity.

                    if ($this->any_log($contextid, $user)) {
                        $score += $scoreperlevel;
                    }

                default:
            }
        }

        // To avoid decimal problems.
        if ($score > self::MAX_VALUE) {
            return self::MAX_VALUE;
        } else if ($score < self::MIN_VALUE) {
            return self::MIN_VALUE;
        }
        return $score;
    }

    /**
     * social_calculate_sample
     *
     * @param $sampleid
     * @param $tablename
     * @param bool $starttime
     * @param bool $endtime
     * @return float|int|null
     */
    protected function social_calculate_sample($sampleid, $tablename, $starttime = false, $endtime = false) {

        // May not be available.
        $user = $this->retrieve('user', $sampleid);

        if (!$useractivities = $this->get_student_activities($sampleid, $tablename, $starttime, $endtime)) {
            // Null if no activities.
            return null;
        }

        $scoreperactivity = (self::get_max_value() - self::get_min_value()) / count($useractivities);

        $score = self::get_min_value();

        foreach ($useractivities as $contextid => $cm) {
            // TODO Add support for other levels than 1.
            if ($this->any_log($contextid, $user)) {
                $score += $scoreperactivity;
            }
        }

        // To avoid decimal problems.
        if ($score > self::MAX_VALUE) {
            return self::MAX_VALUE;
        } else if ($score < self::MIN_VALUE) {
            return self::MIN_VALUE;
        }
        return $score;
    }

    /**
     * calculate_sample
     *
     * @param int $sampleid
     * @param string $tablename
     * @param bool $starttime
     * @param bool $endtime
     * @return float|int|null
     * @throws \coding_exception
     */
    public function calculate_sample($sampleid, $tablename, $starttime = false, $endtime = false) {
        if ($this->get_indicator_type() == self::INDICATOR_COGNITIVE) {
            return $this->cognitive_calculate_sample($sampleid, $tablename, $starttime, $endtime);
        } else if ($this->get_indicator_type() == self::INDICATOR_SOCIAL) {
            return $this->social_calculate_sample($sampleid, $tablename, $starttime, $endtime);
        }
        throw new \coding_exception("Indicator type is invalid.");
    }

    /**
     * Defines indicator type.
     *
     * @return mixed
     */
    abstract protected function get_indicator_type();
}
