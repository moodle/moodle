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

use core\task\adhoc_task;
use mod_assign\notification_helper;

/**
 * Ad-hoc task to queue another task for notifying a user about an overdue assignment.
 *
 * @package    mod_assign
 * @copyright  2024 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class queue_assignment_overdue_notification_tasks_for_users extends adhoc_task {

    /**
     * Execute the task.
     */
    public function execute(): void {
        $assignmentid = $this->get_custom_data()->id;
        $type = notification_helper::TYPE_OVERDUE;
        $users = notification_helper::get_users_within_assignment($assignmentid, $type);
        foreach ($users as $user) {
            $task = new send_assignment_overdue_notification_to_user();
            $task->set_custom_data([
                'assignmentid' => $assignmentid,
                'userid' => $user->id,
            ]);
            $task->set_userid($user->id);
            \core\task\manager::queue_adhoc_task($task, true);
        }
    }
}
