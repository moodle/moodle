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
 * Add-on userlist provider interface.
 *
 * @package    block_xp
 * @copyright  2020 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\privacy;

use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\shared_data_provider;
use core_privacy\local\request\userlist;

/**
 * The interface to implement by the addon to provide userlist.
 *
 * @package    block_xp
 * @copyright  2020 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface addon_userlist_provider extends shared_data_provider {

    /**
     * Add the list of users who have data within a context.
     *
     * @param userlist $userlist The user list.
     */
    public static function add_addon_users_in_context(userlist $userlist);

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The user list.
     */
    public static function delete_addon_data_for_users(approved_userlist $userlist);

}
