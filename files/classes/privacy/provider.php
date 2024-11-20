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
 * @package    core_files
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_files\privacy;
defined('MOODLE_INTERNAL') || die();

use core_privacy\local\metadata\collection;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\approved_userlist;

/**
 * Data provider class.
 *
 * This only describes the files table, all components must handle the file exporting
 * and deletion themselves.
 *
 * @package    core_files
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        \core_privacy\local\metadata\provider,
        \core_privacy\local\request\subsystem\plugin_provider,
        \core_privacy\local\request\core_userlist_provider,
        // We store a userkey for token-based file access.
        \core_privacy\local\request\subsystem\provider,
        \core_privacy\local\request\shared_userlist_provider {

    /**
     * Returns metadata.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {

        $collection->add_database_table('files', [
            'contenthash' => 'privacy:metadata:files:contenthash',
            'filepath' => 'privacy:metadata:files:filepath',
            'filename' => 'privacy:metadata:files:filename',
            'userid' => 'privacy:metadata:files:userid',
            'filesize' => 'privacy:metadata:files:filesize',
            'mimetype' => 'privacy:metadata:files:mimetype',
            'source' => 'privacy:metadata:files:source',
            'author' => 'privacy:metadata:files:author',
            'license' => 'privacy:metadata:files:license',
            'timecreated' => 'privacy:metadata:files:timecreated',
            'timemodified' => 'privacy:metadata:files:timemodified',
        ], 'privacy:metadata:files');

        // Regarding this block, we are unable to export or purge this data, as
        // it would damage the file conversion data across the whole site.
        $collection->add_database_table('file_conversion', [
            'usermodified' => 'privacy:metadata:file_conversion:usermodified',
        ], 'privacy:metadata:file_conversions');

        $collection->add_subsystem_link('core_userkey', [], 'privacy:metadata:core_userkey');

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * This is currently just the user context.
     *
     * @param int $userid The user to search.
     * @return contextlist $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $sql = "SELECT ctx.id
                  FROM {user_private_key} k
                  JOIN {user} u ON k.userid = u.id
                  JOIN {context} ctx ON ctx.instanceid = u.id AND ctx.contextlevel = :contextlevel
                 WHERE k.userid = :userid AND k.script = :script";
        $params = [
            'userid' => $userid,
            'contextlevel' => CONTEXT_USER,
            'script' => 'core_files',
        ];
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

        \core_userkey\privacy\provider::get_user_contexts_with_script($userlist, $context, 'core_files');
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        // If the user has data, then only the CONTEXT_USER should be present so get the first context.
        $contexts = $contextlist->get_contexts();
        if (count($contexts) == 0) {
            return;
        }

        // Sanity check that context is at the user context level, then get the userid.
        $context = reset($contexts);
        if ($context->contextlevel !== CONTEXT_USER) {
            return;
        }

        // Export associated userkeys.
        $subcontext = [
            get_string('files'),
        ];
        \core_userkey\privacy\provider::export_userkeys($context, $subcontext, 'core_files');
    }

    /**
     * Delete all use data which matches the specified deletion_criteria.
     *
     * @param context $context A user context.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        // Sanity check that context is at the user context level, then get the userid.
        if ($context->contextlevel !== CONTEXT_USER) {
            return;
        }

        // Delete all the userkeys.
        \core_userkey\privacy\provider::delete_userkeys('core_files', $context->instanceid);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        $context = $userlist->get_context();

        if ($context instanceof \context_user) {
            \core_userkey\privacy\provider::delete_userkeys('core_files', $context->instanceid);
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        // If the user has data, then only the user context should be present so get the first context.
        $contexts = $contextlist->get_contexts();
        if (count($contexts) == 0) {
            return;
        }

        // Sanity check that context is at the user context level, then get the userid.
        $context = reset($contexts);
        if ($context->contextlevel !== CONTEXT_USER) {
            return;
        }

        // Delete all the userkeys for core_files..
        \core_userkey\privacy\provider::delete_userkeys('core_files', $context->instanceid);
    }
}
