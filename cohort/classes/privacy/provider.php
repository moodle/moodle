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
 * Privacy class for requesting user data.
 *
 * @package    core_cohort
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_cohort\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\metadata\collection;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;

/**
 * Privacy class for requesting user data.
 *
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        \core_privacy\local\metadata\provider,
        \core_privacy\local\request\plugin\provider {

    /**
     * Return the fields which contain personal data.
     *
     * @param   collection $collection The initialised collection to add items to.
     * @return  collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) {
        $collection->add_database_table('cohort_members', [
                'cohortid' => 'privacy:metadata:cohort_members:cohortid',
                'userid' => 'privacy:metadata:cohort_members:userid',
                'timeadded' => 'privacy:metadata:cohort_members:timeadded'
            ], 'privacy:metadata:cohort_members');
        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return contextlist $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid($userid) {
        $sql = "SELECT ctx.id
                  FROM {context} ctx
            INNER JOIN {cohort} c ON c.contextid = ctx.id
            INNER JOIN {cohort_members} cm ON cm.cohortid = c.id
                 WHERE cm.userid = :userid AND (ctx.contextlevel = :contextlevel1 OR ctx.contextlevel = :contextlevel2)";
        $params = [
            'userid'        => $userid,
            'contextlevel1' => CONTEXT_SYSTEM,
            'contextlevel2' => CONTEXT_COURSECAT,
        ];
        $contextlist = new contextlist();
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

        // Remove contexts different from SYSTEM or COURSECAT.
        $contexts = array_reduce($contextlist->get_contexts(), function($carry, $context) {
            if ($context->contextlevel == CONTEXT_SYSTEM || $context->contextlevel == CONTEXT_COURSECAT) {
                $carry[] = $context->id;
            }
            return $carry;
        }, []);

        if (empty($contexts)) {
            return;
        }

        // Get cohort data.
        $userid = $contextlist->get_user()->id;
        list($contextsql, $contextparams) = $DB->get_in_or_equal($contexts, SQL_PARAMS_NAMED);
        $sql = "SELECT c.name,
                       c.idnumber,
                       c.description,
                       c.visible,
                       cm.timeadded,
                       ctx.id as contextid
                  FROM {context} ctx
            INNER JOIN {cohort} c ON c.contextid = ctx.id
            INNER JOIN {cohort_members} cm ON cm.cohortid = c.id
                 WHERE ctx.id {$contextsql}
                       AND cm.userid = :userid";
        $params = [
                'userid'        => $userid
            ] + $contextparams;

        $cohorts = $DB->get_recordset_sql($sql, $params);
        foreach ($cohorts as $cohort) {
            $alldata[$cohort->contextid][] = (object)[
                    'name' => $cohort->name,
                    'idnumber' => $cohort->idnumber,
                    'visible' => transform::yesno($cohort->visible),
                    'timeadded' => transform::datetime($cohort->timeadded),
                ];
        }
        $cohorts->close();

        // Export cohort data.
        array_walk($alldata, function($data, $contextid) {
            $context = \context::instance_by_id($contextid);
            writer::with_context($context)->export_related_data([], 'cohort', $data);
        });

    }

    /**
     * Delete all use data which matches the specified context.
     *
     * @param context $context A user context.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        if (!$context instanceof \context_system && !$context instanceof \context_coursecat) {
            return;
        }

        static::delete_data($context);
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        if (empty($contextlist->count())) {
            return;
        }

        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if (!$context instanceof \context_system && !$context instanceof \context_coursecat) {
                continue;
            }
            static::delete_data($context, $userid);
        }
    }

    /**
     * Delete data related to a context and user (if defined).
     *
     * @param context $context A context.
     * @param int $userid The user ID.
     */
    protected static function delete_data(\context $context, $userid = null) {
        global $DB;

        $cohortids = $DB->get_fieldset_select('cohort', 'id', 'contextid = :contextid', ['contextid' => $context->id]);
        foreach ($cohortids as $cohortid) {
            $params = ['cohortid' => $cohortid];
            if (!empty($userid)) {
                $params['userid'] = $userid;
            }
            $DB->delete_records('cohort_members', $params);
        }
    }
}
