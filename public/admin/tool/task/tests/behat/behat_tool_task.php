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
 * Behat step definitions for scheduled task administration.
 *
 * @package tool_task
 * @copyright 2017 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../../lib/behat/behat_base.php');

/**
 * Behat step definitions for scheduled task administration.
 *
 * @package tool_task
 * @copyright 2017 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_tool_task extends behat_base {

    /**
     * Set a fake fail delay for a scheduled task.
     *
     * @Given /^the scheduled task "(?P<task_name>[^"]+)" has a fail delay of "(?P<seconds_number>\d+)" seconds$/
     * @param string $task Task classname
     * @param int $seconds Fail delay time in seconds
     */
    public function scheduled_task_has_fail_delay_seconds($task, $seconds) {
        global $DB;
        $id = $DB->get_field('task_scheduled', 'id', ['classname' => $task], IGNORE_MISSING);
        if (!$id) {
            throw new Exception('Unknown scheduled task: ' . $task);
        }
        $DB->set_field('task_scheduled', 'faildelay', $seconds, ['id' => $id]);
    }

    /**
     * Set a scheduled task's next run time to the future (tomorrow).
     *
     * @Given /^the scheduled task "(?P<task_name>[^"]+)" has a next run time in the future$/
     * @param string $task Task classname
     */
    public function scheduled_task_has_next_run_time_in_future($task) {
        global $DB;
        if (!$DB->record_exists('task_scheduled', ['classname' => $task])) {
            throw new Exception('Unknown scheduled task: ' . $task);
        }
        $DB->set_field('task_scheduled', 'nextruntime', time() + DAYSECS, ['classname' => $task]);
    }

    /**
     * Set a scheduled task's next run time to the past (already due).
     *
     * @Given /^the scheduled task "(?P<task_name>[^"]+)" has a next run time in the past$/
     * @param string $task Task classname
     */
    public function scheduled_task_has_next_run_time_in_past($task) {
        global $DB;
        if (!$DB->record_exists('task_scheduled', ['classname' => $task])) {
            throw new Exception('Unknown scheduled task: ' . $task);
        }
        $DB->set_field('task_scheduled', 'nextruntime', time() - DAYSECS, ['classname' => $task]);
    }

    /**
     * Disable a scheduled task.
     *
     * @Given /^the scheduled task "(?P<task_name>[^"]+)" is disabled$/
     * @param string $task Task classname
     */
    public function scheduled_task_is_disabled($task) {
        global $DB;
        if (!$DB->record_exists('task_scheduled', ['classname' => $task])) {
            throw new Exception('Unknown scheduled task: ' . $task);
        }
        $DB->set_field('task_scheduled', 'disabled', 1, ['classname' => $task]);
    }

    /**
     * Enable a scheduled task.
     *
     * @Given /^the scheduled task "(?P<task_name>[^"]+)" is enabled$/
     * @param string $task Task classname
     */
    public function scheduled_task_is_enabled($task) {
        global $DB;
        if (!$DB->record_exists('task_scheduled', ['classname' => $task])) {
            throw new Exception('Unknown scheduled task: ' . $task);
        }
        $DB->set_field('task_scheduled', 'disabled', 0, ['classname' => $task]);
    }
}
