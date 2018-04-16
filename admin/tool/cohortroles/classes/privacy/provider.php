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
 * Privacy Subsystem implementation for tool_cohortroles.
 *
 * @package    tool_cohortroles
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_cohortroles\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\context;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy Subsystem for tool_cohortroles implementing metadata and plugin providers.
 *
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements \core_privacy\local\metadata\provider, \core_privacy\local\request\plugin\provider {

    /**
     * Returns meta data about this system.
     *
     * @param   collection $collection The initialised collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) {
        // The tool_cohortroles plugin utilises the mdl_tool_cohortroles table.
        $collection->add_database_table(
            'tool_cohortroles',
            [
                'id' => 'privacy:metadata:tool_cohortroles:id',
                'cohortid' => 'privacy:metadata:tool_cohortroles:cohortid',
                'roleid' => 'privacy:metadata:tool_cohortroles:roleid',
                'userid' => 'privacy:metadata:tool_cohortroles:userid',
                'timecreated' => 'privacy:metadata:tool_cohortroles:timecreated',
                'timemodified' => 'privacy:metadata:tool_cohortroles:timemodified',
                'usermodified' => 'privacy:metadata:tool_cohortroles:usermodified'
            ],
            'privacy:metadata:tool_cohortroles'
        );

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int $userid The user to search.
     * @return  contextlist   $contextlist  The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid($userid) {
        $contextlist = new contextlist();

        // Retrieve the User context associated with tool_cohortroles records.
        $sql = "SELECT DISTINCT c.id
                  FROM {context} c
                  JOIN {tool_cohortroles} cr ON cr.userid = c.instanceid AND c.contextlevel = :contextuser
                 WHERE cr.userid = :userid";

        $params = [
            'contextuser' => CONTEXT_USER,
            'userid'       => $userid
        ];

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

        // If the user has tool_cohortroles data, then only the User context should be present so get the first context.
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

        // Retrieve the tool_cohortroles records created for the user.
        $sql = 'SELECT cr.id as cohortroleid,
                       c.name as cohortname,
                       c.idnumber as cohortidnumber,
                       c.description as cohortdescription,
                       r.shortname as roleshortname,
                       cr.userid as userid,
                       cr.timecreated as timecreated,
                       cr.timemodified as timemodified
                  FROM {tool_cohortroles} cr
                  JOIN {cohort} c ON c.id = cr.cohortid
                  JOIN {role} r ON r.id = cr.roleid
                 WHERE cr.userid = :userid';

        $params = [
            'userid' => $userid
        ];

        $cohortroles = $DB->get_records_sql($sql, $params);
        foreach ($cohortroles as $cohortrole) {
            // The tool_cohortroles data export is organised in:
            // {User Context}/Cohort roles management/{cohort name}/{role shortname}/data.json.
            $subcontext = [
                get_string('pluginname', 'tool_cohortroles'),
                $cohortrole->cohortname,
                $cohortrole->roleshortname
            ];

            $data = (object) [
                'cohortname' => $cohortrole->cohortname,
                'cohortidnumber' => $cohortrole->cohortidnumber,
                'cohortdescription' => $cohortrole->cohortdescription,
                'roleshortname' => $cohortrole->roleshortname,
                'userid' => transform::user($cohortrole->userid),
                'timecreated' => transform::datetime($cohortrole->timecreated),
                'timemodified' => transform::datetime($cohortrole->timemodified)
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

        // Delete the tool_cohortroles records created for the userid.
        $DB->delete_records('tool_cohortroles', ['userid' => $userid]);
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        // If the user has tool_cohortroles data, then only the User context should be present so get the first context.
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

        // Delete the tool_cohortroles records created for the userid.
        $DB->delete_records('tool_cohortroles', ['userid' => $userid]);
    }

}
