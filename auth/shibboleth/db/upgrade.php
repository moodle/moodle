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
 * Shibboleth authentication plugin upgrade code
 *
 * @package    auth_shibboleth
 * @copyright  2017 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Function to upgrade auth_shibboleth.
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_auth_shibboleth_upgrade($oldversion) {
    global $CFG, $DB, $OUTPUT;

    if ($oldversion < 2017020700) {
        // Convert info in config plugins from auth/shibboleth to auth_shibboleth.
        upgrade_fix_config_auth_plugin_names('shibboleth');
        upgrade_fix_config_auth_plugin_defaults('shibboleth');
        upgrade_plugin_savepoint(true, 2017020700, 'auth', 'shibboleth');
    }

    // Automatically generated Moodle v3.3.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.4.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.5.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.6.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.7.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.8.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2019111801) {
        // The 'Data modification API' setting in the Shibboleth authentication plugin can no longer be configured
        // to use files located within the site data directory, as it exposes the site to security risks. Therefore,
        // we need to find every existing case and reset the 'Data modification API' setting to its default value.

        $convertdataconfig = get_config('auth_shibboleth', 'convert_data');

        if (preg_match('/' . preg_quote($CFG->dataroot, '/') . '/', realpath($convertdataconfig))) {
            set_config('convert_data', '', 'auth_shibboleth');

            $warn = 'Your \'Data modification API\' setting in the Shibboleth authentication plugin is currently
            configured to use a file located within the current site data directory ($CFG->dataroot). You are no
            longer able to use files from within this directory for this purpose as it exposes your site to security
            risks. This setting has been reset to its default value. Please reconfigure it by providing a path
            to a file which is not located within the site data directory.';

            echo $OUTPUT->notification($warn, 'notifyproblem');
        }

        upgrade_plugin_savepoint(true, 2019111801, 'auth', 'shibboleth');
    }

    return true;
}
