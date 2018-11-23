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

    // Automatically generated Moodle v3.5.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2018051401) {
        $table = new xmldb_table('assignfeedback_editpdf_queue');
        $field = new xmldb_field('attemptedconversions', XMLDB_TYPE_INTEGER, '10', null,
            XMLDB_NOTNULL, null, 0, 'submissionattempt');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Attempts are removed from the queue after being processed, a duplicate row won't achieve anything productive.
        // So look for any duplicates and remove them so we can add a unique key.
        $sql = "SELECT MIN(id) as minid, submissionid, submissionattempt
                FROM {assignfeedback_editpdf_queue}
                GROUP BY submissionid, submissionattempt
                HAVING COUNT(id) > 1";

        if ($duplicatedrows = $DB->get_recordset_sql($sql)) {
            foreach ($duplicatedrows as $row) {
                $DB->delete_records_select('assignfeedback_editpdf_queue',
                    'submissionid = :submissionid AND submissionattempt = :submissionattempt AND id <> :minid', (array)$row);
            }
        }
        $duplicatedrows->close();

        // Define key submissionid-submissionattempt to be added to assignfeedback_editpdf_queue.
        $table = new xmldb_table('assignfeedback_editpdf_queue');
        $key = new xmldb_key('submissionid-submissionattempt', XMLDB_KEY_UNIQUE, ['submissionid', 'submissionattempt']);

        $dbman->add_key($table, $key);

        upgrade_plugin_savepoint(true, 2018051401, 'assignfeedback', 'editpdf');
    }

    return true;
}
