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
 * True/false question type upgrade code
 *
 * @package     qtype_truefalse
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Method to perform upgrade steps between versions
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_qtype_truefalse_upgrade(int $oldversion): bool {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2022071900) {

        // Define field showstandardinstruction to be added to question_truefalse.
        $table = new xmldb_table('question_truefalse');
        $field = new xmldb_field('showstandardinstruction', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1', 'falseanswer');

        // Conditionally launch add field showstandardinstruction.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Truefalse savepoint reached.
        upgrade_plugin_savepoint(true, 2022071900, 'qtype', 'truefalse');
    }

    // Automatically generated Moodle v4.1.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.2.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.3.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
