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

namespace block_quickmail\persistents;

defined('MOODLE_INTERNAL') || die();

use block_quickmail\persistents\concerns\enhanced_persistent;
use block_quickmail\persistents\concerns\sanitizes_input;
use block_quickmail\persistents\concerns\belongs_to_a_course;
use block_quickmail\persistents\concerns\belongs_to_a_user;
use block_quickmail\persistents\concerns\can_be_soft_deleted;
use block_quickmail\persistents\event_notification;
use block_quickmail\persistents\reminder_notification;
use block_quickmail\persistents\schedule;
use block_quickmail\notifier\notification_condition;
use block_quickmail_string;

class notification extends \block_quickmail\persistents\persistent {

    use enhanced_persistent,
        sanitizes_input,
        belongs_to_a_course,
        belongs_to_a_user,
        can_be_soft_deleted;

    /** Table name for the persistent. */
    const TABLE = 'block_quickmail_notifs';

    public static $requiredcreationkeys = [
        'name',
        'message_type',
        'subject',
        'body',
    ];

    public static $defaultcreationparams = [
        'is_enabled' => false,
        'conditions' => '',
        'alternate_email_id' => 0,
        'signature_id' => 0,
        'editor_format' => 1,
        'send_receipt' => false,
        'send_to_mentors' => false,
        'no_reply' => true,
    ];

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'name' => [
                'type' => PARAM_TEXT,
            ],
            'type' => [
                'type' => PARAM_TEXT,
            ],
            'course_id' => [
                'type' => PARAM_INT,
            ],
            'user_id' => [
                'type' => PARAM_INT,
            ],
            'is_enabled' => [
                'type' => PARAM_BOOL,
                'default' => false,
            ],
            'conditions' => [
                'type' => PARAM_TEXT,
                'default' => null,
                'null' => NULL_ALLOWED,
            ],
            'message_type' => [
                'type' => PARAM_TEXT,
            ],
            'alternate_email_id' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
            'subject' => [
                'type' => PARAM_TEXT,
                'default' => null,
                'null' => NULL_ALLOWED,
            ],
            'signature_id' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
            'body' => [
                'type' => PARAM_RAW,
                'default' => null,
                'null' => NULL_ALLOWED,
            ],
            'editor_format' => [
                'type' => PARAM_INT,
                'default' => 1, // TODO - make this configurable?
            ],
            'send_receipt' => [
                'type' => PARAM_BOOL,
                'default' => false,
            ],
            'send_to_mentors' => [
                'type' => PARAM_BOOL,
                'default' => false,
            ],
            'no_reply' => [
                'type' => PARAM_BOOL,
                'default' => false,
            ],
            'timedeleted' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
        ];
    }

    // Relationships.
    /**
     * Returns the notification type interface instance of this notification
     *
     * @return \block_quickmail\persistents\interfaces\notification_type_interface (event/reminder)
     */
    public function get_notification_type_interface() {
        $class = $this->get_notification_type_interface_persistent_class_name();

        return $class::get_record(['notification_id' => $this->get('id')]);
    }

    public function get_notification_type_interface_persistent_class_name() {
        return 'block_quickmail\persistents\\' . $this->get('type') . '_notification';
    }

    // The Getters.
    /**
     * Reports whether or not this notification is enabled
     *
     * @return bool
     */
    public function is_notification_enabled() {
        return (bool) $this->get('is_enabled');
    }

    /**
     * Reports whether or not this notification should be sent to its course at this moment
     *
     * Note: For a course to be notified it must be visible and, if a enddate is set, the enddate must be in the future
     *
     * @return bool
     */
    public function should_send_to_course() {
        $course = $this->get_course();

        if (!$course) {
            return false;
        }

        if (!$course->visible) {
            return false;
        }

        if (!property_exists($course, 'enddate')) {
            return true;
        }

        if (empty($course->enddate)) {
            return true;
        }

        return time() < $course->enddate;
    }

    // Status Update Methods.
    /**
     * Enables the notification
     *
     * @return void
     */
    public function enable() {
        $this->set('is_enabled', 1);
        $this->update();
    }

    /**
     * Disables the notification
     *
     * @return void
     */
    public function disable() {
        $this->set('is_enabled', 0);
        $this->update();
    }

    // Creation Methods.
    /**
     * Creates a notification of a given type for a given course and user
     *
     * Throws an exception if any missing param keys, see below
     *
     * @param  string  $type   event|reminder
     * @param  object  $course
     * @param  object  $user
     * @param  array   $params
     *         name         (required)
     *         message_type (required)
     *         subject      (required)
     *         body         (required)
     *         condition_ ...
     *         is_enabled
     *         alternate_email_id
     *         signature_id
     *         editor_format
     *         send_receipt
     *         send_to_mentors
     *         no_reply
     *         model (key)
     * @return notification
     * @throws \Exception
     */
    public static function create_for_course_user($type, $course, $user, $params) {
        // Check for required conditions, if any, and get sanitized for storage.
        $conditions = self::sanitize_condition_params($params, $type, $params['model']);

        $params = self::sanitize_creation_params($params);

        $data = array_merge($params, [
            'type' => $type,
            'course_id' => $course->id,
            'user_id' => $user->id,
            'conditions' => $conditions
        ]);

        // Be sure the name gets capitalized.
        $data['name'] = ucfirst($data['name']);

        $notification = self::create_new($data);

        return $notification;
    }

    /**
     * Updates this notification with the given params
     *
     * NOTE: it is assumed that these params have been validated
     *
     * @param  object  $user
     * @param  array   $params
     * @return notification
     * @throws \Exception
     */
    public function update_by_user($user, $params) {
        // Test_validate_schedule_begin_at.
        // If notification has been sent, do not allow.

        // Test_validate_schedule_end_at.
        // Must be greater than current time.

        $notificationtypeinterface = $this->get_notification_type_interface();

        try {
            $this->set('name', $params['notification_name']);
            $this->set('is_enabled', $params['notification_is_enabled']);
            $this->set('conditions', self::sanitize_condition_params(
                $params,
                $this->get('type'),
                $notificationtypeinterface->get('model'))
            );
            $this->set('subject', $params['message_subject']);
            $this->set('body', $params['message_body']['text']);
            $this->set('message_type', $params['message_type']);
            $this->set('send_to_mentors', $params['message_send_to_mentors']);
            $this->update();

            $notificationtypeinterface->update_self($params);

        } catch (\Exception $e) {
            throw new \Exception(block_quickmail_string::get('notification_not_updated'));
        }

        return $this->read();
    }

    // Deletion Methods.
    /**
     * Delete this notification
     *
     * @return void
     */
    public function delete_self() {
        // First, delete the type interface.
        if ($notificationtypeinterface = $this->get_notification_type_interface()) {
            $notificationtypeinterface->soft_delete();
        }

        // Next, delete this notification.
        $this->soft_delete();
    }

    // Conditions.
    /**
     * Returns an array of required condition keys for the given type of notification, and model key
     *
     * @param  string  $type
     * @param  string  $modelkey
     * @param  string  $prepend   optional, if set will prepend output keys with $prepend followed by underscore
     * @return array
     */
    public static function get_required_conditions_for_type($type, $modelkey, $prepend = '') {
        return notification_condition::get_required_condition_keys($type, $modelkey, $prepend);
    }

    /**
     * Throws an exception if required conditions for the given type and model for the given params and returns
     * a condition string formatted for storage
     *
     * Filters out any extraneous keys outside of the required keys
     *
     * @param  array  $params  (with condition keys prepended with "condition_")
     * @param  string $type
     * @param  string $modelkey
     * @return string
     * @throws \Exception
     */
    public static function sanitize_condition_params($params, $type, $modelkey) {
        // Get required keys for this type and model as an array, prepended with "condition_".
        $requiredkeys = self::get_required_conditions_for_type($type, $modelkey, 'condition');

        // Throw exception if any required condition key is missing from input params.
        self::check_required_params($requiredkeys, $params);

        // Filter out any unnecessary condition keys.
        $filteredconditions = array_filter(array_keys($params), function($key) use ($requiredkeys) {
            return in_array($key, $requiredkeys) && strpos($key, 'condition_') == 0;
        });

        $conditions = [];

        foreach ($filteredconditions as $c) {
            $key = str_replace('condition_', '', $c);
            $conditions[$key] = $params[$c];
        }

        return notification_condition::format_for_storage($conditions);
    }

    // SQL Queries.
    /**
     * Returns an array of all "schedulable" notifications that should be sent at the
     * current time which means: 1) next run time is in the past, 2) the notification
     * is not currently being run, 3) the parent notification is enabled
     *
     * Currently the only schedulable class are reminder_notifications...
     *
     * @return array (notification)
     */
    public static function get_all_ready_schedulables() {
        // Get timestamp for right now.
        $now = \DateTime::createFromFormat('U', time(), \core_date::get_server_timezone_object())->getTimestamp();

        global $DB;

        // Fetch all valid reminder notifications.
        $remsql = "SELECT rn.notification_id
                FROM {block_quickmail_rem_notifs} rn
                WHERE rn.is_running = 0
                AND rn.timedeleted = 0
                AND rn.next_run_at IS NOT NULL
                AND rn.next_run_at != 0
                AND rn.next_run_at <= :now";

        $notificationids = $DB->get_fieldset_sql($remsql, ['now' => $now]);

        // If no results here, return empty array.
        if (empty($notificationids)) {
            return [];
        }

        list($sql, $params) = $DB->get_in_or_equal($notificationids);

        $recordset = $DB->get_recordset_sql("
            SELECT * FROM {block_quickmail_notifs}
            WHERE type = 'reminder'
            AND is_enabled = 1
            AND timedeleted = 0
            AND id " . $sql, $params);

        // Iterate through recordset, instantiate persistents, add to array.
        $data = [];
        foreach ($recordset as $record) {
            $data[] = new self(0, $record);
        }
        $recordset->close();

        return $data;
    }

}
