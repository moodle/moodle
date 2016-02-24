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
 * Upgrade code.
 *
 * @package    local_recyclebin
 * @copyright  2015 Skylar Kelty <S.Kelty@kent.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Recycle bin upgrade task
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool always true
 */
function xmldb_local_recyclebin_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2015060400) {
        // Define field deleted to be added to local_recyclebin.
        $table = new xmldb_table('local_recyclebin');
        if ($dbman->table_exists($table)) {
            $field = new xmldb_field('deleted', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'name');
            // Conditionally launch add field deleted.
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }

            // Define index i_deleted (not unique) to be added to local_recyclebin.
            $index = new xmldb_index('i_deleted', XMLDB_INDEX_NOTUNIQUE, array('deleted'));
            // Conditionally launch add index i_deleted.
            if (!$dbman->index_exists($table, $index)) {
                $dbman->add_index($table, $index);
            }
        }

        upgrade_plugin_savepoint(true, 2015060400, 'local', 'recyclebin');
    }

    if ($oldversion < 2015082700) {
        // Define table local_recyclebin to be renamed to local_recyclebin_course.
        $table = new xmldb_table('local_recyclebin');

        // Launch rename table for local_recyclebin.
        if ($dbman->table_exists($table)) {
            $dbman->rename_table($table, 'local_recyclebin_course');
        }

        // Recyclebin savepoint reached.
        upgrade_plugin_savepoint(true, 2015082700, 'local', 'recyclebin');
    }

    if ($oldversion < 2015082701) {
        // Define table local_recyclebin_category to be created.
        $table = new xmldb_table('local_recyclebin_category');

        // Adding fields to table local_recyclebin_category.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('category', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
        $table->add_field('shortname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('fullname', XMLDB_TYPE_CHAR, '254', null, XMLDB_NOTNULL, null, null);
        $table->add_field('deleted', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table local_recyclebin_category.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table local_recyclebin_category.
        $table->add_index('i_category', XMLDB_INDEX_NOTUNIQUE, array('category'));

        // Conditionally launch create table for local_recyclebin_category.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Recyclebin savepoint reached.
        upgrade_plugin_savepoint(true, 2015082701, 'local', 'recyclebin');
    }

    return true;
}
