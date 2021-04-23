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

namespace tool_brickfield;

/**
 * Scheduler class.
 *
 * @package    tool_brickfield
 * @author     Mike Churchward <mike@brickfieldlabs.ie>
 * @copyright  2020 Brickfield Education Labs https://www.brickfield.ie
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL
 */
class scheduler {

    /**
     * Analysis has not been requested.
     */
    const STATUS_NOT_REQUESTED = 0;

    /**
     * Analysis has been requested but not submitted.
     */
    const STATUS_REQUESTED = 1;

    /**
     * Analysis has been submitted.
     */
    const STATUS_SUBMITTED = 2;

    /**
     * The data table used by the scheduler.
     */
    const DATA_TABLE = 'tool_brickfield_schedule';

    /** @var int $instanceid The specific instance id of the context e.g. courseid. */
    public $instanceid;

    /** @var int $contextlevel The context level of the instance id e.g. CONTEXT_COURSE / 50. */
    public $contextlevel;

    /**
     * Scheduler constructor.
     * @param int $instanceid
     * @param int $contextlevel
     */
    public function __construct(int $instanceid = 0, int $contextlevel = CONTEXT_COURSE) {
        $this->instanceid = $instanceid;
        $this->contextlevel = $contextlevel;
    }

    /**
     * Request this schedule object to be analyzed. Create the schedule if not present.
     * @return bool
     */
    public function request_analysis(): bool {
        global $DB;
        if (!$this->create_schedule()) {
            return false;
        }
        if ($DB->set_field(self::DATA_TABLE, 'status', self::STATUS_REQUESTED, $this->standard_search_params())) {
            if ($this->contextlevel == CONTEXT_COURSE) {
                $context = \context_course::instance($this->instanceid);
            } else {
                $context = \context_system::instance();
            }
            $event = \tool_brickfield\event\analysis_requested::create([
                'context' => $context,
                'other' => ['course' => $this->instanceid],
            ]);
            $event->trigger();

            return true;
        } else {
            return false;
        }
    }

    /**
     * Mark this schedule object as analyzed.
     * @return bool
     * @throws \dml_exception
     */
    public function mark_analyzed(): bool {
        global $DB;
        if (!$this->create_schedule()) {
            return false;
        }
        $DB->set_field(self::DATA_TABLE, 'status', self::STATUS_SUBMITTED, $this->standard_search_params());
        $DB->set_field(self::DATA_TABLE, 'timeanalyzed', time(), $this->standard_search_params());
        return true;
    }

    /**
     * Request this schedule object be added. Return true if already added, or the status of the insert operation.
     * @return bool
     */
    public function create_schedule(): bool {
        global $DB;
        if (!$this->is_in_schedule()) {
            $datarecord = $this->get_datarecord();
            return $DB->insert_record(self::DATA_TABLE, $datarecord, false);
        }
        return true;
    }

    /**
     * Request this schedule object be deleted.
     * @return bool
     */
    public function delete_schedule(): bool {
        global $DB;
        if ($this->is_in_schedule()) {
            return $DB->delete_records(self::DATA_TABLE, $this->standard_search_params());
        }
        return true;
    }

    /**
     * Return true if this schedule object is in the schedule.
     * @return bool
     */
    public function is_in_schedule(): bool {
        global $DB;
        return $DB->record_exists(self::DATA_TABLE, $this->standard_search_params());
    }

    /**
     * Return true if this schedule object has been requested to be analyzed.
     * @return bool
     */
    public function is_scheduled(): bool {
        global $DB;
        return $DB->record_exists(self::DATA_TABLE, $this->standard_search_params() + ['status' => self::STATUS_REQUESTED]);
    }

    /**
     * Return true if this schedule object has been submitted.
     * @return bool
     */
    public function is_submitted(): bool {
        global $DB;
        return $DB->record_exists(self::DATA_TABLE, $this->standard_search_params() + ['status' => self::STATUS_SUBMITTED]);
    }

