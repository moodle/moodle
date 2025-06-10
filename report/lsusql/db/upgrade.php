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
 * Upgrade code for report_lsusql.
 *
 * @package report_lsusql
 * @copyright 2015 The Open University
 * @copyright 2022 Louisiana State University
 * @copyright 2022 Robert Russo
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade code for report_lsusql.
 *
 * @param string $oldversion the version we are upgrading from.
 * @return bool true on success.
 */
function xmldb_report_lsusql_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

  // Add more DB columns for further usefulness.
    if ($oldversion < 2022063000) {

        // Define field userlimit to be added to report_lsusql_queries.
        $table = new xmldb_table('report_lsusql_queries');
        $field = new xmldb_field('userlimit', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'customdir');

        // Define field donotescape to be added to report_lsusql_queries.
        $table2 = new xmldb_table('report_lsusql_queries');
        $field2 = new xmldb_field('donotescape', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, '0', 'queryparams');

        // Add the userlimit field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add the donotescape field.
        if (!$dbman->field_exists($table2, $field2)) {
            $dbman->add_field($table2, $field2);
        }

        upgrade_plugin_savepoint(true, 2022063000, 'report', 'lsusql');
    }

    return true;
}
