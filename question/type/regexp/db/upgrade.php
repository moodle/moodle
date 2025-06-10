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
 * Serve question type files
 *
 * @since      2.0
 * @package    qtype_regexp
 * @copyright  Joseph REZEAU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade code for the regexp question type.
 * @param int $oldversion the version we are upgrading from.
 */
function xmldb_qtype_regexp_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();
    $result = true;

    if ($oldversion < 2011022301) {
        // Define field usecase to be added to question_regexp.
        $table = new xmldb_table('question_regexp');
        $field = new xmldb_field('usecase', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'usehint');

        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2011022301, 'qtype', 'regexp');
    }

    if ($oldversion < 2011102300) {
        // Table question_regexp to be renamed to qtype_regexp.
        $table = new xmldb_table('question_regexp');

        // Launch rename table.
        if ($dbman->table_exists($table)) {
            $dbman->rename_table($table, 'qtype_regexp');
        }
        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2011102300,  'qtype', 'regexp');
    }

    if ($oldversion < 2012010100) {
        // Rename field "question" on table "qtype_regexp" to "questiontype".
        $table = new xmldb_table('qtype_regexp');
        $field = new xmldb_field('question', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');

        // Launch rename field.
        if ($dbman->table_exists($table)) {
            if ($dbman->field_exists($table, $field)) {
                $dbman->rename_field($table, $field, 'questionid');
            }
        }
        // Define field studentshowalternate to be added to qtype_regexp.
        $table = new xmldb_table('qtype_regexp');
        $field = new xmldb_field('studentshowalternate', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'usecase');

        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2012010100,  'qtype', 'regexp');
    }

    return true;
}
