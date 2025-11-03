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
 * Observer class containing methods monitoring various events.
 *
 * @package    tool_monitor
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_monitor;

defined('MOODLE_INTERNAL') || die();

/**
 * Observer class containing methods monitoring various events.
 *
 * @since      Moodle 2.8
 * @package    tool_monitor
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class eventobservers {

    /** @var array $buffer buffer of events. */
    protected $buffer = array();

    /** @var int Number of entries in the buffer. */
    protected $count = 0;

    /** @var  eventobservers a reference to a self instance. */
    protected static $instance;

    /**
     * Course delete event observer.
     * This observer monitors course delete event, and when a course is deleted it deletes any rules and subscriptions associated
     * with it, so no orphan data is left behind.
     *
     * @param \core\event\course_deleted $event The course deleted event.
     */
    public static function course_deleted(\core\event\course_deleted $event) {
        // Delete rules defined inside this course and associated subscriptions.
        $rules = rule_manager::get_rules_by_courseid($event->courseid, 0, 0, false);
        foreach ($rules as $rule) {
            rule_manager::delete_rule($rule->id, $event->get_context());
        }
        // Delete remaining subscriptions inside this course (from site-wide rules).
        subscription_manager::remove_all_subscriptions_in_course($event->get_context());
    }

    /**
     * The observer monitoring all the events.
     *
     * This observers puts small event objects in buffer for later writing to the database. At the end of the request the buffer
     * is cleaned up and all data dumped into the tool_monitor_events table.
     *
     * @param \core\event\base $event event object
     */
    public static function process_event(\core\event\base $event) {
        if (!get_config('tool_monitor', 'enablemonitor')) {
            return; // The tool is disabled. Nothing to do.
        }

        if (empty(self::$instance)) {
            self::$instance = new static();
            // Register shutdown handler - this is useful for buffering, processing events, etc.
            \core\shutdown_manager::register_function([self::$instance, 'process_buffer']);
        }

        self::$instance->buffer_event($event);

        if (PHPUNIT_TEST) {
            // Process buffer after every event when unit testing.
            self::$instance->process_buffer();

        }
    }

    /**
     * Api to buffer events to store, to reduce db queries.
     *
     * @param \core\event\base $event
     */
    protected function buffer_event(\core\event\base $event) {

        // If there are no subscriptions for this event do not buffer it.
        if (!\tool_monitor\subscription_manager::event_has_subscriptions($event->eventname, $event->courseid)) {
            return;
        }

        $eventdata = $event->get_data();
        $eventobj = new \stdClass();
        $eventobj->eventname = $eventdata['eventname'];
        $eventobj->contextid = $eventdata['contextid'];
        $eventobj->contextlevel = $eventdata['contextlevel'];
        $eventobj->contextinstanceid = $eventdata['contextinstanceid'];
        if ($event->get_url()) {
            // Get link url if exists.
            $eventobj->link = $event->get_url()->out();
        } else {
            $eventobj->link = '';
        }
        $eventobj->courseid = $eventdata['courseid'];
        $eventobj->timecreated = $eventdata['timecreated'];

        $this->buffer[] = $eventobj;
        $this->count++;
    }

    /**
     * This method process all events stored in the buffer.
     *
     * This is a multi purpose api. It does the following:-
     * 1. Write event data to tool_monitor_events
     * 2. Find out users that need to be notified about rule completion and schedule a task to send them messages.
     */
    public function process_buffer() {
        global $DB;

        $events = $this->flush(); // Flush data.

        $select = "SELECT COUNT(id) FROM {tool_monitor_events} ";
        $now = time();
        $messagestosend = array();
        $allsubids = array();

        // Let us now process the events and check for subscriptions.
        foreach ($events as $eventobj) {
            $subscriptions = subscription_manager::get_subscriptions_by_event($eventobj);
            $idstosend = array();
            foreach ($subscriptions as $subscription) {
                // Only proceed to fire events and notifications if the subscription is active.
                if (!subscription_manager::subscription_is_active($subscription)) {
                    continue;
                }
                $starttime = $now - $subscription->timewindow;
                $starttime = ($starttime > $subscription->lastnotificationsent) ? $starttime : $subscription->lastnotificationsent;
                if ($subscription->courseid == 0) {
                    // Site level subscription. Count all events.
                    $where = "eventname = :eventname AND timecreated >  :starttime";
                    $params = array('eventname' => $eventobj->eventname, 'starttime' => $starttime);
                } else {
                    // Course level subscription.
                    if ($subscription->cmid == 0) {
                        // All modules.
                        $where = "eventname = :eventname AND courseid = :courseid AND timecreated > :starttime";
                        $params = array('eventname' => $eventobj->eventname, 'courseid' => $eventobj->courseid,
                                'starttime' => $starttime);
                    } else {
                        // Specific module.
                        $where = "eventname = :eventname AND courseid = :courseid AND contextinstanceid = :cmid
                                AND timecreated > :starttime";
                        $params = array('eventname' => $eventobj->eventname, 'courseid' => $eventobj->courseid,
                                'cmid' => $eventobj->contextinstanceid, 'starttime' => $starttime);

                    }
                }
                $sql = $select . "WHERE " . $where;
                $count = $DB->count_records_sql($sql, $params);
                if (!empty($count) && $count >= $subscription->frequency) {
                    $idstosend[] = $subscription->id;

                    // Trigger a subscription_criteria_met event.
                    // It's possible that the course has been deleted since the criteria was met, so in that case use
                    // the system context. Set it here and change later if needed.
                    $context = \context_system::instance();
                    // We can't perform if (!empty($subscription->courseid)) below as it uses the magic method
                    // __get to return the variable, which will always result in being empty.
                    $courseid = $subscription->courseid;
                    if (!empty($courseid)) {
                        if ($coursecontext = \context_course::instance($courseid, IGNORE_MISSING)) {
                            $context = $coursecontext;
                        }
                    }

                    $params = array(
                        'userid' => $subscription->userid,
                        'courseid' => $subscription->courseid,
                        'context' => $context,
                        'other' => array(
                            'subscriptionid' => $subscription->id
                        )
                    );
                    $event = \tool_monitor\event\subscription_criteria_met::create($params);
                    $event->trigger();
                }
            }
            if (!empty($idstosend)) {
                $messagestosend[] = array('subscriptionids' => $idstosend, 'event' => $eventobj);
                $allsubids = array_merge($allsubids, $idstosend);
            }
        }

        if (!empty($allsubids)) {
            // Update the last trigger flag.
            list($sql, $params) = $DB->get_in_or_equal($allsubids, SQL_PARAMS_NAMED);
            $params['now'] = $now;
            $sql = "UPDATE {tool_monitor_subscriptions} SET lastnotificationsent = :now WHERE id $sql";
            $DB->execute($sql, $params);
        }

        // Schedule a task to send notification.
        if (!empty($messagestosend)) {
            $adhocktask = new notification_task();
            $adhocktask->set_custom_data($messagestosend);
            $adhocktask->set_component('tool_monitor');
            \core\task\manager::queue_adhoc_task($adhocktask);
        }
    }

    /**
     * Protected method that flushes the buffer of events and writes them to the database.
     *
     * @return array a copy of the events buffer.
     */
    protected function flush() {
        global $DB;

        // Flush the buffer to the db.
        $events = $this->buffer;
        $DB->insert_records('tool_monitor_events', $events); // Insert the whole chunk into the database.
        $this->buffer = array();
        $this->count = 0;
        return $events;
    }

    /**
     * Observer that monitors user deleted event and delete user subscriptions.
     *
     * @param \core\event\user_deleted $event the event object.
     */
    public static function user_deleted(\core\event\user_deleted $event) {
        $userid = $event->objectid;
        subscription_manager::delete_user_subscriptions($userid);
    }

    /**
     * Observer that monitors course module deleted event and delete user subscriptions.
     *
     * @param \core\event\course_module_deleted $event the event object.
     */
    public static function course_module_deleted(\core\event\course_module_deleted $event) {
        $cmid = $event->contextinstanceid;
        subscription_manager::delete_cm_subscriptions($cmid);
    }
}
