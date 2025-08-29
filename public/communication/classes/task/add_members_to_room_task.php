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

namespace core_communication\task;

use core\task\adhoc_task;
use core_communication\processor;

/**
 * Class add_members_to_room_task to add the task to add members to the room and execute the task to action the addition.
 *
 * @package    core_communication
 * @copyright  2023 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class add_members_to_room_task extends adhoc_task {
    public function execute() {
        // Initialize the custom data operation to be used for the action.
        $data = $this->get_custom_data();

        // Call the communication api to action the operation.
        $communication = processor::load_by_id($data->commid);

        if ($communication === null) {
            mtrace("Skipping adding members to room because the instance does not exist");
            return;
        }

        $communication->get_room_user_provider()->add_members_to_room($data->userids);
    }

    /**
     * Queue the task for the next run.
     *
     * @param processor $communication The communication processor to perform the action on
     */
    public static function queue(
        processor $communication
    ): void {

        foreach (array_chunk($communication->get_instance_userids(), 30) as $userids) {
            // Add ad-hoc task to update the provider room.
            $task = new self();
            $task->set_custom_data([
                'commid' => $communication->get_id(),
                'userids' => $userids,
            ]);

            // Queue the task for the next run.
            \core\task\manager::queue_adhoc_task($task);
        }
    }
}
