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
 * Privacy Subsystem implementation for auth_basic.
 *
 * @package    auth_basic
 * @copyright  2018 Olivier SECRET <olivier.secret@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace auth_basic\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use \context;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider {

    /**
     * Returns meta data about this system.
     *
     * @param   collection $collection The initialised collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {
        $collection->add_database_table('auth_basic_master_password', [
            'userid' => 'privacy:metadata:auth_basic_master_password:userid',
        ], 'privacy:metadata:auth_basic_master_password');

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int $userid The user to search.
     * @return  contextlist   $contextlist  The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        $contextlist = new contextlist();

        $sql = "SELECT c.id
                  FROM {auth_basic_master_password} mp
                  JOIN {context} c ON c.instanceid = mp.userid AND c.contextlevel = :contextuser
                 WHERE mp.userid = :userid
              GROUP BY c.id";

        $params = [
            'contextuser'   => CONTEXT_USER,
            'userid'        => $userid
        ];

        $contextlist->add_from_sql($sql, $params);

        return $contextlist;

    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        $contexts = $contextlist->get_contexts();
        if (count($contexts) == 0) {
            return;
        }
        $context = reset($contexts);

        if ($context->contextlevel !== CONTEXT_USER) {
            return;
        }
        $userid = $context->instanceid;

        $subcontext = [
            get_string('pluginname', 'auth_basic'),
            get_string('privacy:metadata:masterpassword', 'auth_basic')
        ];

        $sql = "SELECT *
                  FROM {auth_basic_master_password} mp
                 WHERE mp.userid = :userid
              ORDER BY mp.timecreated";

        $params = [
            'userid' => $userid
        ];

        $password = $DB->get_records_sql($sql, $params);

        $data = (object) [
            'password' => $password
        ];

        writer::with_context($context)->export_data($subcontext, $data);
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param   context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if ($context->contextlevel !== CONTEXT_USER) {
            return;
        }
        $userid = $context->instanceid;

        $DB->delete_records('auth_basic_master_password', ['userid' => $userid]);
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        $contexts = $contextlist->get_contexts();
        if (count($contexts) == 0) {
            return;
        }
        $context = reset($contexts);

        if ($context->contextlevel !== CONTEXT_USER) {
            return;
        }
        $userid = $context->instanceid;

        $DB->delete_records('auth_basic_master_password', ['userid' => $userid]);
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if ($context->contextlevel != CONTEXT_USER) {
            return;
        }

        $sql = "SELECT mp.userid
                  FROM {auth_basic_master_password} mp
                 WHERE mp.userid = :userid";

        $params = [
            'userid' => $context->instanceid
        ];

        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;
        $context = $userlist->get_context();

        if ($context->contextlevel != CONTEXT_USER) {
            return;
        }
        $userid = $context->instanceid;

        $DB->delete_records('auth_basic_master_password', ['userid' => $userid]);
    }
}
