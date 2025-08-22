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
 * @package    core
 * @category   privacy
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

/**
 * Privacy class for requesting user data.
 *
 * @package    core
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        \core_privacy\local\metadata\provider,
        \core_privacy\local\request\subsystem\provider,
        \core_privacy\local\request\core_userlist_provider {

    /**
     * Returns information about the user data stored in this component.
     *
     * @param  collection $collection A list of information about this component
     * @return collection The collection object filled out with information about this component.
     */
    public static function get_metadata(collection $collection): collection {
        // Except for moodlenet_share_progress, these tables are really data about site configuration and not user data.

        // The config_log includes information about which user performed a configuration change.
        // The value and oldvalue may contain sensitive information such as accounts for service passwords..
        // This is not considered to be user data.
        $collection->add_database_table('config_log', [
                'userid'        => 'privacy:metadata:config_log:userid',
                'timemodified'  => 'privacy:metadata:config_log:timemodified',
                'plugin'        => 'privacy:metadata:config_log:plugin',
                'name'          => 'privacy:metadata:config_log:name',
                'value'         => 'privacy:metadata:config_log:value',
                'oldvalue'      => 'privacy:metadata:config_log:oldvalue',
            ], 'privacy:metadata:config_log');

        // The upgrade_log includes information about which user performed an upgrade.
        // This is not considered to be user data.
        $collection->add_database_table('upgrade_log', [
                'type'          => 'privacy:metadata:upgrade_log:type',
                'plugin'        => 'privacy:metadata:upgrade_log:plugin',
                'version'       => 'privacy:metadata:upgrade_log:version',
                'targetversion' => 'privacy:metadata:upgrade_log:targetversion',
                'info'          => 'privacy:metadata:upgrade_log:info',
                'details'       => 'privacy:metadata:upgrade_log:details',
                'backtrace'     => 'privacy:metadata:upgrade_log:backtrace',
                'userid'        => 'privacy:metadata:upgrade_log:userid',
                'timemodified'  => 'privacy:metadata:upgrade_log:timemodified',
            ], 'privacy:metadata:upgrade_log');

        // The task_adhoc includes information about pending adhoc tasks, some of which may be run as a user.
        // These are removed as the task completes.
        $collection->add_database_table('task_adhoc', [
                'component'     => 'privacy:metadata:task_adhoc:component',
                'nextruntime'   => 'privacy:metadata:task_adhoc:nextruntime',
                'userid'        => 'privacy:metadata:task_adhoc:userid',
            ], 'privacy:metadata:task_adhoc');

        // The task_log table stores debugging data for tasks.
        // These are cleaned regularly and intended purely for debugging.
        $collection->add_database_table('task_log', [
                'component'     => 'privacy:metadata:task_log:component',
                'userid'        => 'privacy:metadata:task_log:userid',
            ], 'privacy:metadata:task_log');

        // The events_queue includes information about pending events tasks.
        // These are stored for short periods whilst being processed into other locations.
        $collection->add_database_table('events_queue', [
                'eventdata'     => 'privacy:metadata:events_queue:eventdata',
                'stackdump'     => 'privacy:metadata:events_queue:stackdump',
                'userid'        => 'privacy:metadata:events_queue:userid',
                'timecreated'   => 'privacy:metadata:events_queue:timecreated',
            ], 'privacy:metadata:events_queue');

        // The log table is defined in core but used in logstore_legacy.
        $collection->add_database_table('log', [
            'time' => 'privacy:metadata:log:time',
            'userid' => 'privacy:metadata:log:userid',
            'ip' => 'privacy:metadata:log:ip',
            'action' => 'privacy:metadata:log:action',
            'url' => 'privacy:metadata:log:url',
            'info' => 'privacy:metadata:log:info'
        ], 'privacy:metadata:log');

        // The oauth2_refresh_token stores refresh tokens, allowing ongoing access to select oauth2 services.
        // Such tokens are not considered to be user data.
        $collection->add_database_table('oauth2_refresh_token', [
            'timecreated' => 'privacy:metadata:oauth2_refresh_token:timecreated',
            'timemodified' => 'privacy:metadata:oauth2_refresh_token:timemodified',
            'userid' => 'privacy:metadata:oauth2_refresh_token:userid',
            'issuerid' => 'privacy:metadata:oauth2_refresh_token:issuerid',
            'token' => 'privacy:metadata:oauth2_refresh_token:token',
            'scopehash' => 'privacy:metadata:oauth2_refresh_token:scopehash'
        ], 'privacy:metadata:oauth2_refresh_token');

        // The moodlenet_share_progress includes details of an attempted share of a resource to MoodleNet.
        $collection->add_database_table('moodlenet_share_progress', [
            'type' => 'privacy:metadata:moodlenet_share_progress:type',
            'courseid' => 'privacy:metadata:moodlenet_share_progress:courseid',
            'cmid' => 'privacy:metadata:moodlenet_share_progress:cmid',
            'userid' => 'privacy:metadata:moodlenet_share_progress:userid',
            'timecreated' => 'privacy:metadata:moodlenet_share_progress:timecreated',
            'resourceurl' => 'privacy:metadata:moodlenet_share_progress:resourceurl',
            'status' => 'privacy:metadata:moodlenet_share_progress:status',
        ], 'privacy:metadata:moodlenet_share_progress');

        // This resourceurl field is an external link from MoodleNet.
        $collection->add_external_location_link('moodlenet_share_progress', [
            'resourceurl' => 'privacy:metadata:moodlenet_share_progress:resourceurl',
        ], 'privacy:metadata:moodlenet_share_progress');

        // The shortlink table includes data that associates a user with a shortlink URL.
        $collection->add_database_table('shortlink', [
            'shortcode' => 'privacy:metadata:shortlink:shortcode',
            'userid' => 'privacy:metadata:shortlink:userid',
            'component' => 'privacy:metadata:shortlink:component',
            'linktype' => 'privacy:metadata:shortlink:linktype',
            'identifier' => 'privacy:metadata:shortlink:identifier',
        ], 'privacy:metadata:shortlink');

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int $userid The user to search.
     * @return  contextlist $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();

        // MoodleNet share progress uses the user context.
        $sql = "SELECT ctx.id
                  FROM {context} ctx
                  JOIN {moodlenet_share_progress} msp ON ctx.instanceid = msp.userid
                       AND ctx.contextlevel = :contextlevel
                 WHERE msp.userid = :userid";
        $params = ['userid' => $userid, 'contextlevel' => CONTEXT_USER];
        $contextlist->add_from_sql($sql, $params);

        // Shortlink.
        $sql = "SELECT ctx.id
                  FROM {context} ctx
                  JOIN {shortlink} sl ON ctx.instanceid = sl.userid
                       AND ctx.contextlevel = :contextlevel
                 WHERE sl.userid = :userid";
        $params = ['userid' => $userid, 'contextlevel' => CONTEXT_USER];
        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        // MoodleNet share progress uses the user context.
        if ($context->contextlevel == CONTEXT_USER) {
            // Get all distinct userids from the table.
            $sql = "SELECT DISTINCT userid
                      FROM {moodlenet_share_progress}
                     WHERE userid = :userid";
            $params = ['userid' => $context->instanceid];
            $userlist->add_from_sql('userid', $sql, $params);
        }

        // Shortlink.
        if ($context->contextlevel == CONTEXT_USER) {
            // Get all distinct userids from the table.
            $sql = "SELECT DISTINCT userid
                      FROM {shortlink}
                     WHERE userid = :userid";
            $params = ['userid' => $context->instanceid];
            $userlist->add_from_sql('userid', $sql, $params);
        }
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        // Except for moodlenet_share_progress and shortlink, none of the core tables should be exported.
        global $DB;

        foreach ($contextlist as $context) {
            if ($context->contextlevel == CONTEXT_USER && $context->instanceid == $contextlist->get_user()->id) {
                // Get the user's MoodleNet share progress data.
                $sharedata = $DB->get_records('moodlenet_share_progress', ['userid' => $context->instanceid]);
                $subcontext = get_string('privacy:metadata:moodlenet_share_progress', 'moodle');
                writer::with_context($context)->export_data([$subcontext], (object) $sharedata);

                // Get the user's shortlink data.
                $shortlinkdata = $DB->get_records('shortlink', ['userid' => $context->instanceid]);
                $subcontext = get_string('privacy:metadata:shortlink', 'moodle');
                writer::with_context($context)->export_data([$subcontext], (object) $shortlinkdata);
            }
        }
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        // Except for moodlenet_share_progress and shortlink, none of the data from these tables should be deleted.
        global $DB;

        // MoodleNet share progress uses the user context.
        if ($context->contextlevel == CONTEXT_USER) {
            $DB->delete_records('moodlenet_share_progress', ['userid' => $context->instanceid]);
        }

        // Shortlink.
        if ($context->contextlevel == CONTEXT_USER) {
            $DB->delete_records('shortlink', ['userid' => $context->instanceid]);
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        // Except for moodlenet_share_progress and shortlink, none of the data from these tables should be deleted.
        // Note: Although it may be tempting to delete the adhoc task data, do not do so.
        // The delete process is run as an adhoc task.
        global $DB;

        foreach ($contextlist as $context) {
            if ($context->contextlevel == CONTEXT_USER && $context->instanceid == $contextlist->get_user()->id) {
                // MoodleNet share progress uses the user context.
                $DB->delete_records('moodlenet_share_progress', ['userid' => $context->instanceid]);
                // Shortlink.
                $DB->delete_records('shortlink', ['userid' => $context->instanceid]);
            }
        }
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist       $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        // Except for moodlenet_share_progress and shortlink, none of the data from these tables should be deleted.
        // Note: Although it may be tempting to delete the adhoc task data, do not do so.
        // The delete process is run as an adhoc task.
        global $DB;

        $context = $userlist->get_context();

        if (!in_array($context->instanceid, $userlist->get_userids())) {
            return;
        }

        if ($context->contextlevel == CONTEXT_USER) {
            // MoodleNet share progress uses the user context.
            $DB->delete_records('moodlenet_share_progress', ['userid' => $context->instanceid]);
            // Shortlink.
            $DB->delete_records('shortlink', ['userid' => $context->instanceid]);
        }
    }
}
