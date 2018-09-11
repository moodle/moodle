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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/mod/journal/lib.php');

function xmldb_journal_upgrade($oldversion=0) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // No DB changes since 1.9.0.

    // Add journal instances to the gradebook.
    if ($oldversion < 2010120300) {

        journal_update_grades();

        upgrade_mod_savepoint(true, 2010120300, 'journal');
    }

    // Change assessed field for grade.
    if ($oldversion < 2011040600) {

        // Rename field assessed on table journal to grade.
        $table = new xmldb_table('journal');
        $field = new xmldb_field('assessed', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'days');

        // Launch rename field grade.
        $dbman->rename_field($table, $field, 'grade');

        // Journal savepoint reached.
        upgrade_mod_savepoint(true, 2011040600, 'journal');
    }

    if ($oldversion < 2012032001) {

        // Changing the default of field rating on table journal_entries to drop it.
        $table = new xmldb_table('journal_entries');
        $field = new xmldb_field('rating', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'format');

        // Launch change of default for field rating.
        $dbman->change_field_default($table, $field);

        // Updating the non-marked entries with rating = NULL.
        $entries = $DB->get_records('journal_entries', array('timemarked' => 0));
        if ($entries) {
            foreach ($entries as $entry) {
                $entry->rating = null;
                $DB->update_record('journal_entries', $entry);
            }
        }

        // Journal savepoint reached.
        upgrade_mod_savepoint(true, 2012032001, 'journal');
    }

    return true;
}
