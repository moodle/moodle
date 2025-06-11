<?php
// This file is part of The Course Module Navigation Block
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

namespace core_adminpresets\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;

/**
 * Admin presets this file handle privacy provider.
 *
 * @package          core_adminpresets
 * @copyright        2021 Pimenko <support@pimenko.com><pimenko.com>
 * @author           Jordan Kesraoui | Sylvain Revenu | Pimenko based on David Monllaó <david.monllao@urv.cat> code
 * @license          http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        \core_privacy\local\metadata\provider,
        \core_privacy\local\request\subsystem\provider,
        \core_privacy\local\request\core_userlist_provider {

    /**
     * Returns information about the user data stored in this component.
     *
     * @param  collection $collection A list of information about this component
     * @return collection The collection object filled out with information about this component.
     */
    public static function get_metadata(collection $collection): collection {
        // These tables are really data about site configuration and not user data.

        // The adminpresets includes information about which user performed a configuration change using the admin_presets
        // tool.
        // This is not considered to be user data.
        $collection->add_database_table('adminpresets', [
                'userid'        => 'privacy:metadata:adminpresets:userid',
                'name'          => 'privacy:metadata:adminpresets:name',
                'comments'      => 'privacy:metadata:adminpresets:comments',
                'site'          => 'privacy:metadata:adminpresets:site',
                'moodlerelease' => 'privacy:metadata:adminpresets:moodlerelease',
                'timecreated'   => 'privacy:metadata:adminpresets:timecreated',
            ], 'privacy:metadata:adminpresets');

        // The adminpresets_app includes information about which user performed configuration change using the admin_presets
        // tool.
        // This is not considered to be user data.
        $collection->add_database_table('adminpresets_app', [
                'adminpresetid' => 'privacy:metadata:adminpresets_app:adminpresetid',
                'userid'        => 'privacy:metadata:adminpresets_app:userid',
                'time'          => 'privacy:metadata:adminpresets_app:time',
            ], 'privacy:metadata:adminpresets_app');

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int $userid The user to search.
     * @return  contextlist $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        return new contextlist();
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        // Don't add any user.
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        // None of the core tables should be exported.
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        // None of the the data from these tables should be deleted.
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        // None of the the data from these tables should be deleted.
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist       $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        // None of the the data from these tables should be deleted.
    }
}
