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

use core\hook\task\after_failed_task_max_delay;
use core\url;
use core_user;
use stdClass;

/**
 * Hook listener callbacks for tasks in core
 *
 * @package    core
 * @category   task
 * @copyright  2024 Raquel Ortega <raquel.ortega@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class failed_task_callbacks {

    /**
     * Callback to send a notification when the max fail delay of a task has been reached.
     *
     * @param after_failed_task_max_delay $hook
     */
    public static function send_failed_task_max_delay_message(after_failed_task_max_delay $hook): void {
        $task = $hook->get_task();

        $admins = get_admins();
        foreach ($admins as $admin) {
            $tasklogslink = new url('/admin/tasklogs.php', ['filter' => get_class($task)]);

            $a = new stdClass();
            $a->firstname = $admin->firstname;
            $a->taskname = $task->get_name();
            $a->link = $tasklogslink->out(false);
            $messagetxt = get_string('failedtaskbody', 'moodle', $a);
            // Create message.
            $message = new \core\message\message();
            $message->component = 'moodle';
            $message->name = 'failedtaskmaxdelay';
            $message->userfrom = core_user::get_noreply_user();
            $message->userto = $admin;
            $message->subject = get_string('failedtasksubject', 'moodle', $task->get_name());
            $message->fullmessage = html_to_text($messagetxt);
            $message->fullmessageformat = FORMAT_MARKDOWN;
            $message->fullmessagehtml = text_to_html($messagetxt);
            $message->smallmessage = get_string('failedtasksubject', 'moodle', $task->get_name());
            $message->notification = 1;
            $message->contexturl = $tasklogslink->out(false);
            $message->contexturlname = get_string('tasklogs', 'admin');
            // Actually send the message.
            message_send($message);
        }
    }
}
