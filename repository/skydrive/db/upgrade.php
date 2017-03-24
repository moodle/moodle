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

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade this plugin.
 *
 * @param int $oldversion the version we are upgrading from
 * @package repository_skydrive
 * @return bool result
 */
function xmldb_repository_skydrive_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2017031400) {

        // Define table repository_skydrive_access to be created.
        $table = new xmldb_table('repository_skydrive_access');

        // Adding fields to table repository_skydrive_access.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('permissionid', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('itemid', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table repository_skydrive_access.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('usermodifiedkey', XMLDB_KEY_FOREIGN, array('usermodified'), 'user', array('id'));

        // Conditionally launch create table for repository_skydrive_access.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Skydrive savepoint reached.
        upgrade_plugin_savepoint(true, 2017031400, 'repository', 'skydrive');
    }
    return true;
}
