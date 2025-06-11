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

namespace core\task;

/**
 * Simple task to automatically set the course visibility to shown when the course start date matches the current day.
 *
 * @package   core
 * @copyright 2023 Sara Arjona <sara@moodle.com> based on code from 2016 Tim Gagen and Amanda Doughty
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class show_started_courses_task extends scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('showstartedcoursestask', 'course');
    }

    /**
     * Update course visibility.
     * IMPORTANT: It only processes courses with start/end dates within the past 24 hours and with start/end dates higher than
     * the current one, to avoid updating the course visibility early.
     *
     * @return void
     */
    public function execute() {
        global $CFG, $DB;

        // Use the configured timezone.
        date_default_timezone_set($CFG->timezone);

        $start = time();

        // Get list of courses to update.
        mtrace('\n  Searching for courses to set visibility to ' . $this->get_trace_message() . ' ...');
        $fielddate = $this->get_field_date();
        // Only process courses with dates in the past 24 hours with start/end dates higher than the current, to avoid updating
        // the course visibility early.
        $select = "visible = :visibility AND
                   {$fielddate} BETWEEN :beginofday AND :endofday";
        $params = [
            // Get courses that have the opposite visibility to the one we want to set.
            'visibility' => !$this->get_visibility(),
            'beginofday' => strtotime('-1 day', $start),
            'endofday' => $start,
        ];
        $courses = $DB->get_recordset_select('course', $select, $params);
        $this->update_courses_visibility($courses, $this->get_visibility());
        $courses->close();

        $end = time();
        mtrace(($end - $start) / 60 . ' mins');
    }

    /**
     * Make course visible or hidden if the start date has become due.
     *
     * @param \moodle_recordset $courses
     * @param int $visibility The given courses will be set to this visibility
     * @return void
     */
    private function update_courses_visibility(\moodle_recordset $courses, int $visibility): void {
        global $DB;

        mtrace("\n  There are courses to change visibility...");
        foreach ($courses as $course) {
            if (!$DB->set_field('course', 'visible', $visibility, ['id' => $course->id])) {
                mtrace("    Error updating course visibility for {$course->id}: {$course->shortname}.");
            } else {
                mtrace("    {$course->id}: {$course->shortname} visibility is now '" . $this->get_trace_message() . "'");
                $this->trigger_event($course);
            }
        }
    }

    /**
     * Method to trigger a course event.
     *
     * @param \stdClass $course The course that has been updated.
     */
    private function trigger_event(\stdClass $course): void {
        $params = [
            'objectid' => $course->id,
            'context' => \context_course::instance($course->id),
            'other' => [
                'shortname' => $course->shortname,
                'fullname' => $course->fullname,
                'idnumber' => $course->idnumber,
            ],
        ];
        $event = call_user_func([$this->get_event_classname(), 'create'], $params);
        $event->add_record_snapshot('course', $course);
        $event->trigger();
    }

    /**
     * Get the database field where the date to check is stored (startdate for showing courses and enddate for hiding courses).
     *
     * @return string
     */
    protected function get_field_date(): string {
        return 'startdate';
    }

    /**
     * The expected visibility of the courses after running this task (show = 1 and hidden = 0).
     *
     * @return int
     */
    protected function get_visibility(): int {
        return 1;
    }

    /**
     * The text to display in the trace message about the action that has been applied to the course.
     *
     * @return string
     */
    protected function get_trace_message(): string {
        return 'Show';
    }

    /**
     * The event classname to be triggered for the courses that need to be updated.
     *
     * @return string
     */
    protected function get_event_classname(): string {
        return '\core\event\course_started';
    }
}
