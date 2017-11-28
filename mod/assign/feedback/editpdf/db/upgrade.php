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

defined('MOODLE_INTERNAL') || die();

/**
 * EditPDF upgrade code
 * @param int $oldversion
 * @return bool
 */
function xmldb_assignfeedback_editpdf_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2016021600) {

        // Define table assignfeedback_editpdf_queue to be created.
        $table = new xmldb_table('assignfeedback_editpdf_queue');

        // Adding fields to table assignfeedback_editpdf_queue.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('submissionid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('submissionattempt', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table assignfeedback_editpdf_queue.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for assignfeedback_editpdf_queue.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Editpdf savepoint reached.
        upgrade_plugin_savepoint(true, 2016021600, 'assignfeedback', 'editpdf');
    }

    // Moodle v3.1.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.2.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2017022700) {

        // Get orphaned, duplicate files and delete them.
        $fs = get_file_storage();
        $sqllike = $DB->sql_like("filename", "?");
        $where = "component='assignfeedback_editpdf' AND filearea = 'importhtml' AND " . $sqllike;
        $filerecords = $DB->get_records_select("files", $where, ["onlinetext-%"]);
        foreach ($filerecords as $filerecord) {
            $file = $fs->get_file_instance($filerecord);
            $file->delete();
        }

        // Editpdf savepoint reached.
        upgrade_plugin_savepoint(true, 2017022700, 'assignfeedback', 'editpdf');
    }

    // Automatically generated Moodle v3.3.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.4.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
