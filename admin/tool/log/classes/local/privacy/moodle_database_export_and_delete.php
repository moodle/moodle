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
 * Moodle database: export and delete.
 *
 * @package    tool_log
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_log\local\privacy;
defined('MOODLE_INTERNAL') || die();

use context;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\writer;

/**
 * Moodle database: export and delete trait.
 *
 * This is to be used with logstores which use a database and table with the same columns
 * as the core plugin 'logstore_standard'.
 *
 * This trait expects the following methods to be present in the object:
 *
 * - public static function get_database_and_table(): [moodle_database|null, string|null]
 * - public static function get_export_subcontext(): []
 *
 * @package    tool_log
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait moodle_database_export_and_delete {

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        list($db, $table) = static::get_database_and_table();
        if (!$db || !$table) {
            return;
        }

        $userid = $contextlist->get_user()->id;
        list($insql, $inparams) = $db->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        $sql = "(userid = :userid1 OR relateduserid = :userid2 OR realuserid = :userid3) AND contextid $insql";
        $params = array_merge($inparams, [
            'userid1' => $userid,
            'userid2' => $userid,
            'userid3' => $userid,
        ]);

        $path = static::get_export_subcontext();
        $flush = function($lastcontextid, $data) use ($path) {
            $context = context::instance_by_id($lastcontextid);
            writer::with_context($context)->export_data($path, (object) ['logs' => $data]);
        };

        $lastcontextid = null;
        $data = [];
        $recordset = $db->get_recordset_select($table, $sql, $params, 'contextid, timecreated, id');
        foreach ($recordset as $record) {
            if ($lastcontextid && $lastcontextid != $record->contextid) {
                $flush($lastcontextid, $data);
                $data = [];
            }
            $data[] = helper::transform_standard_log_record_for_userid($record, $userid);
            $lastcontextid = $record->contextid;
        }
        if ($lastcontextid) {
            $flush($lastcontextid, $data);
        }
        $recordset->close();
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(context $context) {
        list($db, $table) = static::get_database_and_table();
        if (!$db || !$table) {
            return;
        }
        $db->delete_records($table, ['contextid' => $context->id]);
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        list($db, $table) = static::get_database_and_table();
        if (!$db || !$table) {
            return;
        }
        list($insql, $inparams) = $db->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);
        $params = array_merge($inparams, ['userid' => $contextlist->get_user()->id]);
        $db->delete_records_select($table, "userid = :userid AND contextid $insql", $params);
    }

}
