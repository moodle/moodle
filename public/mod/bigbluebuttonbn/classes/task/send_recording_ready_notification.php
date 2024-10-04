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

namespace mod_bigbluebuttonbn\task;

use html_writer;

/**
 * Class containing the adhoc task to send a recording ready notification.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2021 Andrew Lyons <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class send_recording_ready_notification extends base_send_notification {
    /**
     * Get the notification type.
     *
     * @return string
     */
    protected function get_notification_type(): string {
        return 'recording_ready';
    }

    /**
     * Get the subject of the notification.
     *
     * @return string
     */
    protected function get_subject(): string {
        $instance = $this->get_instance();

        return get_string('notification_recording_ready_subject', 'mod_bigbluebuttonbn', $this->get_string_vars());
    }

    /**
     * Get the short summary message.
     *
     * @return string
     */
    protected function get_small_message(): string {
        return get_string('notification_recording_ready_small', 'mod_bigbluebuttonbn', $this->get_string_vars());
    }

    /**
     * Get the HTML message content.
     *
     * @return string
     */
    protected function get_html_message(): string {
        return html_writer::tag(
            'p',
            get_string('notification_recording_ready_html', 'mod_bigbluebuttonbn', $this->get_string_vars())
        );
    }

    /**
     * Get variables to make available to strings.
     *
     * @return array
     */
    protected function get_string_vars(): array {
        return [
            'course_fullname' => $this->instance->get_course()->fullname,
            'course_shortname' => $this->instance->get_course()->shortname,
            'name' => $this->instance->get_cm()->name,
            'link' => $this->instance->get_view_url()->out(),
        ];
    }
}
