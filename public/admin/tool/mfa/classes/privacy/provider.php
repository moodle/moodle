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
 * Privacy provider.
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mfa\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\writer;
use core_privacy\local\request\userlist;

/**
 * Privacy provider
 *
 * @package tool_mfa
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\data_provider {

    /**
     * Returns metadata about this plugin's privacy policy.
     *
     * @param   collection $collection The initialised collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table(
            'tool_mfa',
            [
                'id' => 'privacy:metadata:tool_mfa:id',
                'userid' => 'privacy:metadata:tool_mfa:userid',
                'factor' => 'privacy:metadata:tool_mfa:factor',
                'secret' => 'privacy:metadata:tool_mfa:secret',
                'label' => 'privacy:metadata:tool_mfa:label',
                'timecreated' => 'privacy:metadata:tool_mfa:timecreated',
                'createdfromip' => 'privacy:metadata:tool_mfa:createdfromip',
                'timemodified' => 'privacy:metadata:tool_mfa:timemodified',
                'lastverified' => 'privacy:metadata:tool_mfa:lastverified',
            ],
            'privacy:metadata:tool_mfa'
        );

        $collection->add_database_table(
            'tool_mfa_secrets',
            [
                'userid' => 'privacy:metadata:tool_mfa_secrets:userid',
                'factor' => 'privacy:metadata:tool_mfa_secrets:factor',
                'secret' => 'privacy:metadata:tool_mfa_secrets:secret',
                'sessionid' => 'privacy:metadata:tool_mfa_secrets:sessionid',
            ],
            'privacy:metadata:tool_mfa_secrets'
        );

        $collection->add_database_table(
            'tool_mfa_auth',
            [
                'userid' => 'privacy:metadata:tool_mfa_auth:userid',
                'lastverified' => 'privacy:metadata:tool_mfa_auth:lastverified',
            ],
            'privacy:metadata:tool_mfa_auth'
        );

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the given user.
     *
     * @param int $userid the userid to search.
     * @return contextlist the contexts in which data is contained.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new \core_privacy\local\request\contextlist();
        $contextlist->add_user_context($userid);
        $contextlist->add_system_context();
        return $contextlist;
    }

    /**
     * Gets the list of users who have data with a context. Secrets context is a subset of this table.
     *
     * @param userlist $userlist the userlist containing users who have data in this context.
     * @return void
     */
    public static function get_users_in_context(userlist $userlist): void {
        $context = $userlist->get_context();
        // If current context is system, all users are contained within, get all users.
        if ($context->contextlevel == CONTEXT_SYSTEM) {
            $sql = "
            SELECT *
            FROM {tool_mfa}";
            $userlist->add_from_sql('userid', $sql, []);
        }
    }

    /**
     * Exports all data stored in provided contexts for user. Secrets should not be exported as they are transient.
     *
     * @param approved_contextlist $contextlist the list of contexts to export for.
     * @return void
     */
    public static function export_user_data(approved_contextlist $contextlist): void {
        global $DB;
        $userid = $contextlist->get_user()->id;
        foreach ($contextlist as $context) {
            // If not in system context, exit loop.
            if ($context->contextlevel == CONTEXT_SYSTEM) {
                $parentclass = [];

                // Get records for user ID.
                $rows = $DB->get_records('tool_mfa', ['userid' => $userid]);

                if (count($rows) > 0) {
                    $i = 0;
                    foreach ($rows as $row) {
                        $parentclass[$i]['userid'] = $row->userid;
                        $timecreated = \core_privacy\local\request\transform::datetime($row->timecreated);
                        $parentclass[$i]['factor'] = $row->factor;
                        $parentclass[$i]['timecreated'] = $timecreated;
                        $parentclass[$i]['createdfromip'] = $row->createdfromip;
                        $timemodified = \core_privacy\local\request\transform::datetime($row->timemodified);
                        $parentclass[$i]['timemodified'] = $timemodified;
                        $lastverified = \core_privacy\local\request\transform::datetime($row->lastverified);
                        $parentclass[$i]['lastverified'] = $lastverified;
                        $parentclass[$i]['revoked'] = $row->revoked;
                        $i++;
                    }
                }

                // Also get lastverified auth time for user, and add.
                $lastverifiedauth = $DB->get_field('tool_mfa_auth', 'lastverified', ['userid' => $userid]);
                if (!empty($lastverifiedauth)) {
                    $lastverifiedauth = \core_privacy\local\request\transform::datetime($lastverifiedauth);
                    $parentclass['lastverifiedauth'] = $lastverifiedauth;
                }

                writer::with_context($context)->export_data(
                    [get_string('privacy:metadata:tool_mfa', 'tool_mfa')],
                    (object) $parentclass);
            }
        }
    }

    /**
     * Deletes data for all users in context.
     *
     * @param context $context The context to delete for.
     * @return void
     */
    public static function delete_data_for_all_users_in_context(\context $context): void {
        global $DB;
        // All data contained in system context.
        if ($context->contextlevel == CONTEXT_SYSTEM) {
            $DB->delete_records('tool_mfa', []);
            $DB->delete_records('tool_mfa_secrets', []);
            $DB->delete_records('tool_mfa_auth', []);
        }
    }

    /**
     * Deletes all data in all provided contexts for user.
     *
     * @param approved_contextlist $contextlist the list of contexts to delete for.
     * @return void
     */
    public static function delete_data_for_user(approved_contextlist $contextlist): void {
        global $DB;
        $userid = $contextlist->get_user()->id;
        foreach ($contextlist as $context) {
            // If not in system context, skip context.
            if ($context->contextlevel == CONTEXT_SYSTEM) {
                $DB->delete_records('tool_mfa', ['userid' => $userid]);
                $DB->delete_records('tool_mfa_secrets', ['userid' => $userid]);
                $DB->delete_records('tool_mfa_auth', ['userid' => $userid]);
            }
        }
    }

    /**
     * Given a userlist, deletes all data in all provided contexts for the users
     *
     * @param approved_userlist $userlist the list of users to delete data for
     * @return void
     */
    public static function delete_data_for_users(approved_userlist $userlist): void {
        $users = $userlist->get_users();
        foreach ($users as $user) {
            // Create contextlist.
            $contextlist = new approved_contextlist($user, 'tool_mfa', [CONTEXT_SYSTEM]);
            // Call delete data.
            self::delete_data_for_user($contextlist);
        }
    }
}
