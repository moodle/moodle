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

namespace tool_moodlenet\task;

/**
 * Ad-hoc task to send the notification to admin stating MoodleNet is automatically enabled after upgrade.
 *
 * @package   tool_moodlenet
 * @copyright 2022 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class send_enable_notification extends \core\task\adhoc_task {
    public function execute(): void {
        $message = new \core\message\message();
        $message->component = 'moodle';
        $message->name = 'notices';
        $message->userfrom = \core_user::get_noreply_user();
        $message->userto = get_admin();
        $message->notification = 1;
        $message->contexturl = (new \moodle_url('/admin/settings.php',
            ['section' => 'optionalsubsystems'], 'admin-enablemoodlenet'))->out(false);
        $message->contexturlname = get_string('advancedfeatures', 'admin');
        $message->subject = get_string('autoenablenotification_subject', 'tool_moodlenet');
        $message->fullmessageformat = FORMAT_HTML;
        $message->fullmessagehtml = get_string('autoenablenotification', 'tool_moodlenet', (object) [
            'settingslink' => (new \moodle_url('/admin/settings.php', ['section' => 'tool_moodlenet']))->out(false),
        ]);
        $message->smallmessage = strip_tags($message->fullmessagehtml);
        message_send($message);
    }
}
