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
 * Upgrade code for the feedback_editpdfplus module.
 *
 * @package   assignfeedback_editpdfplus
 * @copyright  2016 Université de Lausanne
 * The code is based on mod/assign/feedback/editpdf/db/upgrade.php by Jerome Mouneyrac.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

use assignfeedback_editpdfplus\bdd\type_tool;

/**
 * EditPDFplus upgrade code
 * @param int $oldversion
 * @return bool
 */
function xmldb_assignfeedback_editpdfplus_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2016021600) {

        // Define table assignfeedback_editpdfplus_queue to be created.
        $table = new xmldb_table('assignfeedback_editpp_queue');

        // Adding fields to table assignfeedback_editpp_queue.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('submissionid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('submissionattempt', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table assignfeedback_editpp_queue.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for assignfeedback_editpp_queue.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Editpdfplus savepoint reached.
        upgrade_plugin_savepoint(true, 2016021600, 'assignfeedback', 'editpdfplus');
    }

    if ($oldversion < 2017022700) {

        // Get orphaned, duplicate files and delete them.
        $fs = get_file_storage();
        $sqllike = $DB->sql_like("filename", "?");
        $where = "component='assignfeedback_editpdfplus' AND filearea = 'importhtml' AND " . $sqllike;
        $filerecords = $DB->get_records_select("files", $where, ["onlinetext-%"]);
        foreach ($filerecords as $filerecord) {
            $file = $fs->get_file_instance($filerecord);
            $file->delete();
        }

        // Editpdfplus savepoint reached.
        upgrade_plugin_savepoint(true, 2017022700, 'assignfeedback', 'editpdfplus');
    }

    if ($oldversion < 2017071202) {

        $table = new xmldb_table('assignfeedback_editpp_typet');
        $field = new xmldb_field('configurable', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 1);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $record1 = $DB->get_record('assignfeedback_editpp_typet', array('label' => 'highlight'), '*', MUST_EXIST);
        $typeTool1 = new type_tool($record1);
        $typeTool1->configurable = 0;
        $DB->update_record('assignfeedback_editpp_typet', $typeTool1);
        $record2 = $DB->get_record('assignfeedback_editpp_typet', array('label' => 'oval'), '*', MUST_EXIST);
        $typeTool2 = new type_tool($record2);
        $typeTool2->configurable = 0;
        $DB->update_record('assignfeedback_editpp_typet', $typeTool2);
        $record3 = $DB->get_record('assignfeedback_editpp_typet', array('label' => 'rectangle'), '*', MUST_EXIST);
        $typeTool3 = new type_tool($record3);
        $typeTool3->configurable = 0;
        $DB->update_record('assignfeedback_editpp_typet', $typeTool3);
        $record4 = $DB->get_record('assignfeedback_editpp_typet', array('label' => 'line'), '*', MUST_EXIST);
        $typeTool4 = new type_tool($record4);
        $typeTool4->configurable = 0;
        $DB->update_record('assignfeedback_editpp_typet', $typeTool4);
        $record5 = $DB->get_record('assignfeedback_editpp_typet', array('label' => 'pen'), '*', MUST_EXIST);
        $typeTool5 = new type_tool($record5);
        $typeTool5->configurable = 0;
        $DB->update_record('assignfeedback_editpp_typet', $typeTool5);

        // Editpdfplus savepoint reached.
        upgrade_plugin_savepoint(true, 2017071202, 'assignfeedback', 'editpdfplus');
    }

    if ($oldversion < 2017081306) {
        $sql = "UPDATE {assignfeedback_editpp_typet}
                   SET color = :htmlcolor
                 WHERE color = :textcolor";
        // Update query params.
        $params = [
            'htmlcolor' => '#FF0000',
            'textcolor' => 'red'
        ];
        // Execute DB update for assign instances.
        $DB->execute($sql, $params);
        $sql = "UPDATE {assignfeedback_editpp_tool}
                   SET colors = :htmlcolor
                 WHERE colors = :textcolor";
        // Update query params.
        $params = [
            'htmlcolor' => '#FFA500',
            'textcolor' => 'orange'
        ];
        // Execute DB update for assign instances.
        $DB->execute($sql, $params);
        $sql = "UPDATE {assignfeedback_editpp_tool}
                   SET colors = :htmlcolor
                 WHERE colors = :textcolor";
        // Update query params.
        $params = [
            'htmlcolor' => '#008000',
            'textcolor' => 'green'
        ];
        // Execute DB update for assign instances.
        $DB->execute($sql, $params);
        $sql = "UPDATE {assignfeedback_editpp_tool}
                   SET colors = :htmlcolor
                 WHERE colors = :textcolor";
        // Update query params.
        $params = [
            'htmlcolor' => '#0000FF',
            'textcolor' => 'blue'
        ];
        // Execute DB update for assign instances.
        $DB->execute($sql, $params);

        // Editpdfplus savepoint reached.
        upgrade_plugin_savepoint(true, 2017081306, 'assignfeedback', 'editpdfplus');
    }

    if ($oldversion < 2017081601) {
        $table = new xmldb_table('assignfeedback_editpp_typet');
        $field = new xmldb_field('configurable_cartridge', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 1);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('configurable_cartridge_color', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 1);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('configurable_color', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 1);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('configurable_texts', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 1);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('configurable_question', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 1);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $sql = "UPDATE {assignfeedback_editpp_typet}
                   SET configurable_cartridge = 0,
                   configurable_cartridge_color = 0,
                   configurable_texts = 0,
                   configurable_question = 0
                 WHERE id = 3";
        // Update query params.
        $params = [];
        // Execute DB update for assign instances.
        $DB->execute($sql, $params);

        $sql = "UPDATE {assignfeedback_editpp_typet}
                   SET configurable_cartridge_color = 0,
                   configurable_color = 0
                 WHERE id = 4";
        // Execute DB update for assign instances.
        $DB->execute($sql, []);

        $sql = "UPDATE {assignfeedback_editpp_typet}
                   SET configurable_color = 0,
                   configurable_texts = 0
                 WHERE id = 7";
        // Execute DB update for assign instances.
        $DB->execute($sql, []);

        $sql = "UPDATE {assignfeedback_editpp_typet}
                   SET configurable_color = 0
                 WHERE id = 6";
        // Execute DB update for assign instances.
        $DB->execute($sql, []);

        // Editpdfplus savepoint reached.
        upgrade_plugin_savepoint(true, 2017081601, 'assignfeedback', 'editpdfplus');
    }

    if ($oldversion < 2018091203) {
        $sql = "UPDATE {assignfeedback_editpp_typet}
                   SET id = 12
                 WHERE id = 11 and label = 'highlight'";
        $DB->execute($sql, []);
        $sql = "UPDATE {assignfeedback_editpp_typet}
                   SET id = 11
                 WHERE id = 10 and label = 'oval'";
        $DB->execute($sql, []);
        $sql = "UPDATE {assignfeedback_editpp_typet}
                   SET id = 10
                 WHERE id = 9 and label = 'rectangle'";
        $DB->execute($sql, []);
        $sql = "UPDATE {assignfeedback_editpp_typet}
                   SET id = 9
                 WHERE id = 8 and label = 'line'";
        $DB->execute($sql, []);
        $sql = "UPDATE {assignfeedback_editpp_typet}
                   SET id = 8
                 WHERE id = 7 and label = 'pen'";
        $DB->execute($sql, []);
        $sql = "UPDATE {assignfeedback_editpp_typet}
                   SET id = 7
                 WHERE id = 6 and label = 'commentplus'";
        $DB->execute($sql, []);
        $sql = "UPDATE {assignfeedback_editpp_typet}
                   SET id = 6
                 WHERE id = 5 and label = 'stampcomment'";
        $DB->execute($sql, []);
        $sql = "UPDATE {assignfeedback_editpp_typet}
                   SET id = 5
                 WHERE id = 4 and label = 'verticalline'";
        $DB->execute($sql, []);
        $sql = "UPDATE {assignfeedback_editpp_typet}
                   SET id = 4
                 WHERE id = 3 and label = 'frame'";
        $DB->execute($sql, []);
        $sql = "UPDATE {assignfeedback_editpp_typet}
                   SET id = 3
                 WHERE id = 2 and label = 'stampplus'";
        $DB->execute($sql, []);
        $DB->get_manager()->reset_sequence('assignfeedback_editpp_typet');

        $sql = "UPDATE {assignfeedback_editpp_tool}
                   SET type = 6
                 WHERE id > 13 and type = 5";
        $DB->execute($sql, []);
        $sql = "UPDATE {assignfeedback_editpp_tool}
                   SET type = 5
                 WHERE id > 13 and type = 4";
        $DB->execute($sql, []);
        $sql = "UPDATE {assignfeedback_editpp_tool}
                   SET type = 4
                 WHERE id > 13 and type = 3";
        $DB->execute($sql, []);
        $sql = "UPDATE {assignfeedback_editpp_tool}
                   SET type = 3
                 WHERE id > 14 and type = 2";
        $DB->execute($sql, []);

        // Editpdfplus savepoint reached.
        upgrade_plugin_savepoint(true, 2018091203, 'assignfeedback', 'editpdfplus');
    }

    if ($oldversion < 2019052400) {
        /* queue table */
        $table = new xmldb_table('assignfeedback_editpp_queue');
        $field = new xmldb_field('attemptedconversions', XMLDB_TYPE_INTEGER, '10', null,
                XMLDB_NOTNULL, null, 0, 'submissionattempt');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Attempts are removed from the queue after being processed, a duplicate row won't achieve anything productive.
        // So look for any duplicates and remove them so we can add a unique key.
        $sql = "SELECT MIN(id) as minid, submissionid, submissionattempt
                FROM {assignfeedback_editpp_queue}
                GROUP BY submissionid, submissionattempt
                HAVING COUNT(id) > 1";

        if ($duplicatedrows = $DB->get_recordset_sql($sql)) {
            foreach ($duplicatedrows as $row) {
                $DB->delete_records_select('assignfeedback_editpp_queue',
                        'submissionid = :submissionid AND submissionattempt = :submissionattempt AND id <> :minid', (array) $row);
            }
        }
        $duplicatedrows->close();

        // Define key submissionid-submissionattempt to be added to assignfeedback_editpdf_queue.
        $table = new xmldb_table('assignfeedback_editpp_queue');
        $key = new xmldb_key('submissionid-submissionattempt', XMLDB_KEY_UNIQUE, ['submissionid', 'submissionattempt']);

        $dbman->add_key($table, $key);

        /* rot table */
        $table = new xmldb_table('assignfeedback_editpp_rot');

        // Adding fields to table assignfeedback_editpp_rot.
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

        upgrade_plugin_savepoint(true, 2019052400, 'assignfeedback', 'editpdfplus');
    }

    if ($oldversion < 2019053100) {
        /* annotation table */
        $table = new xmldb_table('assignfeedback_editpp_annot');
        $field = new xmldb_field('pdfdisplay', XMLDB_TYPE_CHAR, '20', null,
                XMLDB_NOTNULL, null, 'footnote', 'parent_annot');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Editpdfplus savepoint reached.
        upgrade_plugin_savepoint(true, 2019053100, 'assignfeedback', 'editpdfplus');
    }

    if ($oldversion < 2019061201) {
        /* model annotation table */
        $table = new xmldb_table('assignfeedback_editpp_modax');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('user', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('axis', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('label', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Adding keys to table assignfeedback_editpp_modax.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('axis', XMLDB_KEY_FOREIGN, ['axis'], 'editpdfpp_axis', ['id']);

        // Adding indexes to table assignfeedback_editpp_modax.
        $table->add_index('useraxis', XMLDB_INDEX_UNIQUE, ['user', 'axis']);

        // Conditionally launch create table for assignfeedback_editpp_modax.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Editpdfplus savepoint reached.
        upgrade_plugin_savepoint(true, 2019061201, 'assignfeedback', 'editpdfplus');
    }

    if ($oldversion < 2019070100) {
        /* annotation table */
        $table = new xmldb_table('assignfeedback_editpp_annot');
        $field = new xmldb_field('draft_id', XMLDB_TYPE_INTEGER, '10', null, false);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Editpdfplus savepoint reached.
        upgrade_plugin_savepoint(true, 2019070100, 'assignfeedback', 'editpdfplus');
    }

    return true;
}
