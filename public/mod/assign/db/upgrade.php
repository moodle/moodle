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
 * Upgrade code for install
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * upgrade this assignment instance - this function could be skipped but it will be needed later
 * @param int $oldversion The old version of the assign module
 * @return bool
 */
function xmldb_assign_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

    // Automatically generated Moodle v4.4.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2024042201) {
        // The 'Never' ('none') option for the additional attempts (attemptreopenmethod) setting is no longer supported
        // and needs to be updated in all relevant instances.

        // The default value for the 'attemptreopenmethod' field in the 'assign' database table is currently set to 'none',
        // This needs to be updated to 'untilpass' to ensure the system functions correctly. Additionally, the default
        // value for the 'maxattempts' field needs to be changed to '1' to prevent multiple attempts and maintain the
        // original behavior.
        $table = new xmldb_table('assign');
        $attemptreopenmethodfield = new xmldb_field('attemptreopenmethod', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL,
            null, 'untilpass');
        $maxattemptsfield = new xmldb_field('maxattempts', XMLDB_TYPE_INTEGER, '6', null, XMLDB_NOTNULL,
            null, '1');
        $dbman->change_field_default($table, $attemptreopenmethodfield);
        $dbman->change_field_default($table, $maxattemptsfield);

        // If the current value for the 'attemptreopenmethod' global configuration in the assignment is set to 'none'.
        if (get_config('assign', 'attemptreopenmethod') == 'none') {
            // Reset the value to 'untilpass'.
            set_config('attemptreopenmethod', 'untilpass', 'assign');
            // Also, setting the value for the 'maxattempts' global config in the assignment to '1' ensures that the
            // original behaviour is preserved by disallowing any additional attempts by default.
            set_config('maxattempts', 1, 'assign');
        }

        // Update all the current assignment instances that have their 'attemptreopenmethod' set to 'none'.
        // By setting 'maxattempts' to 1, additional attempts are disallowed, preserving the original behavior.
        $DB->execute(
            'UPDATE {assign}
                    SET attemptreopenmethod = :newattemptreopenmethod,
                        maxattempts = :maxattempts
                  WHERE attemptreopenmethod = :oldattemptreopenmethod',
            [
                'newattemptreopenmethod' => 'untilpass',
                'maxattempts' => 1,
                'oldattemptreopenmethod' => 'none',
            ]
        );

        // Assign savepoint reached.
        upgrade_mod_savepoint(true, 2024042201, 'assign');
    }

    // Automatically generated Moodle v4.5.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2024121801) {

        // Define field gradepenalty to be added to assign.
        $table = new xmldb_table('assign');
        $field = new xmldb_field('gradepenalty', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'submissionattachments');

        // Conditionally launch add field gradepenalty.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define index gradepenalty (not unique) to be added to assign.
        $index = new xmldb_index('gradepenalty', XMLDB_INDEX_NOTUNIQUE, ['gradepenalty']);

        // Conditionally launch add index gradepenalty.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Define field penalty to be added to assign_grades.
        $table = new xmldb_table('assign_grades');
        $field = new xmldb_field('penalty', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, '0', 'grade');

        // Conditionally launch add field penalty.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Assign savepoint reached.
        upgrade_mod_savepoint(true, 2024121801, 'assign');
    }

    // Automatically generated Moodle v5.0.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v5.1.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2026022300) {
        // Changing precision of field name on table assign to (1333).
        $table = new xmldb_table('assign');
        $field = new xmldb_field('name', XMLDB_TYPE_CHAR, '1333', null, XMLDB_NOTNULL, null, null, 'course');

        // Launch change of precision for field name.
        $dbman->change_field_precision($table, $field);

        // Assign savepoint reached.
        upgrade_mod_savepoint(true, 2026022300, 'assign');
    }

    if ($oldversion < 2026030900) {
        // Define field markercount to be added to assign.
        $table = new xmldb_table('assign');
        $field = new xmldb_field('markercount', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1', 'markingallocation');
        // Conditionally launch add field markercount.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field multimarkmethod to be added to assign.
        $field = new xmldb_field('multimarkmethod', XMLDB_TYPE_CHAR, '10', null, null, null, null, 'markercount');
        // Conditionally launch add field multimarkmethod.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define table assign_mark to be created.
        $table = new xmldb_table('assign_mark');

        // Adding fields to table assign_mark.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('assignment', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'id');
        $table->add_field('gradeid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'assignment');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'gradeid');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'timecreated');
        $table->add_field('marker', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'timemodified');
        $table->add_field('mark', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null, 'marker');
        $table->add_field('workflowstate', XMLDB_TYPE_CHAR, '20', null, null, null, null, 'mark');

        // Adding keys to table assign_grades_mark.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('assignment', XMLDB_KEY_FOREIGN, ['assignment'], 'assign', ['id']);
        $table->add_key('gradeid', XMLDB_KEY_FOREIGN, ['gradeid'], 'assign_grades', ['id']);
        $table->add_key('marker', XMLDB_KEY_FOREIGN, ['marker'], 'user', ['id']);

        // Conditionally launch create table for assign_mark.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table assign_allocated_marker to be created.
        $table = new xmldb_table('assign_allocated_marker');

        // Adding fields to table assign_allocated_marker.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('student', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'id');
        $table->add_field('assignment', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'student');
        $table->add_field('marker', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'assignment');

        // Adding keys to table assign_allocated_marker.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('student', XMLDB_KEY_FOREIGN, ['student'], 'user', ['id']);
        $table->add_key('assignment', XMLDB_KEY_FOREIGN, ['assignment'], 'assign', ['id']);
        $table->add_key('marker', XMLDB_KEY_FOREIGN, ['marker'], 'user', ['id']);

        // Conditionally launch create table for assign_allocated_marker.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define field allocatedmarker to be dropped from assign_user_flags.
        $table = new xmldb_table('assign_user_flags');
        $field = new xmldb_field('allocatedmarker');

        // Populate assign_allocated_marker before the allocatedmarker field is dropped.
        if ($dbman->field_exists($table, $field)) {
            $DB->execute(
                "INSERT INTO {assign_allocated_marker} (assignment, student, marker)
                      SELECT assignment, userid, allocatedmarker
                        FROM {assign_user_flags}"
            );
            $dbman->drop_field($table, $field);
        }

        // Define field multimarkrounding to be added to assign.
        $table = new xmldb_table('assign');
        $field = new xmldb_field('multimarkrounding', XMLDB_TYPE_INTEGER, '2', null, null, null, null, 'multimarkmethod');
        // Conditionally launch add field multimarkrounding.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Assign savepoint reached.
        upgrade_mod_savepoint(true, 2026030900, 'assign');
    }

    if ($oldversion < 2026031300) {
        // Define field reason to be added to assign_overrides.
        $table = new xmldb_table('assign_overrides');
        $field = new xmldb_field('reason', XMLDB_TYPE_TEXT, null, null, null, null, null, 'timelimit');

        // Conditionally launch add field reason.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field reasonformat to be added to assign_overrides.
        $formatfield = new xmldb_field('reasonformat', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'reason');

        // Conditionally launch add field reasonformat.
        if (!$dbman->field_exists($table, $formatfield)) {
            $dbman->add_field($table, $formatfield);
        }

        // Assign savepoint reached.
        upgrade_mod_savepoint(true, 2026031300, 'assign');
    }

    // Automatically generated Moodle v5.2.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2026082000) {
        // A bug in override_manager::save_override() could clear a group override's calendar
        // priority when it was edited, causing both the override and the default due date to
        // show at once. Repair any events already affected.
        $sql = "SELECT e.id AS id, o.sortorder AS priority
                  FROM {assign_overrides} o
                  JOIN {event} e ON (e.modulename = 'assign' AND e.instance = o.assignid AND e.groupid = o.groupid)
                 WHERE o.groupid IS NOT NULL
                       AND e.priority IS NULL
                       AND o.sortorder IS NOT NULL";
        $affectedrs = $DB->get_recordset_sql($sql);
        foreach ($affectedrs as $record) {
            $DB->set_field('event', 'priority', $record->priority, ['id' => $record->id]);
        }
        $affectedrs->close();

        // Assign savepoint reached.
        upgrade_mod_savepoint(true, 2026082000, 'assign');
    }

    if ($oldversion < 2026082700) {
        // Define field optionalmarkercount to be added to assign.
        $table = new xmldb_table('assign');
        $field = new xmldb_field('optionalmarkercount', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'markercount');

        // Conditionally launch add field optionalmarkercount.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field optional to be added to assign_allocated_marker.
        $table = new xmldb_table('assign_allocated_marker');
        $field = new xmldb_field('optional', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'marker');

        // Conditionally launch add field optional.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field enabled to be added to assign_allocated_marker.
        $table = new xmldb_table('assign_allocated_marker');
        $field = new xmldb_field('enabled', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'optional');

        // Conditionally launch add field enabled.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Assign savepoint reached.
        upgrade_mod_savepoint(true, 2026082700, 'assign');
    }

    return true;
}
