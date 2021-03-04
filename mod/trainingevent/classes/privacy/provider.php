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
 * Privacy Subsystem implementation for mod_trainingevent.
 *
 * @package    mod_trainingevent
 * @copyright  2021 Derick Turner
 * @author     Derick Turner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_trainingevent\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\deletion_criteria;
use core_privacy\local\request\helper;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;
use \context_system;
use \context_module;
use \context_user;

defined('MOODLE_INTERNAL') || die();

class provider implements
        // This plugin stores personal data.
        \core_privacy\local\metadata\provider,

        // This plugin is a core_user_data_provider.
        \core_privacy\local\request\plugin\provider,

        // This plugin is capable of determining which users have data within it.
        \core_privacy\local\request\core_userlist_provider {

    /**
     * Return the fields which contain personal data.
     *
     * @param collection $items a reference to the collection to use to store the metadata.
     * @return collection the updated collection of metadata items.
     */
    public static function get_metadata(collection $items) : collection {
        $items->add_database_table(
            'trainingevent_users',
            [
                'trainingeventid' => 'privacy:metadata:trainingeventid:trainingeventid',
                'id' => 'privacy:metadata:trainingeventid:id',
                'userid' => 'privacy:metadata:trainingeventid:userid',
            ],
            'privacy:metadata:trainingevent_users'
        );

        return $items;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid the userid.
     * @return contextlist the list of contexts containing user info for the user.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        // Fetch all training events
        $sql = "SELECT c.id
                  FROM {context} c
            INNER JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
            INNER JOIN {modules} m ON m.id = cm.module AND m.name = :modname
            INNER JOIN {trainingevent} te ON te.id = cm.instance
            INNER JOIN {trainingevent_users} tu ON tu.trainingeventid = te.id
                 WHERE tu.userid = :userid";

        $params = [
            'modname'       => 'trainingevent',
            'contextlevel'  => CONTEXT_MODULE,
            'userid'        => $userid,
        ];
        $contextlist = new contextlist();
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

        if (!$context instanceof context_module) {
            return;
        }

        // Fetch all trainingevent users.
        $sql = "SELECT tu.userid
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {trainingevent_users} tu ON tu.trainingeventid = cm.instance
                 WHERE cm.id = :cmid";

        $params = [
            'cmid'      => $context->instanceid,
            'modname'   => 'trainingevent',
        ];

        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Export personal data for the given approved_contextlist. User and context information is contained within the contextlist.
     *
     * @param approved_contextlist $contextlist a list of contexts approved for export.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        $sql = "SELECT cm.id AS cmid
                  FROM {context} c
            INNER JOIN {course_modules} cm ON cm.id = c.instanceid
            INNER JOIN {trainingevent} te ON te.id = cm.instance
            INNER JOIN {trainingevent_users} tu ON tu.trainingeventid = te.id
                 WHERE c.id {$contextsql}
                       AND tu.userid = :userid
              ORDER BY cm.id";

        $params = ['userid' => $user->id] + $contextparams;

        $trainingevents = $DB->get_recordset_sql($sql, $params);
        $trainingeventsout = (object) [];
        $trainingeventsout->trainingevents = $trainingevents;
        writer::with_context($context)->export_data(array(get_string('pluginname', 'trainingevent')), $trainingevent);
        $trainintevents->close();

    }

    /**
     * Export the supplied personal data for a single trainingevent activity, along with any generic data or area files.
     *
     * @param array $trainingeventdata the personal data to export for the trainingevent.
     * @param context_module $context the context of the trainingevent.
     * @param \stdClass $user the user record
     */
    protected static function export_trainingevent_data_for_user(array $trainingeventdata, context_module $context, \stdClass $user) {
        // Fetch the generic module data for the trainingevent.
        $contextdata = helper::get_context_data($context, $user);

        // Merge with trainingevent data and write it.
        $contextdata = (object)array_merge((array)$contextdata, $trainingeventdata);
        writer::with_context($context)->export_data(array(get_string('pluginname', 'trainingevent')), $contextdata);

        // Write generic module intro files.
        helper::export_context_files($context, $user);
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context the context to delete in.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if (empty($context)) {
            return;
        }
        $instanceid = $DB->get_field('course_modules', 'instance', ['id' => $context->instanceid], MUST_EXIST);
        $DB->delete_records('trainingevent_users', ['trainingeventid' => $instanceid]);
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist a list of contexts approved for deletion.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            $instanceid = $DB->get_field('course_modules', 'instance', ['id' => $context->instanceid], MUST_EXIST);
            $DB->delete_records('trainingevent_users', ['trainingeventid' => $instanceid, 'userid' => $userid]);
        }
    }
    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist       $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();

        if (!$context instanceof context_module) {
            return;
        }

        $cm = get_coursemodule_from_id('trainingevent', $context->instanceid);

        if (!$cm) {
            // Only trainingevent module will be handled.
            return;
        }

        $userids = $userlist->get_userids();
        list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);

        $select = "trainingeventid = :trainingeventid AND userid $usersql";
        $params = ['trainingeventid' => $cm->instance] + $userparams;
        $DB->delete_records_select('trainingevent_users', $select, $params);
    }
}
