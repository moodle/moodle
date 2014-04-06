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

defined('MOODLE_INTERNAL') || die;

// Role to assign (shortname)
define('IOMAD_SIGNUP_ROLE', 'clientadministrator');

/**
 * Event handler for 'user_created'
 * For 'email' authentication (only) add this user
 * to the client admin role (site)
 */
function local_iomad_signup_user_created($user) {
    global $DB;

    // If not 'email' auth then we are not interested
    if ($user->auth != 'email') {
        return true;
    }

    // Get context
    $context = context_system::instance();

    // Get role
    $role = $DB->get_record('role', array('shortname' => IOMAD_SIGNUP_ROLE), '*', MUST_EXIST);

    // assign the user to the role
    role_assign($role->id, $user->id, $context->id);

    return true;
}