    /**
     * Return true if this schedule object has been analyzed.
     * @return bool
     */
    public function is_analyzed(): bool {
        global $DB;

        // Future... If not a course analysis request, just return the schedule table status.
        if ($this->contextlevel != CONTEXT_COURSE) {
            return $this->is_submitted();
        }

        // A course is considered analyzed if it has been submitted and there is summary cache data for it.
        $sql = 'SELECT * ' .
            'FROM {' . self::DATA_TABLE . '} sch ' .
            'INNER JOIN {' . manager::DB_SUMMARY . '} sum ON sch.instanceid = sum.courseid ' .
            'WHERE (sch.contextlevel = :contextlevel) AND (sch.instanceid = :instanceid) AND (sch.status = :status)';
        if (!$DB->record_exists_sql($sql, $this->standard_search_params() + ['status' => self::STATUS_SUBMITTED])) {
            // It may have been created in a prior version, so check before returning false. If it was, add a record for it.
            if ($DB->record_exists(manager::DB_SUMMARY, ['courseid' => $this->instanceid])) {
                return $this->mark_analyzed();
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    /**
     * The nornal data parameters to search for.
     * @return array
     */
    protected function standard_search_params(): array {
        return ['contextlevel' => $this->contextlevel, 'instanceid' => $this->instanceid];
    }

    /**
     * Get the context id for the specified context level and instance.
     * @return int
     * @throws \dml_exception
     */
    protected function get_contextid(): int {
        global $DB;
        $contextid = $DB->get_field('context', 'id', $this->standard_search_params());
        if ($contextid === false) {
            $contextid = 0;
        }
        return $contextid;
    }

    /**
     * Create and return a datarecord object for the data table.
     * @param int $status
     * @return \stdClass
     * @throws \dml_exception
     */
    public function get_datarecord(int $status = self::STATUS_NOT_REQUESTED): \stdClass {
        $datarecord = new \stdClass();
        $datarecord->contextlevel = $this->contextlevel;
        $datarecord->instanceid = $this->instanceid;
        $datarecord->contextid = $this->get_contextid();
        $datarecord->status = $status;
        $datarecord->timeanalyzed = 0;
        $datarecord->timemodified = time();
        return $datarecord;
    }

    /**
     * Process all the course analysis requests, and mark them as analyzed. Limit the number of requests processed by time.
     * @throws \ReflectionException
     * @throws \dml_exception
     */
    public static function process_scheduled_requests() {
        global $DB;

        // Run a registration check.
        if (!(new registration())->validate()) {
            return;
        }

        $runtimemax = MINSECS * 5; // Only process requests for five minutes. May want to tie this to task schedule.
        $runtime = time();
        $requestset = $DB->get_recordset(self::DATA_TABLE, ['status' => self::STATUS_REQUESTED], 'timemodified ASC');
        foreach ($requestset as $request) {
            if ($request->contextlevel == CONTEXT_COURSE) {
                manager::find_new_or_updated_areas_per_course($request->instanceid);
                $request->status = self::STATUS_SUBMITTED;
                $request->timeanalyzed = time();
                $request->timemodified = time();
                $DB->update_record(self::DATA_TABLE, $request);
            }
            $runtime = time() - $runtime;
            if ($runtime >= $runtimemax) {
                break;
            }
        }
        $requestset->close();
    }

    // The following are static versions of the above functions for courses that do not require creating an object first.

    /**
     * Load all requested context types into the schedule as requested. Write records in groups of 100.
     * @param int $contextlevel
     * @return bool
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function initialize_schedule(int $contextlevel = CONTEXT_COURSE): bool {
        global $DB;

        $writelimit = 100;
        $recordcount = 0;
        $records = [];
        $scheduler = new scheduler(0, $contextlevel);
        $coursesset = $DB->get_recordset('course', null, 'id', 'id, id as courseid');
        foreach ($coursesset as $course) {
            $recordcount++;
            $scheduler->instanceid = $course->id;
            $records[] = $scheduler->get_datarecord(self::STATUS_REQUESTED);
            if ($recordcount >= $writelimit) {
                $DB->insert_records(self::DATA_TABLE, $records);
                $recordcount = 0;
                $records = [];
            }
        }

        if ($recordcount > 0) {
            $DB->insert_records(self::DATA_TABLE, $records);
        }
        $coursesset->close();

        return true;
    }

    /**
     * Request the specified course be analyzed.
     * @param int $courseid
     * @return bool
     */
    public static function request_course_analysis(int $courseid) {
        return (new scheduler($courseid))->request_analysis();
    }

    /**
     * Request the specified course be added.
     * @param int $courseid
     * @return bool
     */
    public static function create_course_schedule(int $courseid) {
        return (new scheduler($courseid))->create_schedule();
    }

    /**
     * Delete the specified course from the schedule.
     * @param int $courseid
     * @return bool
     */
    public static function delete_course_schedule(int $courseid) {
        return (new scheduler($courseid))->delete_schedule();
    }

    /**
     * Return true if the specified course is in the schedule.
     * @param int $courseid
     * @return bool
     */
    public static function is_course_in_schedule(int $courseid) {
        return (new scheduler($courseid))->is_in_schedule();
    }

    /**
     * Return true if the specified course is scheduled.
     * @param int $courseid
     * @return bool
     */
    public static function is_course_scheduled(int $courseid) {
        return (new scheduler($courseid))->is_scheduled();
    }

    /**
     * Return true if the specified course has been analyzed.
     * @param int $courseid
     * @return bool
     */
    public static function is_course_analyzed(int $courseid) {
        return (new scheduler($courseid))->is_analyzed();
    }
}
