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
 * MFA upgrade library.
 *
 * @package    tool_mfa
 * @copyright  2020 Peter Burnett <peterburnett@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Function to upgrade tool_mfa.
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_tool_mfa_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2020050700) {
        // Define field lockcounter to be added to tool_mfa.
        $table = new xmldb_table('tool_mfa');
        $field = new xmldb_field('lockcounter', XMLDB_TYPE_INTEGER, '5', null, XMLDB_NOTNULL, null, '0', 'revoked');

        // Conditionally launch add field lockcounter.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // MFA savepoint reached.
        upgrade_plugin_savepoint(true, 2020050700, 'tool', 'mfa');
    }

    if ($oldversion < 2020051900) {
        // Define index userid (not unique) to be added to tool_mfa.
        $table = new xmldb_table('tool_mfa');
        $index = new xmldb_index('userid', XMLDB_INDEX_NOTUNIQUE, ['userid']);

        // Conditionally launch add index userid.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Define index factor (not unique) to be added to tool_mfa.
        $table = new xmldb_table('tool_mfa');
        $index = new xmldb_index('factor', XMLDB_INDEX_NOTUNIQUE, ['factor']);

        // Conditionally launch add index factor.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Define index lockcounter (not unique) to be added to tool_mfa.
        $table = new xmldb_table('tool_mfa');
        $index = new xmldb_index('lockcounter', XMLDB_INDEX_NOTUNIQUE, ['userid', 'factor', 'lockcounter']);

        // Conditionally launch add index lockcounter.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Mfa savepoint reached.
        upgrade_plugin_savepoint(true, 2020051900, 'tool', 'mfa');
    }

    if ($oldversion < 2020090300) {
        // Define table tool_mfa_secrets to be created.
        $table = new xmldb_table('tool_mfa_secrets');

        // Adding fields to table tool_mfa_secrets.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('factor', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('secret', XMLDB_TYPE_CHAR, '1333', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '15', null, XMLDB_NOTNULL, null, null);
        $table->add_field('expiry', XMLDB_TYPE_INTEGER, '15', null, XMLDB_NOTNULL, null, null);
        $table->add_field('revoked', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('sessionid', XMLDB_TYPE_CHAR, '100', null, null, null, null);

        // Adding keys to table tool_mfa_secrets.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);

        // Adding indexes to table tool_mfa_secrets.
        $table->add_index('factor', XMLDB_INDEX_NOTUNIQUE, ['factor']);
        $table->add_index('expiry', XMLDB_INDEX_NOTUNIQUE, ['expiry']);

        // Conditionally launch create table for tool_mfa_secrets.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Mfa savepoint reached.
        upgrade_plugin_savepoint(true, 2020090300, 'tool', 'mfa');
    }

    if ($oldversion < 2021021900) {
        // Define table tool_mfa_auth to be created.
        $table = new xmldb_table('tool_mfa_auth');

        // Adding fields to table tool_mfa_auth.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('lastverified', XMLDB_TYPE_INTEGER, '15', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table tool_mfa_auth.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);

        // Conditionally launch create table for tool_mfa_auth.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Mfa savepoint reached.
        upgrade_plugin_savepoint(true, 2021021900, 'tool', 'mfa');
    }

    return true;
}
