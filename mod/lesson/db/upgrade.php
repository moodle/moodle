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
 * This file keeps track of upgrades to
 * the lesson module
 *
 * Sometimes, changes between versions involve
 * alterations to database structures and other
 * major things that may break installations.
 *
 * The upgrade function in this file will attempt
 * to perform all the necessary actions to upgrade
 * your older installation to the current version.
 *
 * If there's something it cannot do itself, it
 * will tell you what you need to do.
 *
 * The commands in here will all be database-neutral,
 * using the methods of database_manager class
 *
 * Please do not forget to use upgrade_set_timeout()
 * before any action that may take longer time to finish.
 *
 * @package mod_lesson
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 o
 */

defined('MOODLE_INTERNAL') || die();

/**
 *
 * @global stdClass $CFG
 * @global moodle_database $DB
 * @global core_renderer $OUTPUT
 * @param int $oldversion
 * @return bool
 */
function xmldb_lesson_upgrade($oldversion) {
    global $CFG, $DB, $OUTPUT;

    $dbman = $DB->get_manager();


    // Moodle v2.2.0 release upgrade line
    // Put any upgrade step following this

    // Moodle v2.3.0 release upgrade line
    // Put any upgrade step following this


    // Moodle v2.4.0 release upgrade line
    // Put any upgrade step following this


    // Moodle v2.5.0 release upgrade line.
    // Put any upgrade step following this.


    // Moodle v2.6.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.7.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2014091001) {
        $table = new xmldb_table('lesson');
        $field = new xmldb_field('intro', XMLDB_TYPE_TEXT, null, null, null, null, null, 'name');
        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'intro');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2014091001, 'lesson');
    }

    if ($oldversion < 2014100600) {
        // Previously there was no module intro in lesson so don't require
        // it to be filled in for upgraded sites.
        set_config('requiremodintro', 0, 'lesson');
        upgrade_mod_savepoint(true, 2014100600, 'lesson');
    }

    // Moodle v2.8.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2014112300) {

        // Define field completionendreached to be added to lesson.
        $table = new xmldb_table('lesson');
        $field = new xmldb_field('completionendreached', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', 'timemodified');

        // Conditionally launch add field completionendreached.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field completed to be added to lesson_timer.
        $table = new xmldb_table('lesson_timer');
        $field = new xmldb_field('completed', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', 'lessontime');

        // Conditionally launch add field completed.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Lesson savepoint reached.
        upgrade_mod_savepoint(true, 2014112300, 'lesson');
    }

    if ($oldversion < 2014122900) {

        // Changing precision of field grade on table lesson to (10).
        $table = new xmldb_table('lesson');
        $field = new xmldb_field('grade', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'conditions');

        // Launch change of precision for field grade.
        $dbman->change_field_precision($table, $field);

        // Lesson savepoint reached.
        upgrade_mod_savepoint(true, 2014122900, 'lesson');
    }

    if ($oldversion < 2015030300) {

        // Define field nextpageid to be added to lesson_branch.
        $table = new xmldb_table('lesson_branch');
        $field = new xmldb_field('nextpageid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'timeseen');

        // Conditionally launch add field nextpageid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Lesson savepoint reached.
        upgrade_mod_savepoint(true, 2015030300, 'lesson');
    }

    if ($oldversion < 2015030301) {

        // Clean lesson answers that should be plain text.
        // Unfortunately we can't use LESSON_PAGE_XX constants here as we can't include the files.
        // 1 = LESSON_PAGE_SHORTANSWER, 8 = LESSON_PAGE_NUMERICAL, 20 = LESSON_PAGE_BRANCHTABLE.

        $sql = 'SELECT a.*
                  FROM {lesson_answers} a
                  JOIN {lesson_pages} p ON p.id = a.pageid
                 WHERE a.answerformat <> :format
                   AND p.qtype IN (1, 8, 20)';
        $badanswers = $DB->get_recordset_sql($sql, array('format' => FORMAT_MOODLE));

        foreach ($badanswers as $badanswer) {
            // Strip tags from answer text and convert back the format to FORMAT_MOODLE.
            $badanswer->answer = strip_tags($badanswer->answer);
            $badanswer->answerformat = FORMAT_MOODLE;
            $DB->update_record('lesson_answers', $badanswer);
        }
        $badanswers->close();

        // Lesson savepoint reached.
        upgrade_mod_savepoint(true, 2015030301, 'lesson');
    }

    if ($oldversion < 2015030400) {

        // Creating new field timelimit in lesson table.
        $table = new xmldb_table('lesson');
        $field = new xmldb_field('timelimit', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'maxpages');

        // Conditionally launch add field timelimit.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Lesson savepoint reached.
        upgrade_mod_savepoint(true, 2015030400, 'lesson');
    }

    if ($oldversion < 2015030401) {

        // Convert maxtime (minutes) to timelimit (seconds).
        $table = new xmldb_table('lesson');
        $oldfield = new xmldb_field('maxtime');
        $newfield = new xmldb_field('timelimit');
        if ($dbman->field_exists($table, $oldfield) && $dbman->field_exists($table, $newfield)) {
            $sql = 'UPDATE {lesson} SET timelimit = 60 * maxtime';
            $DB->execute($sql);
            // Drop field maxtime.
            $dbman->drop_field($table, $oldfield);
        }

        $oldfield = new xmldb_field('timed');
        if ($dbman->field_exists($table, $oldfield) && $dbman->field_exists($table, $newfield)) {
            // Set timelimit to 0 for non timed lessons.
            $DB->set_field_select('lesson', 'timelimit', 0, 'timed = 0');
            // Drop field timed.
            $dbman->drop_field($table, $oldfield);
        }
        // Lesson savepoint reached.
        upgrade_mod_savepoint(true, 2015030401, 'lesson');
    }

    if ($oldversion < 2015031500) {

        // Define field completiontimespent to be added to lesson.
        $table = new xmldb_table('lesson');
        $field = new xmldb_field('completiontimespent', XMLDB_TYPE_INTEGER, '11', null, null, null, '0', 'completionendreached');

        // Conditionally launch add field completiontimespent.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Lesson savepoint reached.
        upgrade_mod_savepoint(true, 2015031500, 'lesson');
    }

    return true;
}
