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

    // Automatically generated Moodle v3.9.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2021060400) {
        // Remove submissions from the processing queue that have been processed.
        $sql = 'DELETE
                  FROM {assignfeedback_editpdf_queue}
                 WHERE EXISTS (SELECT 1
                                 FROM {assign_submission} s,
                                      {assign_grades} g
                                WHERE s.id = submissionid
                                  AND s.assignment = g.assignment
                                  AND s.userid = g.userid
                                  AND s.attemptnumber = g.attemptnumber)';

        $DB->execute($sql);

        // Editpdf savepoint reached.
        upgrade_plugin_savepoint(true, 2021060400, 'assignfeedback', 'editpdf');
    }

    // Automatically generated Moodle v4.0.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2022061000) {
        $table = new xmldb_table('assignfeedback_editpdf_queue');
        if ($dbman->table_exists($table)) {
            // Convert not yet converted submissions into adhoc tasks.
            $rs = $DB->get_recordset('assignfeedback_editpdf_queue');
            foreach ($rs as $record) {
                $data = [
                    'submissionid' => $record->submissionid,
                    'submissionattempt' => $record->submissionattempt,
                ];
                $task = new assignfeedback_editpdf\task\convert_submission;
                $task->set_custom_data($data);
                \core\task\manager::queue_adhoc_task($task, true);
            }
            $rs->close();

            // Drop the table.
            $dbman->drop_table($table);
        }

        // Editpdf savepoint reached.
        upgrade_plugin_savepoint(true, 2022061000, 'assignfeedback', 'editpdf');
    }

    if ($oldversion < 2022082200) {
        // Conversion records need to be removed in order for conversions to restart.
        $DB->delete_records('file_conversion');

        // Schedule an adhoc task to fix existing stale conversions.
        $task = new \assignfeedback_editpdf\task\bump_submission_for_stale_conversions();
        \core\task\manager::queue_adhoc_task($task);

        upgrade_plugin_savepoint(true, 2022082200, 'assignfeedback', 'editpdf');
    }

    // Automatically generated Moodle v4.1.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2022112801) {
        $task = new \assignfeedback_editpdf\task\remove_orphaned_editpdf_files();
        \core\task\manager::queue_adhoc_task($task);

        upgrade_plugin_savepoint(true, 2022112801, 'assignfeedback', 'editpdf');
    }

    // Automatically generated Moodle v4.2.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
