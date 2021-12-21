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

use core\task\adhoc_task;
use mod_bigbluebuttonbn\output\instance_updated_message;

/**
 * Class containing the adhoc task to send the notification that an update was updated.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2021 Andrew Lyons <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class send_instance_update_notification extends send_notification {

    /** @var int The activity was created */
    const TYPE_CREATED = 0;

    /** @var int The activity was updated */
    const TYPE_UPDATED = 1;

    /**
     * Set the update type.
     *
     * @param int $type
     */
    public function set_update_type(int $type): void {
        $this->append_custom_data(['type' => $type]);
    }

    /**
     * Get the type of update.
     *
     * @return null|int
     */
    protected function get_update_type(): ?int {
        $data = (object) $this->get_custom_data();
        if (property_exists($data, 'type')) {
            return $data->type;
        }

        return self::TYPE_UPDATED;
    }

    /**
     * Get the notification type.
     *
     * @return string
     */
    protected function get_notification_type(): string {
        return 'instance_updated';
    }

    /**
     * Get the subject of the notification.
     *
     * @return string
     */
    protected function get_subject(): string {
        $instance = $this->get_instance();

        if ($this->get_update_type() == self::TYPE_CREATED) {
            $key = 'notification_instance_created_subject';
        } else {
            $key = 'notification_instance_updated_subject';
        }
        return get_string($key, 'mod_bigbluebuttonbn', $this->get_string_vars());
    }

    /**
     * Get the HTML message content.
     *
     * @return string
     */
    protected function get_html_message(): string {
        global $PAGE;

        $renderer = $PAGE->get_renderer('mod_bigbluebuttonbn', '', RENDERER_TARGET_HTMLEMAIL);
        return $renderer->render(new instance_updated_message($this->instance, $this->get_update_type()));
    }

    /**
     * Get the short summary message.
     *
     * @return string
     */
    protected function get_small_message(): string {
        if ($this->get_update_type() == self::TYPE_CREATED) {
            $key = 'notification_instance_created_small';
        } else {
            $key = 'notification_instance_updated_small';
        }
        return get_string($key, 'mod_bigbluebuttonbn', $this->get_string_vars());
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
