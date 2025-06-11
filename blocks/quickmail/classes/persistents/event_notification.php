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
use block_quickmail\persistents\concerns\can_be_soft_deleted;
use block_quickmail\persistents\interfaces\notification_type_interface;
use block_quickmail\persistents\message;
use block_quickmail_plugin;
use block_quickmail_cache;

class event_notification extends \block_quickmail\persistents\persistent implements notification_type_interface {

    use enhanced_persistent,
        sanitizes_input,
        is_notification_type,
        can_be_soft_deleted;

    /** Table name for the persistent. */
    const TABLE = 'block_quickmail_event_notifs';

    /** notification_type_interface */
    public static $notificationtypekey = 'event';

    public static $requiredcreationkeys = [
        'object_id',
    ];

    public static $defaultcreationparams = [
        'time_delay_amount' => 0,
        'time_delay_unit' => '',
        'mute_time_amount' => 0,
        'mute_time_unit' => '',
    ];

    public static $onetimeevents = [
        'course-entered',
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
            'time_delay_amount' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
            'time_delay_unit' => [
                'type' => PARAM_TEXT,
                'default' => null,
            ],
            'mute_time_amount' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
            'mute_time_unit' => [
                'type' => PARAM_TEXT,
                'default' => null,
            ],
            'timedeleted' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
        ];
    }

    // Getters.
    /**
     * Returns a calculate amount of seconds for this event notification's time delay
     *
     * @return int
     */
    public function time_delay() {
        return block_quickmail_plugin::calculate_seconds_from_time_params($this->get('time_delay_unit'),
            $this->get('time_delay_amount'));
    }

    /**
     * Returns a calculate amount of seconds for this event notification's time delay
     *
     * @return int
     */
    public function mute_time() {
        return block_quickmail_plugin::calculate_seconds_from_time_params($this->get('mute_time_unit'),
            $this->get('mute_time_amount'));
    }

    /**
     * Reports whether or not this event notification should notify the given
     * user_id at this moment
     *
     * For one-time events: this notification must not have ever been sent user
     *
     * For non-one-time events: this notification may not be sent until the
     * "next available send time" (NOW - mute_time + time_delay)
     *
     * @return bool
     */
    public function should_notify_user_now($userid) {
        return $this->is_one_time_event()
            ? ! $this->has_ever_notified_user($userid)
            : $this->sufficient_time_since_last_notification($userid);
    }

    /**
     * Reports whether or not this event notification is a "one time" event
     *
     * One time events will only be fired once per event notification instance
     *
     * @return bool
     */
    public function is_one_time_event() {
        return in_array($this->get('model'), static::$onetimeevents);
    }

    /**
     * Returns a timestamp in which this notification should be scheduled to send at
     * when successfully triggered
     *
     * @return int
     */
    public function calculated_send_time() {
        return time() + $this->time_delay();
    }

    /**
     * Returns the earliest timestamp at which this notification should be sent next
     *
     * @return int
     */
    public function next_available_send_time() {
        return $this->calculated_send_time() - $this->mute_time();
    }

    /**
     * Returns the cached timestamp for when this event notification was last fired
     *
     * Attempts to set the time in the cache if not found, defaulting to 0
     *
     * @param  bool  $readable   if true and contains value, will return a human-readable data, defaulting to empty string
     * @return mixed|int|string
     */
    public function cached_last_fired_at($readable = false) {
        $eventnotification = $this;

        $timestamp = (int) block_quickmail_cache::store('qm_event_notif_last_fired_at')->add(
            $this->get('id'), function() use ($eventnotification) {
                if ($recip = $eventnotification->get_last_recip()) {
                    return $recip->notified_at;
                } else {
                    return 0;
                }
            });

        if (!$readable) {
            return $timestamp;
        }

        return ! empty($timestamp)
            ? date('Y-m-d g:i a', $timestamp)
            : '';
    }

    // Creation Methods.
    /**
     * Creates and returns an event notification of the given model key and object for the given course and user
     *
     * Throws an exception if any missing param keys
     *
     * @param  string  $modelkey    an event_notification_model key
     * @param  object  $course
     * @param  object  $user
     * @param  array   $params
     * @param  object  $object       the object that is to be evaluated by this event notification
     * @return event_notification
     * @throws \Exception
     */
    public static function create_type($modelkey, $course, $user, $params, $object = null) {
        // Add the model key to the params.
        $params = array_merge($params, ['model' => $modelkey]);

        // Created the parent notification.
        $notification = notification::create_for_course_user('event', $course, $user, $params);

        // Create the event notification.
        $eventnotification = self::create_for_notification($notification, array_merge([
            // May need to write helper class to get this id.
            'object_id' => ! empty($object) ? $object->id : 0,
        ], $params));

        return $eventnotification;
    }

    /**
     * Creates and returns an event notification to be associated with the given notification
     *
     * @param  notification  $notification
     * @param  array         $params
     * @return event_notification
     */
    private static function create_for_notification($notification, $params) {
        $params = self::sanitize_creation_params($params, [
            'time_delay_amount',
            'time_delay_unit',
            'mute_time_amount',
            'mute_time_unit',
            'model',
            'object_id',
        ]);

        try {
            $eventnotification = self::create_new([
                'notification_id' => $notification->get('id'),
                'model' => $params['model'],
                'time_delay_amount' => $params['time_delay_amount'],
                'time_delay_unit' => $params['time_delay_unit'],
                'mute_time_amount' => $params['mute_time_amount'],
                'mute_time_unit' => $params['mute_time_unit'],
            ]);

            // If there was an error during creation.
        } catch (\Exception $e) {
            $notification->hard_delete();
        }

        return $eventnotification;
    }

    // Update Methods.
    /**
     * Updates and returns an event notification from the given params
     *
     * @param  array         $params
     * @return event_notification
     */
    public function update_self($params) {
        $this->set('time_delay_unit', $params['time_delay_unit']);
        $this->set('time_delay_amount', (int) $params['time_delay_amount']);

        if (array_key_exists('mute_time_unit', $params)) {
            $this->set('mute_time_unit', $params['mute_time_unit']);
        }

        if (array_key_exists('mute_time_amount', $params)) {
            $this->set('mute_time_amount', (int) $params['mute_time_amount']);
        }

        $this->update();

        return $this;
    }

    // Notification Type Interface.
    /**
     * Creates a new message instance to be sent to the given user id, if appropriate
     *
     * @param  int  $userid  (note: for this implementaion, the user_id should always be given)
     * @return void
     */
    public function notify($userid = null) {
        // Make sure this notification should be sent right now based upon configuration.
        if ($this->should_notify_user_now($userid)) {
            try {
                // Get the parent notification.
                $notification = $this->get_notification();

                // Determine when this notification should be sent.
                $sendat = $this->calculated_send_time();

                // Schedule a message.
                message::create_from_notification($notification, [$userid], $sendat);

                // Note that this event has been sent to this user at this time.
                $this->note_sent_to_user($userid, $sendat);
            } catch (\Exception $e) {
                echo $e;
                // Message not created, fail gracefully.
            }
        }
    }

    // Event Recipient Methods.
    /**
     * Reports whether or not this event notification has been sent to the given user before
     *
     * @param  int  $userid
     * @return bool
     */
    private function has_ever_notified_user($userid) {
        global $DB;

        return $DB->record_exists('block_quickmail_event_recips', [
            'event_notification_id' => $this->get('id'),
            'user_id' => $userid
        ]);
    }

    /**
     * Reports whether or not enough time has elapsed since last time this notification
     * notified the user, if at all
     *
     * @param  int  $userid
     * @return bool
     */
    private function sufficient_time_since_last_notification($userid) {
        global $DB;

        // May not be notified until next available send time.
        $result = $DB->get_records_sql(
            "SELECT * FROM {block_quickmail_event_recips}
             WHERE event_notification_id = ?
             AND user_id = ?
             AND notified_at > ?",
            [$this->get('id'), $userid, $this->next_available_send_time()]
        );

        return empty($result);
    }

    /**
     * Inserts a record of the given user being notified by this notification at the
     * given time
     *
     * @param  int  $userid
     * @param  int  $notifiedat   unix timestamp
     * @return void
     */
    private function note_sent_to_user($userid, $notifiedat) {
        global $DB;

        $DB->insert_record('block_quickmail_event_recips', (object) [
            'event_notification_id' => $this->get('id'),
            'user_id' => $userid,
            'notified_at' => $notifiedat,
        ], false);

        block_quickmail_cache::store('qm_event_notif_last_fired_at')->forget($this->get('id'));
    }

    /**
     * Returns the last event_recip record for this event notification, if any
     *
     * @return mixed|stdClass|false
     */
    public function get_last_recip() {
        global $DB;

        $result = $DB->get_record_sql(
            "SELECT * FROM {block_quickmail_event_recips}
             WHERE event_notification_id = ?
             ORDER BY id DESC LIMIT 1",
            [$this->get('id')]
        );

        return $result;
    }

}
