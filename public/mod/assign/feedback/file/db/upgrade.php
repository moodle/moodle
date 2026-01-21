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
 * Upgrade code for the feedback_file module.
 *
 * @package   assignfeedback_file
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Stub for upgrade code
 * @param int $oldversion
 * @return bool
 */
function xmldb_assignfeedback_file_upgrade($oldversion) {
    // Automatically generated Moodle v4.4.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.5.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v5.0.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v5.1.0 release upgrade line.
    // Put any upgrade step following this.
    global $DB;
    $dbman = $DB->get_manager();
    if ($oldversion < 2026042001) {
        // Define field mark to be added to assignfeedback_file.
        $table = new xmldb_table('assignfeedback_file');
        $field = new xmldb_field('mark', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'grade');

        // Conditionally launch add field mark.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Comments savepoint reached.
        upgrade_plugin_savepoint(true, 2026042001, 'assignfeedback', 'file');
    }

    // Automatically generated Moodle v5.2.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
