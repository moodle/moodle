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
 * Data provider.
 *
 * @package    logstore_database
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_database\privacy;
defined('MOODLE_INTERNAL') || die();

use context;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\contextlist;

/**
 * Data provider class.
 *
 * @package    logstore_database
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \tool_log\local\privacy\logstore_provider,
    \tool_log\local\privacy\logstore_userlist_provider {

    use \tool_log\local\privacy\moodle_database_export_and_delete;

    /**
     * Returns metadata.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {
        $collection->add_external_location_link('log', [
            'eventname' => 'privacy:metadata:log:eventname',
            'userid' => 'privacy:metadata:log:userid',
            'relateduserid' => 'privacy:metadata:log:relateduserid',
            'anonymous' => 'privacy:metadata:log:anonymous',
            'other' => 'privacy:metadata:log:other',
            'timecreated' => 'privacy:metadata:log:timecreated',
            'origin' => 'privacy:metadata:log:origin',
            'ip' => 'privacy:metadata:log:ip',
            'realuserid' => 'privacy:metadata:log:realuserid',
        ], 'privacy:metadata:log');
        return $collection;
    }

    /**
     * Add contexts that contain user information for the specified user.
     *
     * @param contextlist $contextlist The contextlist to add the contexts to.
     * @param int $userid The user to find the contexts for.
     * @return void
     */
    public static function add_contexts_for_userid(contextlist $contextlist, $userid) {
        list($db, $table) = static::get_database_and_table();
        if (!$db || !$table) {
            return;
        }

        $sql = 'userid = :userid1 OR relateduserid = :userid2 OR realuserid = :userid3';
        $params = ['userid1' => $userid, 'userid2' => $userid, 'userid3' => $userid];
        $contextids = $db->get_fieldset_select($table, 'DISTINCT contextid', $sql, $params);
        if (empty($contextids)) {
            return;
        }

        $sql = implode(' UNION ', array_map(function($id) use ($db) {
            return 'SELECT ' . $id . $db->sql_null_from_clause();
        }, $contextids));
        $contextlist->add_from_sql($sql, []);
    }

    /**
     * Add user IDs that contain user information for the specified context.
     *
     * @param \core_privacy\local\request\userlist $userlist The userlist to add the users to.
     * @return void
     */
    public static function add_userids_for_context(\core_privacy\local\request\userlist $userlist) {
        list($db, $table) = static::get_database_and_table();
        if (!$db || !$table) {
            return;
        }

        $userids = [];
        $records = $db->get_records($table, ['contextid' => $userlist->get_context()->id], '',
                'id, userid, relateduserid, realuserid');
        if (empty($records)) {
            return;
        }

        foreach ($records as $record) {
            $userids[] = $record->userid;
            if (!empty($record->relateduserid)) {
                $userids[] = $record->relateduserid;
            }
            if (!empty($record->realuserid)) {
                $userids[] = $record->realuserid;
            }
        }
        $userids = array_unique($userids);
        $userlist->add_users($userids);
    }

    /**
     * Get the database object.
     *
     * @return array Containing moodle_database, string, or null values.
     */
    protected static function get_database_and_table() {
        $manager = get_log_manager();
        $store = new \logstore_database\log\store($manager);
        $db = $store->get_extdb();
        return $db ? [$db, $store->get_config_value('dbtable')] : [null, null];
    }

    /**
     * Get the path to export the logs to.
     *
     * @return array
     */
    protected static function get_export_subcontext() {
        return [get_string('privacy:path:logs', 'tool_log'), get_string('pluginname', 'logstore_database')];
    }
}
