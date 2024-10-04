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

namespace core_course\task;

defined('MOODLE_INTERNAL') || die();

use core\task\adhoc_task;

require_once($CFG->libdir . '/gradelib.php');

/**
 * Asynchronously regrade a course.
 *
 * @copyright 2024 onwards Catalyst IT Europe Ltd.
 * @author Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_course
 */
class regrade_final_grades extends adhoc_task {
    use \core\task\logging_trait;
    use \core\task\stored_progress_task_trait;

    /**
     * Create and return an instance of this task for a given course ID.
     *
     * @param int $courseid
     * @return self
     */
    public static function create(int $courseid): self {
        $task = new regrade_final_grades();
        $task->set_custom_data((object)['courseid' => $courseid]);
        $task->set_component('core');
        return $task;
    }

    /**
     * Run regrade_final_grades for the provided course id.
     *
     * @return void
     * @throws \dml_exception
     */
    public function execute(): void {
        $data = $this->get_custom_data();
        $this->start_stored_progress();
        $this->log_start("Recalculating grades for course ID {$data->courseid}");
        // Ensure the course exists.
        try {
            $course = get_course($data->courseid);
        } catch (\dml_missing_record_exception $e) {
            $this->log("Course with id {$data->courseid} not found. It may have been deleted. Skipping regrade.");
            return;
        }
        $this->log("Found course {$course->shortname}. Starting regrade.");
        $results = grade_regrade_final_grades($course->id, progress: $this->get_progress());
        if (is_array($results)) {
            $this->log('Errors reported during regrade:');
            foreach ($results as $id => $result) {
                $this->log("Grade item {$id}: {$result}", 2);
            }
        }
        $this->log_finish('Regrade complete.');
    }
}
