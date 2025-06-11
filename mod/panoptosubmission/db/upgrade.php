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
 * Panopto Student Submission upgrade script.
 *
 * @package mod_panoptosubmission
 * @copyright  Panopto 2021
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * This is ran when the plugin is upgraded
 * @param string $oldversion the version previously installed
 * @return whether the upgrade was a success
 */
function xmldb_panoptosubmission_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2022090744) {
        // Changing type of field grade on table panoptosubmission_submission from int to number.
        $table = new xmldb_table('panoptosubmission_submission');
        $field = new xmldb_field('grade', XMLDB_TYPE_NUMBER, '11,2', null, XMLDB_NOTNULL, null, 0, 'thumbnailheight');

        // Launch change of type for field grade.
        $dbman->change_field_type($table, $field);

        // Changing type of field grade on table panoptosubmission from int to number.
        $table = new xmldb_table('panoptosubmission');
        $field = new xmldb_field('grade', XMLDB_TYPE_NUMBER, '10,2', null, XMLDB_NOTNULL, null, 0, 'emailteachers');

        // Launch change of type for field grade.
        $dbman->change_field_type($table, $field);

        // Panopto savepoint reached.
        upgrade_mod_savepoint(true, 2022090744, 'panoptosubmission');
    }

    if ($oldversion < 2023012400) {
        // Define field cutofftime in the panoptosubmission table.
        $table = new xmldb_table('panoptosubmission');
        $field = new xmldb_field('cutofftime', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'timemodified');

        // Conditionally launch add field creator_mapping.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Panopto savepoint reached.
        upgrade_mod_savepoint(true, 2023012400, 'panoptosubmission');
    }

    if ($oldversion < 2023120100) {
        $table = new xmldb_table('panoptosubmission');

        // Update 'emailteachers' to 'sendnotifications'.
        $field = new xmldb_field('emailteachers', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', null);

        // Check if field exists.
        if ($dbman->field_exists($table, $field)) {
            // Rename field emailteachers to sendnotifications.
            $dbman->rename_field($table, $field, 'sendnotifications');
        }

        // Add sendlatenotifications field.
        $field = new xmldb_field('sendlatenotifications', XMLDB_TYPE_INTEGER,
            '2', null, XMLDB_NOTNULL, null, '0', 'sendnotifications');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add sendstudentnotifications field.
        $field = new xmldb_field('sendstudentnotifications', XMLDB_TYPE_INTEGER,
            '2', null, XMLDB_NOTNULL, null, '0', 'sendlatenotifications');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Update the savepoint.
        upgrade_mod_savepoint(true, 2023120100, 'panoptosubmission');
    }

    return true;
}
