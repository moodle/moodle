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
 * @package    mod
 * @subpackage book
 * @copyright  2009-2010 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

function xmldb_book_upgrade($oldversion) {
    global $CFG, $DB;
    require_once("$CFG->dirroot/mod/book/db/upgradelib.php");

    $dbman = $DB->get_manager();

    if ($oldversion < 2007052001) {

    /// Changing type of field importsrc on table book_chapters to char
        $table = new xmldb_table('book_chapters');
        $field = new xmldb_field('importsrc', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'timemodified');

    /// Launch change of type for field importsrc
        $dbman->change_field_type($table, $field);

        upgrade_mod_savepoint(true, 2007052001, 'book');
    }

//===== 1.9.0 upgrade line ======//

    if ($oldversion < 2010120801) {
       // Rename field summary on table book to intro
        $table = new xmldb_table('book');
        $field = new xmldb_field('summary', XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'name');

        // Launch rename field summary
        $dbman->rename_field($table, $field, 'intro');

        // book savepoint reached
        upgrade_mod_savepoint(true, 2010120801, 'book');
    }

    if ($oldversion < 2010120802) {
        // Define field introformat to be added to book
        $table = new xmldb_table('book');
        $field = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'intro');

        // Launch add field introformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // conditionally migrate to html format in intro
        if ($CFG->texteditors !== 'textarea') {
            $rs = $DB->get_recordset('book', array('introformat'=>FORMAT_MOODLE), '', 'id,intro,introformat');
            foreach ($rs as $r) {
                $r->intro       = text_to_html($r->intro, false, false, true);
                $r->introformat = FORMAT_HTML;
                $DB->update_record('book', $r);
                upgrade_set_timeout();
            }
            $rs->close();
        }

        // book savepoint reached
        upgrade_mod_savepoint(true, 2010120802, 'book');
    }


    return true;
}
