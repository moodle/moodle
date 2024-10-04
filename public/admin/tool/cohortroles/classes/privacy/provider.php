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
 * Privacy Subsystem implementation for tool_cohortroles.
 *
 * @package    tool_cohortroles
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_cohortroles\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\context;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;
use core_privacy\local\request\userlist;
use \core_privacy\local\request\approved_userlist;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy Subsystem for tool_cohortroles implementing metadata and plugin providers.
 *
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
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
    public static function get_metadata(collection $collection): collection {
        // The tool_cohortroles plugin utilises the mdl_tool_cohortroles table.
        $collection->add_database_table(
            'tool_cohortroles',
            [
                'id' => 'privacy:metadata:tool_cohortroles:id',
                'cohortid' => 'privacy:metadata:tool_cohortroles:cohortid',
                'roleid' => 'privacy:metadata:tool_cohortroles:roleid',
                'userid' => 'privacy:metadata:tool_cohortroles:userid',
                'timecreated' => 'privacy:metadata:tool_cohortroles:timecreated',
                'timemodified' => 'privacy:metadata:tool_cohortroles:timemodified',
                'usermodified' => 'privacy:metadata:tool_cohortroles:usermodified'
            ],
            'privacy:metadata:tool_cohortroles'
        );

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int $userid The user to search.
     * @return  contextlist   $contextlist  The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();

        // When we process user deletions and expiries, we always delete from the user context.
        // As a result the cohort role assignments would be deleted, which has a knock-on effect with courses
        // as roles may change and data may be removed earlier than it should be.

        // Retrieve the context associated with tool_cohortroles records.
        $sql = "SELECT DISTINCT c.contextid
                  FROM {tool_cohortroles} tc
                  JOIN {cohort} c
                       ON tc.cohortid = c.id
                  JOIN {context} ctx
                       ON ctx.id = c.contextid
                 WHERE tc.userid = :userid
                       AND (ctx.contextlevel = :contextlevel1
                           OR ctx.contextlevel = :contextlevel2)";

        $params = [
            'userid'        => $userid,
            'contextlevel1' => CONTEXT_SYSTEM,
            'contextlevel2' => CONTEXT_COURSECAT
        ];

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

        // When we process user deletions and expiries, we always delete from the user context.
        // As a result the cohort role assignments would be deleted, which has a knock-on effect with courses
        // as roles may change and data may be removed earlier than it should be.

        $allowedcontextlevels = [
            CONTEXT_SYSTEM,
            CONTEXT_COURSECAT
        ];

        if (!in_array($context->contextlevel, $allowedcontextlevels)) {
            return;
        }

        $sql = "SELECT tc.userid as userid
                  FROM {tool_cohortroles} tc
                  JOIN {cohort} c
                       ON tc.cohortid = c.id
                 WHERE c.contextid = :contextid";

        $params = [
            'contextid' => $context->id
        ];

        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        // Remove contexts different from SYSTEM or COURSECAT.
        $contextids = array_reduce($contextlist->get_contexts(), function($carry, $context) {
            if ($context->contextlevel == CONTEXT_SYSTEM || $context->contextlevel == CONTEXT_COURSECAT) {
                $carry[] = $context->id;
            }
            return $carry;
        }, []);

        if (empty($contextids)) {
            return;
        }

        $userid = $contextlist->get_user()->id;

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextids, SQL_PARAMS_NAMED);

        // Retrieve the tool_cohortroles records created for the user.
        $sql = "SELECT cr.id as cohortroleid,
                       c.name as cohortname,
                       c.idnumber as cohortidnumber,
                       c.description as cohortdescription,
                       c.contextid as contextid,
                       r.shortname as roleshortname,
                       cr.userid as userid,
                       cr.timecreated as timecreated,
                       cr.timemodified as timemodified
                  FROM {tool_cohortroles} cr
                  JOIN {cohort} c ON c.id = cr.cohortid
                  JOIN {role} r ON r.id = cr.roleid
                 WHERE cr.userid = :userid
                       AND c.contextid {$contextsql}";

        $params = ['userid' => $userid] + $contextparams;

        $cohortroles = $DB->get_records_sql($sql, $params);

        foreach ($cohortroles as $cohortrole) {
            // The tool_cohortroles data export is organised in:
            // {User Context}/Cohort roles management/{cohort name}/{role shortname}/data.json.
            $subcontext = [
                get_string('pluginname', 'tool_cohortroles'),
                $cohortrole->cohortname,
                $cohortrole->roleshortname
            ];

            $data = (object) [
                'cohortname' => $cohortrole->cohortname,
                'cohortidnumber' => $cohortrole->cohortidnumber,
                'cohortdescription' => $cohortrole->cohortdescription,
                'roleshortname' => $cohortrole->roleshortname,
                'userid' => transform::user($cohortrole->userid),
                'timecreated' => transform::datetime($cohortrole->timecreated),
                'timemodified' => transform::datetime($cohortrole->timemodified)
            ];

            $context = \context::instance_by_id($cohortrole->contextid);

            writer::with_context($context)->export_data($subcontext, $data);
        }
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param   context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        // When we process user deletions and expiries, we always delete from the user context.
        // As a result the cohort role assignments would be deleted, which has a knock-on effect with courses
        // as roles may change and data may be removed earlier than it should be.

        $allowedcontextlevels = [
            CONTEXT_SYSTEM,
            CONTEXT_COURSECAT
        ];

        if (!in_array($context->contextlevel, $allowedcontextlevels)) {
            return;
        }

        $cohortids = $DB->get_fieldset_select('cohort', 'id', 'contextid = :contextid',
            ['contextid' => $context->id]);

        // Delete the tool_cohortroles records created in the specific context.
        $DB->delete_records_list('tool_cohortroles', 'cohortid', $cohortids);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        // When we process user deletions and expiries, we always delete from the user context.
        // As a result the cohort role assignments would be deleted, which has a knock-on effect with courses
        // as roles may change and data may be removed earlier than it should be.

        $userids = $userlist->get_userids();

        if (empty($userids)) {
            return;
        }

        $context = $userlist->get_context();

        $allowedcontextlevels = [
            CONTEXT_SYSTEM,
            CONTEXT_COURSECAT
        ];

        if (!in_array($context->contextlevel, $allowedcontextlevels)) {
            return;
        }

        $cohortids = $DB->get_fieldset_select('cohort', 'id', 'contextid = :contextid',
            ['contextid' => $context->id]);

        if (empty($cohortids)) {
            return;
        }

        list($cohortsql, $cohortparams) = $DB->get_in_or_equal($cohortids, SQL_PARAMS_NAMED);
        list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);

        $params = $cohortparams + $userparams;
        $select = "cohortid {$cohortsql} AND userid {$usersql}";

        // Delete the tool_cohortroles records created in the specific context for an approved list of users.
        $DB->delete_records_select('tool_cohortroles', $select, $params);
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        // When we process user deletions and expiries, we always delete from the user context.
        // As a result the cohort role assignments would be deleted, which has a knock-on effect with courses
        // as roles may change and data may be removed earlier than it should be.

        // Remove contexts different from SYSTEM or COURSECAT.
        $contextids = array_reduce($contextlist->get_contexts(), function($carry, $context) {
            if ($context->contextlevel == CONTEXT_SYSTEM || $context->contextlevel == CONTEXT_COURSECAT) {
                $carry[] = $context->id;
            }
            return $carry;
        }, []);

        if (empty($contextids)) {
            return;
        }

        $userid = $contextlist->get_user()->id;

        list($contextsql, $contextparams) =  $DB->get_in_or_equal($contextids, SQL_PARAMS_NAMED);
        $selectcontext = "contextid {$contextsql}";
        // Get the cohorts in the specified contexts.
        $cohortids = $DB->get_fieldset_select('cohort', 'id', $selectcontext, $contextparams);

        if (empty($cohortids)) {
            return;
        }

        list($cohortsql, $cohortparams) =  $DB->get_in_or_equal($cohortids, SQL_PARAMS_NAMED);
        $selectcohort = "cohortid {$cohortsql} AND userid = :userid";
        $params = ['userid' => $userid] + $cohortparams;

        // Delete the tool_cohortroles records created for the userid.
        $DB->delete_records_select('tool_cohortroles', $selectcohort, $params);
    }
}
