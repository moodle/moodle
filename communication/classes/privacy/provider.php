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

namespace core_communication\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;

/**
 * Privacy Subsystem for core_communication implementing null_provider.
 *
 * @package    core_communication
 * @copyright  2023 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\subsystem\provider {
    public static function get_metadata(collection $collection): collection {

        $collection->add_database_table('communication_user', [
            'commid' => 'privacy:metadata:communication_user:commid',
            'userid' => 'privacy:metadata:communication_user:userid',
            'synced' => 'privacy:metadata:communication_user:synced',
        ], 'privacy:metadata:communication_user');

        return $collection;
    }

    public static function get_contexts_for_userid(int $userid): contextlist {
        return new contextlist();
    }

    public static function export_user_data(approved_contextlist $contextlist) {
        // None of the core communication tables should be exported.
    }

    public static function delete_data_for_all_users_in_context(\context $context) {
        // None of the data from these tables should be deleted.
    }

    public static function delete_data_for_user(approved_contextlist $contextlist) {
        // None of the data from these tables should be deleted.
    }

    public static function get_users_in_context(userlist $userlist) {
        // Don't add any users.
    }

    public static function delete_data_for_users(approved_userlist $userlist) {
        // None of the data from these tables should be deleted.
    }
}
