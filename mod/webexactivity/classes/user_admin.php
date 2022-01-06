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
 * An activity to interface with WebEx.
 *
 * @package    mod_webexactvity
 * @author     Eric Merrill <merrill@oakland.edu>
 * @copyright  2014 Oakland University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_webexactivity;

use \mod_webexactivity\local\exception;
use \mod_webexactivity\local\type\base\xml_gen;

defined('MOODLE_INTERNAL') || die();

/**
 * A class that represents the WebEx Admin (API) user.
 *
 * @package    mod_webexactvity
 * @author     Eric Merrill <merrill@oakland.edu>
 * @copyright  2014 Oakland University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_admin extends user {
    /** @var bool Is this an admin user. */
    private $_isadmin = true;

    /**
     * Builds the user object.
     *
     * @param stdClass|int|string  $user Object of user record, id of record to load.
     * @throws coding_exception when bad parameter received.
     */
    protected function __construct($user = null) {
        if (is_object($user)) {
            $this->user = $user;
        }

        if ($this->user) {
            return;
        }

        throw new \coding_exception('Unexpected parameter type passed to user constructor.');
    }

    // ---------------------------------------------------
    // User Methods.
    // ---------------------------------------------------
    /**
     * Set the password for the user.
     *
     * @param string   $password The plaintext password to set.
     * @return bool    True on success, false on failure.
     */
    public function update_password($password) {
        debugging('You cannot update the admin user password.');
        return false;
    }

    /**
     * Get a login URL for the user.
     *
     * @param string   $backurl The URL to go to on failure.
     * @param string   $fronturl The URL to go to on success.
     * @return string|bool    The url, false on failure.
     */
    public function get_login_url($backurl = false, $forwardurl = false) {
        debugging('You cannot get the login for the admin user.');
        return false;
    }

    /**
     * Update the schedulingPermission to let the admin user schedule meetings for the user.
     *
     * @return bool    True if auth succeeded, false if failed.
     */
    public function set_scheduling_permission() {
        debugging('You cannot update perms for the admin user.');
        return false;
    }

    // ---------------------------------------------------
    // Support Methods.
    // ---------------------------------------------------
    /**
     * Save this user to the database.
     *
     * @return bool    True if auth succeeded, false if failed.
     */
    public function save_to_db() {
        debugging('You cannot update the admin user.');
        return false;
    }

    /**
     * Save this user to WebEx.
     *
     * @return bool    True if auth succeeded, false if failed.
     * @throws invalid_response_exception for unexpected WebEx response.
     * @throws coding_exception.
     */
    public function save_to_webex() {
        debugging('You cannot update the admin user.');
        return false;
    }
}
