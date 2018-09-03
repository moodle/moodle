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
 * Privacy Subsystem implementation for mnetservice_enrol.
 *
 * @package    mnetservice_enrol
 * @copyright  2018 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mnetservice_enrol\privacy;
defined('MOODLE_INTERNAL') || die();
use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\context;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;
/**
 * Privacy Subsystem for mnetservice_enrol implementing metadata and plugin providers.
 *
 * @copyright  2018 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        \core_privacy\local\metadata\provider,
        \core_privacy\local\request\plugin\provider {
    /**
     * Returns meta data about this system.
     *
     * @param   collection $collection The initialised collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {
        $collection->add_database_table(
            'mnetservice_enrol_enrolments',
            [
                'hostid' => 'privacy:metadata:mnetservice_enrol_enrolments:hostid',
                'userid' => 'privacy:metadata:mnetservice_enrol_enrolments:userid',
                'remotecourseid' => 'privacy:metadata:mnetservice_enrol_enrolments:remotecourseid',
                'rolename' => 'privacy:metadata:mnetservice_enrol_enrolments:rolename',
                'enroltime' => 'privacy:metadata:mnetservice_enrol_enrolments:enroltime',
                'enroltype' => 'privacy:metadata:mnetservice_enrol_enrolments:enroltype'
            ],
            'privacy:metadata:mnetservice_enrol_enrolments:tableexplanation'
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
        $sql = "SELECT c.id
                  FROM {context} c
                  JOIN {mnetservice_enrol_enrolments} me
                    ON me.userid = c.instanceid
                   AND c.contextlevel = :contextlevel
                 WHERE me.userid = :userid";
        $params = [
            'contextlevel' => CONTEXT_USER,
            'userid'       => $userid
        ];
        $contextlist = new contextlist();
        $contextlist->add_from_sql($sql, $params);
        return $contextlist;
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
        $contextuser = \context_user::instance($userid);
        list($insql, $inparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);
        $params = [
            'userid' => $userid,
            'contextlevel' => CONTEXT_USER
         ];
        $params += $inparams;
        $sql = "SELECT me.id,
                       me.rolename,
                       me.enroltime,
                       me.enroltype,
                       mh.name as hostname,
                       mc.fullname
                  FROM {mnetservice_enrol_enrolments} me
                  JOIN {context} ctx
                    ON ctx.instanceid = me.userid
                   AND ctx.contextlevel = :contextlevel
                  JOIN {mnet_host} mh
                    ON mh.id = me.hostid
                  JOIN {mnetservice_enrol_courses} mc
                    ON mc.remoteid = me.remotecourseid
                 WHERE me.userid = :userid
                   AND ctx.id {$insql}";
        $mnetenrolments = $DB->get_records_sql($sql, $params);
        foreach ($mnetenrolments as $mnetenrolment) {
            // The core_enrol data export is organised in:
            // {User Context}/User enrolments/data.json.
            $data[] = (object) [
                'host' => $mnetenrolment->hostname,
                'remotecourseid' => $mnetenrolment->fullname,
                'rolename' => $mnetenrolment->rolename,
                'enroltime' => transform::datetime($mnetenrolment->enroltime),
                'enroltype' => $mnetenrolment->enroltype
            ];
        }
        writer::with_context($contextuser)->export_data(
                [get_string('privacy:metadata:mnetservice_enrol_enrolments', 'mnetservice_enrol')],
                (object)$data
            );
    }
    /**
     * Delete all data for all users in the specified context.
     *
     * @param   context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        // Sanity check that context is at the User context level.
        if ($context->contextlevel == CONTEXT_USER) {
            static::delete_user_data($context->instanceid);
        }
    }
    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        if (empty($contextlist->count())) {
            return;
        }
        $user = $contextlist->get_user();
        foreach ($contextlist->get_contexts() as $context) {
            // Verify the context is a user context and that the instanceid matches the userid of the contextlist.
            if ($context->contextlevel == CONTEXT_USER && $context->instanceid == $user->id) {
                // Get the data and write it.
                 static::delete_user_data($user->id);
            }
        }
    }
    /**
     * This does the deletion of user data for the mnetservice_enrolments.
     *
     * @param  int $userid The user ID
     */
    protected static function delete_user_data(int $userid) {
        global $DB;
        // Because we only use user contexts the instance ID is the user ID.
        $DB->delete_records('mnetservice_enrol_enrolments', ['userid' => $userid]);
    }
}
