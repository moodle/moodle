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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quickmail\persistents\concerns;

defined('MOODLE_INTERNAL') || die();

use core_user;
use lang_string;

trait belongs_to_a_user {
    // Relationships.
    /**
     * Returns the user object for this message recipient.
     *
     * @return stdClass
     */
    public function get_user() {
        try {
            return core_user::get_user($this->get('user_id'), '*', MUST_EXIST);
        } catch (\Exception $e) {
            return null;
        }
    }

    // Setters.
    /**
     * Convenience method to set the user ID.
     *
     * @param object|int $idorobject The user ID, or a user object.
     */
    protected function set_user_id($idorobject) {
        $userid = $idorobject;

        if (is_object($idorobject)) {
            $userid = $idorobject->id;
        }

        $this->raw_set('user_id', $userid);
    }

    // Validators.
    /**
     * Validate the user ID.
     *
     * @param int $value The value.
     * @return true|lang_string
     */
    protected function validate_user_id($value) {
        if (!core_user::is_real_user($value, true)) {
            return new lang_string('invaliduserid', 'error');
        }

        return true;
    }

    // Custom Methods.
    /**
     * Convenience method to determine if this persistent is owned by the given user (or user id)
     *
     * @param object|int $idorobject The user ID, or a user object.
     * @return bool
     */
    public function is_owned_by_user($idorobject) {
        $userid = $idorobject;

        if (is_object($idorobject)) {
            $userid = $idorobject->id;
        }

        return $this->get('user_id') == $userid;
    }

}
