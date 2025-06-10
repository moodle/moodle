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
 * Provider information for onenote.
 *
 * @package local_onenote
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_onenote\privacy;

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\contextlist;
use \core_privacy\local\request\approved_contextlist;
use \core_privacy\local\request\writer;

if (interface_exists('\core_privacy\local\request\core_userlist_provider')) {
    interface local_onenote_userlist extends \core_privacy\local\request\core_userlist_provider {
    }
} else {
    interface local_onenote_userlist {
    }

    ;
}

/**
 * Provider details for onenote
 */
class provider
    implements \core_privacy\local\request\plugin\provider, \core_privacy\local\metadata\provider, local_onenote_userlist {

    /**
     * Returns meta data about this system.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {

        $tables = ['local_onenote_user_sections' => ['user_id', 'course_id', 'section_id',],
            'local_onenote_assign_pages' => ['user_id', 'assign_id', 'submission_student_page_id', 'feedback_student_page_id',
                'submission_teacher_page_id', 'feedback_teacher_page_id', 'teacher_lastviewed', 'student_lastmodified',],];

        foreach ($tables as $table => $fields) {
            $fielddata = [];
            foreach ($fields as $field) {
                $fielddata[$field] = 'privacy:metadata:' . $table . ':' . $field;
            }
            $collection->add_database_table($table, $fielddata, 'privacy:metadata:' . $table);
        }

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return  contextlist   $contextlist  The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        $contextlist = new \core_privacy\local\request\contextlist();
        if (self::user_has_onenote_data($userid)) {
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
        if (self::user_has_onenote_data($context->instanceid)) {
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
        $tables = static::get_table_user_map($user->id);
        foreach ($tables as $table => $filterparams) {
            $records = $DB->get_recordset($table, $filterparams);
            foreach ($records as $record) {
                writer::with_context($context)->export_data([get_string('privacy:metadata:local_onenote', 'local_onenote'),
                    get_string('privacy:metadata:' . $table, 'local_onenote')], $record);
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
     * Return true if the specified userid has data in any local_onenote tables.
     *
     * @param int $userid The user to check for.
     * @return boolean
     */
    private static function user_has_onenote_data(int $userid) {
        global $DB;
        $tables = self::get_table_user_map($userid);
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
        $tables = self::get_table_user_map($userid);
        foreach ($tables as $table => $filterparams) {
            $DB->delete_records($table, $filterparams);
        }
    }

    /**
     * Get a map of database tables that contain user data, and the filters to get records for a user.
     *
     * @param int $userid The user to get the map for.
     * @return array The table user map.
     */
    protected static function get_table_user_map(int $userid) : array {
        $tables =
            ['local_onenote_user_sections' => ['user_id' => $userid], 'local_onenote_assign_pages' => ['user_id' => $userid],];
        return $tables;
    }
}
