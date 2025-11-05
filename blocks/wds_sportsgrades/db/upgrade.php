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
 * Sports Grades block upgrade code
 *
 * @package    block_wds_sportsgrades
 * @copyright  2025 Onwards - Robert Russo
 * @copyright  2025 Onwards - Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_block_wds_sportsgrades_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2025071801) {

        // Define table block_wds_sportsgrades_mentor to be created.
        $table = new xmldb_table('block_wds_sportsgrades_mentor');

        // Adding fields to table block_wds_sportsgrades_mentor.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('mentorid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, false, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, false, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, false, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, false, null);
        $table->add_field('createdby', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, false, null);
        $table->add_field('modifiedby', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, false, null);

        // Adding keys to table block_wds_sportsgrades_mentor.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('mentorid', XMLDB_KEY_FOREIGN, ['mentorid'], 'user', ['id']);
        $table->add_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);

        // Adding indexes to table block_wds_sportsgrades_mentor.
        $table->add_index('mentorid-userid', XMLDB_INDEX_UNIQUE, ['mentorid', 'userid']);

        // Conditionally launch create table for block_wds_sportsgrades_mentor.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Wds_sportsgrades savepoint reached.
        upgrade_block_savepoint(true, 2025071801, 'wds_sportsgrades');
    }

    return true;
}
