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
 * Label module upgrade
 *
 * @package    mod
 * @subpackage label
 * @copyright  2006 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This file keeps track of upgrades to
// the label module
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installation to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the methods of database_manager class
//
// Please do not forget to use upgrade_set_timeout()
// before any action that may take longer time to finish.

defined('MOODLE_INTERNAL') || die;

function xmldb_label_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    //===== 1.9.0 upgrade line ======//

    if ($oldversion < 2009042200) {
        // Rename field content on table label to intro.
        $table = new xmldb_table('label');
        $field = new xmldb_field('content', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, 'name');

        // Launch rename field content.
        $dbman->rename_field($table, $field, 'intro');

        // Label savepoint reached.
        upgrade_mod_savepoint(true, 2009042200, 'label');
    }

    if ($oldversion < 2009042201) {
        // Define field introformat to be added to label.
        $table = new xmldb_table('label');
        $field = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, null, null, '0', 'intro');

        // Launch add field introformat.
        $dbman->add_field($table, $field);

        // All existing labels in 1.9 are in HTML format.
        $DB->set_field('label', 'introformat', FORMAT_HTML, array());

        // Label savepoint reached.
        upgrade_mod_savepoint(true, 2009042201, 'label');
    }

    // Moodle v2.1.0 release upgrade line
    // Put any upgrade step following this

    // Moodle v2.2.0 release upgrade line
    // Put any upgrade step following this

    if ($oldversion < 2011112901) {
        // Alter the label table.
        $table = new xmldb_table('label');
        // Columns to change.
        $arrfields = array();
        $arrfields['name'] = new xmldb_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'course');
        $arrfields['intro'] = new xmldb_field('intro', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, 'name');

        // Loop through columns and update the fields.
        foreach ($arrfields as $fieldname => $field) {
            // Check the field exists.
            if ($dbman->field_exists($table, $fieldname)) {
                $DB->execute("UPDATE {label} SET {$fieldname} = ? WHERE {$fieldname} IS NULL", array($DB->sql_empty()));
                $dbman->change_field_precision($table, $field);
            }
        }

        // Label savepoint reached.
        upgrade_mod_savepoint(true, 2011112901, 'label');
    }

    return true;
}


