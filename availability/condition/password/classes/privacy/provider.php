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
 * Availability password - Privacy provider
 *
 * @package    availability_password
 * @copyright  2018 Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_password\privacy;

use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\transform;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\helper as request_helper;

/**
 * Privacy Subsystem implementing provider.
 *
 * @package    availability_password
 * @copyright  2018 Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements \core_privacy\local\metadata\provider,
        \core_privacy\local\request\plugin\provider,
        \core_privacy\local\request\core_userlist_provider {

    /**
     * Returns meta data about this system.
     *
     * @param collection $collection The initialised item collection to add items to.
     *
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table(
                'availability_password_grant',
                [
                        'courseid' => 'privacy:metadata:availability_password_grant:courseid',
                        'cmid' => 'privacy:metadata:availability_password_grant:cmid',
                        'userid' => 'privacy:metadata:availability_password_grant:userid',
                        'password' => 'privacy:metadata:availability_password_grant:password',
                ],
                'privacy:metadata:availability_password_grant'
        );

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     *
     * @return contextlist $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): \core_privacy\local\request\contextlist {
        $contextlist = new \core_privacy\local\request\contextlist();

        $sql = "SELECT c.id
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
                  JOIN {availability_password_grant} a ON a.cmid = cm.id
                 WHERE (
                    a.userid = :userid
                )
        ";
        $params = [
                'userid' => $userid,
                'contextlevel' => CONTEXT_MODULE,
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

        if (empty($contextlist)) {
            return;
        }

        $user = $contextlist->get_user();
        $userid = $user->id;

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        $sql = "SELECT
                    c.id AS contextid,
                    a.courseid, a.cmid, a.userid, a.password
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
                  JOIN {availability_password_grant} a ON a.cmid = cm.id
                 WHERE (
                    a.userid = :userid AND
                    c.id {$contextsql}
                )
        ";

        $params = [
                'userid' => $userid,
                'contextlevel' => CONTEXT_MODULE,
        ];
        $params += $contextparams;

        $data = $DB->get_recordset_sql($sql, $params);
        foreach ($data as $d) {
            $context = \context::instance_by_id($d->contextid);

            writer::with_context($context)->export_data([get_string('pluginname', 'availability_password')], $d);
        }
        $data->close();
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if ($context->contextlevel != CONTEXT_MODULE) {
            return;
        }

        $cm = get_coursemodule_from_id(null, $context->instanceid);
        if (!$cm) {
            return;
        }

        $DB->delete_records('availability_password_grant', ['cmid' => $cm->id]);
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $userid = $contextlist->get_user()->id;

        foreach ($contextlist->get_contexts() as $context) {
            $DB->delete_records('availability_password_grant', ['cmid' => $context->instanceid, 'userid' => $userid]);
        }
    }

    /**
     * Get the list of users who have data within a context.
     * @param userlist $userlist
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();
        if (!is_a($context, \context_module::class)) {
            return;
        }

        // Users who have correctly entered a password in this context.
        $params = [
            'contextid' => $context->id,
            'contextlevel' => CONTEXT_MODULE,
        ];
        $sql = "
            SELECT p.userid
              FROM {availability_password_grant} p
              JOIN {context} ctx ON ctx.instanceid = p.cmid AND ctx.contextlevel = :contextlevel
             WHERE ctx.id = :contextid
        ";
        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Delete multiple users within a single context
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;
        $context = $userlist->get_context();
        if (!is_a($context, \context_module::class)) {
            return; // Ignore requests that relate to contexts other than the course module context.
        }

        // Delete all password records for the given users in the given course module context.
        $userids = $userlist->get_userids();
        list($usql, $params) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $select = "userid $usql AND cmid = :cmid";
        $params['cmid'] = $context->instanceid;

        $DB->delete_records_select('availability_password_grant', $select, $params);
    }
}



