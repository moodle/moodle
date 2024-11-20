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
 * Upgrade steps
 *
 * @copyright Davo Smith <moodle@davosmith.co.uk>
 * @package mod_realtimequiz
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

/**
 * Upgrade steps
 * @param int $oldversion
 * @return bool
 * @throws ddl_exception
 * @throws ddl_table_missing_exception
 * @throws downgrade_exception
 * @throws upgrade_exception
 */
function xmldb_realtimequiz_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // Add fields that were missing in the Moodle 1.9 version of this plugin.
    if ($oldversion < 2012102100) {

        $table = new xmldb_table('realtimequiz');

        $field = new xmldb_field('intro', XMLDB_TYPE_TEXT, null, null, null, null, null, 'name');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, FORMAT_HTML,
                                 'intro');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0',
                                 'introformat');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0',
                                 'timecreated');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2012102100, 'realtimequiz');
    }

    if ($oldversion < 2012102101) {

        // Define field questiontextformat to be added to realtimequiz_question.
        $table = new xmldb_table('realtimequiz_question');
        $field = new xmldb_field('questiontextformat', XMLDB_TYPE_INTEGER, FORMAT_PLAIN, null, null, null, '1',
                                 'questiontext');

        // Conditionally launch add field questiontextformat.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Realtimequiz savepoint reached.
        upgrade_mod_savepoint(true, 2012102101, 'realtimequiz');
    }

    return true;
}
