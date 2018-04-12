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
 * @package    core_rss
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_rss\privacy;

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\contextlist;
use \core_privacy\local\request\approved_contextlist;
use \core_privacy\local\request\transform;
use \core_privacy\local\request\writer;

/**
 * Privacy class for requesting user data.
 *
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        \core_privacy\local\metadata\provider,
        \core_privacy\local\request\plugin\provider {

    /**
     * Return the fields which contain personal data.
     *
     * @param   collection $collection The initialised collection to add items to.
     * @return  collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {
        $collection->add_database_table('user_private_key', [
                'value' => 'privacy:metadata:user_private_key:value',
                'userid' => 'privacy:metadata:user_private_key:userid',
                'validuntil' => 'privacy:metadata:user_private_key:validuntil',
                'timecreated' => 'privacy:metadata:user_private_key:timecreated'
            ], 'privacy:metadata:user_private_key');
        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return contextlist $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        $sql = "SELECT ctx.id
                  FROM {user_private_key} k
                  JOIN {user} u ON k.userid = u.id
                  JOIN {context} ctx ON ctx.instanceid = u.id AND ctx.contextlevel = :contextlevel
                 WHERE k.userid = :userid AND k.script = 'rss'";

        $params = ['userid' => $userid, 'contextlevel' => CONTEXT_USER];

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
        $results = static::get_records($contextlist->get_user()->id);
        $context = $contextlist->current();
        if ($context->contextlevel == CONTEXT_USER) {
            foreach ($results as $result) {
                $context = \context_user::instance($result->userid);
                $subcontext = [
                    get_string('rss', 'rss'),
                    transform::user($result->userid)
                ];
                $name = 'user_private_key-' . $result->id;
                $data = (object)[
                    'value' => $result->value,
                    'iprestriction' => $result->iprestriction,
                    'validuntil' => $result->validuntil,
                    'timecreated' => transform::datetime($result->timecreated),
                ];
                writer::with_context($context)->export_related_data($subcontext, $name, $data);
            }
        }
    }

    /**
     * Delete all use data which matches the specified deletion_criteria.
     *
     * @param context $context A user context.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        // The information in user_private_key table is removed automaticaly when a user is deteled.
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        // The information in user_private_key table is removed automaticaly when a user is deteled.
    }

    /**
     * Get records related to this plugin and user.
     *
     * @param  int $userid The user ID
     * @return array An array of records.
     */
    protected static function get_records(int $userid) : array {
        global $DB;

        return $DB->get_records('user_private_key', ['userid' => $userid, 'script' => 'rss']);
    }
}
