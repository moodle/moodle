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
 * Privacy subsystem implementation for local_o365.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\privacy;

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\contextlist;
use \core_privacy\local\request\approved_contextlist;
use \core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

interface local_o365_userlist extends \core_privacy\local\request\core_userlist_provider {
};

/**
 * Privacy subsystem implementation for local_o365.
 */
class provider implements
    \core_privacy\local\request\plugin\provider,
    \core_privacy\local\metadata\provider,
    local_o365_userlist {

    /**
     * Returns meta data about this system.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {

        $tables = [
            'local_o365_calidmap' => [
                'userid',
                'origin',
                'outlookeventid',
                'eventid',
            ],
            'local_o365_calsub' => [
                'user_id',
                'caltype',
                'caltypeid',
                'o365calid',
                'isprimary',
                'syncbehav',
                'timecreated',
            ],
            'local_o365_connections' => [
                'muserid',
                'aadupn',
                'uselogin',
            ],
            'local_o365_token' => [
                'user_id',
                'scope',
                'tokenresource',
                'token',
                'expiry',
                'refreshtoken',
            ],
            'local_o365_objects' => [
                'type',
                'subtype',
                'objectid',
                'moodleid',
                'o365name',
                'tenant',
                'metadata',
                'timecreated',
                'timemodified',
            ],
            'local_o365_appassign' => [
                'muserid',
                'assigned',
                'photoid',
                'photoupdated',
            ],
            'local_o365_matchqueue' => [
                'musername',
                'o365username',
                'openidconnect',
                'completed',
                'errormessage',
            ],
            'local_o365_calsettings' => [
                'user_id',
                'o365calid',
                'timecreated',
            ],
        ];

        foreach ($tables as $table => $fields) {
            $fielddata = [];
            foreach ($fields as $field) {
                $fielddata[$field] = 'privacy:metadata:'.$table.':'.$field;
            }
            $collection->add_database_table(
                $table,
                $fielddata,
                'privacy:metadata:'.$table
            );
        }

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return contextlist $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        $contextlist = new \core_privacy\local\request\contextlist();
        if (self::user_has_o365_data($userid)) {
            $contextlist->add_user_context($userid);
        }
        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param \core_privacy\local\request\userlist $userlist The userlist containing the list of users who have data in this
     *                                                       context/plugin combination.
     */
    public static function get_users_in_context(\core_privacy\local\request\userlist $userlist) {
        $context = $userlist->get_context();
        if (!$context instanceof \context_user) {
            return;
        }

        // If the user exists in any of the ELIS core tables, add the user context and return it.
        if (self::user_has_o365_data($context->instanceid)) {
            $userlist->add_user($context->instanceid);
        }
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;
        $user = $contextlist->get_user();
        $context = \context_user::instance($contextlist->get_user()->id);
        $tables = static::get_table_user_map($user);
        foreach ($tables as $table => $filterparams) {
            $records = $DB->get_recordset($table, $filterparams);
            foreach ($records as $record) {
                writer::with_context($context)->export_data([
                    get_string('privacy:metadata:local_o365', 'local_o365'),
                    get_string('privacy:metadata:'.$table, 'local_o365')
                ], $record);
            }
        }
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        if ($context->contextlevel == CONTEXT_USER) {
            self::delete_user_data($context->instanceid);
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        if (empty($contextlist->count())) {
            return;
        }
        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel == CONTEXT_USER) {
                self::delete_user_data($context->instanceid);
            }
        }
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param \core_privacy\local\request\approved_userlist $userlist The approved context and user information to delete
     *                                                                information for.
     */
    public static function delete_data_for_users(\core_privacy\local\request\approved_userlist $userlist) {
        $context = $userlist->get_context();
        if ($context instanceof \context_user) {
            self::delete_user_data($context->instanceid);
        }
    }

    /**
     * Return true if the specified userid has data in any local_o365 tables.
     *
     * @param int $userid The user to check for.
     * @return boolean
     */
    private static function user_has_o365_data(int $userid) {
        global $DB;

        $userdata = new \stdClass;
        $userdata->id = $userid;
        $user = $DB->get_record('user', ['id' => $userid]);
        if (!empty($user)) {
            $userdata->username = $user->username;
        }
        $tables = self::get_table_user_map($userdata);
        foreach ($tables as $table => $filterparams) {
            if ($DB->count_records($table, $filterparams) > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * This does the deletion of user data given a userid.
     *
     * @param int $userid The user ID
     */
    private static function delete_user_data(int $userid) {
        global $DB;

        $userdata = new \stdClass;
        $userdata->id = $userid;
        $user = $DB->get_record('user', ['id' => $userid]);
        if (!empty($user)) {
            $userdata->username = $user->username;
        }
        $tables = self::get_table_user_map($userdata);
        foreach ($tables as $table => $filterparams) {
            $DB->delete_records($table, $filterparams);
        }
    }

    /**
     * Get a map of database tables that contain user data, and the filters to get records for a user.
     *
     * @param \stdClass $user The user to get the map for.
     * @return array The table user map.
     */
    protected static function get_table_user_map(\stdClass $user) : array {
        $tables = [
            'local_o365_calidmap' => ['userid' => $user->id],
            'local_o365_calsub' => ['user_id' => $user->id],
            'local_o365_connections' => ['muserid' => $user->id],
            'local_o365_token' => ['user_id' => $user->id],
            'local_o365_objects' => ['type' => 'user', 'moodleid' => $user->id],
            'local_o365_appassign' => ['muserid' => $user->id],
            'local_o365_calsettings' => ['user_id' => $user->id],
        ];
        if (isset($user->username)) {
            $tables['local_o365_matchqueue'] = ['musername' => $user->username];
        }
        return $tables;
    }
}
