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
 * @package    core_portfolio
 * @copyright  2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_portfolio\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\metadata\collection;
use core_privacy\local\request\context;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\transform;

/**
 * Provider for the portfolio API.
 *
 * @copyright  2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        // The core portfolio system stores preferences related to the other portfolio subsystems.
        \core_privacy\local\metadata\provider,
        \core_privacy\local\request\plugin\provider,
        // The portfolio subsystem will be called by other components.
        \core_privacy\local\request\subsystem\plugin_provider {

    /**
     * Returns meta data about this system.
     *
     * @param   collection $collection The initialised collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) {
        $collection->add_database_table('portfolio_instance_user', [
            'instance' => 'privacy:metadata:instance',
            'userid' => 'privacy:metadata:userid',
            'name' => 'privacy:metadata:name',
            'value' => 'privacy:metadata:value'
        ], 'privacy:metadata:instancesummary');

        $collection->add_database_table('portfolio_log', [
            'userid' => 'privacy:metadata:portfolio_log:userid',
            'time' => 'privacy:metadata:portfolio_log:time',
            'caller_class' => 'privacy:metadata:portfolio_log:caller_class',
            'caller_component' => 'privacy:metadata:portfolio_log:caller_component',
        ], 'privacy:metadata:portfolio_log');

        // Temporary data is not exported/deleted in privacy API. It is cleaned by cron.
        $collection->add_database_table('portfolio_tempdata', [
            'data' => 'privacy:metadata:portfolio_tempdata:data',
            'expirytime' => 'privacy:metadata:portfolio_tempdata:expirytime',
            'userid' => 'privacy:metadata:portfolio_tempdata:userid',
            'instance' => 'privacy:metadata:portfolio_tempdata:instance',
        ], 'privacy:metadata:portfolio_tempdata');

        $collection->add_plugintype_link('portfolio', [], 'privacy:metadata');
        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int $userid The user to search.
     * @return  contextlist $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid($userid) {
        $sql = "SELECT ctx.id
                  FROM {context} ctx
                 WHERE ctx.instanceid = :userid AND ctx.contextlevel = :usercontext
                  AND (EXISTS (SELECT 1 FROM {portfolio_instance_user} WHERE userid = :userid1) OR
                       EXISTS (SELECT 1 FROM {portfolio_log} WHERE userid = :userid2))
                 ";
        $params = ['userid' => $userid, 'usercontext' => CONTEXT_USER, 'userid1' => $userid, 'userid2' => $userid];
        $contextlist = new contextlist();
        $contextlist->add_from_sql($sql, $params);
        return $contextlist;
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        if ($contextlist->get_component() != 'core_portfolio') {
            return;
        }

        $correctusercontext = array_filter($contextlist->get_contexts(), function($context) use ($contextlist) {
            if ($context->contextlevel == CONTEXT_USER && $context->instanceid == $contextlist->get_user()->id) {
                return $context;
            }
        });

        if (empty($correctusercontext)) {
            return;
        }

        $usercontext = array_shift($correctusercontext);

        $sql = "SELECT pi.id AS instanceid, pi.name,
                       piu.id AS preferenceid, piu.name AS preference, piu.value,
                       pl.id AS logid, pl.time AS logtime, pl.caller_class, pl.caller_file,
                       pl.caller_component, pl.returnurl, pl.continueurl
                  FROM {portfolio_instance} pi
             LEFT JOIN {portfolio_instance_user} piu ON piu.instance = pi.id AND piu.userid = :userid1
             LEFT JOIN {portfolio_log} pl ON pl.portfolio = pi.id AND pl.userid = :userid2
                 WHERE piu.id IS NOT NULL OR pl.id IS NOT NULL";
        $params = ['userid1' => $usercontext->instanceid, 'userid2' => $usercontext->instanceid];
        $instances = [];
        $rs = $DB->get_recordset_sql($sql, $params);
        foreach ($rs as $record) {
            $instances += [$record->name =>
                (object)[
                    'name' => $record->name,
                    'preferences' => [],
                    'logs' => [],
                ]
            ];
            if ($record->preferenceid) {
                $instances[$record->name]->preferences[$record->preferenceid] = (object)[
                    'name' => $record->preference,
                    'value' => $record->value,
                ];
            }
            if ($record->logid) {
                $instances[$record->name]->logs[$record->logid] = (object)[
                    'time' => transform::datetime($record->logtime),
                    'caller_class' => $record->caller_class,
                    'caller_file' => $record->caller_file,
                    'caller_component' => $record->caller_component,
                    'returnurl' => $record->returnurl,
                    'continueurl' => $record->continueurl
                ];
            }
        }
        $rs->close();

        if (!empty($instances)) {
            foreach ($instances as &$instance) {
                if (!empty($instance->preferences)) {
                    $instance->preferences = array_values($instance->preferences);
                } else {
                    unset($instance->preferences);
                }
                if (!empty($instance->logs)) {
                    $instance->logs = array_values($instance->logs);
                } else {
                    unset($instance->logs);
                }
            }
            \core_privacy\local\request\writer::with_context($contextlist->current())->export_data(
                    [get_string('privacy:path', 'portfolio')], (object) $instances);
        }
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;
        // Context could be anything, BEWARE!
        if ($context->contextlevel == CONTEXT_USER) {
            $DB->delete_records('portfolio_instance_user', ['userid' => $context->instanceid]);
            $DB->delete_records('portfolio_tempdata', ['userid' => $context->instanceid]);
            $DB->delete_records('portfolio_log', ['userid' => $context->instanceid]);
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        if ($contextlist->get_component() != 'core_portfolio') {
            return;
        }

        $correctusercontext = array_filter($contextlist->get_contexts(), function($context) use ($contextlist) {
            if ($context->contextlevel == CONTEXT_USER && $context->instanceid == $contextlist->get_user()->id) {
                return $context;
            }
        });

        if (empty($correctusercontext)) {
            return;
        }

        $usercontext = array_shift($correctusercontext);

        $DB->delete_records('portfolio_instance_user', ['userid' => $usercontext->instanceid]);
        $DB->delete_records('portfolio_tempdata', ['userid' => $usercontext->instanceid]);
        $DB->delete_records('portfolio_log', ['userid' => $usercontext->instanceid]);
    }

    /**
     * Export all portfolio data from each portfolio plugin for the specified userid and context.
     *
     * @param   int         $userid The user to export.
     * @param   \context    $context The context to export.
     * @param   array       $subcontext The subcontext within the context to export this information to.
     * @param   array       $linkarray The weird and wonderful link array used to display information for a specific item
     */
    public static function export_portfolio_user_data($userid, \context $context, array $subcontext, array $linkarray) {
        static::call_plugin_method('export_portfolio_user_data', [$userid, $context, $subcontext, $linkarray]);
    }

    /**
     * Deletes all user content for a context in all portfolio plugins.
     *
     * @param  \context $context The context to delete user data for.
     */
    public static function delete_portfolio_for_context(\context $context) {
        static::call_plugin_method('delete_portfolio_for_context', [$context]);
    }

    /**
     * Deletes all user content for a user in a context in all portfolio plugins.
     *
     * @param  int      $userid    The user to delete
     * @param  \context $context   The context to refine the deletion.
     */
    public static function delete_portfolio_for_user($userid, \context $context) {
        static::call_plugin_method('delete_portfolio_for_user', [$userid, $context]);
    }

    /**
     * Internal method for looping through all of the portfolio plugins and calling a method.
     *
     * @param  string $methodname Name of the method to call on the plugins.
     * @param  array $params     The parameters that go with the method being called.
     */
    protected static function call_plugin_method($methodname, $params) {
        // Note: Even if portfolio is _now_ disabled, there may be legacy data to export.
        \core_privacy\manager::plugintype_class_callback('portfolio', portfolio_provider::class, $methodname, $params);
    }
}
