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
 * Class to manage subscriptions.
 *
 * @package    tool_monitor
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_monitor;

defined('MOODLE_INTERNAL') || die();

/**
 * Class to manage subscriptions.
 *
 * @since      Moodle 2.8
 * @package    tool_monitor
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class subscription_manager {

    /** @const Period of time, in days, after which an inactive subscription will be removed completely.*/
    const INACTIVE_SUBSCRIPTION_LIFESPAN_IN_DAYS = 30;

    /**
     * Subscribe a user to a given rule.
     *
     * @param int $ruleid  Rule id.
     * @param int $courseid Course id.
     * @param int $cmid Course module id.
     * @param int $userid User who is subscribing, defaults to $USER.
     *
     * @return bool|int returns id of the created subscription.
     */
    public static function create_subscription($ruleid, $courseid, $cmid, $userid = 0) {
        global $DB, $USER;

        $subscription = new \stdClass();
        $subscription->ruleid = $ruleid;
        $subscription->courseid = $courseid;
        $subscription->cmid = $cmid;
        $subscription->userid = empty($userid) ? $USER->id : $userid;
        if ($DB->record_exists('tool_monitor_subscriptions', (array)$subscription)) {
            // Subscription already exists.
            return false;
        }

        $subscription->timecreated = time();
        $subscription->id = $DB->insert_record('tool_monitor_subscriptions', $subscription);

        // Trigger a subscription created event.
        if ($subscription->id) {
            if (!empty($subscription->courseid)) {
                $courseid = $subscription->courseid;
                $context = \context_course::instance($subscription->courseid);
            } else {
                $courseid = 0;
                $context = \context_system::instance();
            }

            $params = array(
                'objectid' => $subscription->id,
                'courseid' => $courseid,
                'context' => $context
            );
            $event = \tool_monitor\event\subscription_created::create($params);
            $event->trigger();

            // Let's invalidate the cache.
            $cache = \cache::make('tool_monitor', 'eventsubscriptions');
            $cache->delete($courseid);
        }

        return $subscription->id;
    }

    /**
     * Delete a subscription.
     *
     * @param subscription|int $subscriptionorid an instance of subscription class or id.
     * @param bool $checkuser Check if the subscription belongs to current user before deleting.
     *
     * @return bool
     * @throws \coding_exception if $checkuser is true and the subscription doesn't belong to the current user.
     */
    public static function delete_subscription($subscriptionorid, $checkuser = true) {
        global $DB, $USER;
        if (is_object($subscriptionorid)) {
            $subscription = $subscriptionorid;
        } else {
            $subscription = self::get_subscription($subscriptionorid);
        }
        if ($checkuser && $subscription->userid != $USER->id) {
            throw new \coding_exception('Invalid subscription supplied');
        }

        // Store the subscription before we delete it.
        $subscription = $DB->get_record('tool_monitor_subscriptions', array('id' => $subscription->id));

        $success = $DB->delete_records('tool_monitor_subscriptions', array('id' => $subscription->id));

        // If successful trigger a subscription_deleted event.
        if ($success) {
            if (!empty($subscription->courseid) &&
                    ($coursecontext = \context_course::instance($subscription->courseid, IGNORE_MISSING))) {
                $courseid = $subscription->courseid;
                $context = $coursecontext;
            } else {
                $courseid = 0;
                $context = \context_system::instance();
            }

            $params = array(
                'objectid' => $subscription->id,
                'courseid' => $courseid,
                'context' => $context
            );
            $event = \tool_monitor\event\subscription_deleted::create($params);
            $event->add_record_snapshot('tool_monitor_subscriptions', $subscription);
            $event->trigger();

            // Let's invalidate the cache.
            $cache = \cache::make('tool_monitor', 'eventsubscriptions');
            $cache->delete($courseid);
        }

        return $success;
    }

    /**
     * Delete all subscriptions for a user.
     *
     * @param int $userid user id.
     *
     * @return mixed
     */
    public static function delete_user_subscriptions($userid) {
        global $DB;
        return $DB->delete_records('tool_monitor_subscriptions', array('userid' => $userid));
    }

    /**
     * Delete all subscriptions for a course module.
     *
     * @param int $cmid cm id.
     *
     * @return mixed
     */
    public static function delete_cm_subscriptions($cmid) {
        global $DB;
        return $DB->delete_records('tool_monitor_subscriptions', array('cmid' => $cmid));
    }

    /**
     * Delete all subscribers for a given rule.
     *
     * @param int $ruleid rule id.
     * @param \context|null $coursecontext the context of the course - this is passed when we
     *      can not get the context via \context_course as the course has been deleted.
     *
     * @return bool
     */
    public static function remove_all_subscriptions_for_rule($ruleid, $coursecontext = null) {
        global $DB;

        // Store all the subscriptions we have to delete.
        $subscriptions = $DB->get_recordset('tool_monitor_subscriptions', array('ruleid' => $ruleid));

        // Now delete them.
        $success = $DB->delete_records('tool_monitor_subscriptions', array('ruleid' => $ruleid));

        // If successful and there were subscriptions that were deleted trigger a subscription deleted event.
        if ($success && $subscriptions) {
            foreach ($subscriptions as $subscription) {
                // It is possible that we are deleting rules associated with a deleted course, so we should be
                // passing the context as the second parameter.
                if (!is_null($coursecontext)) {
                    $context = $coursecontext;
                    $courseid = $subscription->courseid;
                } else if (!empty($subscription->courseid) && ($coursecontext =
                        \context_course::instance($subscription->courseid, IGNORE_MISSING))) {
                    $courseid = $subscription->courseid;
                    $context = $coursecontext;
                } else {
                    $courseid = 0;
                    $context = \context_system::instance();
                }

                $params = array(
                    'objectid' => $subscription->id,
                    'courseid' => $courseid,
                    'context' => $context
                );
                $event = \tool_monitor\event\subscription_deleted::create($params);
                $event->add_record_snapshot('tool_monitor_subscriptions', $subscription);
                $event->trigger();

                // Let's invalidate the cache.
                $cache = \cache::make('tool_monitor', 'eventsubscriptions');
                $cache->delete($courseid);
            }
        }

        $subscriptions->close();

        return $success;
    }

    /**
     * Delete all subscriptions in a course.
     *
     * This is called after a course was deleted, context no longer exists but we kept the object
     *
     * @param \context_course $coursecontext the context of the course
     */
    public static function remove_all_subscriptions_in_course($coursecontext) {
        global $DB;

        // Store all the subscriptions we have to delete.
        if ($subscriptions = $DB->get_records('tool_monitor_subscriptions', ['courseid' => $coursecontext->instanceid])) {
            // Delete subscriptions in bulk.
            $DB->delete_records('tool_monitor_subscriptions', ['courseid' => $coursecontext->instanceid]);

            // Trigger events one by one.
            foreach ($subscriptions as $subscription) {
                $params = ['objectid' => $subscription->id, 'context' => $coursecontext];
                $event = \tool_monitor\event\subscription_deleted::create($params);
                $event->add_record_snapshot('tool_monitor_subscriptions', $subscription);
                $event->trigger();
            }
        }
    }

    /**
     * Get a subscription instance for an given subscription id.
     *
     * @param subscription|int $subscriptionorid an instance of subscription class or id.
     *
     * @return subscription returns a instance of subscription class.
     */
    public static function get_subscription($subscriptionorid) {
        global $DB;

        if (is_object($subscriptionorid)) {
            return new subscription($subscriptionorid);
        }

        $sql = self::get_subscription_join_rule_sql();
        $sql .= "WHERE s.id = :id";
        $sub = $DB->get_record_sql($sql, array('id' => $subscriptionorid), MUST_EXIST);
        return new subscription($sub);
    }

    /**
     * Get an array of subscriptions for a given user in a given course.
     *
     * @param int $courseid course id.
     * @param int $limitfrom Limit from which to fetch rules.
     * @param int $limitto  Limit to which rules need to be fetched.
     * @param int $userid Id of the user for which the subscription needs to be fetched. Defaults to $USER;
     * @param string $order Order to sort the subscriptions.
     *
     * @return array list of subscriptions
     */
    public static function get_user_subscriptions_for_course($courseid, $limitfrom = 0, $limitto = 0, $userid = 0,
            $order = 's.timecreated DESC' ) {
        global $DB, $USER;
        if ($userid == 0) {
            $userid = $USER->id;
        }
        $sql = self::get_subscription_join_rule_sql();
        $sql .= "WHERE s.courseid = :courseid AND s.userid = :userid ORDER BY $order";

        return self::get_instances($DB->get_records_sql($sql, array('courseid' => $courseid, 'userid' => $userid), $limitfrom,
                $limitto));
    }

    /**
     * Get count of subscriptions for a given user in a given course.
     *
     * @param int $courseid course id.
     * @param int $userid Id of the user for which the subscription needs to be fetched. Defaults to $USER;
     *
     * @return int number of subscriptions
     */
    public static function count_user_subscriptions_for_course($courseid, $userid = 0) {
        global $DB, $USER;
        if ($userid == 0) {
            $userid = $USER->id;
        }
        $sql = self::get_subscription_join_rule_sql(true);
        $sql .= "WHERE s.courseid = :courseid AND s.userid = :userid";

        return $DB->count_records_sql($sql, array('courseid' => $courseid, 'userid' => $userid));
    }

    /**
     * Get an array of subscriptions for a given user.
     *
     * @param int $limitfrom Limit from which to fetch rules.
     * @param int $limitto  Limit to which rules need to be fetched.
     * @param int $userid Id of the user for which the subscription needs to be fetched. Defaults to $USER;
     * @param string $order Order to sort the subscriptions.
     *
     * @return array list of subscriptions
     */
    public static function get_user_subscriptions($limitfrom = 0, $limitto = 0, $userid = 0,
                                                             $order = 's.courseid ASC, r.name' ) {
        global $DB, $USER;
        if ($userid == 0) {
            $userid = $USER->id;
        }
        $sql = self::get_subscription_join_rule_sql();
        $sql .= "WHERE s.userid = :userid ORDER BY $order";

        return self::get_instances($DB->get_records_sql($sql, array('userid' => $userid), $limitfrom, $limitto));
    }

    /**
     * Get count of subscriptions for a given user.
     *
     * @param int $userid Id of the user for which the subscription needs to be fetched. Defaults to $USER;
     *
     * @return int number of subscriptions
     */
    public static function count_user_subscriptions($userid = 0) {
        global $DB, $USER;;
        if ($userid == 0) {
            $userid = $USER->id;
        }
        $sql = self::get_subscription_join_rule_sql(true);
        $sql .= "WHERE s.userid = :userid";

        return $DB->count_records_sql($sql, array('userid' => $userid));
    }

    /**
     * Return a list of subscriptions for a given event.
     *
     * @param \stdClass $event the event object.
     *
     * @return array
     */
    public static function get_subscriptions_by_event(\stdClass $event) {
        global $DB;

        $sql = self::get_subscription_join_rule_sql();
        if ($event->contextlevel == CONTEXT_MODULE && $event->contextinstanceid != 0) {
            $sql .= "WHERE r.eventname = :eventname AND s.courseid = :courseid AND (s.cmid = :cmid OR s.cmid = 0)";
            $params = array('eventname' => $event->eventname, 'courseid' => $event->courseid, 'cmid' => $event->contextinstanceid);
        } else {
            $sql .= "WHERE r.eventname = :eventname AND (s.courseid = :courseid OR s.courseid = 0)";
            $params = array('eventname' => $event->eventname, 'courseid' => $event->courseid);
        }
        return self::get_instances($DB->get_records_sql($sql, $params));
    }

    /**
     * Return sql to join rule and subscription table.
     *
     * @param bool $count Weather if this is a count query or not.
     *
     * @return string the sql.
     */
    protected static function get_subscription_join_rule_sql($count = false) {
        if ($count) {
            $select = "SELECT COUNT(s.id) ";
        } else {
            $select = "SELECT s.*, r.description, r.descriptionformat, r.name, r.userid as ruleuserid, r.courseid as rulecourseid,
            r.plugin, r.eventname, r.template, r.templateformat, r.frequency, r.timewindow";
        }
        $sql = $select . "
                  FROM {tool_monitor_rules} r
                  JOIN {tool_monitor_subscriptions} s
                        ON r.id = s.ruleid ";
        return $sql;
    }

    /**
     * Helper method to convert db records to instances.
     *
     * @param array $arr of subscriptions.
     *
     * @return array of subscriptions as instances.
     */
    protected static function get_instances($arr) {
        $result = array();
        foreach ($arr as $key => $sub) {
            $result[$key] = new subscription($sub);
        }
        return $result;
    }

    /**
     * Get count of subscriptions for a given rule.
     *
     * @param int $ruleid rule id of the subscription.
     *
     * @return int number of subscriptions
     */
    public static function count_rule_subscriptions($ruleid) {
        global $DB;
        $sql = self::get_subscription_join_rule_sql(true);
        $sql .= "WHERE s.ruleid = :ruleid";

        return $DB->count_records_sql($sql, array('ruleid' => $ruleid));
    }

    /**
     * Returns true if an event in a particular course has a subscription.
     *
     * @param string $eventname the name of the event
     * @param int $courseid the course id
     * @return bool returns true if the event has subscriptions in a given course, false otherwise.
     */
    public static function event_has_subscriptions($eventname, $courseid) {
        global $DB;

        // Check if we can return these from cache.
        $cache = \cache::make('tool_monitor', 'eventsubscriptions');

        // The SQL we will be using to fill the cache if it is empty.
        $sql = "SELECT DISTINCT(r.eventname)
                  FROM {tool_monitor_subscriptions} s
            INNER JOIN {tool_monitor_rules} r
                    ON s.ruleid = r.id
                 WHERE s.courseid = :courseid";

        $sitesubscriptions = $cache->get(0);
        // If we do not have the site subscriptions in the cache then return them from the DB.
        if ($sitesubscriptions === false) {
            // Set the array for the cache.
            $sitesubscriptions = array();
            if ($subscriptions = $DB->get_records_sql($sql, array('courseid' => 0))) {
                foreach ($subscriptions as $subscription) {
                    $sitesubscriptions[$subscription->eventname] = true;
                }
            }
            $cache->set(0, $sitesubscriptions);
        }

        // Check if a subscription exists for this event site wide.
        if (isset($sitesubscriptions[$eventname])) {
            return true;
        }

        // If the course id is for the site, and we reached here then there is no site wide subscription for this event.
        if (empty($courseid)) {
            return false;
        }

        $coursesubscriptions = $cache->get($courseid);
        // If we do not have the course subscriptions in the cache then return them from the DB.
        if ($coursesubscriptions === false) {
            // Set the array for the cache.
            $coursesubscriptions = array();
            if ($subscriptions = $DB->get_records_sql($sql, array('courseid' => $courseid))) {
                foreach ($subscriptions as $subscription) {
                    $coursesubscriptions[$subscription->eventname] = true;
                }
            }
            $cache->set($courseid, $coursesubscriptions);
        }

        // Check if a subscription exists for this event in this course.
        if (isset($coursesubscriptions[$eventname])) {
            return true;
        }

        return false;
    }

    /**
     * Activates a group of subscriptions based on an input array of ids.
     *
     * @since 3.1.1
     * @param array $ids of subscription ids.
     * @return bool true if the operation was successful, false otherwise.
     */
    public static function activate_subscriptions(array $ids) {
        global $DB;
        if (!empty($ids)) {
            list($sql, $params) = $DB->get_in_or_equal($ids);
            $success = $DB->set_field_select('tool_monitor_subscriptions', 'inactivedate', '0', 'id ' . $sql, $params);
            return $success;
        }
        return false;
    }

    /**
     * Deactivates a group of subscriptions based on an input array of ids.
     *
     * @since 3.1.1
     * @param array $ids of subscription ids.
     * @return bool true if the operation was successful, false otherwise.
     */
    public static function deactivate_subscriptions(array $ids) {
        global $DB;
        if (!empty($ids)) {
            $inactivedate = time();
            list($sql, $params) = $DB->get_in_or_equal($ids);
            $success = $DB->set_field_select('tool_monitor_subscriptions', 'inactivedate', $inactivedate, 'id ' . $sql,
                                             $params);
            return $success;
        }
        return false;
    }

    /**
     * Deletes subscriptions which have been inactive for a period of time.
     *
     * @since 3.1.1
     * @param int $userid if provided, only this user's stale subscriptions will be deleted.
     * @return bool true if the operation was successful, false otherwise.
     */
    public static function delete_stale_subscriptions($userid = 0) {
        global $DB;
        // Get the expiry duration, in days.
        $cutofftime = strtotime("-" . self::INACTIVE_SUBSCRIPTION_LIFESPAN_IN_DAYS . " days", time());

        if (!empty($userid)) {
            // Remove any stale subscriptions for the desired user only.
            $success = $DB->delete_records_select('tool_monitor_subscriptions',
                                                  'userid = ? AND inactivedate < ? AND inactivedate <> 0',
                                                  array($userid, $cutofftime));

        } else {
            // Remove all stale subscriptions.
            $success = $DB->delete_records_select('tool_monitor_subscriptions',
                                                  'inactivedate < ? AND inactivedate <> 0',
                                                  array($cutofftime));
        }
        return $success;
    }

    /**
     * Check whether a subscription is active.
     *
     * @since 3.1.1
     * @param \tool_monitor\subscription $subscription instance.
     * @return bool true if the subscription is active, false otherwise.
     */
    public static function subscription_is_active(subscription $subscription) {
        return empty($subscription->inactivedate);
    }
}
