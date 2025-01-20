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

namespace core_sms\task;

use core\task\adhoc_task;
use core_sms\message;

/**
 * Ad-hoc task to send an SMS.
 *
 * Please note: sensitive SMS should not be sent from this task, it's already handled via the SMS api to exclude them.
 *
 * @package    core_sms
 * @copyright  2025 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class send_sms_task extends adhoc_task {

    #[\Override]
    public function execute(): void {

        $smsdata = $this->get_custom_data();

        $manager = \core\di::get(\core_sms\manager::class);
        $message = $manager->get_message(
            filter: ['id' => $smsdata->messageid],
        );
        $manager->send_message(
            message: $message,
            async: false,
        );

    }

    /**
     * Queue the SMS.
     *
     * @param message $message The message object.
     */
    public static function queue(
        message $message,
    ): void {
        $task = new self();
        $task->set_custom_data(
            [
                'messageid' => $message->id,
            ]
        );
        \core\task\manager::queue_adhoc_task($task);
    }
}
