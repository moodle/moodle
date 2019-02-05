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
 * Privacy class for requesting user data for auth_oauth2.
 *
 * @package    auth_oauth2
 * @copyright  2018 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace auth_oauth2\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\metadata\collection;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;
use core_privacy\local\request\userlist;
use core_privacy\local\request\approved_userlist;

/**
 * Privacy provider for auth_oauth2
 *
 * @package    auth_oauth2
 * @copyright  2018 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider {

    /**
     * Get information about the user data stored by this plugin.
     *
     * @param  collection $collection An object for storing metadata.
     * @return collection The metadata.
     */
    public static function get_metadata(collection $collection) : collection {
        $authfields = [
            'timecreated' => 'privacy:metadata:auth_oauth2:timecreated',
            'timemodified' => 'privacy:metadata:auth_oauth2:timemodified',
            'usermodified' => 'privacy:metadata:auth_oauth2:usermodified',
            'userid' => 'privacy:metadata:auth_oauth2:userid',
            'issuerid' => 'privacy:metadata:auth_oauth2:issuerid',
            'username' => 'privacy:metadata:auth_oauth2:username',
            'email' => 'privacy:metadata:auth_oauth2:email',
            'confirmtoken' => 'privacy:metadata:auth_oauth2:confirmtoken',
            'confirmtokenexpires' => 'privacy:metadata:auth_oauth2:confirmtokenexpires'
        ];

        $collection->add_database_table('auth_oauth2_linked_login', $authfields, 'privacy:metadata:auth_oauth2:tableexplanation');
        $collection->link_subsystem('core_auth', 'privacy:metadata:auth_oauth2:authsubsystem');

        return $collection;
    }

    /**
     * Return all contexts for this userid. In this situation the user context.
     *
     * @param  int $userid The user ID.
     * @return contextlist The list of context IDs.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        $sql = "SELECT ctx.id
                  FROM {auth_oauth2_linked_login} ao
                  JOIN {context} ctx ON ctx.instanceid = ao.userid AND ctx.contextlevel = :contextlevel
                 WHERE ao.userid = :userid";
        $params = ['userid' => $userid, 'contextlevel' => CONTEXT_USER];
        $contextlist = new contextlist();
        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Get the list of users within a specific context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!$context instanceof \context_user) {
            return;
        }

        $params = [
            'contextuser' => CONTEXT_USER,
            'contextid' => $context->id
        ];

        $sql = "SELECT ctx.instanceid as userid
                  FROM {auth_oauth2_linked_login} ao
                  JOIN {context} ctx
                       ON ctx.instanceid = ao.userid
                       AND ctx.contextlevel = :contextuser
                 WHERE ctx.id = :contextid";

        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Export all oauth2 information for the list of contexts and this user.
     *
     * @param  approved_contextlist $contextlist The list of approved contexts for a user.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        // Export oauth2 linked accounts.
        $context = \context_user::instance($contextlist->get_user()->id);
        $sql = "SELECT ll.id, ll.username, ll.email, ll.timecreated, ll.timemodified, oi.name as issuername
                FROM {auth_oauth2_linked_login} ll JOIN {oauth2_issuer} oi ON oi.id = ll.issuerid
                WHERE ll.userid = :userid";
        if ($oauth2accounts = $DB->get_records_sql($sql, ['userid' => $contextlist->get_user()->id])) {
            foreach ($oauth2accounts as $oauth2account) {
                $data = (object)[
                    'timecreated' => transform::datetime($oauth2account->timecreated),
                    'timemodified' => transform::datetime($oauth2account->timemodified),
                    'issuerid' => $oauth2account->issuername,
                    'username' => $oauth2account->username,
                    'email' => $oauth2account->email
                ];
                writer::with_context($context)->export_data([
                        get_string('privacy:metadata:auth_oauth2', 'auth_oauth2'),
                        $oauth2account->issuername
                    ], $data);
            }
        }
    }

    /**
     * Delete all user data for this context.
     *
     * @param  \context $context The context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        if ($context->contextlevel != CONTEXT_USER) {
            return;
        }
        static::delete_user_data($context->instanceid);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        $context = $userlist->get_context();

        if ($context instanceof \context_user) {
            static::delete_user_data($context->instanceid);
        }
    }

    /**
     * Delete all user data for this user only.
     *
     * @param  approved_contextlist $contextlist The list of approved contexts for a user.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        if (empty($contextlist->count())) {
            return;
        }
        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel != CONTEXT_USER) {
                continue;
            }
            if ($context->instanceid == $userid) {
                // Because we only use user contexts the instance ID is the user ID.
                static::delete_user_data($context->instanceid);
            }
        }
    }

    /**
     * This does the deletion of user data for the auth_oauth2.
     *
     * @param  int $userid The user ID
     */
    protected static function delete_user_data(int $userid) {
        global $DB;

        // Because we only use user contexts the instance ID is the user ID.
        $DB->delete_records('auth_oauth2_linked_login', ['userid' => $userid]);
    }
}
