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

namespace factor_sms\task;

use core\task\adhoc_task;
use moodle_url;

/**
 * Notification for admins to notify about the migration of SMS setup from MFA to SMS gateway plugins.
 *
 * @package    factor_sms
 * @copyright  2024 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sms_gateway_migration_notification extends adhoc_task {

    public function execute(): void {

        $siteadmins = get_admins();
        foreach ($siteadmins as $siteadmin) {
            $smsconfigureurl = new moodle_url('/sms/sms_gateways.php');
            $messagebody = get_string('notification:smsgatewaymigrationinfo', 'factor_sms', $smsconfigureurl);
            $messagesubject = get_string('notification:smsgatewaymigration', 'factor_sms');

            $message = new \core\message\message();
            $message->component = 'moodle';
            $message->name = 'notices';
            $message->userfrom = \core_user::get_noreply_user();
            $message->subject = $messagesubject;
            $message->fullmessageformat = FORMAT_HTML;
            $message->notification = 1;
            $message->userto = $siteadmin;
            $message->smallmessage = $messagesubject;
            $message->fullmessagehtml = $messagebody;
            $message->fullmessage = html_to_text($messagebody);
            $message->contexturl = $smsconfigureurl;
            $message->contexturlname = $messagesubject;

            // Send message.
            message_send($message);
        }
    }
}
