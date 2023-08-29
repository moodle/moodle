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
 * OneRoster enrolment plugin upgrade.
 *
 * @package    enrol_oneroster
 * @copyright  2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Perfrom the OneRoster upgrade steps.
 *
 * @param   float $oldversion
 * @return  bool
 */
function xmldb_enrol_oneroster_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2020120700) {

        // Define table enrol_oneroster_user_map to be created.
        $table = new xmldb_table('enrol_oneroster_user_map');

        // Adding fields to table enrol_oneroster_user_map.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('parentid', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('mappedid', XMLDB_TYPE_CHAR, '255', null, null, null, null);

        // Adding keys to table enrol_oneroster_user_map.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table enrol_oneroster_user_map.
        $table->add_index('mappedid', XMLDB_INDEX_NOTUNIQUE, ['mappedid']);

        // Conditionally launch create table for enrol_oneroster_user_map.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Oneroster savepoint reached.
        upgrade_plugin_savepoint(true, 2020120700, 'enrol', 'oneroster');
    }

    return true;
}
