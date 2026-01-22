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

use core\task\manager;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use stdClass;

/**
 * Unit tests for reset_course
 *
 * @package   core_course
 * @copyright 2025 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(reset_course::class)]
#[CoversMethod(\core\task\stored_progress_task_trait::class, 'initialise_stored_progress')]
final class reset_course_test extends \advanced_testcase {
    /**
     * Skip processing if the course does not exist.
     */
    public function test_execute_course_not_found(): void {
        $this->resetAfterTest();
        // Define a reset task with dummy data.
        $resetdata = new stdClass();
        $resetdata->id = 100;
        $resetdata->reset_start_date_old = time();
        $resetdata->reset_start_date = time();
        $resetdata->reset_end_date = time();
        $resetdata->reset_end_date_old = time();
        $resetdata->reset_notes = true;
        $task = reset_course::create($resetdata);

        $this->expectOutputRegex("~Resetting course ID 100~");
        $this->expectOutputRegex("~Course with ID 100 not found. It may have been deleted. Skipping reset.~");
        // Run the task.
        $task->execute();
    }

    /**
     * Reset a course.
     */
    public function test_execute(): void {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/notes/lib.php');

        $this->resetAfterTest();

        $generator = $this->getDataGenerator();

        // Create test course and user, enrol one in the other.
        $course = $generator->create_course();
        $user = $generator->create_user();
        $roleid = $DB->get_field('role', 'id', ['shortname' => 'student'], MUST_EXIST);
        $generator->enrol_user($user->id, $course->id, $roleid);

        // Define a reset task.
        $resetdata = new stdClass();
        $resetdata->id = $course->id;
        $resetdata->reset_start_date_old = $course->startdate;
        $resetdata->reset_start_date = $course->startdate;
        $resetdata->reset_end_date = $course->enddate;
        $resetdata->reset_end_date_old = $course->enddate;
        $resetdata->reset_notes = true;
        $task = reset_course::create($resetdata);

        // Create a note that will be deleted by the reset.
        $note = (object) ['content' => 'Note 1', 'courseid' => $course->id];
        note_save($note);

        $this->assertTrue($DB->record_exists('post', ['module' => 'notes', 'courseid' => $course->id]));

        $this->expectOutputRegex("~Resetting course ID {$course->id}~");
        $this->expectOutputRegex("~Found course {$course->shortname}. Starting reset.~");
        $this->expectOutputRegex("~Delete notes~");
        $this->expectOutputRegex("~Reset complete~");
        // Run the task.
        $task->execute();

        // Verify the course has been reset.
        $this->assertFalse($DB->record_exists('post', ['module' => 'notes', 'courseid' => $course->id]));
    }

    /**
     * Get the task ID for a course.
     */
    public function test_get_taskid_for_course(): void {
        global $DB;
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();

        // Create test course and user, enrol one in the other.
        $course = $generator->create_course();
        $user = $generator->create_user();
        $roleid = $DB->get_field('role', 'id', ['shortname' => 'student'], MUST_EXIST);
        $generator->enrol_user($user->id, $course->id, $roleid);

        // There is no task ID yet, so we should get null.
        $this->assertNull(reset_course::get_taskid_for_course($course->id));

        // Define a reset task.
        $resetdata = new stdClass();
        $resetdata->id = $course->id;
        $resetdata->reset_start_date_old = $course->startdate;
        $resetdata->reset_start_date = $course->startdate;
        $resetdata->reset_end_date = $course->enddate;
        $resetdata->reset_end_date_old = $course->enddate;
        $resetdata->reset_notes = true;
        $task = reset_course::create($resetdata);
        $queuedid = manager::queue_adhoc_task($task);

        // Get the task ID from the database.
        $taskid = reset_course::get_taskid_for_course($course->id);
        $this->assertEquals($queuedid, $taskid);
    }

    /**
     * Initialising progress for an existing running task does not replace its progress record.
     *
     */
    public function test_initialise_stored_progress_preserves_running_progress(): void {
        global $DB;
        $this->resetAfterTest();

        $clock = $this->mock_clock_with_frozen();
        $resetdata = (object) ['id' => 123];

        $task = reset_course::create($resetdata);
        $taskid = manager::queue_adhoc_task($task, true);
        $this->assertIsInt($taskid);
        $task->set_id($taskid);
        $task->initialise_stored_progress();

        $progress = $DB->get_record('stored_progress', [], '*', MUST_EXIST);
        $DB->update_record('stored_progress', [
            'id' => $progress->id,
            'timestart' => $clock->time(),
            'percentcompleted' => 50,
        ]);
        $DB->set_field('task_adhoc', 'timestarted', $clock->time(), ['id' => $taskid]);

        $duplicatetask = reset_course::create($resetdata);
        $duplicateid = manager::queue_adhoc_task($duplicatetask, true);
        $this->assertEquals($taskid, $duplicateid);
        $duplicatetask->set_id($duplicateid);
        $duplicatetask->initialise_stored_progress();

        $updatedprogress = $DB->get_record('stored_progress', [], '*', MUST_EXIST);
        $this->assertEquals($progress->id, $updatedprogress->id);
        $this->assertEquals(50, $updatedprogress->percentcompleted);
    }
}
