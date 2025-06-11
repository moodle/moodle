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
 * Moodle course analysable
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/lib/gradelib.php');
require_once($CFG->dirroot . '/lib/enrollib.php');

/**
 * Moodle course analysable
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course implements \core_analytics\analysable {

    /**
     * @var bool Has this course data been already loaded.
     */
    protected $loaded = false;

    /**
     * @var int $cachedid self::$cachedinstance analysable id.
     */
    protected static $cachedid = 0;

    /**
     * @var \core_analytics\course $cachedinstance
     */
    protected static $cachedinstance = null;

    /**
     * Course object
     *
     * @var \stdClass
     */
    protected $course = null;

    /**
     * The course context.
     *
     * @var \context_course
     */
    protected $coursecontext = null;

    /**
     * The course activities organized by activity type.
     *
     * @var array
     */
    protected $courseactivities = array();

    /**
     * Course start time.
     *
     * @var int
     */
    protected $starttime = null;


    /**
     * Has the course already started?
     *
     * @var bool
     */
    protected $started = null;

    /**
     * Course end time.
     *
     * @var int
     */
    protected $endtime = null;

    /**
     * Is the course finished?
     *
     * @var bool
     */
    protected $finished = null;

    /**
     * Course students ids.
     *
     * @var int[]
     */
    protected $studentids = [];


    /**
     * Course teachers ids
     *
     * @var int[]
     */
    protected $teacherids = [];

    /**
     * Cached copy of the total number of logs in the course.
     *
     * @var int
     */
    protected $ntotallogs = null;

    /** @var int Store current Unix timestamp. */
    protected int $now = 0;

    /**
     * Course manager constructor.
     *
     * Use self::instance() instead to get cached copies of the course. Instances obtained
     * through this constructor will not be cached.
     *
     * @param int|\stdClass $course Course id or mdl_course record
     * @param \context|null $context
     * @return void
     */
    public function __construct($course, ?\context $context = null) {

        if (is_scalar($course)) {
            $this->course = new \stdClass();
            $this->course->id = $course;
        } else {
            $this->course = $course;
        }

        if (!is_null($context)) {
            $this->coursecontext = $context;
        }
    }

    /**
     * Returns an analytics course instance.
     *
     * Lazy load of course data, students and teachers.
     *
     * @param int|\stdClass $course Course object or course id
     * @param \context|null $context
     * @return \core_analytics\course
     */
    public static function instance($course, ?\context $context = null) {

        $courseid = $course;
        if (!is_scalar($courseid)) {
            $courseid = $course->id;
        }

        if (self::$cachedid === $courseid) {
            return self::$cachedinstance;
        }

        $cachedinstance = new \core_analytics\course($course, $context);
        self::$cachedinstance = $cachedinstance;
        self::$cachedid = (int)$courseid;
        return self::$cachedinstance;
    }

    /**
     * get_id
     *
     * @return int
     */
    public function get_id() {
        return $this->course->id;
    }

    /**
     * Loads the analytics course object.
     *
     * @return void
     */
    protected function load() {

        // The instance constructor could be already loaded with the full course object. Using shortname
        // because it is a required course field.
        if (empty($this->course->shortname)) {
            $this->course = get_course($this->course->id);
        }

        $this->coursecontext = $this->get_context();

        $this->now = time();

        // Get the course users, including users assigned to student and teacher roles at an higher context.
        $cache = \cache::make_from_params(\cache_store::MODE_REQUEST, 'core_analytics', 'rolearchetypes');

        // Flag the instance as loaded.
        $this->loaded = true;

        if (!$studentroles = $cache->get('student')) {
            $studentroles = array_keys(get_archetype_roles('student'));
            $cache->set('student', $studentroles);
        }
        $this->studentids = $this->get_user_ids($studentroles);

        if (!$teacherroles = $cache->get('teacher')) {
            $teacherroles = array_keys(get_archetype_roles('editingteacher') + get_archetype_roles('teacher'));
            $cache->set('teacher', $teacherroles);
        }
        $this->teacherids = $this->get_user_ids($teacherroles);
    }

    /**
     * The course short name
     *
     * @return string
     */
    public function get_name() {
        return format_string($this->get_course_data()->shortname, true, array('context' => $this->get_context()));
    }

    /**
     * get_context
     *
     * @return \context
     */
    public function get_context() {
        if ($this->coursecontext === null) {
            $this->coursecontext = \context_course::instance($this->course->id);
        }
        return $this->coursecontext;
    }

    /**
     * Get the course start timestamp.
     *
     * @return int Timestamp or 0 if has not started yet.
     */
    public function get_start() {

        if ($this->starttime !== null) {
            return $this->starttime;
        }

        // The field always exist but may have no valid if the course is created through a sync process.
        if (!empty($this->get_course_data()->startdate)) {
            $this->starttime = (int)$this->get_course_data()->startdate;
        } else {
            $this->starttime = 0;
        }

        return $this->starttime;
    }

    /**
     * Guesses the start of the course based on students' activity and enrolment start dates.
     *
     * @return int
     */
    public function guess_start() {
        global $DB;

        if (!$this->get_total_logs()) {
            // Can't guess.
            return 0;
        }

        if (!$logstore = \core_analytics\manager::get_analytics_logstore()) {
            return 0;
        }

        // We first try to find current course student logs.
        $firstlogs = array();
        foreach ($this->get_students() as $studentid) {
            // Grrr, we are limited by logging API, we could do this easily with a
            // select min(timecreated) from xx where courseid = yy group by userid.

            // Filters based on the premise that more than 90% of people will be using
            // standard logstore, which contains a userid, contextlevel, contextinstanceid index.
            $select = "userid = :userid AND contextlevel = :contextlevel AND contextinstanceid = :contextinstanceid";
            $params = array('userid' => $studentid, 'contextlevel' => CONTEXT_COURSE, 'contextinstanceid' => $this->get_id());
            $events = $logstore->get_events_select($select, $params, 'timecreated ASC', 0, 1);
            if ($events) {
                $event = reset($events);
                $firstlogs[] = $event->timecreated;
            }
        }
        if (empty($firstlogs)) {
            // Can't guess if no student accesses.
            return 0;
        }

        sort($firstlogs);
        $firstlogsmedian = $this->median($firstlogs);

        $studentenrolments = enrol_get_course_users($this->get_id(), $this->get_students());
        if (empty($studentenrolments)) {
            return 0;
        }

        $enrolstart = array();
        foreach ($studentenrolments as $studentenrolment) {
            $enrolstart[] = ($studentenrolment->uetimestart) ? $studentenrolment->uetimestart : $studentenrolment->uetimecreated;
        }
        sort($enrolstart);
        $enrolstartmedian = $this->median($enrolstart);

        return intval(($enrolstartmedian + $firstlogsmedian) / 2);
    }

    /**
     * Get the course end timestamp.
     *
     * @return int Timestamp or 0 if time end was not set.
     */
    public function get_end() {
        global $DB;

        if ($this->endtime !== null) {
            return $this->endtime;
        }

        // The enddate field is only available from Moodle 3.2 (MDL-22078).
        if (!empty($this->get_course_data()->enddate)) {
            $this->endtime = (int)$this->get_course_data()->enddate;
            return $this->endtime;
        }

        return 0;
    }

    /**
     * Get the course end timestamp.
     *
     * @return int Timestamp, \core_analytics\analysable::MAX_TIME if we don't know but ongoing and 0 if we can not work it out.
     */
    public function guess_end() {
        global $DB;

        if ($this->get_total_logs() === 0) {
            // No way to guess if there are no logs.
            $this->endtime = 0;
            return $this->endtime;
        }

        list($filterselect, $filterparams) = $this->course_students_query_filter('ula');

        // Consider the course open if there are still student accesses.
        $monthsago = time() - (WEEKSECS * 4 * 2);
        $select = $filterselect . ' AND timeaccess > :timeaccess';
        $params = $filterparams + array('timeaccess' => $monthsago);
        $sql = "SELECT DISTINCT timeaccess FROM {user_lastaccess} ula
                  JOIN {enrol} e ON e.courseid = ula.courseid
                  JOIN {user_enrolments} ue ON e.id = ue.enrolid AND ue.userid = ula.userid
                 WHERE $select";
        if ($records = $DB->get_records_sql($sql, $params)) {
            return 0;
        }

        $sql = "SELECT DISTINCT timeaccess FROM {user_lastaccess} ula
                  JOIN {enrol} e ON e.courseid = ula.courseid
                  JOIN {user_enrolments} ue ON e.id = ue.enrolid AND ue.userid = ula.userid
                 WHERE $filterselect AND ula.timeaccess != 0
                 ORDER BY timeaccess DESC";
        $studentlastaccesses = $DB->get_fieldset_sql($sql, $filterparams);
        if (empty($studentlastaccesses)) {
            return 0;
        }
        sort($studentlastaccesses);

        return $this->median($studentlastaccesses);
    }

    /**
     * Returns a course plain object.
     *
     * @return \stdClass
     */
    public function get_course_data() {

        if (!$this->loaded) {
            $this->load();
        }

        return $this->course;
    }

    /**
     * Has the course started?
     *
     * @return bool
     */
    public function was_started() {

        if ($this->started === null) {
            if ($this->get_start() === 0 || $this->now < $this->get_start()) {
                // Not yet started.
                $this->started = false;
            } else {
                $this->started = true;
            }
        }

        return $this->started;
    }

    /**
     * Has the course finished?
     *
     * @return bool
     */
    public function is_finished() {

        if ($this->finished === null) {
            $endtime = $this->get_end();
            if ($endtime === 0 || $this->now < $endtime) {
                // It is not yet finished or no idea when it finishes.
                $this->finished = false;
            } else {
                $this->finished = true;
            }
        }

        return $this->finished;
    }

    /**
     * Returns a list of user ids matching the specified roles in this course.
     *
     * @param array $roleids
     * @return array
     */
    public function get_user_ids($roleids) {

        // We need to index by ra.id as a user may have more than 1 $roles role.
        $records = get_role_users($roleids, $this->get_context(), true, 'ra.id, u.id AS userid, r.id AS roleid', 'ra.id ASC');

        // If a user have more than 1 $roles role array_combine will discard the duplicate.
        $callable = array($this, 'filter_user_id');
        $userids = array_values(array_map($callable, $records));
        return array_combine($userids, $userids);
    }

    /**
     * Returns the course students.
     *
     * @return int[]
     */
    public function get_students() {

        if (!$this->loaded) {
            $this->load();
        }

        return $this->studentids;
    }

    /**
     * Returns the total number of student logs in the course
     *
     * @return int
     */
    public function get_total_logs() {
        global $DB;

        // No logs if no students.
        if (empty($this->get_students())) {
            return 0;
        }

        if ($this->ntotallogs === null) {
            list($filterselect, $filterparams) = $this->course_students_query_filter();
            if (!$logstore = \core_analytics\manager::get_analytics_logstore()) {
                $this->ntotallogs = 0;
            } else {
                $this->ntotallogs = $logstore->get_events_select_count($filterselect, $filterparams);
            }
        }

        return $this->ntotallogs;
    }

    /**
     * Returns all the activities of the provided type the course has.
     *
     * @param string $activitytype
     * @return array
     */
    public function get_all_activities($activitytype) {

        // Using is set because we set it to false if there are no activities.
        if (!isset($this->courseactivities[$activitytype])) {
            $modinfo = get_fast_modinfo($this->get_course_data(), -1);
            $instances = $modinfo->get_instances_of($activitytype);

            if ($instances) {
                $this->courseactivities[$activitytype] = array();
                foreach ($instances as $instance) {
                    // By context.
                    $this->courseactivities[$activitytype][$instance->context->id] = $instance;
                }
            } else {
                $this->courseactivities[$activitytype] = false;
            }
        }

        return $this->courseactivities[$activitytype];
    }

    /**
     * Returns the course students grades.
     *
     * @param array $courseactivities
     * @return array
     */
    public function get_student_grades($courseactivities) {

        if (empty($courseactivities)) {
            return array();
        }

        $grades = array();
        foreach ($courseactivities as $contextid => $instance) {
            $gradesinfo = grade_get_grades($this->course->id, 'mod', $instance->modname, $instance->instance, $this->studentids);

            // Sort them by activity context and user.
            if ($gradesinfo && $gradesinfo->items) {
                foreach ($gradesinfo->items as $gradeitem) {
                    foreach ($gradeitem->grades as $userid => $grade) {
                        if (empty($grades[$contextid][$userid])) {
                            // Initialise it as array because a single activity can have multiple grade items (e.g. workshop).
                            $grades[$contextid][$userid] = array();
                        }
                        $grades[$contextid][$userid][$gradeitem->id] = $grade;
                    }
                }
            }
        }

        return $grades;
    }

    /**
     * Used by get_user_ids to extract the user id.
     *
     * @param \stdClass $record
     * @return int The user id.
     */
    protected function filter_user_id($record) {
        return $record->userid;
    }

    /**
     * Returns the average time between 2 timestamps.
     *
     * @param int $start
     * @param int $end
     * @return array [starttime, averagetime, endtime]
     */
    protected function update_loop_times($start, $end) {
        $avg = intval(($start + $end) / 2);
        return array($start, $avg, $end);
    }

    /**
     * Returns the query and params used to filter the logstore by this course students.
     *
     * @param string $prefix
     * @return array
     */
    protected function course_students_query_filter($prefix = false) {
        global $DB;

        if ($prefix) {
            $prefix = $prefix . '.';
        }

        // Check the amount of student logs in the 4 previous weeks.
        list($studentssql, $studentsparams) = $DB->get_in_or_equal($this->get_students(), SQL_PARAMS_NAMED);
        $filterselect = $prefix . 'courseid = :courseid AND ' . $prefix . 'userid ' . $studentssql;
        $filterparams = array('courseid' => $this->course->id) + $studentsparams;

        return array($filterselect, $filterparams);
    }

    /**
     * Calculate median
     *
     * Keys are ignored.
     *
     * @param int[]|float[] $values Sorted array of values
     * @return int
     */
    protected function median($values) {
        $count = count($values);

        if ($count === 1) {
            return reset($values);
        }

        $middlevalue = (int)floor(($count - 1) / 2);

        if ($count % 2) {
            // Odd number, middle is the median.
            $median = $values[$middlevalue];
        } else {
            // Even number, calculate avg of 2 medians.
            $low = $values[$middlevalue];
            $high = $values[$middlevalue + 1];
            $median = (($low + $high) / 2);
        }
        return intval($median);
    }
}
