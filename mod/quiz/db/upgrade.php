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

    if ($oldversion < 2016032600) {
        // Update quiz_sections to repair quizzes what were broken by MDL-53507.
        $problemquizzes = $DB->get_records_sql("
                SELECT quizid, MIN(firstslot) AS firstsectionfirstslot
                FROM {quiz_sections}
                GROUP BY quizid
                HAVING MIN(firstslot) > 1");

        if ($problemquizzes) {
            $pbar = new progress_bar('upgradequizfirstsection', 500, true);
            $total = count($problemquizzes);
            $done = 0;
            foreach ($problemquizzes as $problemquiz) {
                $DB->set_field('quiz_sections', 'firstslot', 1,
                        array('quizid' => $problemquiz->quizid,
                        'firstslot' => $problemquiz->firstsectionfirstslot));
                $done += 1;
                $pbar->update($done, $total, "Fixing quiz layouts - {$done}/{$total}.");
            }
        }

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2016032600, 'quiz');
    }

    // Moodle v3.1.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2016092000) {
        // Define new fields to be added to quiz.
        $table = new xmldb_table('quiz');

        $field = new xmldb_field('allowofflineattempts', XMLDB_TYPE_INTEGER, '1', null, null, null, 0, 'completionpass');
        // Conditionally launch add field allowofflineattempts.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2016092000, 'quiz');
    }

    if ($oldversion < 2016092001) {
        // New field for quiz_attemps.
        $table = new xmldb_table('quiz_attempts');

        $field = new xmldb_field('timemodifiedoffline', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0, 'timemodified');
        // Conditionally launch add field timemodifiedoffline.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2016092001, 'quiz');
    }

    if ($oldversion < 2016100300) {
        // Find quizzes with the combination of require passing grade and grade to pass 0.
        $gradeitems = $DB->get_records_sql("
            SELECT gi.id, gi.itemnumber, cm.id AS cmid
              FROM {quiz} q
        INNER JOIN {course_modules} cm ON q.id = cm.instance
        INNER JOIN {grade_items} gi ON q.id = gi.iteminstance
        INNER JOIN {modules} m ON m.id = cm.module
             WHERE q.completionpass = 1
               AND gi.gradepass = 0
               AND cm.completiongradeitemnumber IS NULL
               AND gi.itemmodule = m.name
               AND gi.itemtype = ?
               AND m.name = ?", array('mod', 'quiz'));

        foreach ($gradeitems as $gradeitem) {
            $DB->execute("UPDATE {course_modules}
                             SET completiongradeitemnumber = :itemnumber
                           WHERE id = :cmid",
                array('itemnumber' => $gradeitem->itemnumber, 'cmid' => $gradeitem->cmid));
        }
        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2016100300, 'quiz');
    }

    // Automatically generated Moodle v3.2.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.3.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.4.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
