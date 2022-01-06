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
 * Privacy Subsystem implementation for mod_googlemeet.
 *
 * @package     mod_googlemeet
 * @copyright   2020 Rone Santos <ronefel@hotmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_googlemeet\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\helper;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

/**
 * The mod_googlemeet module does not store any data.
 */
class provider implements
        // This plugin does store personal user data.
        \core_privacy\local\metadata\provider,

        // This plugin is a core_user_data_provider.
        \core_privacy\local\request\plugin\provider,

        // This plugin is capable of determining which users have data within it.
        \core_privacy\local\request\core_userlist_provider {
    /**
     * Return the fields which contain personal data.
     *
     * @param collection $collection a reference to the collection to use to store the metadata.
     * @return collection the updated collection of metadata items.
     */
    public static function get_metadata(collection $collection) : collection {
        $collection->add_database_table(
            'googlemeet_notify_done',
            [
                'eventid' => 'privacy:metadata:googlemeet_notify_done:eventid',
                'userid' => 'privacy:metadata:googlemeet_notify_done:userid',
                'timesent' => 'privacy:metadata:googlemeet_notify_done:timesent',
            ],
            'privacy:metadata:googlemeet_notify_done'
        );

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int           $userid       The user to search.
     * @return  contextlist   $contextlist  The list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {

        $sql = "SELECT c.id
                  FROM {context} c
            INNER JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
            INNER JOIN {modules} m ON m.id = cm.module AND m.name = :modname
            INNER JOIN {googlemeet} g ON g.id = cm.instance
            INNER JOIN {googlemeet_events} ge ON ge.googlemeetid = g.id
            INNER JOIN {googlemeet_notify_done} gnd ON gnd.eventid = ge.id
                 WHERE gnd.userid = :userid";

        $params = [
            'modname'       => 'googlemeet',
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
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!$context instanceof \context_module) {
            return;
        }

        // Fetch all event notifications done.
        $sql = "SELECT gnd.userid
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modulename
                  JOIN {googlemeet} g ON g.id = cm.instance
                  JOIN {googlemeet_events} ge ON ge.googlemeetid = g.id
                  JOIN {googlemeet_notify_done} gnd ON gnd.eventid = ge.id
                 WHERE cm.id = :cmid";

        $params = [
            'cmid'          => $context->instanceid,
            'modulename'    => 'choice',
        ];

        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Export all user data for the specified user, in the specified contexts, using the supplied exporter instance.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        if (!count($contextlist)) {
            return;
        }

        $user = $contextlist->get_user();

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        $sql = "SELECT cm.id AS cmid,
                       gnd.timesent
                  FROM {context} c
            INNER JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
            INNER JOIN {modules} m ON m.id = cm.module AND m.name = :modname
            INNER JOIN {googlemeet} g ON g.id = cm.instance
            INNER JOIN {googlemeet_events} ge ON ge.googlemeetid = g.id
            INNER JOIN {googlemeet_notify_done} gnd ON gnd.eventid = ge.id
                 WHERE c.id {$contextsql}
                   AND gnd.userid = :userid
              ORDER BY cm.id";

        $params = [
            'modname' => 'googlemeet',
            'contextlevel' => CONTEXT_MODULE,
            'userid' => $user->id
        ] + $contextparams;

        $notifications = $DB->get_recordset_sql($sql, $params);

        foreach ($notifications as $notification) {
            $notificationdata = [
                'timesent' => \core_privacy\local\request\transform::datetime($notification->timesent)
            ];

            $context = \context_module::instance($notification->cmid);

            $contextdata = helper::get_context_data($context, $user);
            $contextdata = (object)array_merge((array)$contextdata, $notificationdata);

            writer::with_context($context)->export_data([], $contextdata);
        }

        $notifications->close();
    }

    /**
     * Delete all personal data for all users in the specified context.
     *
     * @param context $context Context to delete data from.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if ($context->contextlevel != CONTEXT_MODULE) {
            return;
        }

        $cm = get_coursemodule_from_id('googlemeet', $context->instanceid);
        if (!$cm) {
            return;
        }

        $DB->delete_records_select(
            'googlemeet_notify_done',
            "eventid IN (SELECT id FROM {googlemeet_events} WHERE googlemeetid = :googlemeetid)",
            [
                'googlemeetid' => $cm->instance,
            ]
        );
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

            if (!$context instanceof \context_module) {
                continue;
            }
            $instanceid = $DB->get_field('course_modules', 'instance', ['id' => $context->instanceid]);
            if (!$instanceid) {
                continue;
            }

            $DB->delete_records_select(
                'googlemeet_notify_done',
                "userid = :userid AND eventid IN (SELECT id FROM {googlemeet_events} WHERE googlemeetid = :googlemeetid)",
                [
                    'userid'        => $userid,
                    'googlemeetid'  => $instanceid,
                ]
            );
        }
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();

        if (!$context instanceof \context_module) {
            return;
        }

        $cm = get_coursemodule_from_id('googlemeet', $context->instanceid);

        if (!$cm) {
            // Only googlemeet module will be handled.
            return;
        }

        list($usersql, $userparams) = $DB->get_in_or_equal($userlist->get_userids(), SQL_PARAMS_NAMED);

        $select = "eventid IN (SELECT id FROM {googlemeet_events} WHERE googlemeetid = :googlemeetid) AND userid $usersql";
        $params = ['googlemeetid' => $cm->instance] + $userparams;
        $DB->delete_records_select('googlemeet_notify_done', $select, $params);
    }
}
