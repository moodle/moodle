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

    // Automatically generated Moodle v3.6.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2019010800) {
        // Define table assignfeedback_editpdf_rot to be created.
        $table = new xmldb_table('assignfeedback_editpdf_rot');

        // Adding fields to table assignfeedback_editpdf_rot.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('gradeid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('pageno', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('pathnamehash', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('isrotated', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('degree', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table assignfeedback_editpdf_rot.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('gradeid', XMLDB_KEY_FOREIGN, ['gradeid'], 'assign_grades', ['id']);

        // Adding indexes to table assignfeedback_editpdf_rot.
        $table->add_index('gradeid_pageno', XMLDB_INDEX_UNIQUE, ['gradeid', 'pageno']);

        // Conditionally launch create table for assignfeedback_editpdf_rot.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Editpdf savepoint reached.
        upgrade_plugin_savepoint(true, 2019010800, 'assignfeedback', 'editpdf');
    }

    // Automatically generated Moodle v3.7.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.8.0 release upgrade line.
    // Put any upgrade step following this.

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

    if ($oldversion < 2022041901) {
        // Conversion records need to be removed in order for conversions to restart.
        $DB->delete_records('file_conversion');

        // Schedule an adhoc task to fix existing stale conversions.
        $task = new \assignfeedback_editpdf\task\bump_submission_for_stale_conversions();
        \core\task\manager::queue_adhoc_task($task);

        upgrade_plugin_savepoint(true, 2022041901, 'assignfeedback', 'editpdf');
    }

    if ($oldversion < 2022041902) {
        $task = new \assignfeedback_editpdf\task\remove_orphaned_editpdf_files();
        \core\task\manager::queue_adhoc_task($task);

        upgrade_plugin_savepoint(true, 2022041902, 'assignfeedback', 'editpdf');
    }
    return true;
}
