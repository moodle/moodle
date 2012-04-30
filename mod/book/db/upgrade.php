<?php
// This file is part of Book module for Moodle - http://moodle.org/
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
 * Book module upgrade code
 *
 * @package    mod_book
 * @copyright  2009-2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Book module upgrade task
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool always true
 */
function xmldb_book_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2004081100) {
        throw new upgrade_exception('mod_book', $oldversion, 'Can not upgrade such an old book module, sorry, you should have upgraded it long time ago in 1.9 already.');
    }

    if ($oldversion < 2007052001) {

        // Changing type of field importsrc on table book_chapters to char
        $table = new xmldb_table('book_chapters');
        $field = new xmldb_field('importsrc', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'timemodified');

        // Launch change of type for field importsrc
        $dbman->change_field_type($table, $field);

        upgrade_mod_savepoint(true, 2007052001, 'book');
    }

// 1.9.0 upgrade line.

    if ($oldversion < 2010120801) {
        // Rename field summary on table book to intro
        $table = new xmldb_table('book');
        $field = new xmldb_field('summary', XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'name');

        // Launch rename field summary
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'intro');
        }

        // book savepoint reached
        upgrade_mod_savepoint(true, 2010120801, 'book');
    }

    if ($oldversion < 2010120802) {
        // Rename field summary on table book to intro
        $table = new xmldb_table('book');
        $field = new xmldb_field('intro', XMLDB_TYPE_TEXT, 'big', null, null, null, null, 'name');

        // Launch rename field summary
        $dbman->change_field_precision($table, $field);

        // book savepoint reached
        upgrade_mod_savepoint(true, 2010120802, 'book');
    }

    if ($oldversion < 2010120803) {
        // Define field introformat to be added to book
        $table = new xmldb_table('book');
        $field = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'intro');

        // Launch add field introformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Conditionally migrate to html format in intro
        if ($CFG->texteditors !== 'textarea') {
            $rs = $DB->get_recordset('book', array('introformat'=>FORMAT_MOODLE), '', 'id,intro,introformat');
            foreach ($rs as $b) {
                $b->intro       = text_to_html($b->intro, false, false, true);
                $b->introformat = FORMAT_HTML;
                $DB->update_record('book', $b);
                upgrade_set_timeout();
            }
            unset($b);
            $rs->close();
        }

        // book savepoint reached
        upgrade_mod_savepoint(true, 2010120803, 'book');
    }

    if ($oldversion < 2010120804) {
        // Define field introformat to be added to book
        $table = new xmldb_table('book_chapters');
        $field = new xmldb_field('contentformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'content');

        // Launch add field introformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $DB->set_field('book_chapters', 'contentformat', FORMAT_HTML, array());

        // book savepoint reached
        upgrade_mod_savepoint(true, 2010120804, 'book');
    }

    if ($oldversion < 2010120805) {
        require_once("$CFG->dirroot/mod/book/db/upgradelib.php");

        $sqlfrom = "FROM {book} b
                    JOIN {modules} m ON m.name = 'book'
                    JOIN {course_modules} cm ON (cm.module = m.id AND cm.instance = b.id)";

        $count = $DB->count_records_sql("SELECT COUNT('x') $sqlfrom");

        if ($rs = $DB->get_recordset_sql("SELECT b.id, b.course, cm.id AS cmid $sqlfrom ORDER BY b.course, b.id")) {

            $pbar = new progress_bar('migratebookfiles', 500, true);

            $i = 0;
            foreach ($rs as $book) {
                $i++;
                upgrade_set_timeout(360); // set up timeout, may also abort execution
                $pbar->update($i, $count, "Migrating book files - $i/$count.");

                $context = get_context_instance(CONTEXT_MODULE, $book->cmid);

                mod_book_migrate_moddata_dir_to_legacy($book, $context, '/');

                // remove dirs if empty
                @rmdir("$CFG->dataroot/$book->course/$CFG->moddata/book/$book->id/");
                @rmdir("$CFG->dataroot/$book->course/$CFG->moddata/book/");
                @rmdir("$CFG->dataroot/$book->course/$CFG->moddata/");
                @rmdir("$CFG->dataroot/$book->course/");
            }
            $rs->close();
        }

        // book savepoint reached
        upgrade_mod_savepoint(true, 2010120805, 'book');
    }

    if ($oldversion < 2011011600) {
        // Define field disableprinting to be dropped from book
        $table = new xmldb_table('book');
        $field = new xmldb_field('disableprinting');

        // Conditionally launch drop field disableprinting
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // book savepoint reached
        upgrade_mod_savepoint(true, 2011011600, 'book');
    }

    if ($oldversion < 2011011601) {
        unset_config('book_tocwidth');

        // book savepoint reached
        upgrade_mod_savepoint(true, 2011011601, 'book');
    }

    if ($oldversion < 2011090800) {
        require_once("$CFG->dirroot/mod/book/db/upgradelib.php");

        mod_book_migrate_all_areas();

        upgrade_mod_savepoint(true, 2011090800, 'book');
    }

    if ($oldversion < 2011100900) {

        // Define field revision to be added to book
        $table = new xmldb_table('book');
        $field = new xmldb_field('revision', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'customtitles');

        // Conditionally launch add field revision
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // book savepoint reached
        upgrade_mod_savepoint(true, 2011100900, 'book');
    }

    return true;
}
