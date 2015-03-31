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

    // Moodle v2.2.0 release upgrade line
    // Put any upgrade step following this

    // Moodle v2.3.0 release upgrade line
    // Put any upgrade step following this

    // Note: The next steps (up to 2012090408 included, are a "replay" of old upgrade steps,
    // because some sites updated to Moodle 2.3 didn't have the latest contrib mod_book
    // installed, so some required changes were missing.
    //
    // All the steps are run conditionally so sites upgraded from latest contrib mod_book or
    // new (2.3 and upwards) sites won't get affected.
    //
    // Warn: It will be safe to delete these steps once Moodle 2.5 (not 2.4!) is declared as minimum
    // requirement (environment.xml) in some future Moodle 2.x version. Never, never, before!
    //
    // See MDL-35297 and commit msg for more information.

    if ($oldversion < 2012090401) {
        // Rename field summary on table book to intro
        $table = new xmldb_table('book');
        $field = new xmldb_field('summary', XMLDB_TYPE_TEXT, null, null, null, null, null, 'name');

        // Launch rename field summary
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'intro');
        }

        // book savepoint reached
        upgrade_mod_savepoint(true, 2012090401, 'book');
    }

    if ($oldversion < 2012090402) {
        // Define field introformat to be added to book
        $table = new xmldb_table('book');
        $field = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'intro');

        // Launch add field introformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
            // Conditionally migrate to html format in intro
            // Si está activo el htmleditor!!!!!
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
        }

        // book savepoint reached
        upgrade_mod_savepoint(true, 2012090402, 'book');
    }

    if ($oldversion < 2012090403) {
        // Define field introformat to be added to book
        $table = new xmldb_table('book_chapters');
        $field = new xmldb_field('contentformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'content');

        // Launch add field introformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);

            $DB->set_field('book_chapters', 'contentformat', FORMAT_HTML, array());
        }

        // book savepoint reached
        upgrade_mod_savepoint(true, 2012090403, 'book');
    }

    if ($oldversion < 2012090404) {
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

                $context = context_module::instance($book->cmid);

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
        upgrade_mod_savepoint(true, 2012090404, 'book');
    }

    if ($oldversion < 2012090405) {
        // Define field disableprinting to be dropped from book
        $table = new xmldb_table('book');
        $field = new xmldb_field('disableprinting');

        // Conditionally launch drop field disableprinting
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // book savepoint reached
        upgrade_mod_savepoint(true, 2012090405, 'book');
    }

    if ($oldversion < 2012090406) {
        unset_config('book_tocwidth');

        // book savepoint reached
        upgrade_mod_savepoint(true, 2012090406, 'book');
    }

    if ($oldversion < 2012090407) {
        require_once("$CFG->dirroot/mod/book/db/upgradelib.php");

        mod_book_migrate_all_areas();

        upgrade_mod_savepoint(true, 2012090407, 'book');
    }

    if ($oldversion < 2012090408) {

        // Define field revision to be added to book
        $table = new xmldb_table('book');
        $field = new xmldb_field('revision', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'customtitles');

        // Conditionally launch add field revision
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // book savepoint reached
        upgrade_mod_savepoint(true, 2012090408, 'book');
    }
    // End of MDL-35297 "replayed" steps.

    // Moodle v2.4.0 release upgrade line
    // Put any upgrade step following this


    // Moodle v2.5.0 release upgrade line.
    // Put any upgrade step following this.


    // Moodle v2.6.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.7.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.8.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2014111800) {

        // Define field navstyle to be added to book.
        $table = new xmldb_table('book');
        $field = new xmldb_field('navstyle', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '1', 'numbering');

        // Conditionally launch add field navstyle.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Book savepoint reached.
        upgrade_mod_savepoint(true, 2014111800, 'book');
    }

    return true;
}
