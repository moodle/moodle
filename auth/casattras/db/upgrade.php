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
 * CAS attributes authentication plugin upgrade code
 *
 * @package    auth_casattras
 * @copyright  2019 Lafayette College ITS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Function to upgrade auth_casattras.
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_auth_casattras_upgrade($oldversion) {
    global $CFG, $DB;

    if ($oldversion < 2019101100) {
        // Convert info in config plugins from auth/casattras to auth_casattras.
        upgrade_fix_config_auth_plugin_names('casattras');
        upgrade_fix_config_auth_plugin_defaults('casattras');
        upgrade_plugin_savepoint(true, 2019101100, 'auth', 'casattras');
    }
    return true;
}
