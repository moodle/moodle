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
 * Privacy provider implementation for core_contentbank.
 *
 * @package    core_contentbank
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_contentbank\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;

/**
 * Privacy provider implementation for core_contentbank.
 *
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider {

    /**
     * Returns metadata.
     * TODO: MDL-67798.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {
        // We are not implementing a proper privacy provider for now.
        // A right privacy provider will be implemented in MDL-67798.

        $collection->add_database_table('contentbank_content', [
            'usercreated' => 'privacy:metadata:content:usercreated',
            'usermodified' => 'privacy:metadata:content:usermodified',
        ], 'privacy:metadata:userid');

        return $collection;
    }

    /**
     * TODO: MDL-67798.
     *
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        // We are not implementing a proper privacy provider for now.
        // A right privacy provider will be implemented in MDL-67798.
    }

    /**
     * TODO: MDL-67798.
     *
     * @param   approved_userlist       $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        // We are not implementing a proper privacy provider for now.
        // A right privacy provider will be implemented in MDL-67798.
    }

    /**
     * TODO: MDL-67798.
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int         $userid     The user to search.
     * @return  contextlist   $contextlist  The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        // We are not implementing a proper privacy provider for now.
        // A right privacy provider will be implemented in MDL-67798.

        return (new contextlist());
    }

    /**
     * TODO: MDL-67798.
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        // We are not implementing a proper privacy provider for now.
        // A right privacy provider will be implemented in MDL-67798.
    }

    /**
     * TODO: MDL-67798.
     * Delete all data for all users in the specified context.
     *
     * @param   context                 $context   The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        // We are not implementing a proper privacy provider for now.
        // A right privacy provider will be implemented in MDL-67798.
    }

    /**
     * TODO: MDL-67798.
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        // We are not implementing a proper privacy provider for now.
        // A right privacy provider will be implemented in MDL-67798.
    }
}
