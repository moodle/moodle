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
 * Add-on provider interface.
 *
 * @package    block_xp
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\privacy;

use context;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\shared_data_provider;

/**
 * The interface to implement by the addon.
 *
 * @package    block_xp
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface addon_provider extends shared_data_provider {

    /**
     * Add the list of contexts for user.
     *
     * @param contextlist $contextlist The context list.
     * @param int $userid The user to search.
     */
    public static function add_addon_contexts_for_userid(contextlist $contextlist, $userid);

    /**
     * Export user preferences.
     *
     * @param int $userid The userid of the user whose data is to be exported.
     */
    public static function export_addon_user_preferences($userid);

    /**
     * Export the addon user data.
     *
     * @param array $rootpath The root path to export at.
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_addon_user_data(array $rootpath, approved_contextlist $contextlist);

    /**
     * Delete addon data for all users in the specified context.
     *
     * @param context $context The specific context to delete data for.
     */
    public static function delete_addon_data_for_all_users_in_context(context $context);

    /**
     * Delete addon data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_addon_data_for_user(approved_contextlist $contextlist);

}
