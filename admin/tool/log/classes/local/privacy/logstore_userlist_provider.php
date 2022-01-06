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
 * Logstore userlist provider interface.
 *
 * @package    tool_log
 * @copyright  2018 Adrian Greeve
 * @author     Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_log\local\privacy;
defined('MOODLE_INTERNAL') || die();

/**
 * Logstore userlist provider interface.
 *
 * Logstore subplugins providers must implement this interface.
 *
 * @package    tool_log
 * @copyright  2018 Adrian Greeve
 * @author     Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface logstore_userlist_provider extends
        \core_privacy\local\request\plugin\subplugin_provider,
        \core_privacy\local\request\shared_userlist_provider
    {

    /**
     * Add user IDs that contain user information for the specified context.
     *
     * @param \core_privacy\local\request\userlist $userlist The userlist to add the users to.
     * @return void
     */
    public static function add_userids_for_context(\core_privacy\local\request\userlist $userlist);


    /**
     * Delete all data for a list of users in the specified context.
     *
     * @param \core_privacy\local\request\approved_userlist $userlist The specific context and users to delete data for.
     * @return void
     */
    public static function delete_data_for_userlist(\core_privacy\local\request\approved_userlist $userlist);
}
