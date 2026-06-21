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
 * Upgrade code for the feedback_editpdf module.
 *
 * @package   assignfeedback_editpdf
 * @copyright 2013 Jerome Mouneyrac
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * EditPDF upgrade code
 * @param int $oldversion
 * @return bool
 */
function xmldb_assignfeedback_editpdf_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    // Automatically generated Moodle v4.4.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.5.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v5.0.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v5.1.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2026042001) {
        $table = new xmldb_table('assignfeedback_editpdf_cmnt');
        $field = new xmldb_field('markid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'gradeid');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table('assignfeedback_editpdf_annot');
        $field = new xmldb_field('markid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'pageno');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table('assignfeedback_editpdf_rot');
        $field = new xmldb_field('markid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'pageno');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // The index on the rotations table needs to include markid. So we have to drop existing then re-create.
        $index = new xmldb_index('gradeid_pageno', XMLDB_INDEX_UNIQUE, ['gradeid', 'pageno']);
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Now create new one.
        $index = new xmldb_index('gradeid_markid_pageno', XMLDB_INDEX_UNIQUE, ['gradeid', 'markid', 'pageno']);

        // Conditionally launch add index gradeid_pageno.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_plugin_savepoint(true, 2026042001, 'assignfeedback', 'editpdf');
    }

    return true;
}
