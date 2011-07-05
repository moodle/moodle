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
 * Keeps track of upgrades to the auth_mnet plugin
 *
 * @package    auth
 * @subpackage mnet
 * @copyright  2010 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_auth_mnet_upgrade($oldversion) {
    global $CFG, $DB, $OUTPUT;

    // fix the plugin type in config_plugins table
    if ($oldversion < 2010071300) {
        if ($configs = $DB->get_records('config_plugins', array('plugin' => 'auth/mnet'))) {
            foreach ($configs as $config) {
                unset_config($config->name, $config->plugin);
                set_config($config->name, $config->value, 'auth_mnet');
            }
        }
        unset($configs);
        upgrade_plugin_savepoint(true, 2010071300, 'auth', 'mnet');
    }

    // Moodle v2.1.0 release upgrade line
    // Put any upgrade step following this

    return true;
}
