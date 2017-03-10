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
 * OAuth2 authentication plugin upgrade code
 *
 * @package    auth_oauth2
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade function
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_auth_oauth2_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // Automatically generated Moodle v3.2.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2017030700) {

        // Define table auth_oauth2_linked_login to be created.
        $table = new xmldb_table('auth_oauth2_linked_login');

        // Adding fields to table auth_oauth2_linked_login.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('issuerid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('username', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('email', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table auth_oauth2_linked_login.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('usermodified_key', XMLDB_KEY_FOREIGN, array('usermodified'), 'user', array('id'));
        $table->add_key('userid_key', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));
        $table->add_key('issuerid_key', XMLDB_KEY_FOREIGN, array('issuerid'), 'oauth2_issuer', array('id'));
        $table->add_key('uniq_key', XMLDB_KEY_UNIQUE, array('userid', 'issuerid', 'username'));

        // Adding indexes to table auth_oauth2_linked_login.
        $table->add_index('search_index', XMLDB_INDEX_NOTUNIQUE, array('issuerid', 'username'));

        // Conditionally launch create table for auth_oauth2_linked_login.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Oauth2 savepoint reached.
        upgrade_plugin_savepoint(true, 2017030700, 'auth', 'oauth2');
    }

    if ($oldversion < 2017031000) {

        // Changing type of field email on table auth_oauth2_linked_login to text.
        $table = new xmldb_table('auth_oauth2_linked_login');
        $field = new xmldb_field('email', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, 'username');

        // Launch change of type for field email.
        $dbman->change_field_type($table, $field);

        // Oauth2 savepoint reached.
        upgrade_plugin_savepoint(true, 2017031000, 'auth', 'oauth2');
    }

    return true;
}
