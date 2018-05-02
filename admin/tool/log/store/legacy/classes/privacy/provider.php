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
 * @package    logstore_legacy
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_legacy\privacy;
defined('MOODLE_INTERNAL') || die();

use context;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;
use tool_log\local\privacy\helper;

/**
 * Data provider class.
 *
 * @package    logstore_legacy
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \tool_log\local\privacy\logstore_provider {

    /**
     * Returns metadata.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) {
        $collection->add_external_location_link('log', [
            'time' => 'privacy:metadata:log:time',
            'userid' => 'privacy:metadata:log:userid',
            'ip' => 'privacy:metadata:log:ip',
            'action' => 'privacy:metadata:log:action',
            'url' => 'privacy:metadata:log:url',
            'info' => 'privacy:metadata:log:info',
        ], 'privacy:metadata:log');
        return $collection;
    }

    /**
     * Add contexts that contain user information for the specified user.
     *
     * @param contextlist $contextlist The contextlist to add the contexts to.
     * @param int $userid The user to find the contexts for.
     * @return void
     */
    public static function add_contexts_for_userid(contextlist $contextlist, $userid) {
        $sql = "
            SELECT ctx.id
              FROM {context} ctx
              JOIN {log} l
                ON (l.cmid = 0 AND l.course = ctx.instanceid AND ctx.contextlevel = :courselevel)
                OR (l.cmid > 0 AND l.cmid = ctx.instanceid AND ctx.contextlevel = :modulelevel)
                OR (l.course <= 0 AND ctx.id = :syscontextid)
             WHERE l.userid = :userid";
        $params = [
            'courselevel' => CONTEXT_COURSE,
            'modulelevel' => CONTEXT_MODULE,
            'syscontextid' => SYSCONTEXTID,
            'userid' => $userid,
        ];
        $contextlist->add_from_sql($sql, $params);
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        $userid = $contextlist->get_user()->id;
        list($insql, $inparams) = static::get_sql_where_from_contexts($contextlist->get_contexts());
        if (empty($insql)) {
            return;
        }
        $sql = "userid = :userid AND $insql";
        $params = array_merge($inparams, ['userid' => $userid]);

        $path = [get_string('privacy:path:logs', 'tool_log'), get_string('pluginname', 'logstore_legacy')];
        $flush = function($lastcontextid, $data) use ($path) {
            $context = context::instance_by_id($lastcontextid);
            writer::with_context($context)->export_data($path, (object) ['logs' => $data]);
        };

        $lastcontextid = null;
        $data = [];
        $recordset = $DB->get_recordset_select('log', $sql, $params, 'course, cmid, time, id');
        foreach ($recordset as $record) {
            $event = \logstore_legacy\event\legacy_logged::restore_legacy($record);
            $context = $event->get_context();
            if ($lastcontextid && $lastcontextid != $context->id) {
                $flush($lastcontextid, $data);
                $data = [];
            }

            $extra = $event->get_logextra();
            $data[] = [
                'name' => $event->get_name(),
                'description' => $event->get_description(),
                'timecreated' => transform::datetime($event->timecreated),
                'ip' => $extra['ip'],
                'origin' => helper::transform_origin($extra['origin']),
            ];

            $lastcontextid = $context->id;
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
        global $DB;
        list($sql, $params) = static::get_sql_where_from_contexts([$context]);
        if (empty($sql)) {
            return;
        }
        $DB->delete_records_select('log', $sql, $params);
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;
        list($sql, $params) = static::get_sql_where_from_contexts($contextlist->get_contexts());
        if (empty($sql)) {
            return;
        }
        $userid = $contextlist->get_user()->id;
        $DB->delete_records_select('log', "$sql AND userid = :userid", array_merge($params, ['userid' => $userid]));
    }

    /**
     * Get an SQL where statement from a list of contexts.
     *
     * @param array $contexts The contexts.
     * @return array [$sql, $params]
     */
    protected static function get_sql_where_from_contexts(array $contexts) {
        global $DB;

        $sorted = array_reduce($contexts, function ($carry, $context) {
            $level = $context->contextlevel;
            if ($level == CONTEXT_MODULE || $level == CONTEXT_COURSE) {
                $carry[$level][] = $context->instanceid;
            } else if ($level == CONTEXT_SYSTEM) {
                $carry[$level] = $context->id;
            }
            return $carry;
        }, [
            CONTEXT_COURSE => [],
            CONTEXT_MODULE => [],
            CONTEXT_SYSTEM => null,
        ]);

        $sqls = [];
        $params = [];

        if (!empty($sorted[CONTEXT_MODULE])) {
            list($insql, $inparams) = $DB->get_in_or_equal($sorted[CONTEXT_MODULE], SQL_PARAMS_NAMED);
            $sqls[] = "cmid $insql";
            $params = array_merge($params, $inparams);
        }

        if (!empty($sorted[CONTEXT_COURSE])) {
            list($insql, $inparams) = $DB->get_in_or_equal($sorted[CONTEXT_COURSE], SQL_PARAMS_NAMED);

            $sqls[] = "cmid = 0 AND course $insql";
            $params = array_merge($params, $inparams);
        }

        if (!empty($sorted[CONTEXT_SYSTEM])) {
            $sqls[] = "course <= 0";
        }

        if (empty($sqls)) {
            return [null, null];
        }

        return ['((' . implode(') OR (', $sqls) . '))', $params];
    }
}
