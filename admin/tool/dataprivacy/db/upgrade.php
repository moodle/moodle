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
 * tool_dataprivacy plugin upgrade code
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Function to upgrade tool_dataprivacy.
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_tool_dataprivacy_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // Automatically generated Moodle v3.9.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2020061501) {

        // Define field commentsformat to be added to tool_dataprivacy_request.
        $table = new xmldb_table('tool_dataprivacy_request');
        $field = new xmldb_field('commentsformat', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'comments');

        // Conditionally launch add field commentsformat.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field dpocommentformat to be added to tool_dataprivacy_request.
        $field = new xmldb_field('dpocommentformat', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'dpocomment');

        // Conditionally launch add field dpocommentformat.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field systemapproved to be added to tool_dataprivacy_request.
        $field = new xmldb_field('systemapproved', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'dpocommentformat');

        // Conditionally launch add field systemapproved.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Dataprivacy savepoint reached.
        upgrade_plugin_savepoint(true, 2020061501, 'tool', 'dataprivacy');
    }

    // Automatically generated Moodle v4.0.0 release upgrade line.
    // Put any upgrade step following this.
    if ($oldversion < 2022053000) {

        // Define key usermodified (foreign) to be added to tool_dataprivacy_purposerole.
        $table = new xmldb_table('tool_dataprivacy_purposerole');
        $key = new xmldb_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);

        // Launch add key usermodified.
        $dbman->add_key($table, $key);

        // Dataprivacy savepoint reached.
        upgrade_plugin_savepoint(true, 2022053000, 'tool', 'dataprivacy');
    }

    // Automatically generated Moodle v4.1.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.2.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
