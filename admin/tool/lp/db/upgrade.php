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
 * Leaning plan upgrade steps.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade the plugin.
 *
 * @param int $oldversion
 * @return bool always true
 */
function xmldb_tool_lp_upgrade($oldversion) {
    global $CFG, $DB, $OUTPUT;

    $dbman = $DB->get_manager();

    if ($oldversion < 2015052403) {

        // Define index idnumber (unique) to be added to tool_lp_competency_framework.
        $table = new xmldb_table('tool_lp_competency_framework');
        $index = new xmldb_index('idnumber', XMLDB_INDEX_UNIQUE, array('idnumber'));

        // Conditionally launch add index idnumber.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Lp savepoint reached.
        upgrade_plugin_savepoint(true, 2015052403, 'tool', 'lp');
    }

    if ($oldversion < 2015052404) {

        // Define index idnumberframework (unique) to be added to tool_lp_competency.
        $table = new xmldb_table('tool_lp_competency');
        $index = new xmldb_index('idnumberframework', XMLDB_INDEX_UNIQUE, array('competencyframeworkid', 'idnumber'));

        // Conditionally launch add index idnumberframework.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Lp savepoint reached.
        upgrade_plugin_savepoint(true, 2015052404, 'tool', 'lp');
    }

    return true;
}
