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
 * @package    tool_monitor
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_monitor\privacy;

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\contextlist;
use \core_privacy\local\request\approved_contextlist;
use \core_privacy\local\request\transform;
use \core_privacy\local\request\writer;
use \tool_monitor\subscription_manager;
use \tool_monitor\rule_manager;

/**
 * Privacy provider for tool_monitor
 *
 * @package    tool_monitor
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements \core_privacy\local\metadata\provider, \core_privacy\local\request\plugin\provider {

    /**
     * Get information about the user data stored by this plugin.
     *
     * @param  collection $collection An object for storing metadata.
     * @return collection The metadata.
     */
    public static function get_metadata(collection $collection) : collection {
        $toolmonitorrules = [
            'description' => 'privacy:metadata:description',
            'name' => 'privacy:metadata:name',
            'userid' => 'privacy:metadata:userid',
            'plugin' => 'privacy:metadata:plugin',
            'eventname' => 'privacy:metadata:eventname',
            'template' => 'privacy:metadata:template',
            'frequency' => 'privacy:metadata:frequency',
            'timewindow' => 'privacy:metadata:timewindow',
            'timemodified' => 'privacy:metadata:timemodifiedrule',
            'timecreated' => 'privacy:metadata:timecreatedrule'
        ];
        $toolmonitorsubscriptions = [
            'userid' => 'privacy:metadata:useridsub',
            'timecreated' => 'privacy:metadata:timecreatedsub',
            'lastnotificationsent' => 'privacy:metadata:lastnotificationsent',
            'inactivedate' => 'privacy:metadata:inactivedate'
        ];
        // Tool monitor history doesn't look like it is used at all.
        $toolmonitorhistory = [
            'userid' => 'privacy:metadata:useridhistory',
            'timesent' => 'privacy:metadata:timesent'
        ];
        $collection->add_database_table('tool_monitor_rules', $toolmonitorrules, 'privacy:metadata:rulessummary');
        $collection->add_database_table('tool_monitor_subscriptions', $toolmonitorsubscriptions,
                'privacy:metadata:subscriptionssummary');
        $collection->add_database_table('tool_monitor_history', $toolmonitorhistory, 'privacy:metadata:historysummary');
        $collection->link_subsystem('core_message', 'privacy:metadata:messagesummary');
        return $collection;
    }

    /**
     * Return all contexts for this userid. In this situation the user context.
     *
     * @param  int $userid The user ID.
     * @return contextlist The list of context IDs.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        $params = ['useridrules' => $userid, 'useridsubscriptions' => $userid, 'contextuserrule' => CONTEXT_USER,
                'contextusersub' => CONTEXT_USER];
        $sql = "SELECT DISTINCT ctx.id
                  FROM {context} ctx
             LEFT JOIN {tool_monitor_rules} mr ON ctx.instanceid = mr.userid AND ctx.contextlevel = :contextuserrule
                       AND mr.userid = :useridsubscriptions
             LEFT JOIN {tool_monitor_subscriptions} ms ON ctx.instanceid = ms.userid AND ctx.contextlevel = :contextusersub
                       AND ms.userid = :useridrules
                 WHERE ms.id IS NOT NULL OR mr.id IS NOT NULL";

        $contextlist = new contextlist();
        $contextlist->add_from_sql($sql, $params);
        return $contextlist;
    }

    /**
     * Export all event monitor information for the list of contexts and this user.
     *
     * @param  approved_contextlist $contextlist The list of approved contexts for a user.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;
        // Export rules.
        $context = \context_user::instance($contextlist->get_user()->id);
        $rules = $DB->get_records('tool_monitor_rules', ['userid' => $contextlist->get_user()->id]);
        if ($rules) {
            static::export_monitor_rules($rules, $context);
        }
        // Export subscriptions.
        $subscriptions = subscription_manager::get_user_subscriptions(0, 0, $contextlist->get_user()->id);
        if ($subscriptions) {
            static::export_monitor_subscriptions($subscriptions, $context);
        }
    }

    /**
     * Delete all user data for this context.
     *
     * @param  \context $context The context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        // Only delete data for user contexts.
        if ($context->contextlevel == CONTEXT_USER) {
            static::delete_user_data($context->instanceid);
        }
    }

    /**
     * Delete all user data for this user only.
     *
     * @param  approved_contextlist $contextlist The list of approved contexts for a user.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        static::delete_user_data($contextlist->get_user()->id);
    }

    /**
     * This does the deletion of user data for the event monitor.
     *
     * @param  int $userid The user ID
     */
    protected static function delete_user_data(int $userid) {
        global $DB;
        // Delete this user's subscriptions first.
        subscription_manager::delete_user_subscriptions($userid);
        // Because we only use user contexts the instance ID is the user ID.
        // Get the rules and check if this user has the capability to delete them.
        $rules = $DB->get_records('tool_monitor_rules', ['userid' => $userid]);
        foreach ($rules as $ruledata) {
            $rule = rule_manager::get_rule($ruledata);
            // If no-one is suscribed to the rule then it is safe to delete.
            if ($rule->can_manage_rule($userid) && subscription_manager::count_rule_subscriptions($rule->id) == 0) {
                $rule->delete_rule();
            }
        }
    }

    /**
     * This formats and then exports the monitor rules.
     *
     * @param  array $rules The monitor rules.
     * @param  context_user $context The user context
     */
    protected static function export_monitor_rules(array $rules, \context_user $context) {
        foreach ($rules as $rule) {
            $rule = rule_manager::get_rule($rule);
            $ruledata = new \stdClass();
            $ruledata->name = $rule->name;
            $ruledata->eventname = $rule->get_event_name();
            $ruledata->description = $rule->get_description($context);
            $ruledata->plugin = $rule->get_plugin_name();
            $ruledata->template = $rule->template;
            $ruledata->frequency = $rule->get_filters_description();
            $ruledata->course = $rule->get_course_name($context);
            $ruledata->timecreated = transform::datetime($rule->timecreated);
            $ruledata->timemodified = transform::datetime($rule->timemodified);
            writer::with_context($context)->export_data([get_string('privacy:createdrules', 'tool_monitor'),
                    $rule->name . '_' . $rule->id], $ruledata);
        }
    }

    /**
     * This formats and then exports the event monitor subscriptions.
     *
     * @param  array $subscriptions Subscriptions
     * @param  \context_user $context The user context
     */
    protected static function export_monitor_subscriptions(array $subscriptions, \context_user $context) {
        foreach ($subscriptions as $subscription) {
            $subscriptiondata = new \stdClass();
            $subscriptiondata->instancename = $subscription->get_instance_name();
            $subscriptiondata->eventname = $subscription->get_event_name();
            $subscriptiondata->frequency = $subscription->get_filters_description();
            $subscriptiondata->name = $subscription->get_name($context);
            $subscriptiondata->description = $subscription->get_description($context);
            $subscriptiondata->pluginname = $subscription->get_plugin_name();
            $subscriptiondata->course = $subscription->get_course_name($context);
            $subscriptiondata->timecreated = transform::datetime($subscription->timecreated);
            $subscriptiondata->lastnotificationsent = transform::datetime($subscription->lastnotificationsent);
            writer::with_context($context)->export_data([get_string('privacy:subscriptions', 'tool_monitor'),
                    $subscriptiondata->name . '_' . $subscription->id, $subscriptiondata->course, $subscriptiondata->instancename],
                    $subscriptiondata);
        }
    }
}
