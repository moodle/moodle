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
 * Tests for log cleanup task.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2021 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

use tool_ally\logging\constants;
use tool_ally\logging\logger;
use tool_ally\task\cleanup_log_task;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/abstract_testcase.php');

/**
 * Tests for log cleanup task.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2021 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cleanup_log_task_test extends abstract_testcase {
    /**
     * Test the general behavior of the task execution.
     */
    public function test_execute() {
        global $DB;

        $this->resetAfterTest();

        // Log all log levels.
        set_config('logrange', constants::RANGE_ALL, 'tool_ally');
        $logger = logger::get();
        $logger->setlevelrange(constants::RANGE_ALL);

        $gen = $this->getDataGenerator()->get_plugin_generator('tool_ally');

        $logs = [];

        // Make our log entries.
        for ($i = -1; $i <= 10; $i++) {
            // We subtract an extra hour from the time just to make sure we don't hit a +/- second issue.
            $logs[$i] = $gen->create_log_entry(['time' => (time() - ($i * DAYSECS) - 3600)]);
        }

        $this->assertEquals(12, $DB->count_records('tool_ally_log'));

        $task = new cleanup_log_task();

        // Check for negatives and 0, should make no change.
        set_config('loglifetimedays', -100, 'tool_ally');
        $startwrites = $DB->perf_get_writes();
        $task->execute();
        // No writes should have happened, since -100 is "disabled".
        $this->assertEquals($startwrites, $DB->perf_get_writes());

        set_config('loglifetimedays', 0, 'tool_ally');
        $startwrites = $DB->perf_get_writes();
        $task->execute();
        // No writes should have happened, since 0 is "disabled".
        $this->assertEquals($startwrites, $DB->perf_get_writes());

        // Make sure we still have 12.
        $this->assertEquals(12, $DB->count_records('tool_ally_log'));

        // Now do some tests that involve actual values.
        set_config('loglifetimedays', 100, 'tool_ally');
        $startwrites = $DB->perf_get_writes();
        $task->execute();
        // Confirm there was a DB call, even though no actual deletes happen.
        $this->assertEquals($startwrites + 1, $DB->perf_get_writes());
        $this->assertEquals(12, $DB->count_records('tool_ally_log'));

        // Now actually delete some records and check them.
        set_config('loglifetimedays', 7, 'tool_ally');
        $task->execute();
        $this->assertEquals(8, $DB->count_records('tool_ally_log'));

        set_config('loglifetimedays', 3, 'tool_ally');
        $task->execute();
        $this->assertEquals(4, $DB->count_records('tool_ally_log'));

        // Make sure the correct logs exist.
        for ($i = -1; $i <= 2; $i++) {
            $this->assertTrue($DB->record_exists('tool_ally_log', ['id' => $logs[$i]->id]));
        }
        // And that the correct logs don't exist.
        for ($i = 3; $i <= 10; $i++) {
            $this->assertFalse($DB->record_exists('tool_ally_log', ['id' => $logs[$i]->id]));
        }

        set_config('loglifetimedays', 1, 'tool_ally');
        $task->execute();
        $this->assertEquals(2, $DB->count_records('tool_ally_log'));
    }
}
