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
 * Callbacks for auth_lti.
 *
 * @package   auth_lti
 * @copyright 2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Callback to remove linked logins for deleted users.
 *
 * @param stdClass $user the user being deleted.
 */
function auth_lti_pre_user_delete($user) {
    global $DB;
    $DB->delete_records('auth_lti_linked_login', ['userid' => $user->id]);
}
