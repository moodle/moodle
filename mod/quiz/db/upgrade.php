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

defined('MOODLE_INTERNAL') || die();

/**
 * Quiz module upgrade function.
 * @param string $oldversion the version we are upgrading from.
 */
function xmldb_quiz_upgrade($oldversion) {
    global $CFG, $DB;
    $dbman = $DB->get_manager();

    // Automatically generated Moodle v3.9.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2020061501) {

        // Define field completionminattempts to be added to quiz.
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('completionminattempts', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0',
            'completionpass');

        // Conditionally launch add field completionminattempts.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2020061501, 'quiz');
    }

    if ($oldversion < 2021052503) {
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('completionpass');

        if ($dbman->field_exists($table, $field)) {
            $sql = "SELECT q.id, m.id as quizid " .
                "FROM {quiz} q " .
                "INNER JOIN {course_modules} cm ON cm.instance = q.id " .
                "INNER JOIN {modules} m ON m.id = cm.module " .
                "WHERE m.name = :name AND q.completionpass = :completionpass";

            /** @var moodle_recordset $records */
            $records = $DB->get_recordset_sql($sql, ['name' => 'quiz', 'completionpass' => 1], 0, 1000);
            while ($records->valid()) {
                $quizmodule = null;
                foreach ($records as $record) {
                    $ids[] = $record->id;
                    $quizmodule = $record->quizid;
                }

                if ($ids) {
                    list($insql, $params) = $DB->get_in_or_equal($ids, SQL_PARAMS_NAMED);
                    $DB->set_field_select('course_modules', 'completionpassgrade', 1,
                        "module = :quiz AND instance $insql", $params + ['quiz' => $quizmodule]);

                    // Reset the value so it doesn't get picked on the next run. The field will be dropped later.
                    $DB->set_field_select('quiz', 'completionpass', 0, "id $insql", $params);

                    // Get the next batch of records.
                    $records = $DB->get_recordset_sql($sql, ['name' => 'quiz', 'completionpass' => 1], 0, 1000);
                }
            }
            $records->close();

            // We have completed our checks. Drop the field.
            if ($dbman->field_exists($table, $field)) {
                $dbman->drop_field($table, $field);
            }
        }

        upgrade_mod_savepoint(true, 2021052503, 'quiz');
    }

    if ($oldversion < 2021101900) {

        // Define field gradednotificationsenttime to be added to quiz_attempts.
        $table = new xmldb_table('quiz_attempts');
        $field = new xmldb_field('gradednotificationsenttime', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'sumgrades');

        // Conditionally launch add field gradednotificationsenttime.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);

            $DB->execute('UPDATE {quiz_attempts} SET gradednotificationsenttime = timefinish');
        }

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2021101900, 'quiz');
    }

    if ($oldversion < 2022020300) {
        // Define table quiz_slot_tags to be dropped.
        $table = new xmldb_table('quiz_slot_tags');

        // Conditionally launch drop table for quiz_slot_tags.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Define fields to be dropped from quiz_slots.
        $table = new xmldb_table('quiz_slots');

        // Define key questionid (foreign) to be dropped form quiz_slots.
        $key = new xmldb_key('questionid', XMLDB_KEY_FOREIGN, ['questionid'], 'question', ['id']);

        // Launch drop key questionid.
        $dbman->drop_key($table, $key);

        // Define key questioncategoryid (foreign) to be dropped form quiz_slots.
        $key = new xmldb_key('questioncategoryid', XMLDB_KEY_FOREIGN, ['questioncategoryid'], 'question_categories', ['id']);

        // Launch drop key questioncategoryid.
        $dbman->drop_key($table, $key);

        $field = new xmldb_field('questionid');
        // Conditionally launch drop field questionid.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $field = new xmldb_field('questioncategoryid');
        // Conditionally launch drop field questioncategoryid.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $field = new xmldb_field('includingsubcategories');
        // Conditionally launch drop field includingsubcategories.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2022020300, 'quiz');
    }

    // Automatically generated Moodle v4.0.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.1.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
