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
 * Privacy Subsystem implementation for core_repository.
 *
 * @package    core_repository
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_repository\privacy;

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
 * Privacy Subsystem for core_repository implementing metadata and plugin providers.
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
        $collection->add_database_table(
            'repository_instances',
            [
                'name' => 'privacy:metadata:repository_instances:name',
                'typeid' => 'privacy:metadata:repository_instances:typeid',
                'userid' => 'privacy:metadata:repository_instances:userid',
                'username' => 'privacy:metadata:repository_instances:username',
                'password' => 'privacy:metadata:repository_instances:password',
                'timecreated' => 'privacy:metadata:repository_instances:timecreated',
                'timemodified' => 'privacy:metadata:repository_instances:timemodified',
            ],
            'privacy:metadata:repository_instances'
        );

        $collection->add_plugintype_link('repository', [], 'privacy:metadata:repository');

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

        // The repository_instances data is associated at the user context level, so retrieve the user's context id.
        $sql = "SELECT c.id
                  FROM {repository_instances} ri
                  JOIN {context} c ON c.instanceid = ri.userid AND c.contextlevel = :contextuser
                 WHERE ri.userid = :userid
              GROUP BY c.id";

        $params = [
            'contextuser'   => CONTEXT_USER,
            'userid'        => $userid
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

        if (!$context instanceof \context_user) {
            return;
        }

        $sql = "SELECT userid
                  FROM {repository_instances}
                 WHERE userid = ?";
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

        // If the user has repository_instances data, then only the User context should be present so get the first context.
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

        $sql = "SELECT DISTINCT
                       ri.id as id,
                       r.type as type,
                       ri.name as name,
                       ri.timecreated as timecreated,
                       ri.timemodified as timemodified
                  FROM {repository_instances} ri
                  JOIN {repository} r ON r.id = ri.typeid
                 WHERE ri.userid = :userid";

        $params = [
            'userid' => $userid
        ];

        $repositoryinstances = $DB->get_records_sql($sql, $params);

        foreach ($repositoryinstances as $repositoryinstance) {
            // The repository_instances data export is organised in: {User Context}/Repository plug-ins/{Plugin Name}/data.json.
            $subcontext = [
                get_string('plugin', 'core_repository'),
                get_string('pluginname', 'repository_' . $repositoryinstance->type)
            ];

            $data = (object) [
                'type' => $repositoryinstance->type,
                'name' => $repositoryinstance->name,
                'timecreated' => transform::datetime($repositoryinstance->timecreated),
                'timemodified' => transform::datetime($repositoryinstance->timemodified)
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

        // Delete the repository_instances records created for the userid.
        $DB->delete_records('repository_instances', ['userid' => $userid]);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();

        if ($context instanceof \context_user) {
            $DB->delete_records('repository_instances', ['userid' => $context->instanceid]);
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        // If the user has repository_instances data, then only the User context should be present so get the first context.
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

        // Delete the repository_instances records created for the userid.
        $DB->delete_records('repository_instances', ['userid' => $userid]);
    }

}
