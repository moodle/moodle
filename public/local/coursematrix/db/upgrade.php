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
 * Database upgrade script for local_coursematrix.
 *
 * @package    local_coursematrix
 * @copyright  2024 Author Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade the local_coursematrix plugin.
 *
 * @param int $oldversion The version we are upgrading from.
 * @return bool
 */
function xmldb_local_coursematrix_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    // Add learning plans support (version 2024011400).
    if ($oldversion < 2024011400) {

        // Add learningplans field to existing local_coursematrix table.
        $table = new xmldb_table('local_coursematrix');
        $field = new xmldb_field('learningplans', XMLDB_TYPE_TEXT, null, null, null, null, null, 'courses');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Create local_coursematrix_plans table.
        $table = new xmldb_table('local_coursematrix_plans');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Create local_coursematrix_plan_courses table.
        $table = new xmldb_table('local_coursematrix_plan_courses');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('planid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('duedays', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '14');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('planid', XMLDB_KEY_FOREIGN, ['planid'], 'local_coursematrix_plans', ['id']);
        $table->add_key('courseid', XMLDB_KEY_FOREIGN, ['courseid'], 'course', ['id']);
        $table->add_index('planid_sortorder', XMLDB_INDEX_NOTUNIQUE, ['planid', 'sortorder']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Create local_coursematrix_user_plans table.
        $table = new xmldb_table('local_coursematrix_user_plans');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('planid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('currentcourseid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('startdate', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('status', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'active');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
        $table->add_key('planid', XMLDB_KEY_FOREIGN, ['planid'], 'local_coursematrix_plans', ['id']);
        $table->add_key('currentcourseid', XMLDB_KEY_FOREIGN, ['currentcourseid'], 'course', ['id']);
        $table->add_index('userid_planid', XMLDB_INDEX_UNIQUE, ['userid', 'planid']);
        $table->add_index('status', XMLDB_INDEX_NOTUNIQUE, ['status']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Create local_coursematrix_reminders table.
        $table = new xmldb_table('local_coursematrix_reminders');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('planid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('daysbefore', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '7');
        $table->add_field('enabled', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('planid', XMLDB_KEY_FOREIGN, ['planid'], 'local_coursematrix_plans', ['id']);
        $table->add_index('planid_daysbefore', XMLDB_INDEX_UNIQUE, ['planid', 'daysbefore']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Coursematrix savepoint reached.
        upgrade_plugin_savepoint(true, 2024011400, 'local', 'coursematrix');
    }

    return true;
}
