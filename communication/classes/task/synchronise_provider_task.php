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
 * Class synchronise_provider_task to add a task to synchronise the provider and execute the task to action the synchronisation.
 *
 * @package    core_communication
 * @copyright  2023 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class synchronise_provider_task extends adhoc_task {

    public function execute() {
        $data = $this->get_custom_data();

        $communication = processor::load_by_id($data->id);

        if ($communication === null) {
            mtrace("Skipping provider sync because the instance does not exist");
            return;
        }

        // Sync room members for the instance.
        $communication->get_sync_provider()->synchronise_room_members();
    }

    /**
     * Queue the task for the next run.
     *
     * @param processor $communication The communication processor to perform the action on
     */
    public static function queue(
        processor $communication
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
