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
 * Data provider.
 *
 * @package    tool_log
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_log\privacy;
defined('MOODLE_INTERNAL') || die();

use context;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;
use tool_log\log\manager;

/**
 * Data provider class.
 *
 * @package    tool_log
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\subsystem\provider,
    \core_privacy\local\request\core_userlist_provider {

    /**
     * Returns metadata.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_plugintype_link('logstore', [], 'privacy:metadata:logstore');
        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return contextlist $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): \core_privacy\local\request\contextlist {
        $contextlist = new \core_privacy\local\request\contextlist();
        static::call_subplugins_method_with_args('add_contexts_for_userid', [$contextlist, $userid]);
        return $contextlist;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   \core_privacy\local\request\userlist    $userlist   The userlist containing the list of users who have data in
     * this context/plugin combination.
     */
    public static function get_users_in_context(\core_privacy\local\request\userlist $userlist) {
        $interface = \tool_log\local\privacy\logstore_userlist_provider::class;
        static::call_subplugins_method_with_args('add_userids_for_context', [$userlist], $interface);
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        if (get_config('tool_log', 'exportlog')) {
            static::call_subplugins_method_with_args('export_user_data', [$contextlist]);
        }
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(context $context) {
        static::call_subplugins_method_with_args('delete_data_for_all_users_in_context', [$context]);
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        static::call_subplugins_method_with_args('delete_data_for_user', [$contextlist]);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param \core_privacy\local\request\approved_userlist $userlist The approved context and user information to delete
     * information for.
     */
    public static function delete_data_for_users(\core_privacy\local\request\approved_userlist $userlist) {
        $interface = \tool_log\local\privacy\logstore_userlist_provider::class;
        static::call_subplugins_method_with_args('delete_data_for_userlist', [$userlist], $interface);
    }

    /**
     * Invoke the subplugins method with arguments.
     *
     * @param string $method The method name.
     * @param array $args The arguments.
     * @param string $interface The interface to use. By default uses the logstore_provider.
     * @return void
     */
    protected static function call_subplugins_method_with_args($method, array $args = [], string $interface = null) {
        if (!isset($interface)) {
            $interface = \tool_log\local\privacy\logstore_provider::class;
        }
        \core_privacy\manager::plugintype_class_callback('logstore', $interface, $method, $args);
    }
}
