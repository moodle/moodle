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
 * Adhoc task to render and deliver localized OAuth 2 secret expiry notifications.
 *
 * @package    core
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class send_oauth2_secret_expiry_notification_adhoc_task extends adhoc_task {
    /**
     * Execute the adhoc task in the recipient user's localized context.
     *
     * @return void
     */
    public function execute(): void {
        global $OUTPUT, $USER;

        // The task runs as the recipient (see set_userid()), so they are available in $USER.
        $data = $this->get_custom_data();

        // Determine localized subject string.
        if ($data->expirydays > 1) {
            $subject = get_string(
                'oauth2server_secretexpireindaysnotificationsubject',
                'admin',
                (object) [
                    'clientname' => $data->clientname,
                    'expirydays' => $data->expirydays,
                ]
            );
        } else {
            $subject = get_string(
                'oauth2server_secretexpireindaynotificationsubject',
                'admin',
                $data->clientname
            );
        }

        // Determine localized warning text string.
        $warningtext = $this->get_expiry_warning($data->clientname, $data->expirydays, $data->secretcount);

        $managesecretsurl = \core\router\util::get_path_for_callable(
            [\core_admin\route\controller\oauth2\server\client_management::class, 'manage_client_secrets'],
            ['client' => $data->clientid]
        );

        $actiontext = get_string('oauth2server_rotateexpiringsecrets', 'admin', $managesecretsurl->out());

        $messagehtml = $OUTPUT->render_from_template(
            'core_admin/oauth2/server/client_secret_expiry_notification',
            [
                'warningtext' => $warningtext,
                'actiontext' => $actiontext,
            ]
        );

        $eventdata = new message();
        $eventdata->component = 'moodle';
        $eventdata->name = 'oauth2_secret_expiry_warning';
        $eventdata->userfrom = core_user::get_noreply_user();
        $eventdata->subject = $subject;
        $eventdata->fullmessage = html_to_text($messagehtml);
        $eventdata->fullmessageformat = FORMAT_HTML;
        $eventdata->fullmessagehtml = $messagehtml;
        $eventdata->smallmessage = $warningtext;
        $eventdata->notification = 1;
        $eventdata->userto = $USER;

        message_send($eventdata);
    }

    /**
     * Get the appropriate localized warning message based on the defined expiry period and secret count.
     *
     * @param string $clientname Name of the client.
     * @param int $days Number of days before expiry.
     * @param int $count Number of secrets expiring.
     * @return string
     */
    private function get_expiry_warning(string $clientname, int $days, int $count): string {
        if ($days > 1) {
            if ($count > 1) {
                return get_string(
                    'oauth2server_secretsexpireindays',
                    'admin',
                    (object) [
                        'clientname' => $clientname,
                        'secretcount' => $count,
                        'expirydays' => $days,
                    ]
                );
            }

            return get_string(
                'oauth2server_secretexpiresindays',
                'admin',
                (object) [
                    'clientname' => $clientname,
                    'expirydays' => $days,
                ]
            );
        }

        if ($count > 1) {
            return get_string(
                'oauth2server_secretsexpireinday',
                'admin',
                (object) [
                    'clientname' => $clientname,
                    'secretcount' => $count,
                ]
            );
        }

        return get_string('oauth2server_secretexpiresinday', 'admin', $clientname);
    }
}
