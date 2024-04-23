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
 * Upgrade script for the quiz module.
 *
 * @package    mod_quiz
 * @copyright  2006 Eloy Lafuente (stronk7)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Quiz module upgrade function.
 * @param string $oldversion the version we are upgrading from.
 */
function xmldb_quiz_upgrade($oldversion) {
    global $CFG, $DB;
    $dbman = $DB->get_manager();

    // Automatically generated Moodle v4.1.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2022120500) {
        // Define field displaynumber to be added to quiz_slots.
        $table = new xmldb_table('quiz_slots');
        $field = new xmldb_field('displaynumber', XMLDB_TYPE_CHAR, '16', null, null, null, null, 'page');

        // Conditionally launch add field displaynumber.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2022120500, 'quiz');
    }

    // Automatically generated Moodle v4.2.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2023042401) {
        // Define field reviewmaxmarks to be added to quiz.
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('reviewmaxmarks', XMLDB_TYPE_INTEGER, '6', null, XMLDB_NOTNULL, null, '0', 'reviewcorrectness');

        // Conditionally launch add field reviewmaxmarks.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2023042401, 'quiz');
    }

    // Automatically generated Moodle v4.3.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2023112300) {

        // Set the value for all existing rows to match the previous behaviour,
        // but only where users have not already set another value.
        $DB->set_field('quiz', 'reviewmaxmarks', 0x11110, ['reviewmaxmarks' => 0]);

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2023112300, 'quiz');
    }

    if ($oldversion < 2023112400) {

        // Define table quiz_grade_items to be created.
        $table = new xmldb_table('quiz_grade_items');

        // Adding fields to table quiz_grade_items.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('quizid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table quiz_grade_items.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('quizid', XMLDB_KEY_FOREIGN, ['quizid'], 'quiz', ['id']);

        // Adding indexes to table quiz_grade_items.
        $table->add_index('quizid-sortorder', XMLDB_INDEX_UNIQUE, ['quizid', 'sortorder']);

        // Conditionally launch create table for quiz_grade_items.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2023112400, 'quiz');
    }

    if ($oldversion < 2023112401) {

        // Define field quizgradeitemid to be added to quiz_slots.
        $table = new xmldb_table('quiz_slots');
        $field = new xmldb_field('quizgradeitemid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'maxmark');

        // Conditionally launch add field quizgradeitemid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2023112401, 'quiz');
    }

    if ($oldversion < 2023112402) {

        // Define key quizgradeitemid (foreign) to be added to quiz_slots.
        $table = new xmldb_table('quiz_slots');
        $key = new xmldb_key('quizgradeitemid', XMLDB_KEY_FOREIGN, ['quizgradeitemid'], 'quiz_grade_items', ['id']);

        // Launch add key quizgradeitemid.
        $dbman->add_key($table, $key);

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2023112402, 'quiz');
    }

    // Automatically generated Moodle v4.4.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
