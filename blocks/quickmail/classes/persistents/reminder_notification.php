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
use block_quickmail\persistents\concerns\is_notification_type;
use block_quickmail\persistents\concerns\is_schedulable;
use block_quickmail\persistents\concerns\can_be_soft_deleted;
use block_quickmail\persistents\interfaces\notification_type_interface;
use block_quickmail\persistents\interfaces\schedulable_interface;
use block_quickmail\persistents\schedule;
use block_quickmail\persistents\message;
use block_quickmail\repos\user_repo;

class reminder_notification extends \block_quickmail\persistents\persistent
    implements notification_type_interface, schedulable_interface {

    use enhanced_persistent,
        sanitizes_input,
        is_notification_type,
        is_schedulable,
        can_be_soft_deleted;

    /** Table name for the persistent. */
    const TABLE = 'block_quickmail_rem_notifs';

    /** notification_type_interface */
    public static $notificationtypekey = 'reminder';

    public static $requiredcreationkeys = [
        'object_id',
        'schedule_unit',
        'schedule_amount',
        'schedule_begin_at',
    ];

    public static $defaultcreationparams = [
        'max_per_interval' => 0,
        'schedule_id' => 0,
        'schedule_end_at' => null,
    ];

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'notification_id' => [
                'type' => PARAM_INT,
            ],
            'model' => [
                'type' => PARAM_TEXT,
            ],
            'object_id' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
            'max_per_interval' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
            'schedule_id' => [
                'type' => PARAM_INT,
                'default' => null,
                'null' => NULL_ALLOWED,
            ],
            'last_run_at' => [
                'type' => PARAM_INT,
                'default' => null,
                'null' => NULL_ALLOWED,
            ],
            'next_run_at' => [
                'type' => PARAM_INT,
                'default' => null,
                'null' => NULL_ALLOWED,
            ],
            'is_running' => [
                'type' => PARAM_BOOL,
                'default' => false,
            ],
            'timedeleted' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
        ];
    }

    /**
     * Creates and returns a reminder notification of the given model key and object for the given course and user
     *
     * Throws an exception if any missing param keys
     *
     * @param  string  $modelkey    a reminder_notification_model key
     * @param  object  $course
     * @param  object  $user
     * @param  array   $params
     * @param  object  $object       the object that is to be evaluated by this reminder notification
     * @return reminder_notification
     * @throws \Exception
     */
    public static function create_type($modelkey, $course, $user, $params, $object = null) {
        // Add the model key to the params.
        $params = array_merge($params, ['model' => $modelkey]);

        // Create the parent notification.
        $notification = notification::create_for_course_user('reminder', $course, $user, $params);

        // Create the reminder notification.
        $remindernotification = self::create_for_notification($notification, array_merge([
            'object_id' => ! empty($object) ? $object->id : 0, // May need to write helper class to get this id.
        ], $params));

        return $remindernotification;
    }

    /**
     * Creates and returns a reminder notification to be associated with the given notification
     *
     * Note: creates the reminder notification's schedule before creating the notification
     *
     * @param  notification  $notification
     * @param  array         $params
     * @return reminder_notification
     * @throws \Exception
     */
    private static function create_for_notification($notification, $params) {
        $params = self::sanitize_creation_params($params, [
            'schedule_unit',
            'schedule_amount',
            'schedule_begin_at',
            'schedule_end_at',
            'model',
            'object_id',
        ]);

        try {
            $schedule = schedule::create_from_params([
                'unit' => $params['schedule_unit'],
                'amount' => $params['schedule_amount'],
                'begin_at' => $params['schedule_begin_at'],
                'end_at' => $params['schedule_end_at'],
            ]);

            $remindernotification = self::create_new([
                'notification_id' => $notification->get('id'),
                'model' => $params['model'],
                'object_id' => $params['object_id'],
                'schedule_id' => $schedule->get('id'),
            ]);

            // If there was an error during creation, delete potentially-created associative data.
        } catch (\Exception $e) {
            $notification->hard_delete();

            if (!empty($schedule)) {
                $schedule->hard_delete();
            }

            throw new \Exception;
        }

        return $remindernotification;
    }

    // Update Methods.
    /**
     * Updates and returns an event notification from the given params
     *
     * @param  array         $params
     * @return event_notification
     */
    public function update_self($params) {
        // Update schedule details if necessary.
        if ($schedule = $this->get_schedule()) {
            if (isset($params['schedule_begin_at'])) {
                $beginat = schedule::get_sanitized_date_time_selector_value($params['schedule_begin_at'], 0);

                $schedule->set('begin_at', $beginat);
            }

            if (isset($params['schedule_end_at'])) {
                $endat = schedule::get_sanitized_date_time_selector_value($params['schedule_end_at'], 0);

                $schedule->set('end_at', $endat);
            }

            $schedule->set('unit', $params['schedule_time_unit']);
            $schedule->set('amount', $params['schedule_time_amount']);
            $schedule->update();

            $this->set_next_run_time();
        }

        return $this;
    }

    // Getters.
    /**
     * Returns this reminder_notification's max_per_interval as an int
     *
     * @return int
     */
    public function max_per_interval() {
        return (int) $this->get('max_per_interval');
    }

    // Schedulable Interface.
    public function run_scheduled() {
        $this->handle_schedule_pre_run_actions();

        $this->notify();

        $this->handle_schedule_post_run_actions();
    }

    // Methods.
    /**
     * Returns a filtered array of user ids to be notified given a qualified array of user ids
     *
     * @param  array  $userids
     * @return array
     */
    public function filter_notifiable_user_ids($userids = []) {
        // Pull all users that this message creator is capable of emailing within the course.
        $allowedusersids = array_keys(
            user_repo::get_course_user_selectable_users($this->get_notification()->get_course(),
            $this->get_notification()->get_user()));

        // Filter out any user ids that are not allowed.
        $userids = array_filter($userids, function($id) use ($allowedusersids) {
            return in_array($id, $allowedusersids);
        });

        // If this reminder_notification has a max_per_interval has.
        if ($this->max_per_interval()) {
            // Pull all users to be ignored based on this reminder_notification's configuration.
            $ignoreuserids = $this->get_user_ids_to_ignore();

            // Filter out all of the user ids to ignore from the user ids to be notified.
            $userids = array_filter($userids, function ($id) use ($ignoreuserids) {
                return in_array($id, $ignoreuserids);
            });
        }

        return $userids;
    }

    /**
     * Returns an array of user ids whom have already been notified at least the "max_per_interval" times since last run
     *
     * @return array
     */
    public function get_user_ids_to_ignore() {
        // TODO - Need to do this!!
        return [];
    }

    // Notification Type Interface.
    /**
     * Pulls all users who should be notified in this notification and creates a new message
     * instance to be sent out in the queue ASAP
     *
     * Note: if no users can be found, no message is created or sent
     *
     * @param  int  $userid  (note: for this implementaion, the user_id should always be null)
     * @return void
     */
    public function notify($userid = null) {
        // Instantiate this notification_type_interface's notification model.
        $model = $this->get_notification_model();

        try {
            // Get the parent notification.
            $notification = $this->get_notification();

            // Get all user ids to be notified, if no user ids, do nothing.
            if ($userids = $this->filter_notifiable_user_ids($model->get_user_ids_to_notify())) {
                message::create_from_notification($notification, $userids);
            }
        } catch (\Exception $e) {
            // Message not created, fail gracefully.
            echo($e->getMessage());
        }
    }

}
