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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quickmail\tasks;

defined('MOODLE_INTERNAL') || die();

use core\task\scheduled_task;
use block_quickmail_string;
use block_quickmail\repos\queued_repo;
use block_quickmail\tasks\send_message_adhoc_task;
use core\task\manager as task_manager;

class send_all_ready_messages_task extends scheduled_task {

    public function get_name() {
        return block_quickmail_string::get('send_all_ready_messages_task');
    }

    /*
     * This tasks queries for all messages that should be sent at the current time
     * and initiates sending of each
     * Note: this will in turn kick off subsequent scheduled tasks for each individual message delivery
     *
     * Required custom data: none
     */
    public function execute() {
        // Get all messages that are queued and ready to send.
        $messages = queued_repo::get_all_messages_to_send();

        // Iterate through each message.
        foreach ($messages as $message) {
            $message->mark_as_sending();

            // Create a job.
            $task = new send_message_adhoc_task();

            $task->set_custom_data([
                'message_id' => $message->get('id')
            ]);

            // Queue job.
            task_manager::queue_adhoc_task($task);
        }
    }

}
