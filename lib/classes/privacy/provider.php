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
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\privacy;

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\contextlist;
use \core_privacy\local\request\approved_contextlist;

/**
 * Privacy class for requesting user data.
 *
 * @package    core
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements \core_privacy\local\metadata\provider, \core_privacy\local\request\subsystem\provider {

    /**
     * Returns information about the user data stored in this component.
     *
     * @param  collection $collection A list of information about this component
     * @return collection The collection object filled out with information about this component.
     */
    public static function get_metadata(collection $collection) {
        // These tables are really data about site configuration and not user data.

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

        // The events_queue includes information about pending events tasks.
        // These are stored for short periods whilst being processed into other locations.
        $collection->add_database_table('events_queue', [
                'eventdata'     => 'privacy:metadata:events_queue:eventdata',
                'stackdump'     => 'privacy:metadata:events_queue:stackdump',
                'userid'        => 'privacy:metadata:events_queue:userid',
                'timecreated'   => 'privacy:metadata:events_queue:timecreated',
            ], 'privacy:metadata:events_queue');

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int $userid The user to search.
     * @return  contextlist $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid($userid) {
        return new contextlist();
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        // None of the core tables should be exported.
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        // None of the the data from these tables should be deleted.
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        // None of the the data from these tables should be deleted.
        // Note: Although it may be tempting to delete the adhoc task data, do not do so.
        // The delete process is run as an adhoc task.
    }
}
