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
 * User indicator interface.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\indicator;

/**
 * User indicator interface.
 *
 * This interface allows to attach a flag value to a user.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface user_indicator {

    /**
     * Get a user's flag.
     *
     * @param int $userid The user ID.
     * @param string $flag The flag name.
     * @return mixed|null The flag value.
     */
    public function get_user_flag($userid, $flag);

    /**
     * Set a user's flag.
     *
     * @param int $userid The user ID.
     * @param string $flag The flag name.
     * @param mixed $value The flag value.
     */
    public function set_user_flag($userid, $flag, $value);

    /**
     * Unset a user's flag.
     *
     * @param int $userid The user ID.
     * @param string $flag The flag name.
     */
    public function unset_user_flag($userid, $flag);

    /**
     * Unset all user's flag.
     *
     * @param string $flag The flag name.
     */
    public function unset_users_flag($flag);

    /**
     * Whether the user has the flag.
     *
     * @param int $userid The user ID.
     * @param string $flag The flag name.
     * @return bool
     */
    public function user_has_flag($userid, $flag);

}
