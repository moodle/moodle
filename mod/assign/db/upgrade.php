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

    // Automatically generated Moodle v4.2.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.3.0 release upgrade line.
    // Put any upgrade step following this.

    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

    if ($oldversion < 2023103000) {
        // Define field activity to be added to assign.
        $table = new xmldb_table('assign');
        $field = new xmldb_field(
            'markinganonymous',
            XMLDB_TYPE_INTEGER,
            '2',
            null,
            XMLDB_NOTNULL,
            null,
            '0',
            'markingallocation'
        );
        // Conditionally launch add field activity.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Assign savepoint reached.
        upgrade_mod_savepoint(true, 2023103000, 'assign');
    }

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

    return true;
}
