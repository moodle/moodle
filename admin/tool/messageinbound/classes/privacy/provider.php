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
 * @package    tool_messageinbound
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_messageinbound\privacy;
defined('MOODLE_INTERNAL') || die();

use context;
use context_user;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

/**
 * Data provider class.
 *
 * @package    tool_messageinbound
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider {

    /**
     * Returns metadata.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {

        $collection->add_database_table('messageinbound_messagelist', [
            'messageid' => 'privacy:metadata:messagelist:messageid',
            'userid' => 'privacy:metadata:messagelist:userid',
            'address' => 'privacy:metadata:messagelist:address',
            'timecreated' => 'privacy:metadata:messagelist:timecreated',
        ], 'privacy:metadata:messagelist');

        // Arguably the keys are handled by \core\message\inbound\address_manager and thus could/should be handled by core.
        $collection->add_subsystem_link('core_userkey', [], 'privacy:metadata:coreuserkey');

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return \contextlist $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): \core_privacy\local\request\contextlist {
        $contextlist = new \core_privacy\local\request\contextlist();

        // Always add the user context so we're sure we're not dodging user keys, besides it's not costly to do so.
        $contextlist->add_user_context($userid);

        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();

        if (!is_a($context, \context_user::class)) {
            return;
        }

        // Add user if any messagelist data exists.
        if ($DB->record_exists('messageinbound_messagelist', ['userid' => $context->instanceid])) {
            // Only using user context, so instance ID will be the only user ID.
            $userlist->add_user($context->instanceid);
        }

        // Add users based on userkey (since we also delete those).
        \core_userkey\privacy\provider::get_user_contexts_with_script($userlist, $context, 'messageinbound_handler');
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;
        if (!static::approved_contextlist_contains_my_context($contextlist)) {
            // We only care about the user's user context.
            return;
        }

        $userid = $contextlist->get_user()->id;
        $context = context_user::instance($userid);
        $path = [get_string('messageinbound', 'tool_messageinbound')];

        // Export user keys.
        \core_userkey\privacy\provider::export_userkeys($context, $path, 'messageinbound_handler');

        // Export the message list.
        $data = [];
        $recordset = $DB->get_recordset('messageinbound_messagelist', ['userid' => $userid], 'timecreated, id');
        foreach ($recordset as $record) {
            $data[] = [
                'received_at' => $record->address,
                'timecreated' => transform::datetime($record->timecreated),
            ];
        }
        $recordset->close();
        writer::with_context($context)->export_data($path, (object) ['messages_pending_validation' => $data]);
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(context $context) {
        global $DB;
        if ($context->contextlevel != CONTEXT_USER) {
            return;
        }

        static::delete_user_data($context->instanceid);
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;
        if (!static::approved_contextlist_contains_my_context($contextlist)) {
            // We only care about the user's user context.
            return;
        }

        static::delete_user_data($contextlist->get_user()->id);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist       $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        $context = $userlist->get_context();
        $userids = $userlist->get_userids();

        // Since this falls within a user context, only that user should be valid.
        if ($context->contextlevel != CONTEXT_USER || count($userids) != 1 || $context->instanceid != $userids[0]) {
            return;
        }

        static::delete_user_data($userids[0]);
    }

    /**
     * Delete a user's data.
     *
     * @param int $userid The user ID.
     * @return void
     */
    protected static function delete_user_data($userid) {
        global $DB;
        $DB->delete_records_select('messageinbound_messagelist', 'userid = :userid', ['userid' => $userid]);
        \core_userkey\privacy\provider::delete_userkeys('messageinbound_handler', $userid);
    }

    /**
     * Return whether the contextlist contains our own context.
     *
     * @param approved_contextlist $contextlist The contextlist
     * @return bool
     */
    protected static function approved_contextlist_contains_my_context(approved_contextlist $contextlist) {
        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel == CONTEXT_USER && $context->instanceid == $userid) {
                return true;
            }
        }
        return false;
    }

}
