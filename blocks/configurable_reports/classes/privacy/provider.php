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
 * Privacy subsystem implementation for block_configurable_reports.
 *
 * @package    block_configurable_reports
 * @category   privacy
 * @copyright  2019 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_configurable_reports\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

/**
 * Implementation of the privacy plugin provider for the configurable report block.
 *
 * @copyright  2019 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    // This plugin has data.
    \core_privacy\local\metadata\provider,

    // This plugin is capable of determining which users have data within it.
    \core_privacy\local\request\core_userlist_provider,

    // This plugin currently implements the original plugin\provider interface.
    \core_privacy\local\request\plugin\provider {

    // This trait must be included to provide the relevant polyfill for the metadata provider.
    use \core_privacy\local\legacy_polyfill;

    /**
     * Returns metadata.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function _get_metadata(collection $collection) {

        $collection->add_database_table('block_configurable_reports', [
            'courseid' => 'privacy:metadata:block_configurable_reports:courseid',
            'ownerid' => 'privacy:metadata:block_configurable_reports:ownerid',
            'visible' => 'privacy:metadata:block_configurable_reports:visible',
            'global' => 'privacy:metadata:block_configurable_reports:global',
            'name' => 'privacy:metadata:block_configurable_reports:name',
            'summary' => 'privacy:metadata:block_configurable_reports:summary',
            'type' => 'privacy:metadata:block_configurable_reports:type',
            'components' => 'privacy:metadata:block_configurable_reports:components',
            'lastexecutiontime' => 'privacy:metadata:block_configurable_reports:lastexecutiontime',
        ], 'privacy:metadata:block_configurable_reports');

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return  contextlist $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function _get_contexts_for_userid($userid) {
        $contextlist = new contextlist();

        // Find the reports created by the userid.
        $sql = "SELECT ctx.id
                FROM {block_configurable_reports} bcr
                JOIN {context} ctx
                  ON ctx.instanceid = bcr.ownerid AND ctx.contextlevel = :contextlevel
                WHERE bcr.ownerid = :ownerid";

        $params = ['ownerid' => $userid, 'contextlevel' => CONTEXT_USER];

        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!$context instanceof \context_user) {
            return;
        }

        $params = [
            'contextid' => $context->id,
            'contextuser' => CONTEXT_USER,
        ];

        $sql = "SELECT bcr.ownerid as ownerid
                  FROM {block_configurable_reports} bcr
                  JOIN {context} ctx
                       ON ctx.instanceid = bcr.ownerid
                       AND ctx.contextlevel = :contextuser
                 WHERE ctx.id = :contextid";

        $userlist->add_from_sql('ownerid', $sql, $params);
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function _export_user_data(approved_contextlist $contextlist) {
        global $DB;

        $reportsdata = [];
        $sql = "SELECT bcr.* , c.fullname as coursename
                  FROM {block_configurable_reports} bcr
                  JOIN {course} c ON c.id = bcr.courseid
                 WHERE bcr.ownerid = :ownerid";
        $params = ['ownerid' => $contextlist->get_user()->id];
        $results = $DB->get_records_sql($sql, $params);
        foreach ($results as $result) {
            $reportsdata[] = (object) [
                'coursename' => format_string($result->coursename, true),
                'visible' => transform::yesno($result->visible),
                'global' => transform::yesno($result->global),
                'name' => $result->name,
                'summary' => $result->summary,
                'type' => $result->type,
                'components' => $result->components,
                'lastexecutiontime' => transform::datetime($result->lastexecutiontime),
            ];
        }
        if (!empty($reportsdata)) {
            $data = (object) [
                'reports' => $reportsdata,
            ];
            writer::with_context($contextlist->current())->export_data([
                get_string('pluginname', 'block_configurable_reports'),
            ], $data);
        }
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context The specific context to delete data for.
     */
    public static function _delete_data_for_all_users_in_context(\context $context) {
        if ($context instanceof \context_user) {
            static::delete_data($context->instanceid);
        }
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
    public static function _delete_data_for_user(approved_contextlist $contextlist) {
        static::delete_data($contextlist->get_user()->id);
    }

    /**
     * Delete data related to a userid.
     *
     * @param int $userid The user ID
     */
    protected static function delete_data($userid) {
        global $DB;

        // Reports are considered to be 'owned' by the institution, even if they were originally written by a specific
        // user. They are still exported in the list of a users data, but they are not removed.
        // The ownerid is instead anonymised.
        $params['ownerid'] = $userid;
        $DB->set_field_select('block_configurable_reports', 'ownerid', 0, "ownerid = :ownerid", $params);
    }

}
