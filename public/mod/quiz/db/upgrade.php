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

    // Automatically generated Moodle v4.4.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.5.0 release upgrade line.
    // Put any upgrade step following this.
    if ($oldversion < 2025011300) {
        // Define field precreateattempts to be added to quiz.
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('precreateattempts', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'allowofflineattempts');

        // Conditionally launch add field precreateattempts.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2025011300, 'quiz');
    }

    // Automatically generated Moodle v5.0.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v5.1.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2026022300) {
        // Changing precision of field name on table quiz to (1333).
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('name', XMLDB_TYPE_CHAR, '1333', null, XMLDB_NOTNULL, null, null, 'course');

        // Launch change of precision for field name.
        $dbman->change_field_precision($table, $field);

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2026022300, 'quiz');
    }

    if ($oldversion < 2026022400) {
        // Queue tasks to process stuck quiz attempts (state = 'submitted').
        $attemptids = $DB->get_fieldset_select(
            'quiz_attempts',
            'id',
            'state = ?',
            [\mod_quiz\quiz_attempt::SUBMITTED],
        );

        foreach ($attemptids as $attemptid) {
            $task = \mod_quiz\task\grade_submission::instance($attemptid);
            \core\task\manager::queue_adhoc_task($task, true);
        }

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2026022400, 'quiz');
    }

    if ($oldversion < 2026030600) {
        // Define field reason to be added to quiz_overrides.
        $table = new xmldb_table('quiz_overrides');
        $field = new xmldb_field('reason', XMLDB_TYPE_TEXT, null, null, null, null, null, 'password');

        // Conditionally launch add field reason.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field reasonformat to be added to quiz_overrides.
        $formatfield = new xmldb_field('reasonformat', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'reason');

        // Conditionally launch add field reasonformat.
        if (!$dbman->field_exists($table, $formatfield)) {
            $dbman->add_field($table, $formatfield);
        }

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2026030600, 'quiz');
    }

    // Automatically generated Moodle v5.2.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2026083100) {
        // Define field duedate to be added to quiz.
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('duedate', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'precreateattempts');

        // Conditionally launch add field duedate.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field duedate to be added to quiz_overrides.
        $table = new xmldb_table('quiz_overrides');
        $field = new xmldb_field('duedate', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'password');

        // Conditionally launch add field duedate.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2026083100, 'quiz');
    }

    return true;
}
