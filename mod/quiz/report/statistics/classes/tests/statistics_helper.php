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
namespace quiz_statistics\tests;

/**
 * Test helper functions for statistics
 *
 * @package   quiz_statistics
 * @copyright 2023 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class statistics_helper {
    /**
     * Run any ad-hoc recalculation tasks that have been scheduled.
     *
     * We need a special function to do this as the tasks are deferred by one hour,
     * so we need to pass a custom $timestart argument.
     *
     * @param bool $discardoutput Capture and discard output from executed tasks?
     * @return void
     */
    public static function run_pending_recalculation_tasks(bool $discardoutput = false): void {
        while ($task = \core\task\manager::get_next_adhoc_task(
            time() + HOURSECS + 1,
            false,
            '\quiz_statistics\task\recalculate'
        )) {
            if ($discardoutput) {
                ob_start();
            }
            $task->execute();
            if ($discardoutput) {
                ob_end_clean();
            }
            \core\task\manager::adhoc_task_complete($task);
        }
    }

}
