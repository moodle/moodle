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
 * @package    core_webservice
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_webservice\privacy;
defined('MOODLE_INTERNAL') || die();

use context;
use context_user;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;
use core_privacy\local\request\userlist;
use core_privacy\local\request\approved_userlist;

/**
 * Data provider class.
 *
 * @package    core_webservice
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\subsystem\provider {

    /**
     * Returns metadata.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {

        $collection->add_database_table('external_tokens', [
            'token' => 'privacy:metadata:tokens:token',
            'privatetoken' => 'privacy:metadata:tokens:privatetoken',
            'tokentype' => 'privacy:metadata:tokens:tokentype',
            'userid' => 'privacy:metadata:tokens:userid',
            'creatorid' => 'privacy:metadata:tokens:creatorid',
            'iprestriction' => 'privacy:metadata:tokens:iprestriction',
            'validuntil' => 'privacy:metadata:tokens:validuntil',
            'timecreated' => 'privacy:metadata:tokens:timecreated',
            'lastaccess' => 'privacy:metadata:tokens:lastaccess',
        ], 'privacy:metadata:tokens');

        $collection->add_database_table('external_services_users', [
            'userid' => 'privacy:metadata:serviceusers:userid',
            'iprestriction' => 'privacy:metadata:serviceusers:iprestriction',
            'validuntil' => 'privacy:metadata:serviceusers:validuntil',
            'timecreated' => 'privacy:metadata:serviceusers:timecreated',
        ], 'privacy:metadata:serviceusers');

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return contextlist $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : \core_privacy\local\request\contextlist {
        $contextlist = new \core_privacy\local\request\contextlist();

        $sql = "
            SELECT ctx.id
              FROM {external_tokens} t
              JOIN {context} ctx
                ON ctx.instanceid = t.userid
               AND ctx.contextlevel = :userlevel
             WHERE t.userid = :userid1
                OR t.creatorid = :userid2";
        $contextlist->add_from_sql($sql, ['userlevel' => CONTEXT_USER, 'userid1' => $userid, 'userid2' => $userid]);

        $sql = "
            SELECT ctx.id
              FROM {external_services_users} su
              JOIN {context} ctx
                ON ctx.instanceid = su.userid
               AND ctx.contextlevel = :userlevel
             WHERE su.userid = :userid";
        $contextlist->add_from_sql($sql, ['userlevel' => CONTEXT_USER, 'userid' => $userid]);

        return $contextlist;
    }

    /**
     * Get the list of users within a specific context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();

        if (!$context instanceof \context_user) {
            return;
        }

        $userid = $context->instanceid;

        $hasdata = false;
        $hasdata = $hasdata || $DB->record_exists_select('external_tokens', 'userid = ? OR creatorid = ?', [$userid, $userid]);
        $hasdata = $hasdata || $DB->record_exists('external_services_users', ['userid' => $userid]);

        if ($hasdata) {
            $userlist->add_user($userid);
        }
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        $userid = $contextlist->get_user()->id;
        $contexts = array_reduce($contextlist->get_contexts(), function($carry, $context) use ($userid) {
            if ($context->contextlevel == CONTEXT_USER) {
                if ($context->instanceid == $userid) {
                    $carry['has_mine'] = true;
                } else {
                    $carry['others'][] = $context->instanceid;
                }
            }
            return $carry;
        }, [
            'has_mine' => false,
            'others' => []
        ]);

        $path = [get_string('webservices', 'core_webservice')];

        // Exporting my stuff.
        if ($contexts['has_mine']) {

            $data = [];

            // Exporting my tokens.
            $sql = "
                SELECT t.*, s.name as externalservicename
                  FROM {external_tokens} t
                  JOIN {external_services} s
                    ON s.id = t.externalserviceid
                 WHERE t.userid = :userid
              ORDER BY t.id";
            $recordset = $DB->get_recordset_sql($sql, ['userid' => $userid]);
            foreach ($recordset as $record) {
                if (!isset($data['tokens'])) {
                    $data['tokens'] = [];
                }
                $data['tokens'][] = static::transform_token($record);
            }
            $recordset->close();

            // Exporting the services I have access to.
            $sql = "
                SELECT su.*, s.name as externalservicename
                  FROM {external_services_users} su
                  JOIN {external_services} s
                    ON s.id = su.externalserviceid
                 WHERE su.userid = :userid
              ORDER BY su.id";
            $recordset = $DB->get_recordset_sql($sql, ['userid' => $userid]);
            foreach ($recordset as $record) {
                if (!isset($data['services_user'])) {
                    $data['services_user'] = [];
                }
                $data['services_user'][] = [
                    'external_service' => $record->externalservicename,
                    'ip_restriction' => $record->iprestriction,
                    'valid_until' => $record->validuntil ? transform::datetime($record->validuntil) : null,
                    'created_on' => transform::datetime($record->timecreated),
                ];
            }
            $recordset->close();

            if (!empty($data)) {
                writer::with_context(context_user::instance($userid))->export_data($path, (object) $data);
            };
        }

        // Exporting the tokens I created.
        if (!empty($contexts['others'])) {
            list($insql, $inparams) = $DB->get_in_or_equal($contexts['others'], SQL_PARAMS_NAMED);
            $sql = "
                SELECT t.*, s.name as externalservicename
                  FROM {external_tokens} t
                  JOIN {external_services} s
                    ON s.id = t.externalserviceid
                 WHERE t.userid $insql
                   AND t.creatorid = :userid1
                   AND t.userid <> :userid2
              ORDER BY t.userid, t.id";
            $params = array_merge($inparams, ['userid1' => $userid, 'userid2' => $userid]);
            $recordset = $DB->get_recordset_sql($sql, $params);
            static::recordset_loop_and_export($recordset, 'userid', [], function($carry, $record) {
                $carry[] = static::transform_token($record);
                return $carry;
            }, function($userid, $data) use ($path) {
                writer::with_context(context_user::instance($userid))->export_related_data($path, 'created_by_you', (object) [
                    'tokens' => $data
                ]);
            });
        }
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(context $context) {
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
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        $userid = $contextlist->get_user()->id;
        foreach ($contextlist as $context) {
            if ($context->contextlevel == CONTEXT_USER && $context->instanceid == $userid) {
                static::delete_user_data($context->instanceid);
                break;
            }
        }
    }

    /**
     * Delete user data.
     *
     * @param int $userid The user ID.
     * @return void
     */
    protected static function delete_user_data($userid) {
        global $DB;
        $DB->delete_records('external_tokens', ['userid' => $userid]);
        $DB->delete_records('external_services_users', ['userid' => $userid]);
    }

    /**
     * Transform a token entry.
     *
     * @param object $record The token record.
     * @return array
     */
    protected static function transform_token($record) {
        $notexportedstr = get_string('privacy:request:notexportedsecurity', 'core_webservice');
        return [
            'external_service' => $record->externalservicename,
            'token' => $notexportedstr,
            'private_token' => $record->privatetoken ? $notexportedstr : null,
            'ip_restriction' => $record->iprestriction,
            'valid_until' => $record->validuntil ? transform::datetime($record->validuntil) : null,
            'created_on' => transform::datetime($record->timecreated),
            'last_access' => $record->lastaccess ? transform::datetime($record->lastaccess) : null,
        ];
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
    protected static function recordset_loop_and_export(\moodle_recordset $recordset, $splitkey, $initial,
            callable $reducer, callable $export) {

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
}
