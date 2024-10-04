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

namespace gradepenalty_duedate\privacy;

use context;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

/**
 * Privacy Subsystem for gradepenalty_duedate implementing null_provider.
 *
 * @package   gradepenalty_duedate
 * @copyright 2024 Catalyst IT Australia Pty Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider {

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return contextlist A list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $sql = "SELECT ctx.id
                  FROM {gradepenalty_duedate_rule} gdr
                  JOIN {context} ctx ON ctx.instanceid = gdr.usermodified AND ctx.contextlevel = :contextlevel
                 WHERE gdr.usermodified = :userid";

        $params = ['userid' => $userid, 'contextlevel' => CONTEXT_USER];
        $contextlist = new contextlist();
        $contextlist->add_from_sql($sql, $params);
        return $contextlist;
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist): void {
        global $DB;

        $user = $contextlist->get_user();

        // Get all the rules edited by the user.
        $rules = $DB->get_records('gradepenalty_duedate_rule', ['usermodified' => $user->id]);
        $data = (object) $rules;

        // Write out the data.
        writer::with_context(\context_user::instance($user->id))->export_data([
            get_string('privacy:metadata:gradepenalty_duedate_rule', 'gradepenalty_duedate'),
            'rules',
        ], $data);
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(context $context): void {
        if ($context->contextlevel != CONTEXT_USER) {
            return;
        }
        static::delete_user_data($context->instanceid);
    }

    /**
     * Delete user data in the list of given contexts.
     *
     * @param approved_contextlist $contextlist the list of contexts.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist): void {
        if (empty($contextlist->count())) {
            return;
        }
        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel != CONTEXT_USER) {
                continue;
            }
            if ($context->instanceid == $userid) {
                static::delete_user_data($context->instanceid);
            }
        }
    }

    /**
     * Get the list of users within a specific context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist): void {
        $context = $userlist->get_context();

        if (!$context instanceof \context_user) {
            return;
        }

        $sql = "SELECT DISTINCT u.id
                  FROM {gradepenalty_duedate_rule} gdr
                  JOIN {user} u ON u.id = gdr.usermodified
                 WHERE gdr.usermodified = :userid";
        $params = ['userid' => $context->instanceid];
        $userlist->add_from_sql('id', $sql, $params);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist): void {
        $context = $userlist->get_context();

        if ($context instanceof \context_user) {
            static::delete_user_data($context->instanceid);
        }
    }

    /**
     * Retrieve the user metadata stored by plugin.
     *
     * @param collection $collection Collection of metadata.
     * @return collection Collection of metadata.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table(
            'gradepenalty_duedate_rule',
            [
                'usermodified' => 'privacy:metadata:gradepenalty_duedate_rule:usermodified',
              ],
            'privacy:metadata:gradepenalty_duedate_rule'
        );
        return $collection;
    }

    /**
     * Set the usermodified to 0 instead of deleting the data.
     *
     * @param  int $userid The id of the user.
     */
    protected static function delete_user_data(int $userid): void {
        global $DB;

        // Set the usermodified to 0.
        $DB->set_field_select('gradepenalty_duedate_rule', 'usermodified', 0,
            "usermodified = :userid", ['userid' => $userid]);
    }
}
