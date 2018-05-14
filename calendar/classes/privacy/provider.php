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
 * Privacy class for requesting user data.
 *
 * @package    core_calendar
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_calendar\privacy;
defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\approved_contextlist;
use \core_privacy\local\request\context;
use \core_privacy\local\request\contextlist;
use \core_privacy\local\request\transform;
use \core_privacy\local\request\writer;

/**
 * Privacy Subsystem for core_calendar implementing metadata, plugin, and user_preference providers.
 *
 * @package    core_calendar
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider,
    \core_privacy\local\request\user_preference_provider
{

    /**
     * Provides meta data that is stored about a user with core_calendar.
     *
     * @param  collection $collection A collection of meta data items to be added to.
     * @return  collection Returns the collection of metadata.
     */
    public static function get_metadata(collection $collection) {
        // The calendar 'event' table contains user data.
        $collection->add_database_table(
            'event',
            [
                'name' => 'privacy:metadata:calendar:event:name',
                'description' => 'privacy:metadata:calendar:event:description',
                'eventtype' => 'privacy:metadata:calendar:event:eventtype',
                'timestart' => 'privacy:metadata:calendar:event:timestart',
                'timeduration' => 'privacy:metadata:calendar:event:timeduration',
            ],
            'privacy:metadata:calendar:event'
        );

        // The calendar 'event_subscriptions' table contains user data.
        $collection->add_database_table(
            'event_subscriptions',
            [
                'name' => 'privacy:metadata:calendar:event_subscriptions:name',
                'url' => 'privacy:metadata:calendar:event_subscriptions:url',
                'eventtype' => 'privacy:metadata:calendar:event_subscriptions:eventtype',
            ],
            'privacy:metadata:calendar:event_subscriptions'
        );

        // The calendar user preference setting 'calendar_savedflt'.
        $collection->add_user_preference(
            'calendar_savedflt',
            'privacy:metadata:calendar:preferences:calendar_savedflt'
        );

        return $collection;
    }

    /**
     * Get the list of contexts that contain calendar user information for the specified user.
     *
     * @param   int $userid The user to search.
     * @return  contextlist   $contextlist  The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid($userid) {
        $contextlist = new contextlist();

        // Calendar Events can exist at Site, Course Category, Course, Course Group, User, or Course Modules contexts.
        $params = [
            'sitecontext'        => CONTEXT_SYSTEM,
            'coursecontext'      => CONTEXT_COURSE,
            'groupcontext'       => CONTEXT_COURSE,
            'usercontext'        => CONTEXT_USER,
            'cuserid'            => $userid,
            'modulecontext'      => CONTEXT_MODULE,
            'muserid'            => $userid
        ];

        // Get contexts of Calendar Events for the owner.
        $sql = "SELECT ctx.id
                  FROM {context} ctx
                  JOIN {event} e ON
                       (e.eventtype = 'site' AND ctx.contextlevel = :sitecontext) OR
                       (e.courseid = ctx.instanceid AND e.eventtype = 'course' AND ctx.contextlevel = :coursecontext) OR
                       (e.courseid = ctx.instanceid AND e.eventtype = 'group' AND ctx.contextlevel = :groupcontext) OR
                       (e.userid = ctx.instanceid AND e.eventtype = 'user' AND ctx.contextlevel = :usercontext)
                 WHERE e.userid = :cuserid
                 UNION
                SELECT ctx.id
                  FROM {context} ctx
                  JOIN {course_modules} cm ON cm.id = ctx.instanceid AND ctx.contextlevel = :modulecontext
                  JOIN {modules} m ON m.id = cm.module
                  JOIN {event} e ON e.modulename = m.name AND e.courseid = cm.course AND e.instance = cm.instance
                 WHERE e.userid = :muserid";
        $contextlist->add_from_sql($sql, $params);

        // Calendar Subscriptions can exist at Site, Course Category, Course, Course Group, or User contexts.
        $params = [
            'sitecontext'       => CONTEXT_SYSTEM,
            'coursecontext'     => CONTEXT_COURSE,
            'groupcontext'      => CONTEXT_COURSE,
            'usercontext'       => CONTEXT_USER,
            'userid'            => $userid
        ];

        // Get contexts for Calendar Subscriptions for the owner.
        $sql = "SELECT ctx.id
                  FROM {context} ctx
                  JOIN {event_subscriptions} s ON
                       (s.eventtype = 'site' AND ctx.contextlevel = :sitecontext) OR
                       (s.courseid = ctx.instanceid AND s.eventtype = 'course' AND ctx.contextlevel = :coursecontext) OR
                       (s.courseid = ctx.instanceid AND s.eventtype = 'group' AND ctx.contextlevel = :groupcontext) OR
                       (s.userid = ctx.instanceid AND s.eventtype = 'user' AND ctx.contextlevel = :usercontext)
                 WHERE s.userid = :userid";
        $contextlist->add_from_sql($sql, $params);

        // Return combined contextlist for Calendar Events & Calendar Subscriptions.
        return $contextlist;
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        if (empty($contextlist)) {
            return;
        }

        self::export_user_calendar_event_data($contextlist);
        self::export_user_calendar_subscription_data($contextlist);
    }

    /**
     * Export all user preferences for the plugin.
     *
     * @param   int $userid The userid of the user whose data is to be exported.
     */
    public static function export_user_preferences($userid) {
        $calendarsavedflt = get_user_preferences('calendar_savedflt', null, $userid);

        if (null !== $calendarsavedflt) {
            writer::export_user_preference(
                'core_calendar',
                'calendarsavedflt',
                $calendarsavedflt,
                get_string('privacy:metadata:calendar:preferences:calendar_savedflt', 'core_calendar')
            );
        }
    }

    /**
     * Delete all Calendar Event and Calendar Subscription data for all users in the specified context.
     *
     * @param   context $context Transform the specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        if (empty($context)) {
            return;
        }

        // Delete all Calendar Events in the specified context in batches.
        if ($eventids = array_keys(self::get_calendar_event_ids_by_context($context))) {
            self::delete_batch_records('event', 'id', $eventids);
        }

        // Delete all Calendar Subscriptions in the specified context in batches.
        if ($subscriptionids = array_keys(self::get_calendar_subscription_ids_by_context($context))) {
            self::delete_batch_records('event_subscriptions', 'id', $subscriptionids);
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        if (empty($contextlist)) {
            return;
        }

        // Delete all Calendar Events for the owner and specified contexts in batches.
        $eventdetails = self::get_calendar_event_details_by_contextlist($contextlist);
        $eventids = [];
        foreach ($eventdetails as $eventdetail) {
            $eventids[] = $eventdetail->eventid;
        }
        $eventdetails->close();
        self::delete_batch_records('event', 'id', $eventids);

        // Delete all Calendar Subscriptions for the owner and specified contexts in batches.
        $subscriptiondetails = self::get_calendar_subscription_details_by_contextlist($contextlist);
        $subscriptionids = [];
        foreach ($subscriptiondetails as $subscriptiondetail) {
            $subscriptionids[] = $subscriptiondetail->subscriptionid;
        }
        $subscriptiondetails->close();
        self::delete_batch_records('event_subscriptions', 'id', $subscriptionids);
    }

    /**
     * Helper function to export Calendar Events data by a User's contextlist.
     *
     * @param approved_contextlist $contextlist
     * @throws \coding_exception
     */
    protected static function export_user_calendar_event_data(approved_contextlist $contextlist) {
        // Calendar Events can exist at Site, Course Category, Course, Course Group, User, or Course Modules contexts.
        $eventdetails = self::get_calendar_event_details_by_contextlist($contextlist);

        // Multiple Calendar Events of the same eventtype and time can exist for a context, so collate them for export.
        $eventrecords = [];
        foreach ($eventdetails as $eventdetail) {
            // Create an array key based on the contextid, eventtype, and time.
            $key = $eventdetail->contextid . $eventdetail->eventtype . $eventdetail->timestart;

            if (array_key_exists($key, $eventrecords) === false) {
                $eventrecords[$key] = [ $eventdetail ];
            } else {
                $eventrecords[$key] = array_merge($eventrecords[$key], [$eventdetail]);
            }
        }
        $eventdetails->close();

        // Export Calendar Event data.
        foreach ($eventrecords as $eventrecord) {
            $index = (count($eventrecord) > 1) ? 1 : 0;

            foreach ($eventrecord as $event) {
                // Export the events using the structure Calendar/Events/{datetime}/{eventtype}-event.json.
                $subcontexts = [
                    get_string('calendar', 'calendar'),
                    get_string('events', 'calendar'),
                    date('c', $event->timestart)
                ];
                $name = $event->eventtype . '-event';

                // Use name {eventtype}-event-{index}.json if multiple eventtypes and time exists at the same context.
                if ($index != 0) {
                    $name .= '-' . $index;
                    $index++;
                }

                $eventdetails = (object) [
                    'name' => $event->name,
                    'description' => $event->description,
                    'eventtype' => $event->eventtype,
                    'timestart' => transform::datetime($event->timestart),
                    'timeduration' => $event->timeduration
                ];

                $context = \context::instance_by_id($event->contextid);
                writer::with_context($context)->export_related_data($subcontexts, $name, $eventdetails);
            }
        }
    }

    /**
     * Helper function to export Calendar Subscriptions data by a User's contextlist.
     *
     * @param approved_contextlist $contextlist
     * @throws \coding_exception
     */
    protected static function export_user_calendar_subscription_data(approved_contextlist $contextlist) {
        // Calendar Subscriptions can exist at Site, Course Category, Course, Course Group, or User contexts.
        $subscriptiondetails = self::get_calendar_subscription_details_by_contextlist($contextlist);

        // Multiple Calendar Subscriptions of the same eventtype can exist for a context, so collate them for export.
        $subscriptionrecords = [];
        foreach ($subscriptiondetails as $subscriptiondetail) {
            // Create an array key based on the contextid and eventtype.
            $key = $subscriptiondetail->contextid . $subscriptiondetail->eventtype;

            if (array_key_exists($key, $subscriptionrecords) === false) {
                $subscriptionrecords[$key] = [ $subscriptiondetail ];
            } else {
                $subscriptionrecords[$key] = array_merge($subscriptionrecords[$key], [$subscriptiondetail]);
            }
        }
        $subscriptiondetails->close();

        // Export Calendar Subscription data.
        foreach ($subscriptionrecords as $subscriptionrecord) {
            $index = (count($subscriptionrecord) > 1) ? 1 : 0;

            foreach ($subscriptionrecord as $subscription) {
                // Export the events using the structure Calendar/Subscriptions/{eventtype}-subscription.json.
                $subcontexts = [
                    get_string('calendar', 'calendar'),
                    get_string('subscriptions', 'calendar')
                ];
                $name = $subscription->eventtype . '-subscription';

                // Use name {eventtype}-subscription-{index}.json if multiple eventtypes exists at the same context.
                if ($index != 0) {
                    $name .= '-' . $index;
                    $index++;
                }

                $context = \context::instance_by_id($subscription->contextid);
                writer::with_context($context)->export_related_data($subcontexts, $name, $subscription);
            }
        }
    }

    /**
     * Helper function to return all Calendar Event id results for a specified context.
     *
     * @param \context $context
     * @return array|null
     * @throws \dml_exception
     */
    protected static function get_calendar_event_ids_by_context(\context $context) {
        global $DB;

        // Calendar Events can exist at Site, Course Category, Course, Course Group, User, or Course Modules contexts.
        $events = null;

        if ($context->contextlevel == CONTEXT_MODULE) { // Course Module Contexts.
            $params = [
                'modulecontext'     => $context->contextlevel,
                'contextid'         => $context->id
            ];

            // Get Calendar Events for the specified Course Module context.
            $sql = "SELECT DISTINCT
                           e.id AS eventid
                      FROM {context} ctx
                INNER JOIN {course_modules} cm ON cm.id = ctx.instanceid AND ctx.contextlevel = :modulecontext
                INNER JOIN {modules} m ON m.id = cm.module
                INNER JOIN {event} e ON e.modulename = m.name AND e.courseid = cm.course AND e.instance = cm.instance
                     WHERE ctx.id = :contextid";
            $events = $DB->get_records_sql($sql, $params);
        } else {                                        // Other Moodle Contexts.
            $params = [
                'sitecontext'       => CONTEXT_SYSTEM,
                'coursecontext'     => CONTEXT_COURSE,
                'groupcontext'      => CONTEXT_COURSE,
                'usercontext'       => CONTEXT_USER,
                'contextid'         => $context->id
            ];

            // Get Calendar Events for the specified Moodle context.
            $sql = "SELECT DISTINCT
                           e.id AS eventid
                      FROM {context} ctx
                INNER JOIN {event} e ON
                           (e.eventtype = 'site' AND ctx.contextlevel = :sitecontext) OR
                           (e.courseid = ctx.instanceid AND (e.eventtype = 'course' OR e.eventtype = 'group' OR e.modulename != '0') AND ctx.contextlevel = :coursecontext) OR
                           (e.userid = ctx.instanceid AND e.eventtype = 'user' AND ctx.contextlevel = :usercontext)
                     WHERE ctx.id = :contextid";
            $events = $DB->get_records_sql($sql, $params);
        }

        return $events;
    }

    /**
     * Helper function to return all Calendar Subscription id results for a specified context.
     *
     * @param \context $context
     * @return array
     * @throws \dml_exception
     */
    protected static function get_calendar_subscription_ids_by_context(\context $context) {
        global $DB;

        // Calendar Subscriptions can exist at Site, Course Category, Course, Course Group, or User contexts.
        $params = [
            'sitecontext'       => CONTEXT_SYSTEM,
            'coursecontext'     => CONTEXT_COURSE,
            'groupcontext'      => CONTEXT_COURSE,
            'usercontext'       => CONTEXT_USER,
            'contextid'         => $context->id
        ];

        // Get Calendar Subscriptions for the specified context.
        $sql = "SELECT DISTINCT
                       s.id AS subscriptionid
                  FROM {context} ctx
            INNER JOIN {event_subscriptions} s ON
                       (s.eventtype = 'site' AND ctx.contextlevel = :sitecontext) OR
                       (s.courseid = ctx.instanceid AND s.eventtype = 'course' AND ctx.contextlevel = :coursecontext) OR
                       (s.courseid = ctx.instanceid AND s.eventtype = 'group' AND ctx.contextlevel = :groupcontext) OR
                       (s.userid = ctx.instanceid AND s.eventtype = 'user' AND ctx.contextlevel = :usercontext)
                 WHERE ctx.id = :contextid";

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Helper function to return the Calendar Events for a given user and context list.
     *
     * @param approved_contextlist $contextlist
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     */
    protected static function get_calendar_event_details_by_contextlist(approved_contextlist $contextlist) {
        global $DB;

        $userid = $contextlist->get_user()->id;

        list($contextsql1, $contextparams1) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);
        list($contextsql2, $contextparams2) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        // Calendar Events can exist at Site, Course Category, Course, Course Group, User, or Course Modules contexts.
        $params = [
            'sitecontext'       => CONTEXT_SYSTEM,
            'coursecontext'     => CONTEXT_COURSE,
            'groupcontext'      => CONTEXT_COURSE,
            'usercontext'       => CONTEXT_USER,
            'cuserid'           => $userid,
            'modulecontext'     => CONTEXT_MODULE,
            'muserid'           => $userid
        ];
        $params += $contextparams1;
        $params += $contextparams2;

        // Get Calendar Events details for the approved contexts and the owner.
        $sql = "SELECT ctxid as contextid,
                       details.id as eventid,
                       details.name as name,
                       details.description as description,
                       details.eventtype as eventtype,
                       details.timestart as timestart,
                       details.timeduration as timeduration
                  FROM (
                          SELECT e.id AS id,
                                 ctx.id AS ctxid
                            FROM {context} ctx
                      INNER JOIN {event} e ON
                                 (e.eventtype = 'site' AND ctx.contextlevel = :sitecontext) OR
                                 (e.courseid = ctx.instanceid AND e.eventtype = 'course' AND ctx.contextlevel = :coursecontext) OR
                                 (e.courseid = ctx.instanceid AND e.eventtype = 'group' AND ctx.contextlevel = :groupcontext) OR
                                 (e.userid = ctx.instanceid AND e.eventtype = 'user' AND ctx.contextlevel = :usercontext)
                           WHERE e.userid = :cuserid
                             AND ctx.id {$contextsql1}
                           UNION
                          SELECT e.id AS id,
                                 ctx.id AS ctxid
                            FROM {context} ctx
                      INNER JOIN {course_modules} cm ON cm.id = ctx.instanceid AND ctx.contextlevel = :modulecontext
                      INNER JOIN {modules} m ON m.id = cm.module
                      INNER JOIN {event} e ON e.modulename = m.name AND e.courseid = cm.course AND e.instance = cm.instance
                           WHERE e.userid = :muserid
                             AND ctx.id {$contextsql2}
                  ) ids
                  JOIN {event} details ON details.id = ids.id
              ORDER BY ids.id";

        return $DB->get_recordset_sql($sql, $params);
    }

    /**
     * Helper function to return the Calendar Subscriptions for a given user and context list.
     *
     * @param approved_contextlist $contextlist
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     */
    protected static function get_calendar_subscription_details_by_contextlist(approved_contextlist $contextlist) {
        global $DB;

        $user = $contextlist->get_user();

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        $params = [
            'sitecontext' => CONTEXT_SYSTEM,
            'coursecontext' => CONTEXT_COURSE,
            'groupcontext' => CONTEXT_COURSE,
            'usercontext' => CONTEXT_USER,
            'userid' => $user->id
        ];
        $params += $contextparams;

        // Get Calendar Subscriptions for the approved contexts and the owner.
        $sql = "SELECT DISTINCT
                       c.id as contextid,
                       s.id as subscriptionid,
                       s.name as name,
                       s.url as url,
                       s.eventtype as eventtype
                  FROM {context} c
            INNER JOIN {event_subscriptions} s ON
                       (s.eventtype = 'site' AND c.contextlevel = :sitecontext) OR
                       (s.courseid = c.instanceid AND s.eventtype = 'course' AND c.contextlevel = :coursecontext) OR
                       (s.courseid = c.instanceid AND s.eventtype = 'group' AND c.contextlevel = :groupcontext) OR
                       (s.userid = c.instanceid AND s.eventtype = 'user' AND c.contextlevel = :usercontext)
                 WHERE s.userid = :userid
                   AND c.id {$contextsql}";

        return $DB->get_recordset_sql($sql, $params);
    }

    /**
     * Helper function to delete records in batches in order to minimise amount of deletion queries.
     *
     * @param string    $tablename  The table name to delete from.
     * @param string    $field      The table column field name to delete records by.
     * @param array     $values     The table column field values to delete records by.
     * @throws \dml_exception
     */
    protected static function delete_batch_records($tablename, $field, $values) {
        global $DB;

        // Batch deletion with an upper limit of 2000 records to minimise the number of deletion queries.
        $batchrecords = array_chunk($values, 2000);

        foreach ($batchrecords as $batchrecord) {
            $DB->delete_records_list($tablename, $field, $batchrecord);
        }
    }

}
