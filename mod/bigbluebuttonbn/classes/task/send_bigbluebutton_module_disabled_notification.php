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

declare(strict_types=1);

namespace mod_bigbluebuttonbn\task;

use core\task\adhoc_task;
use core\message\message;
use mod_bigbluebuttonbn\local\config;

/**
 * Ad-hoc task to send a notification related to the disabling of the BigBlueButton activity module.
 *
 * The ad-hoc tasks sends a notification to the administrator informing that the BigBlueButton activity module has
 * been disabled and they are required to confirm their acceptance of the data processing agreement prior to
 * re-enabling it.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2022 Mihail Geshoski <mihail@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class send_bigbluebutton_module_disabled_notification extends adhoc_task {

    /**
     * Execute the task.
     */
    public function execute(): void {
        $message = new message();
        $message->component = 'moodle';
        $message->name = 'notices';
        $message->userfrom = \core_user::get_noreply_user();
        $message->userto = get_admin();
        $message->notification = 1;
        $message->contexturl = (new \moodle_url('/admin/modules.php'))->out(false);
        $message->contexturlname = get_string('modsettings', 'admin');
        $message->subject = get_string('bigbluebuttondisablednotification_subject', 'mod_bigbluebuttonbn');
        $message->fullmessageformat = FORMAT_HTML;
        $message->fullmessagehtml = get_string('bigbluebuttondisablednotification', 'mod_bigbluebuttonbn',
            config::DEFAULT_DPA_URL);
        $message->smallmessage = strip_tags($message->fullmessagehtml);

        message_send($message);
    }
}
