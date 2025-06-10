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
 * Privacy Subsystem implementation for mod_hotpot.
 *
 * @package    mod_hotpot
 * @category   privacy
 * @copyright  2018 onwards Catalyst IT Ltd <http://catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Anupama Dharmajan <anupamadharmajan@catalyst-au.net>
 */

namespace mod_hotpot\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\deletion_criteria;
use core_privacy\local\request\helper;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

/**
 * Implementation of the privacy subsystem plugin provider for the hotpot activity module.
 *
 * @copyright  2018 onwards Catalyst IT Ltd <http://catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Anupama Dharmajan <anupamadharmajan@catalyst-au.net>
 */
class provider implements

    // This plugin stores personal data.
    \core_privacy\local\metadata\provider,

    // This plugin is a core_user_data_provider.
    \core_privacy\local\request\plugin\provider,

    // This plugin is capable of determining which users have data within it.
    \core_privacy\local\request\core_userlist_provider {

    /**
     * Return the fields which contain personal data.
     *
     * @param collection $items a reference to the collection to use to store the metadata.
     * @return collection the updated collection of metadata items.
     */
    public static function get_metadata(collection $items): collection {
        $items->add_database_table(
            'hotpot_attempts',
            [
                'hotpotid' => 'privacy:metadata:hotpot_attempts:hotpotid',
                'userid' => 'privacy:metadata:hotpot_attempts:userid',
                'starttime' => 'privacy:metadata:hotpot_attempts:starttime',
                'endtime' => 'privacy:metadata:hotpot_attempts:endtime',
                'score' => 'privacy:metadata:hotpot_attempts:score',
                'penalties' => 'privacy:metadata:hotpot_attempts:penalties',
                'attempt' => 'privacy:metadata:hotpot_attempts:attempt',
                'timestart' => 'privacy:metadata:hotpot_attempts:timestart',
                'timefinish' => 'privacy:metadata:hotpot_attempts:timefinish',
                'status' => 'privacy:metadata:hotpot_attempts:status',
                'clickreportid' => 'privacy:metadata:hotpot_attempts:clickreportid',
                'timemodified' => 'privacy:metadata:hotpot_attempts:timemodified',
            ],
            'privacy:metadata:hotpot_attempts'
        );

        return $items;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid the userid.
     * @return contextlist the list of contexts containing user info for the user.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        // Fetch all hotpot details.
        $sql = "SELECT c.id
                  FROM {context} c
            INNER JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
            INNER JOIN {modules} m ON m.id = cm.module AND m.name = :modname
            INNER JOIN {hotpot} h ON h.id = cm.instance
            INNER JOIN {hotpot_cache} hc ON hc.hotpotid = h.id
            INNER JOIN {hotpot_attempts} ha ON ha.hotpotid = h.id
            INNER JOIN {hotpot_details} hd ON hd.attemptid = ha.id
            INNER JOIN {hotpot_questions} hq ON hq.hotpotid = h.id
            INNER JOIN {hotpot_responses} hr ON hr.attemptid = ha.id AND hr.questionid = hq.id
                 WHERE ha.userid = :userid";

        $params = [
            'modname'       => 'hotpot',
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
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!$context instanceof \context_module) {
            return;
        }

        // Fetch all hotpot answers.
        $sql = "SELECT ha.userid
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {hotpot} h ON h.id = cm.instance
                  INNER JOIN {hotpot_cache} hc ON hc.hotpotid = h.id
                  INNER JOIN {hotpot_attempts} ha ON ha.hotpotid = h.id
                  INNER JOIN {hotpot_details} hd ON hd.attemptid = ha.id
                  INNER JOIN {hotpot_questions} hq ON hq.hotpotid = h.id
                  INNER JOIN {hotpot_responses} hr ON hr.attemptid = ha.id AND hr.questionid = hq.id
                 WHERE cm.id = :cmid";

        $params = [
            'cmid'      => $context->instanceid,
            'modname'   => 'hotpot',
        ];

        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Export personal data for the given approved_contextlist. User and context information is contained within the contextlist.
     *
     * @param approved_contextlist $contextlist a list of contexts approved for export.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        $sql = "SELECT cm.id AS cmid,
                       hd.details as details,
                       ha.timemodified
                  FROM {context} c
            INNER JOIN {course_modules} cm ON cm.id = c.instanceid
            INNER JOIN {hotpot} h ON h.id = cm.instance
            INNER JOIN {hotpot_cache} hc ON hc.hotpotid = h.id
            INNER JOIN {hotpot_attempts} ha ON ha.hotpotid = h.id
            INNER JOIN {hotpot_details} hd ON hd.attemptid = ha.id
            INNER JOIN {hotpot_questions} hq ON hq.hotpotid = h.id
            INNER JOIN {hotpot_responses} hr ON hr.attemptid = ha.id AND hr.questionid = hq.id
                 WHERE c.id {$contextsql}
                       AND ha.userid = :userid
              ORDER BY cm.id";

        $params = ['modname' => 'hotpot', 'contextlevel' => CONTEXT_MODULE, 'userid' => $user->id] + $contextparams;

        // Reference to the hotpot attempts activity seen in the last iteration of the loop. By comparing this with the current record, and
        // because we know the results are ordered, we know when we've moved to the hotpot details for a new hotpot attempts activity and therefore
        // when we can export the complete data for the last activity.
        $lastcmid = null;

        $hotpotdetails = $DB->get_recordset_sql($sql, $params);
        foreach ($hotpotdetails as $hotpotdetail) {
            // If we've moved to a new hotpot attempts, then write the last hotpot data and reinit the hotpot data array.
            if ($lastcmid != $hotpotdetail->cmid) {
                if (!empty($hotpotdata)) {
                    $context = \context_module::instance($lastcmid);
                    self::export_hotpot_data_for_user($hotpotdata, $context, $user);
                }
                $hotpotdata = [
                    'details' => [],
                    'timemodified' => \core_privacy\local\request\transform::datetime($hotpotdetail->timemodified),
                ];
            }
            $hotpotdata['details'][] = $hotpotdetail->details;
            $lastcmid = $hotpotdetail->cmid;
        }
        $hotpotdetails->close();

        // The data for the last activity won't have been written yet, so make sure to write it now!
        if (!empty($hotpotdata)) {
            $context = \context_module::instance($lastcmid);
            self::export_hotpot_data_for_user($hotpotdata, $context, $user);
        }
    }

    /**
     * Export the supplied personal data for a single hotpot attempts activity, along with any generic data or area files.
     *
     * @param array $hotpotdata the personal data to export for the hotpot.
     * @param \context_module $context the context of the hotpot.
     * @param \stdClass $user the user record
     */
    protected static function export_hotpot_data_for_user(array $hotpotdata, \context_module $context, \stdClass $user) {
        // Fetch the generic module data for the hotpot.
        $contextdata = helper::get_context_data($context, $user);

        // Merge with hotpot data and write it.
        $contextdata = (object)array_merge((array)$contextdata, $hotpotdata);
        writer::with_context($context)->export_data([], $contextdata);

        // Write generic module intro files.
        helper::export_context_files($context, $user);
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

        if ($cm = get_coursemodule_from_id('hotpot', $context->instanceid)) {
            $DB->delete_records('hotpot_attempts', ['hotpotid' => $cm->instance]);
        }
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
            $DB->delete_records('hotpot_attempts', ['hotpotid' => $instanceid, 'userid' => $userid]);
        }
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist       $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();

        if (!$context instanceof \context_module) {
            return;
        }

        $cm = get_coursemodule_from_id('hotpot', $context->instanceid);

        if (!$cm) {
            // Only hotpot module will be handled.
            return;
        }

        $userids = $userlist->get_userids();
        list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);

        $select = "hotpotid = :hotpotid AND userid $usersql";
        $params = ['hotpotid' => $cm->instance] + $userparams;
        $DB->delete_records_select('hotpot_attempts', $select, $params);
    }
}