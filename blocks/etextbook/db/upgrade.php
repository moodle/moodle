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
 * Version Details: First Version
 * Block displaying information about whether or not there is an etextbook
 * for the course
 *
 * @package    block_etextbook
 * @copyright  2016 Lousiana State University - David Elliott, Robert Russo, Chad Mazilly
 * @author     Robert Russo <rrusso@lsu.edu>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade script for the block_etextbook plugin.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_block_etextbook_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // Version check.
    if ($oldversion < 2025110400) {

        // Define the table we’re modifying.
        $table = new xmldb_table('block_etextbook');

        // Column term: change from varchar(25) to varchar(255).
        $field = new xmldb_field('term', XMLDB_TYPE_CHAR, '255', null, null, null, null, null);
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_precision($table, $field);
        }

        // Column dept: change from varchar(9) to varchar(255).
        $field = new xmldb_field('dept', XMLDB_TYPE_CHAR, '255', null, null, null, null, null);
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_precision($table, $field);
        }

        // Column course_number: change from int to varchar(255).
        $field = new xmldb_field('course_number', XMLDB_TYPE_CHAR, '255', null, null, null, null, null);
        if ($dbman->field_exists($table, $field)) {
            // Change field type.
            $dbman->change_field_type($table, $field);
        }

        // Column section: change from smallint to varchar(255).
        $field = new xmldb_field('section', XMLDB_TYPE_CHAR, '255', null, null, null, null, null);
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_type($table, $field);
        }

        // Column course_title: ensure varchar(255).
        $field = new xmldb_field('course_title', XMLDB_TYPE_CHAR, '255', null, null, null, null, null);
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_precision($table, $field);
        }

        // Column instructor: ensure varchar(255).
        $field = new xmldb_field('instructor', XMLDB_TYPE_CHAR, '255', null, null, null, null, null);
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_precision($table, $field);
        }

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2025110400, 'block', 'etextbook');
    }

    return true;
}
