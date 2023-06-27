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
 * Privacy Subsystem implementation for repository_onedrive.
 *
 * @package    repository_onedrive
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace repository_onedrive\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\context;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use \core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy Subsystem for repository_onedrive implementing metadata and plugin providers.
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
    public static function get_metadata(collection $collection) : collection {
        $collection->add_external_location_link(
            'onedrive.live.com',
            [
                'searchtext' => 'privacy:metadata:repository_onedrive:searchtext'
            ],
            'privacy:metadata:repository_onedrive'
        );

        // The repository_onedrive has a 'repository_onedrive_access' table that contains user data.
        $collection->add_database_table(
            'repository_onedrive_access',
            [
                'itemid' => 'privacy:metadata:repository_onedrive:repository_onedrive_access:itemid',
                'permissionid' => 'privacy:metadata:repository_onedrive:repository_onedrive_access:permissionid',
                'timecreated' => 'privacy:metadata:repository_onedrive:repository_onedrive_access:timecreated',
                'timemodified' => 'privacy:metadata:repository_onedrive:repository_onedrive_access:timemodified',
                'usermodified' => 'privacy:metadata:repository_onedrive:repository_onedrive_access:usermodified'
            ],
            'privacy:metadata:repository_onedrive'
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
        $contextlist = new contextlist();

        // The data is associated at the user context level, so retrieve the user's context id.
        $sql = "SELECT c.id
                  FROM {repository_onedrive_access} roa
                  JOIN {context} c ON c.instanceid = roa.usermodified AND c.contextlevel = :contextuser
                 WHERE roa.usermodified = :userid
              GROUP BY c.id";

        $params = [
            'contextuser'   => CONTEXT_USER,
            'userid'        => $userid
        ];

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

        if (!$context instanceof \context_user) {
            return;
        }

        // The data is associated at the user context level, so retrieve the user's context id.
        $sql = "SELECT usermodified AS userid
                  FROM {repository_onedrive_access}
                 WHERE usermodified = ?";
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

        // If the user has data, then only the User context should be present so get the first context.
        $contexts = $contextlist->get_contexts();
        if (count($contexts) == 0) {
            return;
        }
        $context = reset($contexts);

        // Sanity check that context is at the User context level, then get the userid.
        if ($context->contextlevel !== CONTEXT_USER) {
            return;
        }
        $userid = $context->instanceid;

        $sql = "SELECT roa.id as id,
                       roa.itemid as itemid,
                       roa.permissionid as permissionid,
                       roa.timecreated as timecreated,
                       roa.timemodified as timemodified
                  FROM {repository_onedrive_access} roa
                 WHERE roa.usermodified = :userid";

        $params = [
            'userid' => $userid
        ];

        $onedriveaccesses = $DB->get_records_sql($sql, $params);
        $index = 0;
        foreach ($onedriveaccesses as $onedriveaccess) {
            // Data export is organised in: {User Context}/Repository plug-ins/{Plugin Name}/Access/{index}/data.json.
            $index++;
            $subcontext = [
                get_string('plugin', 'core_repository'),
                get_string('pluginname', 'repository_onedrive'),
                get_string('access', 'repository_onedrive'),
                $index
            ];

            $data = (object) [
                'itemid' => $onedriveaccess->itemid,
                'permissionid' => $onedriveaccess->permissionid,
                'timecreated' => transform::datetime($onedriveaccess->timecreated),
                'timemodified' => transform::datetime($onedriveaccess->timemodified)
            ];

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

        // Sanity check that context is at the User context level, then get the userid.
        if ($context->contextlevel !== CONTEXT_USER) {
            return;
        }
        $userid = $context->instanceid;

        $DB->delete_records('repository_onedrive_access', ['usermodified' => $userid]);
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        // If the user has data, then only the User context should be present so get the first context.
        $contexts = $contextlist->get_contexts();
        if (count($contexts) == 0) {
            return;
        }
        $context = reset($contexts);

        // Sanity check that context is at the User context level, then get the userid.
        if ($context->contextlevel !== CONTEXT_USER) {
            return;
        }
        $userid = $context->instanceid;

        $DB->delete_records('repository_onedrive_access', ['usermodified' => $userid]);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist       $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;
        $context = $userlist->get_context();

        // Sanity check that context is at the User context level, then get the userid.
        if ($context->contextlevel !== CONTEXT_USER) {
            return;
        }

        $userids = $userlist->get_userids();
        list($insql, $inparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $params = [
            'contextid' => $context->id,
            'contextuser' => CONTEXT_USER,
        ];
        $params = array_merge($params, $inparams);

        // Fetch the repository_onedrive_access IDs in the context for approved users.
        $sql = "SELECT roa.id
                  FROM {repository_onedrive_access} roa
                  JOIN {context} c ON c.instanceid = roa.usermodified
                   AND c.contextlevel = :contextuser
                   AND c.id = :contextid
                 WHERE roa.usermodified {$insql}";
        $accessids = $DB->get_fieldset_sql($sql, $params);

        // Delete the relevant repository_onedrive_access data.
        list($insql, $params) = $DB->get_in_or_equal($accessids, SQL_PARAMS_NAMED);
        $DB->delete_records_select('repository_onedrive_access', "id {$insql}", $params);
    }
}
