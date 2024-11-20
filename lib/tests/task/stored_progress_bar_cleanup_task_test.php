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

use core\output\stored_progress_bar;

/**
 * Unit tests for stored_progress_bar_cleanup
 *
 * @package   core
 * @copyright 2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \core\task\stored_progress_bar_cleanup_task
 */
final class stored_progress_bar_cleanup_task_test extends \advanced_testcase {
    /**
     * Clean up stored_progress records that were last updated over 24 hours ago.
     */
    public function test_execute(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $neverupdated = $generator->create_stored_progress();
        $updatednow = $generator->create_stored_progress(lastupdate: time());
        $updated23hours = $generator->create_stored_progress(lastupdate: time() - (HOURSECS * 23));
        $updated24hours = $generator->create_stored_progress(lastupdate: time() - DAYSECS - 1);

        $task = new stored_progress_bar_cleanup_task();
        $this->expectOutputRegex('/Deleted old stored_progress records/');
        $task->execute();

        $this->assertNotNull(stored_progress_bar::get_by_id($neverupdated->id));
        $this->assertNotNull(stored_progress_bar::get_by_id($updatednow->id));
        $this->assertNotNull(stored_progress_bar::get_by_id($updated23hours->id));
        $this->assertNull(stored_progress_bar::get_by_id($updated24hours->id));
    }
}
