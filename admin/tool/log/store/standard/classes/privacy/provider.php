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
 * @package    logstore_standard
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_standard\privacy;
defined('MOODLE_INTERNAL') || die();

use context;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\contextlist;

/**
 * Data provider class.
 *
 * @package    logstore_standard
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \tool_log\local\privacy\logstore_provider {

    use \tool_log\local\privacy\moodle_database_export_and_delete;

    /**
     * Returns metadata.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {
        $collection->add_database_table('log', [
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
        $sql = "
            SELECT l.contextid
              FROM {logstore_standard_log} l
             WHERE l.userid = :userid1
                OR l.relateduserid = :userid2
                OR l.realuserid = :userid3";
        $contextlist->add_from_sql($sql, [
            'userid1' => $userid,
            'userid2' => $userid,
            'userid3' => $userid,
        ]);
    }

    /**
     * Get the database object.
     *
     * @return array Containing moodle_database, string, or null values.
     */
    protected static function get_database_and_table() {
        global $DB;
        return [$DB, 'logstore_standard_log'];
    }

    /**
     * Get the path to export the logs to.
     *
     * @return array
     */
    protected static function get_export_subcontext() {
        return [get_string('privacy:path:logs', 'tool_log'), get_string('pluginname', 'logstore_standard')];
    }
}
