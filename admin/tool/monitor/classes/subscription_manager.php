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
        return $DB->insert_record('tool_monitor_subscriptions', $subscription);
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
        return $DB->delete_records('tool_monitor_subscriptions', array('id' => $subscription->id));
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
     *
     * @return bool
     */
    public static function remove_all_subscriptions_for_rule($ruleid) {
        global $DB;
        return $DB->delete_records('tool_monitor_subscriptions', array('ruleid' => $ruleid));
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
     * @return array list of subscriptions
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
}
