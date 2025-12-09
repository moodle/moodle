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
 * Upgrade logic for local_masterbuilder.
 *
 * @package    local_masterbuilder
 * @copyright  2024 AuST
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade function.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_local_masterbuilder_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2025120201) {
        // Define table local_masterbuilder_state to be created.
        $table = new xmldb_table('local_masterbuilder_state');

        // Adding fields to table local_masterbuilder_state.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course_shortname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('version', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table local_masterbuilder_state.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table local_masterbuilder_state.
        $table->add_index('course_shortname', XMLDB_INDEX_UNIQUE, ['course_shortname']);

        // Conditionally launch create table for local_masterbuilder_state.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Masterbuilder savepoint reached.
        upgrade_plugin_savepoint(true, 2025120201, 'local', 'masterbuilder');
    }

    return true;
}
