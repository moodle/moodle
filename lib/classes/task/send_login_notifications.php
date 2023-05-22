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

/**
 * Adhoc task that send login notifications.
 *
 * @package    core
 * @copyright  2021 Moodle Pty Ltd.
 * @author     Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class send_login_notifications extends adhoc_task {

    use \core\task\logging_trait;

    /**
     * Run the adhoc task and preform the backup.
     */
    public function execute() {
        global $CFG, $DB, $SITE, $USER, $PAGE;

        $customdata = $this->get_custom_data();

        // First check the mobile app special case, to detect if the user is not using a new device after login from a different IP.
        if (!empty($customdata->ismoodleapp)) {
            $where = 'userid = ? AND timecreated >= ?';
            if (!$DB->count_records_select('user_devices', $where, [$USER->id, $customdata->logintime])) {
                // Do nothing, seems to be the same person doing login from a new IP using a known device.
                return;
            }
        }

        $this->log_start("Sending login notification to {$USER->username}");
        $sitename = format_string($SITE->fullname);
        $siteurl = $CFG->wwwroot;
        $userfullname = fullname($USER);
        $username = $USER->username;
        $useremail = ($USER->username != $USER->email) ? $USER->email : '';
        $logindevice = $customdata->ismoodleapp ? get_string('mobileapp', 'tool_mobile') : '';
        $logindevice .= ' ' . $customdata->useragent;
        $loginip = $customdata->loginip;
        $logintime = userdate($customdata->logintime);

        $changepasswordlink = (new \moodle_url('/user/preferences.php', ['userid' => $USER->id]))->out(false);
        // Find a better final URL for changing password.
        $userauth = get_auth_plugin($USER->auth);
        if ($userauth->can_change_password()) {
            if ($changepwurl = $userauth->change_password_url()) {
                $changepasswordlink = $changepwurl;
            } else {
                $changepasswordlink = (new \moodle_url('/login/change_password.php'))->out(false);
            }
        }

        $eventdata = new \core\message\message();
        $eventdata->courseid          = SITEID;
        $eventdata->component         = 'moodle';
        $eventdata->name              = 'newlogin';
        $eventdata->userfrom          = \core_user::get_noreply_user();
        $eventdata->userto            = $USER;
        $eventdata->notification      = 1;
        $eventdata->subject           = get_string('newloginnotificationtitle', 'moodle', $sitename);
        $eventdata->fullmessageformat = FORMAT_HTML;
        $info = compact('sitename', 'siteurl', 'userfullname', 'username', 'useremail',
            'logindevice', 'logintime', 'loginip', 'changepasswordlink');
        $eventdata->fullmessagehtml   = get_string('newloginnotificationbodyfull', 'moodle', $info);
        $eventdata->fullmessage       = html_to_text($eventdata->fullmessagehtml);
        $eventdata->smallmessage      = get_string('newloginnotificationbodysmall', 'moodle', $username);

        $userpicture = new \user_picture($USER);
        $userpicture->size = 1; // Use f1 size.
        $userpicture->includetoken = $USER->id; // Generate an out-of-session token for the user receiving the message.
        $eventdata->customdata = ['notificationiconurl' => $userpicture->get_url($PAGE)->out(false)];

        if (message_send($eventdata)) {
            $this->log_finish("Notification successfully sent");
        } else {
            $this->log_finish("Failed to send notification");
        }
    }
}
