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
 * Upgrade for onenote
 * @package    local_onenote
 * @author Vinayak (Vin) Bhalerao (v-vibhal@microsoft.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  Microsoft, Inc.
 */

/**
 * Upgrade the local_onenote plugin.
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_local_onenote_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2014110503) {
        // Define table to be created.
        $table = new xmldb_table('onenote_user_sections');

        // Adding fields to table.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('user_id', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('course_id', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('section_id', XMLDB_TYPE_CHAR, '255', null, null, null, null);

        // Adding keys to table.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Create table.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $dbman->create_table($table);

        // Define table onenote_assign_pages to be created.
        $table = new xmldb_table('onenote_assign_pages');

        // Adding fields to table.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('user_id', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('assign_id', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('submission_student_page_id', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('feedback_student_page_id', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('submission_teacher_page_id', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('feedback_teacher_page_id', XMLDB_TYPE_CHAR, '255', null, null, null, null);

        // Adding keys to table.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Create table.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }
        $dbman->create_table($table);

        // Onenote savepoint reached.
        upgrade_plugin_savepoint(true, 2014110503, 'local', 'onenote');
    }

    if ($oldversion < 2015111905) {
        // Define field submission_teacher_lastview to be added to onenote_assign_pages.
        $table = new xmldb_table('onenote_assign_pages');
        $field = new xmldb_field('teacher_lastviewed', XMLDB_TYPE_INTEGER, '10', null, null, null, null,
                'feedback_teacher_page_id');
        // Conditionally launch add field submission_teacher_lastview.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('student_lastmodified', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'teacher_lastviewed');
        // Conditionally launch add field submission_student_checksum.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Onenote savepoint reached.
        upgrade_plugin_savepoint(true, 2015111905, 'local', 'onenote');
    }

    if ($oldversion < 2016062002) {
        // Define table to be renamed.
        $table = new xmldb_table('onenote_user_sections');
        if ($dbman->table_exists($table)) {
            // Rename the table to use the correct Moodle naming convention.
            $dbman->rename_table($table, 'local_onenote_user_sections');
        }

        // Define table to be renamed.
        $table = new xmldb_table('onenote_assign_pages');
        if ($dbman->table_exists($table)) {
            // Rename the table to use the correct Moodle naming convention.
            $dbman->rename_table($table, 'local_onenote_assign_pages');
        }

        // Onenote savepoint reached.
        upgrade_plugin_savepoint(true, 2016062002, 'local', 'onenote');
    }

    if ($oldversion < 2021051716) {
        // Make sure teacher_lastviewed is an integer.
        $table = new xmldb_table('local_onenote_assign_pages');
        $field = new xmldb_field('teacher_lastviewed', XMLDB_TYPE_INTEGER, '10', null, null, null, null,
            'feedback_teacher_page_id');
        $dbman->change_field_type($table, $field);

        // Onenote savepoint reached.
        upgrade_plugin_savepoint(true, 2021051716, 'local', 'onenote');
    }

    return true;
}
