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
 * Internal library of functions for module auth_ldap
 *
 * @package    auth_ldap
 * @author     David Balch <david.balch@conted.ox.ac.uk>
 * @copyright  2017 The Chancellor Masters and Scholars of the University of Oxford {@link http://www.tall.ox.ac.uk/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Get a list of system roles assignable by the current or a specified user, including their localised names.
 *
 * @param integer|object $user A user id or object. By default (null) checks the permissions of the current user.
 * @return array $roles, each role as an array with id, shortname, localname, and settingname for the config value.
 */
function get_ldap_assignable_role_names($user = null) {
    $roles = array();

    if ($assignableroles = get_assignable_roles(context_system::instance(), ROLENAME_SHORT, false, $user)) {
        $systemroles = role_fix_names(get_all_roles(), context_system::instance(), ROLENAME_ORIGINAL);
        foreach ($assignableroles as $shortname) {
            foreach ($systemroles as $systemrole) {
                if ($systemrole->shortname == $shortname) {
                    $roles[] = array('id' => $systemrole->id,
                                     'shortname' => $shortname,
                                     'localname' => $systemrole->localname,
                                     'settingname' => $shortname . 'context');
                    break;
                }
            }
        }
    }

    return $roles;
}
