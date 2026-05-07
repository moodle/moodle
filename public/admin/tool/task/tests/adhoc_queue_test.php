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

namespace tool_task\check;

use core\check\result;

/**
 * Tests for the adhocqueue class.
 *
 * @package    tool_task
 * @copyright  2025 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \tool_task\check\adhocqueue
 */
final class adhoc_queue_test extends \advanced_testcase {
    /**
     * Test the get_result method.
     *
     * Test that an ad-hoc task initially passes the queue check,
     * but fails once its nextruntime is forced far into the past.
     */
    public function test_get_result(): void {
        global $DB;

        $this->resetAfterTest(true);
        // Run the health check.
        $check = new adhocqueue();
        $result = $check->get_result();
        $this->assertEquals(result::OK, $result->get_status(), 'Empty adhoc queue is OK');

        $task = new \core\task\asynchronous_backup_task();
        $id = \core\task\manager::queue_adhoc_task($task);
        $result = $check->get_result();
        $this->assertEquals(result::INFO, $result->get_status(), 'Queue with tasks is info');

        // Make the task moderately old (> 10 mins, < 4 hours) to trigger a warning.
        $DB->update_record('task_adhoc', [
            'id' => $id,
            'nextruntime' => time() - 30 * MINSECS,
        ]);

        $result = $check->get_result();
        $this->assertEquals(result::WARNING, $result->get_status(), 'Queue with tasks older than 10 mins should warn');

        // Make the task old enough to trigger an error (> 4 hours).
        $DB->update_record('task_adhoc', [
            'id' => $id,
            'nextruntime' => time() - DAYSECS,
        ]);

        // Re-run the health check.
        $result = $check->get_result();
        $this->assertEquals(result::ERROR, $result->get_status(), 'Queue with old tasks should error');

        // Make the task have no more attempts.
        $DB->update_record('task_adhoc', [
            'id' => $id,
            'attemptsavailable' => 0,
        ]);

        // Re-run the health check.
        $result = $check->get_result();
        $this->assertEquals(result::OK, $result->get_status(), 'Queue with old tasks with no attempts should not error');
    }
}
