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
 * Calculated question type upgrade code.
 *
 * @package    search_simpledb
 * @copyright  2022 Renaud Lemaire {@link http://www.cblue.be}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade code for the simpledb search engine.
 * @param int $oldversion the version we are upgrading from.
 */
function xmldb_search_simpledb_upgrade($oldversion = 0) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2022050400) {

        $table = new xmldb_table('search_simpledb_index');

        // Define index areaid (not unique) to be added to search_simpledb_index.
        $index = new xmldb_index('contextid', XMLDB_INDEX_NOTUNIQUE, ['contextid']);

        // Conditionally launch add index contextid.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Define index courseid (not unique) to be added to search_simpledb_index.
        $index = new xmldb_index('courseid', XMLDB_INDEX_NOTUNIQUE, ['courseid']);

        // Conditionally launch add index courseid.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Define index areaid (not unique) to be added to search_simpledb_index.
        $index = new xmldb_index('areaid', XMLDB_INDEX_NOTUNIQUE, ['areaid']);

        // Conditionally launch add index areaid.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Simpledb savepoint reached.
        upgrade_plugin_savepoint(true, 2022050400, 'search', 'simpledb');
    }

    // Automatically generated Moodle v4.1.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
