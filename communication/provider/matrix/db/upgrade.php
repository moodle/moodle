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
 * Install steps for communication_matrix.
 *
 * @package    communication_matrix
 * @copyright  2023 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade procedures for the matrix plugin.
 *
 * @return bool
 */
function xmldb_communication_matrix_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();
    if ($oldversion < 2023060101) {
        $table = new xmldb_table('matrix_rooms');
        $field = new xmldb_field('topic', XMLDB_TYPE_CHAR, '255', null, false, false, null, 'roomid');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Plugin savepoint reached.
        upgrade_plugin_savepoint(true, 2023060101, 'communication', 'matrix');
    }

    if ($oldversion < 2023071900) {
        $table = new xmldb_table('matrix_rooms');
        $dbman->rename_table($table, 'matrix_room');

        // Plugin savepoint reached.
        upgrade_plugin_savepoint(true, 2023071900, 'communication', 'matrix');
    }

    // Automatically generated Moodle v4.3.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.4.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
