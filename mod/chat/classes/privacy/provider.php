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
 * @package    mod_chat
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_chat\privacy;
defined('MOODLE_INTERNAL') || die();

use context;
use context_helper;
use context_module;
use moodle_recordset;
use stdClass;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\helper;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;

/**
 * Data provider class.
 *
 * @package    mod_chat
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider {

    /**
     * Returns metadata.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {

        $collection->add_database_table('chat_messages', [
            'userid' => 'privacy:metadata:messages:userid',
            'message' => 'privacy:metadata:messages:message',
            'issystem' => 'privacy:metadata:messages:issystem',
            'timestamp' => 'privacy:metadata:messages:timestamp',
        ], 'privacy:metadata:messages');

        // The tables chat_messages_current and chat_users are not exported/deleted
        // because they are considered as short-lived data and are deleted on a
        // regular basis by cron, or during normal requests. TODO MDL-62006.

        $collection->add_database_table('chat_messages_current', [
            'userid' => 'privacy:metadata:messages:userid',
            'message' => 'privacy:metadata:messages:message',
            'issystem' => 'privacy:metadata:messages:issystem',
            'timestamp' => 'privacy:metadata:messages:timestamp'
        ], 'privacy:metadata:chat_messages_current');

        $collection->add_database_table('chat_users', [
            'userid' => 'privacy:metadata:chat_users:userid',
            'version' => 'privacy:metadata:chat_users:version',
            'ip' => 'privacy:metadata:chat_users:ip',
            'firstping' => 'privacy:metadata:chat_users:firstping',
            'lastping' => 'privacy:metadata:chat_users:lastping',
            'lastmessageping' => 'privacy:metadata:chat_users:lastmessageping',
            'lang' => 'privacy:metadata:chat_users:lang'
        ], 'privacy:metadata:chat_users');

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
            SELECT DISTINCT ctx.id
              FROM {chat} c
              JOIN {modules} m
                ON m.name = :chat
              JOIN {course_modules} cm
                ON cm.instance = c.id
               AND cm.module = m.id
              JOIN {context} ctx
                ON ctx.instanceid = cm.id
               AND ctx.contextlevel = :modulelevel
              JOIN {chat_messages} chm
                ON chm.chatid = c.id
             WHERE chm.userid = :userid";

        $params = [
            'chat' => 'chat',
            'modulelevel' => CONTEXT_MODULE,
            'userid' => $userid,
        ];
        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        $user = $contextlist->get_user();
        $userid = $user->id;
        $cmids = array_reduce($contextlist->get_contexts(), function($carry, $context) {
            if ($context->contextlevel == CONTEXT_MODULE) {
                $carry[] = $context->instanceid;
            }
            return $carry;
        }, []);
        if (empty($cmids)) {
            return;
        }

        $chatidstocmids = static::get_chat_ids_to_cmids_from_cmids($cmids);
        $chatids = array_keys($chatidstocmids);

        // Export the messages.
        list($insql, $inparams) = $DB->get_in_or_equal($chatids, SQL_PARAMS_NAMED);
        $params = array_merge($inparams, ['userid' => $userid]);
        $recordset = $DB->get_recordset_select('chat_messages', "chatid $insql AND userid = :userid", $params, 'timestamp, id');
        static::recordset_loop_and_export($recordset, 'chatid', [], function($carry, $record) use ($user, $chatidstocmids) {
            $message = $record->message;
            if ($record->issystem) {
                $message = get_string('message' . $record->message, 'mod_chat', fullname($user));
            }
            $carry[] = [
                'message' => $message,
                'sent_at' => transform::datetime($record->timestamp),
                'is_system_generated' => transform::yesno($record->issystem),
            ];
            return $carry;

        }, function($chatid, $data) use ($user, $chatidstocmids) {
            $context = context_module::instance($chatidstocmids[$chatid]);
            $contextdata = helper::get_context_data($context, $user);
            $finaldata = (object) array_merge((array) $contextdata, ['messages' => $data]);
            helper::export_context_files($context, $user);
            writer::with_context($context)->export_data([], $finaldata);
        });
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(context $context) {
        global $DB;

        if ($context->contextlevel != CONTEXT_MODULE) {
            return;
        }

        $cm = get_coursemodule_from_id('chat', $context->instanceid);
        if (!$cm) {
            return;
        }

        $chatid = $cm->instance;
        $DB->delete_records_select('chat_messages', 'chatid = :chatid', ['chatid' => $chatid]);
        $DB->delete_records_select('chat_messages_current', 'chatid = :chatid', ['chatid' => $chatid]);
        $DB->delete_records_select('chat_users', 'chatid = :chatid', ['chatid' => $chatid]);
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        $userid = $contextlist->get_user()->id;
        $cmids = array_reduce($contextlist->get_contexts(), function($carry, $context) {
            if ($context->contextlevel == CONTEXT_MODULE) {
                $carry[] = $context->instanceid;
            }
            return $carry;
        }, []);
        if (empty($cmids)) {
            return;
        }

        $chatidstocmids = static::get_chat_ids_to_cmids_from_cmids($cmids);
        $chatids = array_keys($chatidstocmids);

        list($insql, $inparams) = $DB->get_in_or_equal($chatids, SQL_PARAMS_NAMED);
        $sql = "chatid $insql AND userid = :userid";
        $params = array_merge($inparams, ['userid' => $userid]);

        $DB->delete_records_select('chat_messages', $sql, $params);
        $DB->delete_records_select('chat_messages_current', $sql, $params);
        $DB->delete_records_select('chat_users', $sql, $params);
    }

    /**
     * Return a dict of chat IDs mapped to their course module ID.
     *
     * @param array $cmids The course module IDs.
     * @return array In the form of [$chatid => $cmid].
     */
    protected static function get_chat_ids_to_cmids_from_cmids(array $cmids) {
        global $DB;
        list($insql, $inparams) = $DB->get_in_or_equal($cmids, SQL_PARAMS_NAMED);
        $sql = "
            SELECT c.id, cm.id AS cmid
              FROM {chat} c
              JOIN {modules} m
                ON m.name = :chat
              JOIN {course_modules} cm
                ON cm.instance = c.id
               AND cm.module = m.id
             WHERE cm.id $insql";
        $params = array_merge($inparams, ['chat' => 'chat']);
        return $DB->get_records_sql_menu($sql, $params);
    }

    /**
     * Loop and export from a recordset.
     *
     * @param moodle_recordset $recordset The recordset.
     * @param string $splitkey The record key to determine when to export.
     * @param mixed $initial The initial data to reduce from.
     * @param callable $reducer The function to return the dataset, receives current dataset, and the current record.
     * @param callable $export The function to export the dataset, receives the last value from $splitkey and the dataset.
     * @return void
     */
    protected static function recordset_loop_and_export(moodle_recordset $recordset, $splitkey, $initial,
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
