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

namespace mod_assign\task;

use core\task\scheduled_task;
use mod_assign\notification_helper;

/**
 * Scheduled task to queue tasks for notifying about assignments with an approaching due date.
 *
 * @package    mod_assign
 * @copyright  2024 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class queue_all_assignment_due_soon_notification_tasks extends scheduled_task {

    /**
     * Return the task name.
     *
     * @return string The name of the task.
     */
    public function get_name(): string {
        return get_string('sendnotificationduedatesoon', 'mod_assign');
    }

    /**
     * Execute the task.
     */
    public function execute(): void {
        $assignments = notification_helper::get_due_soon_assignments();
        foreach ($assignments as $assignment) {
            $task = new queue_assignment_due_soon_notification_tasks_for_users();
            $task->set_custom_data($assignment);
            \core\task\manager::queue_adhoc_task($task, true);
        }
        $assignments->close();
    }
}
