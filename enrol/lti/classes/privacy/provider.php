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
 * Privacy Subsystem implementation for enrol_lti.
 *
 * @package    enrol_lti
 * @copyright  2018 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_lti\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy Subsystem for enrol_lti.
 *
 * @copyright  2018 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider {

    /**
     * Return the fields which contain personal data.
     *
     * @param collection $items a reference to the collection to use to store the metadata.
     * @return collection the updated collection of metadata items.
     */
    public static function get_metadata(collection $items) {
        $items->add_database_table(
            'enrol_lti_users',
            [
                'userid' => 'privacy:metadata:enrol_lti_users:userid',
                'lastgrade' => 'privacy:metadata:enrol_lti_users:lastgrade',
                'lastaccess' => 'privacy:metadata:enrol_lti_users:lastaccess',
                'timecreated' => 'privacy:metadata:enrol_lti_users:timecreated'
            ],
            'privacy:metadata:enrol_lti_users'
        );

        return $items;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid($userid) {
        $contextlist = new contextlist();

        $sql = "SELECT DISTINCT ctx.id
                  FROM {enrol_lti_users} ltiusers
                  JOIN {enrol_lti_tools} ltitools
                    ON ltiusers.toolid = ltitools.id
                  JOIN {context} ctx
                    ON ctx.id = ltitools.contextid
                 WHERE ltiusers.userid = :userid";
        $params = ['userid' => $userid];
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

        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        $sql = "SELECT ltiusers.lastgrade, ltiusers.lastaccess, ltiusers.timecreated, ltitools.contextid
                  FROM {enrol_lti_users} ltiusers
                  JOIN {enrol_lti_tools} ltitools
                    ON ltiusers.toolid = ltitools.id
                  JOIN {context} ctx
                    ON ctx.id = ltitools.contextid
                 WHERE ctx.id {$contextsql}
                   AND ltiusers.userid = :userid";
        $params = $contextparams + ['userid' => $user->id];
        $ltiusers = $DB->get_recordset_sql($sql, $params);
        self::recordset_loop_and_export($ltiusers, 'contextid', [], function($carry, $record) {
            $carry[] = [
                'lastgrade' => $record->lastgrade,
                'timecreated' => transform::datetime($record->lastaccess),
                'timemodified' => transform::datetime($record->timecreated)
            ];
            return $carry;
        }, function($contextid, $data) {
            $context = \context::instance_by_id($contextid);
            $finaldata = (object) $data;
            writer::with_context($context)->export_data(['enrol_lti_users'], $finaldata);
        });
    }

    /**
     * Delete all user data which matches the specified context.
     *
     * @param \context $context A user context.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if (!($context instanceof \context_course || $context instanceof \context_module)) {
            return;
        }

        $enrolltitools = $DB->get_fieldset_select('enrol_lti_tools', 'id', 'contextid = :contextid',
            ['contextid' => $context->id]);
        if (!empty($enrolltitools)) {
            list($sql, $params) = $DB->get_in_or_equal($enrolltitools, SQL_PARAMS_NAMED);
            $DB->delete_records_select('enrol_lti_users', 'toolid ' . $sql, $params);
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        $userid = $contextlist->get_user()->id;

        foreach ($contextlist->get_contexts() as $context) {
            if (!($context instanceof \context_course || $context instanceof \context_module)) {
                continue;
            }

            $enrolltitools = $DB->get_fieldset_select('enrol_lti_tools', 'id', 'contextid = :contextid',
                ['contextid' => $context->id]);
            if (!empty($enrolltitools)) {
                list($sql, $params) = $DB->get_in_or_equal($enrolltitools, SQL_PARAMS_NAMED);
                $params = array_merge($params, ['userid' => $userid]);
                $DB->delete_records_select('enrol_lti_users', "toolid $sql AND userid = :userid", $params);
            }
        }
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
