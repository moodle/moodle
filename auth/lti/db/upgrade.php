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
 * LTI authentication plugin upgrade code
 *
 * @package    auth_lti
 * @copyright  2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade function.
 *
 * @param int $oldversion the version we are upgrading from.
 * @return bool result.
 */
function xmldb_auth_lti_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2021100500) {
        // Define table auth_lti_linked_login to be created.
        $table = new xmldb_table('auth_lti_linked_login');

        // Adding fields to table auth_lti_linked_login.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('issuer', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('issuer256', XMLDB_TYPE_CHAR, '64', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sub', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sub256', XMLDB_TYPE_CHAR, '64', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table auth_lti_linked_login.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('userid_key', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
        $table->add_key('unique_key', XMLDB_KEY_UNIQUE, ['userid', 'issuer256', 'sub256']);

        // Conditionally launch create table for auth_lti_linked_login.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Auth LTI savepoint reached.
        upgrade_plugin_savepoint(true, 2021100500, 'auth', 'lti');
    }

    if ($oldversion < 2022030900) {
        // Fix the unique key made up of {userid, issuer256, sub256}.
        // This was improperly defined as ['userid, issuer256, sub256'] in the upgrade step above (note the quotes),
        // resulting in the potential for missing keys on some databases.
        // Drop and re-add the key to make sure we have it in place.

        // Define table auth_lti_linked_login to be modified.
        $table = new xmldb_table('auth_lti_linked_login');

        // Define the key to be dropped and re-added.
        $key = new xmldb_key('unique_key', XMLDB_KEY_UNIQUE, ['userid', 'issuer256', 'sub256'], 'auth_lti_linked_login');

        // Drop the key.
        $dbman->drop_key($table, $key);

        // Create the key.
        $dbman->add_key($table, $key);

        // Auth LTI savepoint reached.
        upgrade_plugin_savepoint(true, 2022030900, 'auth', 'lti');
    }

    // Automatically generated Moodle v4.0.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
