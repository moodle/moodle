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
 * @package    message_airnotifier
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace message_airnotifier\privacy;

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\contextlist;
use \core_privacy\local\request\approved_contextlist;
use \core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use \core_privacy\local\request\approved_userlist;

/**
 * Privacy class for requesting user data.
 *
 * @package    message_airnotifier
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
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
     * @return  collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {
        $collection->link_subsystem('core_user', 'privacy:metadata:usersubsystem');
        $collection->add_database_table('message_airnotifier_devices', [
                'userdeviceid' => 'privacy:metadata:userdeviceid',
                'enabled' => 'privacy:metadata:enabled'
            ], 'privacy:metadata:tableexplanation');
        $collection->link_external_location('External airnotifier site.', [
                'userid' => 'privacy:metadata:userid',
                'username' => 'privacy:metadata:username',
                'userfromid' => 'privacy:metadata:userfromid',
                'userfromfullname' => 'privacy:metadata:userfromfullname',
                'date' => 'privacy:metadata:date',
                'subject' => 'privacy:metadata:subject',
                'notification' => 'privacy:metadata:notification',
                'smallmessage' => 'privacy:metadata:smallmessage',
                'fullmessage' => 'privacy:metadata:fullmessage'
        ], 'privacy:metadata:externalpurpose');
        // This system is unaware of user preferences such as message_provider_moodle_instantmessage_loggedin.
        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int         $userid     The user to search.
     * @return  contextlist $contextlist  The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        $sql = "SELECT ctx.id
                  FROM {message_airnotifier_devices} mad
                  JOIN {user_devices} ud ON ud.id = mad.userdeviceid
                  JOIN {user} u ON ud.userid = u.id
                  JOIN {context} ctx ON ctx.instanceid = u.id AND ctx.contextlevel = :contextlevel
                 WHERE ud.userid = :userid";

        $params = ['userid' => $userid, 'contextlevel' => CONTEXT_USER];

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

        if (!$context instanceof \context_user) {
            return;
        }

        $sql = "SELECT ud.userid
                  FROM {message_airnotifier_devices} mad
                  JOIN {user_devices} ud ON ud.id = mad.userdeviceid
                 WHERE ud.userid = ?";
        $params = [$context->instanceid];
        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        $results = static::get_records($contextlist->get_user()->id);
        $context = $contextlist->current();
        foreach ($results as $result) {
            $data = (object)[
                'appid' => $result->appid,
                'pushid' => $result->pushid,
                'name' => $result->name,
                'model' => $result->model,
                'platform' => $result->platform,
                'version' => $result->version,
                'timecreated' => transform::datetime($result->timecreated),
                'timemodified' => transform::datetime($result->timemodified),
                'enabled' => transform::yesno($result->enable)
            ];
            \core_privacy\local\request\writer::with_context($context)->export_data([
                    get_string('privacy:subcontext', 'message_airnotifier'),
                    $result->model . '_' . $result->pushid
                ], $data);
        }
    }

    /**
     * Delete all use data which matches the specified deletion_criteria.
     *
     * @param context $context A context.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {

        if (!$context instanceof \context_user) {
            return;
        }

        static::delete_data($context->instanceid);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        $context = $userlist->get_context();

        if ($context instanceof \context_user) {
            static::delete_data($context->instanceid);
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        static::delete_data($contextlist->get_user()->id);
    }

    /**
     * Delete data related to a userid.
     *
     * @param int $userid The user ID
     */
    protected static function delete_data(int $userid) {
        global $DB;

        foreach (static::get_records($userid) as $record) {
            $DB->delete_records('message_airnotifier_devices', ['id' => $record->id]);
        }
    }

    /**
     * Get records related to this plugin and user.
     *
     * @param  int $userid The user ID
     * @return array An array of records.
     */
    protected static function get_records(int $userid) : array {
        global $DB;
        $sql = "SELECT mad.id, mad.enable, ud.appid, ud.name, ud.model, ud.platform, ud.version, ud.timecreated, ud.timemodified,
                        ud.pushid
                FROM {message_airnotifier_devices} mad
                JOIN {user_devices} ud ON mad.userdeviceid = ud.id
                WHERE ud.userid = :userid";
        $params = ['userid' => $userid];
        return $DB->get_records_sql($sql, $params);
    }
}
