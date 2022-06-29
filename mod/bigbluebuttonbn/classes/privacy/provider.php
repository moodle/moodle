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

namespace mod_bigbluebuttonbn\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\helper;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

/**
 * Privacy class for requesting user data.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2018 - present, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 */
class provider implements
    // This plugin has data.
    \core_privacy\local\metadata\provider,

    // This plugin currently implements the original plugin\provider interface.
    \core_privacy\local\request\plugin\provider,

    // This plugin is capable of determining which users have data within it.
    \core_privacy\local\request\core_userlist_provider {

    /**
     * Returns metadata.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {

         // The table bigbluebuttonbn stores only the room properties.
         // However, there is a chance that some personal information is stored as metadata.
         // This would be done in the column 'participants' where rules can be set to define BBB roles.
         // It is fair to say that only the userid is stored, which is useless if user is removed.
         // But if this is a concern a refactoring on the way the rules are stored will be required.
        $collection->add_database_table('bigbluebuttonbn', [
            'participants' => 'privacy:metadata:bigbluebuttonbn:participants',
        ], 'privacy:metadata:bigbluebuttonbn');

        // The table bigbluebuttonbn_logs stores events triggered by users when using the plugin.
        // Some personal information along with the resource accessed is stored.
        $collection->add_database_table('bigbluebuttonbn_logs', [
            'userid' => 'privacy:metadata:bigbluebuttonbn_logs:userid',
            'timecreated' => 'privacy:metadata:bigbluebuttonbn_logs:timecreated',
            'meetingid' => 'privacy:metadata:bigbluebuttonbn_logs:meetingid',
            'log' => 'privacy:metadata:bigbluebuttonbn_logs:log',
            'meta' => 'privacy:metadata:bigbluebuttonbn_logs:meta',
        ], 'privacy:metadata:bigbluebuttonbn_logs');

        $collection->add_database_table('bigbluebuttonbn_recordings', [
            'userid' => 'privacy:metadata:bigbluebuttonbn_logs:userid',
        ], 'privacy:metadata:bigbluebuttonbn_recordings');

        // Personal information has to be passed to BigBlueButton.
        // This includes the user ID and fullname.
        $collection->add_external_location_link('bigbluebutton', [
                'userid' => 'privacy:metadata:bigbluebutton:userid',
                'fullname' => 'privacy:metadata:bigbluebutton:fullname',
            ], 'privacy:metadata:bigbluebutton');

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int           $userid       The user to search.
     * @return  contextlist   $contextlist  The list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        // If user was already deleted, do nothing.
        if (!\core_user::get_user($userid)) {
            return new contextlist();
        }
        // Fetch all bigbluebuttonbn logs.
        $sql = "SELECT c.id
                  FROM {context} c
            INNER JOIN {course_modules} cm
                    ON cm.id = c.instanceid
                   AND c.contextlevel = :contextlevel
            INNER JOIN {modules} m
                    ON m.id = cm.module
                   AND m.name = :modname
            INNER JOIN {bigbluebuttonbn} bigbluebuttonbn
                    ON bigbluebuttonbn.id = cm.instance
            INNER JOIN {bigbluebuttonbn_logs} bigbluebuttonbnlogs
                    ON bigbluebuttonbnlogs.bigbluebuttonbnid = bigbluebuttonbn.id
                 WHERE bigbluebuttonbnlogs.userid = :userid";

        $params = [
            'modname' => 'bigbluebuttonbn',
            'contextlevel' => CONTEXT_MODULE,
            'userid' => $userid,
        ];
        $contextlist = new contextlist();
        $contextlist->add_from_sql($sql, $params);
        return $contextlist;
    }

    /**
     * Export personal data for the given approved_contextlist. User and context information is contained within the contextlist.
     *
     * @param approved_contextlist $contextlist a list of contexts approved for export.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        // Filter out any contexts that are not related to modules.
        $cmids = array_reduce($contextlist->get_contexts(), function($carry, $context) {
            if ($context->contextlevel == CONTEXT_MODULE) {
                $carry[] = $context->instanceid;
            }
            return $carry;
        }, []);

        if (empty($cmids)) {
            return;
        }

        $user = $contextlist->get_user();

        // Get all the bigbluebuttonbn activities associated with the above course modules.
        $instanceidstocmids = self::get_instance_ids_to_cmids_from_cmids($cmids);
        $instanceids = array_keys($instanceidstocmids);

        list($insql, $inparams) = $DB->get_in_or_equal($instanceids, SQL_PARAMS_NAMED);
        $params = array_merge($inparams, ['userid' => $user->id]);
        $recordset = $DB->get_recordset_select(
            'bigbluebuttonbn_logs',
            "bigbluebuttonbnid $insql AND userid = :userid",
            $params,
            'timecreated, id'
        );
        self::recordset_loop_and_export($recordset, 'bigbluebuttonbnid', [],
            function($carry, $record) use ($user, $instanceidstocmids) {
                $carry[] = [
                    'timecreated' => transform::datetime($record->timecreated),
                    'meetingid' => $record->meetingid,
                    'log' => $record->log,
                    'meta' => $record->meta,
                  ];
                return $carry;
            },
            function($instanceid, $data) use ($user, $instanceidstocmids) {
                $context = \context_module::instance($instanceidstocmids[$instanceid]);
                $contextdata = helper::get_context_data($context, $user);
                $finaldata = (object) array_merge((array) $contextdata, ['logs' => $data]);
                helper::export_context_files($context, $user);
                writer::with_context($context)->export_data([], $finaldata);
            }
        );
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context the context to delete in.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if (!$context instanceof \context_module) {
            return;
        }

        $instanceid = $DB->get_field('course_modules', 'instance', ['id' => $context->instanceid], MUST_EXIST);
        $DB->delete_records('bigbluebuttonbn_logs', ['bigbluebuttonbnid' => $instanceid]);
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist a list of contexts approved for deletion.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;
        $count = $contextlist->count();
        if (empty($count)) {
            return;
        }
        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if (!$context instanceof \context_module) {
                return;
            }
            $instanceid = $DB->get_field('course_modules', 'instance', ['id' => $context->instanceid], MUST_EXIST);
            $DB->delete_records('bigbluebuttonbn_logs', ['bigbluebuttonbnid' => $instanceid, 'userid' => $userid]);
        }
    }

    /**
     * Return a dict of bigbluebuttonbn IDs mapped to their course module ID.
     *
     * @param array $cmids The course module IDs.
     * @return array In the form of [$bigbluebuttonbnid => $cmid].
     */
    protected static function get_instance_ids_to_cmids_from_cmids(array $cmids) {
        global $DB;

        list($insql, $inparams) = $DB->get_in_or_equal($cmids, SQL_PARAMS_NAMED);
        $sql = "SELECT bigbluebuttonbn.id, cm.id AS cmid
                 FROM {bigbluebuttonbn} bigbluebuttonbn
                 JOIN {modules} m
                   ON m.name = :bigbluebuttonbn
                 JOIN {course_modules} cm
                   ON cm.instance = bigbluebuttonbn.id
                  AND cm.module = m.id
                WHERE cm.id $insql";
        $params = array_merge($inparams, ['bigbluebuttonbn' => 'bigbluebuttonbn']);

        return $DB->get_records_sql_menu($sql, $params);
    }

    /**
     * Loop and export from a recordset.
     *
     * @param \moodle_recordset $recordset The recordset.
     * @param string $splitkey The record key to determine when to export.
     * @param mixed $initial The initial data to reduce from.
     * @param callable $reducer The function to return the dataset, receives current dataset, and the current record.
     * @param callable $export The function to export the dataset, receives the last value from $splitkey and the dataset.
     * @return void
     */
    protected static function recordset_loop_and_export(
        \moodle_recordset $recordset,
        $splitkey,
        $initial,
        callable $reducer,
        callable $export
    ) {
        $data = $initial;
        $lastid = null;

        foreach ($recordset as $record) {
            if ($lastid && $record->{$splitkey} != $lastid) {
                $export($lastid, $data);
                $data = $initial;
            }
            $data = $reducer($data, $record);
            $lastid = $record->{$splitkey};
        }
        $recordset->close();

        if (!empty($lastid)) {
            $export($lastid, $data);
        }
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(\core_privacy\local\request\userlist $userlist) {
        $context = $userlist->get_context();

        if (!$context instanceof \context_module) {
            return;
        }

        $params = [
            'instanceid' => $context->instanceid,
            'modulename' => 'bigbluebuttonbn',
        ];

        $sql = "SELECT bnl.userid
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modulename
                  JOIN {bigbluebuttonbn} bn ON bn.id = cm.instance
                  JOIN {bigbluebuttonbn_logs} bnl ON bnl.bigbluebuttonbnid = bn.id
                 WHERE cm.id = :instanceid";

        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist       $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(\core_privacy\local\request\approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();
        $cm = $DB->get_record('course_modules', ['id' => $context->instanceid]);

        list($userinsql, $userinparams) = $DB->get_in_or_equal($userlist->get_userids(), SQL_PARAMS_NAMED);
        $params = array_merge(['bigbluebuttonbnid' => $cm->instance], $userinparams);
        $sql = "bigbluebuttonbnid = :bigbluebuttonbnid AND userid {$userinsql}";

        $DB->delete_records_select('bigbluebuttonbn_logs', $sql, $params);
    }
}
