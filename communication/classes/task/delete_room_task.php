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
 * Class delete_room_task to add a task to delete a room and execute the task to action the deletion.
 *
 * this task will be queued by the communication api and will use the communication handler api to action the deletion.
 *
 * @package    core_communication
 * @copyright  2023 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class delete_room_task extends adhoc_task {
    public function execute() {
        $data = $this->get_custom_data();

        $communication = processor::load_by_id($data->id);

        if ($communication === null) {
            mtrace("Skipping room creation because the instance does not exist");
            return;
        }

        // First remove the members from the room.
        $communication->get_room_user_provider()->remove_members_from_room($communication->get_instance_userids(true, true));
        // Now remove any mapping for users who are not in the room.
        $communication->delete_instance_non_synced_user_mapping($communication->get_instance_userids(false, true));

        // Now delete the room.
        if ($communication->get_room_provider()->delete_chat_room()) {
            $communication->delete_instance();
        }
    }

    /**
     * Queue the task for the next run.
     *
     * @param processor $communication The communication processor to perform the action on
     */
    public static function queue(
        processor $communication,
    ): void {

        // Add ad-hoc task to update the provider room.
        $task = new self();
        $task->set_custom_data([
            'id' => $communication->get_id(),
        ]);

        // Queue the task for the next run.
        \core\task\manager::queue_adhoc_task($task);
    }
}
