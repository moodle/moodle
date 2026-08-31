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

namespace core_ai\task;

/**
 * Test the backfill_action_courseid adhoc task.
 *
 * @package    core_ai
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core_ai\task\backfill_action_courseid
 */
final class backfill_action_courseid_test extends \advanced_testcase {
    /**
     * Insert a bare ai_action_register row with the given contextid, defaulting courseid to 0
     * as it would be immediately after the upgrade step that adds the column.
     *
     * @param int $contextid
     * @return int The id of the inserted row.
     */
    private function insert_unbackfilled_row(int $contextid): int {
        global $DB;

        return $DB->insert_record('ai_action_register', (object) [
            'actionname' => 'generate_text',
            'actionid' => 1,
            'success' => 1,
            'userid' => 1,
            'contextid' => $contextid,
            'provider' => 'aiprovider_openai',
            'timecreated' => time(),
            'timecompleted' => time(),
            'courseid' => 0,
        ]);
    }

    /**
     * Test the task backfills courseid for rows with a course-resolvable context.
     */
    public function test_execute_backfills_course_context(): void {
        $this->resetAfterTest();
        global $DB;

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);

        $id = $this->insert_unbackfilled_row($coursecontext->id);

        $task = new backfill_action_courseid();
        $this->expectOutputRegex('/^Backfilled courseid for 1 ai_action_register rows\.\n$/');
        $task->execute();

        $this->assertEquals($course->id, $DB->get_field('ai_action_register', 'courseid', ['id' => $id]));
    }

    /**
     * Test the task marks rows that do not resolve to a course as -1, so they are not reprocessed.
     */
    public function test_execute_marks_non_course_context_as_resolved(): void {
        $this->resetAfterTest();
        global $DB;

        $id = $this->insert_unbackfilled_row(\context_system::instance()->id);

        $task = new backfill_action_courseid();
        $this->expectOutputRegex('/^Backfilled courseid for 1 ai_action_register rows\.\n$/');
        $task->execute();

        $this->assertEquals(-1, $DB->get_field('ai_action_register', 'courseid', ['id' => $id]));
    }

    /**
     * Test the task leaves already-backfilled rows (courseid != 0) untouched.
     */
    public function test_execute_does_not_reprocess_backfilled_rows(): void {
        $this->resetAfterTest();
        global $DB;

        $course = $this->getDataGenerator()->create_course();
        $id = $this->insert_unbackfilled_row(\context_system::instance()->id);
        $DB->set_field('ai_action_register', 'courseid', $course->id, ['id' => $id]);

        $task = new backfill_action_courseid();
        $this->expectOutputRegex('/^Backfilled courseid for 0 ai_action_register rows\.\n$/');
        $task->execute();

        // Still the course id we manually set, not re-resolved to -1 for the system context.
        $this->assertEquals($course->id, $DB->get_field('ai_action_register', 'courseid', ['id' => $id]));
    }
}
