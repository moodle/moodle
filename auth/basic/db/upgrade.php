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
 * Upgrade code for the auth_basic.
 *
 * @package   auth_basic
 * @copyright 2018 Nathan Nguyen <nathannguyen@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_auth_basic_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2018121400) {
        $table = new xmldb_table('auth_basic_master_password');

        // Adding fields to the table.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('password', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('usage', XMLDB_TYPE_INTEGER, '10', null, null, null, 0);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timeexpired', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to the table.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('userid_key', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

        // Create table.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2018121400, 'auth', 'auth_basic');
    }

    if ($oldversion < 2020083100) {

        // Changing the default of field usage on table auth_basic_master_password to 0.
        $table = new xmldb_table('auth_basic_master_password');
        $field = new xmldb_field('usage', XMLDB_TYPE_INTEGER, '10', null, null, null, '0', 'password');

        // Launch change of default for field usage.
        $dbman->change_field_default($table, $field);
        // Launch change of nullability for field usage.
        $dbman->change_field_notnull($table, $field);

        // Basic savepoint reached.
        upgrade_plugin_savepoint(true, 2020083100, 'auth', 'basic');
    }

    if ($oldversion < 2020120300) {

        // Rename field usage on table auth_basic_master_password to uses.
        $table = new xmldb_table('auth_basic_master_password');
        $field = new xmldb_field('usage', XMLDB_TYPE_INTEGER, '10', null, null, null, '0', 'password');

        // Launch rename field
        $dbman->rename_field($table, $field, 'uses');

        // Basic savepoint reached.
        upgrade_plugin_savepoint(true, 2020120300, 'auth', 'basic');
    }

    return true;
}
