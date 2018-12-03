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
 * LDAP authentication plugin upgrade code
 *
 * @package    auth_ldap
 * @copyright  2013 Iñaki Arenaza
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Function to upgrade auth_ldap.
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_auth_ldap_upgrade($oldversion) {
    global $CFG;

    // Automatically generated Moodle v3.2.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2017020700) {
        // Convert info in config plugins from auth/ldap to auth_ldap.
        upgrade_fix_config_auth_plugin_names('ldap');
        upgrade_fix_config_auth_plugin_defaults('ldap');
        upgrade_plugin_savepoint(true, 2017020700, 'auth', 'ldap');
    }

    // Automatically generated Moodle v3.3.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2017080100) {
        // The "auth_ldap/coursecreators" setting was replaced with "auth_ldap/coursecreatorcontext" (created
        // dynamically from system-assignable roles) - so migrate any existing value to the first new slot.
        if ($ldapcontext = get_config('auth_ldap', 'creators')) {
            // Get info about the role that the old coursecreators setting would apply.
            $creatorrole = get_archetype_roles('coursecreator');
            $creatorrole = array_shift($creatorrole); // We can only use one, let's use the first.

            // Create new setting.
            set_config($creatorrole->shortname . 'context', $ldapcontext, 'auth_ldap');

            // Delete old setting.
            set_config('creators', null, 'auth_ldap');

            upgrade_plugin_savepoint(true, 2017080100, 'auth', 'ldap');
        }
    }

    // Automatically generated Moodle v3.4.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.5.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.6.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
