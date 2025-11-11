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
 * Upgrade script for tool_recyclebin.
 *
 * @package   tool_recyclebin
 * @copyright The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade the plugin.
 *
 * @param int $oldversion
 * @return bool always true
 */
function xmldb_tool_recyclebin_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // Automatically generated Moodle v4.4.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.5.0 release upgrade line.
    // Put any upgrade step following this.


    // Automatically generated Moodle v5.0.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2025041401) {

        // Changing precision of field shortname on table tool_recyclebin_category to (1333).
        $table = new xmldb_table('tool_recyclebin_category');
        $field = new xmldb_field('shortname', XMLDB_TYPE_CHAR, '1333', null, XMLDB_NOTNULL, null, null, 'categoryid');

        // Launch change of precision for field shortname.
        $dbman->change_field_precision($table, $field);

        // Changing precision of field fullname on table tool_recyclebin_category to (1333).
        $table = new xmldb_table('tool_recyclebin_category');
        $field = new xmldb_field('fullname', XMLDB_TYPE_CHAR, '1333', null, XMLDB_NOTNULL, null, null, 'shortname');

        // Launch change of precision for field fullname.
        $dbman->change_field_precision($table, $field);

        // Changing precision of field name on table tool_recyclebin_course to (1333).
        $table = new xmldb_table('tool_recyclebin_course');
        $field = new xmldb_field('name', XMLDB_TYPE_CHAR, '1333', null, null, null, null, 'module');

        // Launch change of precision for field name.
        $dbman->change_field_precision($table, $field);

        // Recyclebin savepoint reached.
        upgrade_plugin_savepoint(true, 2025041401, 'tool', 'recyclebin');
    }

    // Automatically generated Moodle v5.1.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
