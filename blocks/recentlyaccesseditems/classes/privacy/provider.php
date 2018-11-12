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
 * Privacy Subsystem implementation for Recently accessed items block.
 *
 * @package    block_recentlyaccesseditems
 * @copyright  2018 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_recentlyaccesseditems\privacy;

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\transform;
use \core_privacy\local\request\contextlist;
use \core_privacy\local\request\approved_contextlist;
use \core_privacy\local\request\writer;

/**
 * Privacy Subsystem for block_recentlyaccesseditems.
 *
 * @package    block_recentlyaccesseditems
 * @copyright  2018 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements \core_privacy\local\metadata\provider, \core_privacy\local\request\subsystem\provider {

    /**
     * Returns information about the user data stored in this component.
     *
     * @param  collection $collection A list of information about this component
     * @return collection The collection object filled out with information about this component.
     */
    public static function get_metadata(collection $collection) : collection {
        $recentitems = [
                'userid' => 'privacy:metadata:userid',
                'courseid' => 'privacy:metadata:courseid',
                'cmid' => 'privacy:metadata:cmid',
                'timeaccess' => 'privacy:metadata:timeaccess'
        ];

        $collection->add_database_table('block_recentlyaccesseditems', $recentitems,
                'privacy:metadata:block_recentlyaccesseditemstablesummary');

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int $userid The user to search.
     * @return  contextlist $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        $params = ['userid' => $userid, 'contextuser' => CONTEXT_USER];
        $sql = "SELECT c.id
                  FROM {context} c
                  JOIN {block_recentlyaccesseditems} b
                    ON b.userid = c.instanceid
                 WHERE c.instanceid = :userid
                   AND c.contextlevel = :contextuser";
        $contextlist = new contextlist();
        $contextlist->add_from_sql($sql, $params);
        return $contextlist;
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        $context = $contextlist->current();
        $user = \core_user::get_user($contextlist->get_user()->id);
        static::export_recentitems($user->id, $context);
    }

    /**
     * Export information about the most recently accessed items.
     *
     * @param  int $userid The user ID.
     * @param  \context $context The user context.
     */
    protected static function export_recentitems(int $userid, \context $context) {
        global $DB;
        $sql = "SELECT ra.id, c.fullname, ra.timeaccess, m.name, ra.cmid
                  FROM {block_recentlyaccesseditems} ra
                  JOIN {course} c ON c.id = ra.courseid
                  JOIN {course_modules} cm on cm.id = ra.cmid
                  JOIN {modules} m ON m.id = cm.module
                 WHERE ra.userid = :userid";

        $params = ['userid' => $userid];
        $records = $DB->get_records_sql($sql, $params);
        if (!empty($records)) {
            $recentitems = (object) array_map(function($record) use($context) {
                return [
                        'course_name' => format_string($record->fullname, true, ['context' => $context]),
                        'module_name' => format_string($record->name),
                        'timeaccess' => transform::datetime($record->timeaccess)
                ];
            }, $records);
            writer::with_context($context)->export_data([get_string('privacy:recentlyaccesseditemspath',
                    'block_recentlyaccesseditems')], $recentitems);
        }
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        // Only delete data for a user context.
        if ($context->contextlevel == CONTEXT_USER) {
            // Delete recent items access.
            $DB->delete_records('block_recentlyaccesseditems', ['userid' => $context->instanceid]);
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        foreach ($contextlist as $context) {
            // Let's be super certain that we have the right information for this user here.
            if ($context->contextlevel == CONTEXT_USER && $contextlist->get_user()->id == $context->instanceid) {
                $DB->delete_records('block_recentlyaccesseditems', ['userid' => $context->instanceid]);
            }
        }
    }
}