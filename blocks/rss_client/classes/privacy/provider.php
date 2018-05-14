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
 * @package    block_rss_client
 * @copyright  2018 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_rss_client\privacy;

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\contextlist;
use \core_privacy\local\request\approved_contextlist;

/**
 * Privacy class for requesting user data.
 *
 * @package    block_rss_client
 * @copyright  2018 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements \core_privacy\local\metadata\provider, \core_privacy\local\request\plugin\provider {

    /**
     * Returns meta data about this system.
     *
     * @param   collection $collection The initialised collection to add items to.
     * @return  collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) {
        $collection->add_database_table('block_rss_client', [
            'userid' => 'privacy:metadata:block_rss_client:userid',
            'title' => 'privacy:metadata:block_rss_client:title',
            'preferredtitle' => 'privacy:metadata:block_rss_client:preferredtitle',
            'description' => 'privacy:metadata:block_rss_client:description',
            'shared' => 'privacy:metadata:block_rss_client:shared',
            'url' => 'privacy:metadata:block_rss_client:url',
            'skiptime' => 'privacy:metadata:block_rss_client:skiptime',
            'skipuntil' => 'privacy:metadata:block_rss_client:skipuntil',
        ], 'privacy:metadata:block_rss_client:tableexplanation');
        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int         $userid     The user to search.
     * @return  contextlist $contextlist  The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid($userid) {
        $sql = "SELECT ctx.id
                FROM {block_rss_client} brc
                JOIN {user} u
                    ON brc.userid = u.id
                JOIN {context} ctx
                    ON ctx.instanceid = u.id
                        AND ctx.contextlevel = :contextlevel
                WHERE brc.userid = :userid";

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
        foreach ($results as $result) {
            $data = (object) [
                'title' => $result->title,
                'preferredtitle' => $result->preferredtitle,
                'description' => $result->description,
                'shared' => \core_privacy\local\request\transform::yesno($result->shared),
                'url' => $result->url
            ];

            \core_privacy\local\request\writer::with_context($contextlist->current())->export_data([
                    get_string('pluginname', 'block_rss_client')], $data);
        }
    }

    /**
     * Delete all use data which matches the specified deletion_criteria.
     *
     * @param   context $context A user context.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        if ($context instanceof \context_user) {
            static::delete_data($context->instanceid);
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        static::delete_data($contextlist->get_user()->id);
    }

    /**
     * Delete data related to a userid.
     *
     * @param  int $userid The user ID
     */
    protected static function delete_data($userid) {
        global $DB;

        $DB->delete_records('block_rss_client', ['userid' => $userid]);
    }

    /**
     * Get records related to this plugin and user.
     *
     * @param  int $userid The user ID
     * @return array An array of records.
     */
    protected static function get_records($userid) {
        global $DB;

        return $DB->get_records('block_rss_client', ['userid' => $userid]);
    }
}
