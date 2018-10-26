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
 * Privacy Subsystem implementation for core_enrol.
 *
 * @package    core_enrol
 * @copyright  2018 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_enrol\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\context;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;
use core_privacy\local\request\userlist;
use \core_privacy\local\request\approved_userlist;

/**
 * Privacy Subsystem for core_enrol implementing metadata and plugin providers.
 *
 * @copyright  2018 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        \core_privacy\local\metadata\provider,
        \core_privacy\local\request\core_userlist_provider,
        \core_privacy\local\request\subsystem\provider {
    /**
     * Returns meta data about this system.
     *
     * @param   collection $collection The initialised collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {
        $collection->add_database_table(
            'user_enrolments',
            [
                'status' => 'privacy:metadata:user_enrolments:status',
                'enrolid' => 'privacy:metadata:user_enrolments:enrolid',
                'userid' => 'privacy:metadata:user_enrolments:userid',
                'timestart' => 'privacy:metadata:user_enrolments:timestart',
                'timeend' => 'privacy:metadata:user_enrolments:timeend',
                'modifierid' => 'privacy:metadata:user_enrolments:modifierid',
                'timecreated' => 'privacy:metadata:user_enrolments:timecreated',
                'timemodified' => 'privacy:metadata:user_enrolments:timemodified'
            ],
            'privacy:metadata:user_enrolments:tableexplanation'
        );

        return $collection;
    }
    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int $userid The user to search.
     * @return  contextlist   $contextlist  The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        $sql = "SELECT ctx.id
                  FROM {user_enrolments} ue
                  JOIN {enrol} e
                    ON e.id = ue.enrolid
                   AND ue.userid = :userid
                  JOIN {context} ctx
                    ON ctx.instanceid = e.courseid
                   AND ctx.contextlevel = :contextlevel";
        $params = [
            'contextlevel' => CONTEXT_COURSE,
            'userid'       => $userid
        ];
        $contextlist = new contextlist();
        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Get the list of users within a specific context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!$context instanceof \context_course) {
            return;
        }

        $sql = "SELECT ue.userid as userid
                  FROM {user_enrolments} ue
                  JOIN {enrol} e ON e.id = ue.enrolid
                 WHERE e.courseid = ?";
        $params = [$context->instanceid];
        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }
        $userid = $contextlist->get_user()->id;
        list($insql, $inparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);
        $params = [
            'contextlevel' => CONTEXT_COURSE,
            'userid' => $userid
         ];
        $params += $inparams;
        $sql = "SELECT ue.id,
                       ue.status,
                       ue.timestart,
                       ue.timeend,
                       ue.timecreated,
                       ue.timemodified,
                       e.enrol,
                       ctx.id as contextid
                  FROM {user_enrolments} ue
                  JOIN {enrol} e
                    ON e.id = ue.enrolid
                   AND ue.userid = :userid
                  JOIN {context} ctx
                    ON ctx.instanceid = e.courseid
                   AND ctx.contextlevel = :contextlevel
                 WHERE ctx.id $insql
                 ORDER BY ctx.id, e.enrol";
        $data = [];
        $lastcontextid = null;
        $lastenrol = null;
        $path = [get_string('privacy:metadata:user_enrolments', 'core_enrol')];
        $flush = function($lastcontextid, $lastenrol, $data) use ($path) {
            $context = \context::instance_by_id($lastcontextid);
            writer::with_context($context)->export_related_data(
                $path,
                $lastenrol,
                (object)$data
            );
        };
        $userenrolments = $DB->get_recordset_sql($sql, $params);
        foreach ($userenrolments as $userenrolment) {
            if (($lastcontextid && $lastcontextid != $userenrolment->contextid) ||
                    ($lastenrol && $lastenrol != $userenrolment->enrol)) {
                $flush($lastcontextid, $lastenrol, $data);
                $data = [];
            }
            $data[] = (object) [
                'status' => $userenrolment->status,
                'timecreated' => transform::datetime($userenrolment->timecreated),
                'timemodified' => transform::datetime($userenrolment->timemodified),
                'timestart' => transform::datetime($userenrolment->timestart),
                'timeend' => transform::datetime($userenrolment->timeend)
            ];
            $lastcontextid = $userenrolment->contextid;
            $lastenrol = $userenrolment->enrol;
        }
        if (!empty($data)) {
            $flush($lastcontextid, $lastenrol, $data);
        }
        $userenrolments->close();
    }
    /**
     * Delete all data for all users in the specified context.
     *
     * @param   context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        // Sanity check that context is at the User context level.
        if ($context->contextlevel == CONTEXT_COURSE) {
            $sql = "SELECT ue.id
                      FROM {user_enrolments} ue
                      JOIN {enrol} e
                        ON e.id = ue.enrolid
                      JOIN {context} ctx
                        ON ctx.instanceid = e.courseid
                     WHERE ctx.id = :contextid";
            $params = ['contextid' => $context->id];
            $enrolsids = $DB->get_fieldset_sql($sql, $params);
            if (!empty($enrolsids)) {
                list($insql, $inparams) = $DB->get_in_or_equal($enrolsids, SQL_PARAMS_NAMED);
                static::delete_user_data($insql, $inparams);
            }
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

        if ($context instanceof \context_course) {
            list($usersql, $userparams) = $DB->get_in_or_equal($userlist->get_userids(), SQL_PARAMS_NAMED);

            $sql = "SELECT ue.id
                      FROM {user_enrolments} ue
                      JOIN {enrol} e
                        ON e.id = ue.enrolid
                      JOIN {context} ctx
                        ON ctx.instanceid = e.courseid
                     WHERE ctx.id = :contextid
                     AND ue.userid {$usersql}";

            $params = ['contextid' => $context->id] + $userparams;
            $enrolsids = $DB->get_fieldset_sql($sql, $params);

            if (!empty($enrolsids)) {
                list($insql, $inparams) = $DB->get_in_or_equal($enrolsids, SQL_PARAMS_NAMED);
                static::delete_user_data($insql, $inparams);
            }
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }
        $userid = $contextlist->get_user()->id;
        list($insql, $inparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);
        $params = [
            'contextlevel' => CONTEXT_COURSE,
            'userid' => $userid
         ];
        $params += $inparams;
        $sql = "SELECT ue.id
                  FROM {user_enrolments} ue
                  JOIN {enrol} e
                    ON e.id = ue.enrolid
                   AND ue.userid = :userid
                  JOIN {context} ctx
                    ON ctx.instanceid = e.courseid
                   AND ctx.contextlevel = :contextlevel
                 WHERE ctx.id $insql";
        $enrolsids = $DB->get_fieldset_sql($sql, $params);
        if (!empty($enrolsids)) {
            list($insql, $inparams) = $DB->get_in_or_equal($enrolsids, SQL_PARAMS_NAMED);
            static::delete_user_data($insql, $inparams);
        }
    }

    /**
     * Delete data from $tablename with the IDs returned by $sql query.
     *
     * @param  string $sql    SQL query for getting the IDs of the uer enrolments entries to delete.
     * @param  array  $params SQL params for the query.
     */
    protected static function delete_user_data(string $sql, array $params) {
        global $DB;

        $DB->delete_records_select('user_enrolments', "id $sql", $params);
    }

    /**
     * Get the subcontext for export.
     *
     * @param array $subcontext Any additional subcontext to use.
     * @return array The array containing the full subcontext, i.e. [enrolments, subcontext]
     */
    public static function get_subcontext(array $subcontext) {
        return array_merge(
            [get_string('privacy:metadata:user_enrolments', 'core_enrol')],
            $subcontext
        );
    }
}
