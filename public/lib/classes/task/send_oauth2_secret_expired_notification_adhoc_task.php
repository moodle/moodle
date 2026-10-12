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

use core\message\message;
use core_user;

/**
 * Adhoc task to render and deliver localized OAuth 2 secret expired notification.
 *
 * @package    core
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class send_oauth2_secret_expired_notification_adhoc_task extends adhoc_task {
    /**
     * Execute the adhoc task in the recipient user's localized context.
     *
     * @return void
     */
    public function execute(): void {
        global $OUTPUT, $USER;

        $data = $this->get_custom_data();

        // Formatted string representation of the secret expiry datetime in the recipient user's timezone.
        $secretexpireddatetime = userdate($data->secretexpiredtime, get_string('strftimedatetime', 'langconfig'));

        $managesecretsurl = \core\router\util::get_path_for_callable(
            [\core_admin\route\controller\oauth2\server\client_management::class, 'manage_client_secrets'],
            ['client' => $data->clientid]
        );

        $messagehtml = $OUTPUT->render_from_template(
            'core_admin/oauth2/server/client_secret_expired_notification',
            [
                'clientname' => $data->clientname,
                'secretexpireddatetime' => $secretexpireddatetime,
                'managesecretsurl' => $managesecretsurl->out(),
            ],
        );

        $eventdata = new message();
        $eventdata->component = 'moodle';
        $eventdata->name = 'oauth2_secret_expired';
        $eventdata->userfrom = core_user::get_noreply_user();
        $eventdata->subject = get_string('oauth2server_secretexpirednotificationsubject', 'admin', $data->clientname);
        $eventdata->fullmessage = html_to_text($messagehtml);
        $eventdata->fullmessageformat = FORMAT_HTML;
        $eventdata->fullmessagehtml = $messagehtml;
        $eventdata->smallmessage = get_string(
            'oauth2server_secretexpirednotificationintro',
            'admin',
            [
                'clientname' => $data->clientname,
                'secretexpireddatetime' => $secretexpireddatetime,
            ],
        );
        $eventdata->notification = 1;
        $eventdata->userto = $USER;

        message_send($eventdata);
    }
}
