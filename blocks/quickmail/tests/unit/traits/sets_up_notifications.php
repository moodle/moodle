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

/**
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Notification test helpers.
use block_quickmail\persistents\reminder_notification;
use block_quickmail\persistents\event_notification;

trait sets_up_notifications {

    // Notification creation.
    public function create_reminder_notification_for_course_user($modelkey, $course, $user, $object = null, $overrides = []) {
        $params = $this->get_reminder_notification_params([], $overrides);

        $notification = reminder_notification::create_type($modelkey, $course, $user, $params, $object);

        return $notification;
    }

    public function create_event_notification_for_course_user($modelkey, $course, $user, $object = null, $overrides = []) {
        $params = $this->get_event_notification_params([], $overrides);

        $notification = event_notification::create_type($modelkey, $course, $user, $params, $object);

        return $notification;
    }

    // Notification Scaffolding.
    public function get_event_notification_params($attr = [], $overrides = []) {
        return $this->get_notification_params($attr, $overrides, $this->get_default_event_notification_params());
    }

    public function get_reminder_notification_params($attr = [], $overrides = []) {
        return $this->get_notification_params($attr, $overrides, $this->get_default_reminder_notification_params());
    }

    public function get_notification_params($attr = [], $overrides = [], $defaults = []) {
        if (is_string($attr)) {
            return $defaults[$attr];
        }

        if (count($overrides)) {
            $defaults = $this->override_params($defaults, $overrides);
        }

        if (!count($attr)) {
            return $defaults;
        }

        // Get rid of non-course-configurable fields.
        return \block_quickmail_plugin::array_filter_key($defaults, function ($key) use ($attr) {
            return in_array($key, $attr);
        });
    }

    public function get_default_event_notification_params() {
        return array_merge($this->get_default_notification_params(), [
            'name' => 'My Event Notification',
            'time_delay_unit' => 'minute',
            'time_delay_amount' => '5',
            'mute_time_unit' => 'day',
            'mute_time_amount' => '2',
        ]);
    }

    public function get_default_reminder_notification_params() {
        $now = time();

        return array_merge($this->get_default_notification_params(), [
            'name' => 'My Reminder Notification',
            'schedule_unit' => 'week',
            'schedule_amount' => 1,
            'schedule_begin_at' => $now,
            'schedule_end_at' => null,
            'max_per_interval' => 0,
        ]);
    }

    public function get_default_notification_params() {
        return [
            'message_type' => 'email',
            'subject' => 'This is the subject',
            'body' => 'This is the body',
            'is_enabled' => 1,
            'alternate_email_id' => 0,
            'signature_id' => 0,
            'editor_format' => 1,
            'send_receipt' => 0,
            'send_to_mentors' => 0,
            'no_reply' => 1,
            'conditions' => '',
            'condition_time_amount' => 4,
            'condition_time_unit' => 'day',
            'condition_grade_greater_than' => 40,
            'condition_grade_less_than' => 60,
        ];
    }

    private function get_notification_input($overrides = []) {
        return (object) array_merge($this->get_default_input_notification_params(), $overrides);
    }

    private function get_default_input_notification_params() {
        return [
            'notification_name' => 'My new notification',
            'notification_is_enabled' => '1',
            'schedule_time_unit' => 'day',
            'schedule_time_amount' => '3',
            'condition_time_unit' => 'week',
            'condition_time_amount' => '1',
            'message_subject' => 'The subject',
            'message_body' => [
                'text' => 'One fine body',
                'format' => '1',
            ],
            'message_type' => 'email',
            'message_send_to_mentors' => '1',
        ];
    }

}
