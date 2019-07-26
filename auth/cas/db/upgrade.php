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
 * CAS authentication plugin upgrade code
 *
 * @package    auth_cas
 * @copyright  2013 Iñaki Arenaza
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Function to upgrade auth_cas.
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_auth_cas_upgrade($oldversion) {
    global $CFG;
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2017020700) {
        // Convert info in config plugins from auth/cas to auth_cas.
        upgrade_fix_config_auth_plugin_names('cas');
        upgrade_fix_config_auth_plugin_defaults('cas');
        upgrade_plugin_savepoint(true, 2017020700, 'auth', 'cas');
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

    if ($oldversion < 2019072900) {

        // Define table auth_cas_tickets to be created.
        $table = new xmldb_table('auth_cas_tickets');

        // Adding fields to table auth_cas_tickets.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('usercas', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('ticket', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table auth_cas_tickets.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table auth_cas_tickets.
        $table->add_index('ticket', XMLDB_INDEX_UNIQUE, array('ticket'));

        // Conditionally launch create table for auth_cas_tickets.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Cas savepoint reached.
        upgrade_plugin_savepoint(true, 2019072900, 'auth', 'cas');
    }

    return true;
}
