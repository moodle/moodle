<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin upgrade steps are defined here.
 *
 * @package     mod_qbank
 * @category    upgrade
 * @copyright   2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author      Simon Adams <simon.adams@catalyst-eu.net>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Execute mod_qbank upgrade from the given old version.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_qbank_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // Automatically generated Moodle v5.0.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2025041401) {

        // Changing precision of field name on table qbank to (1333).
        $table = new xmldb_table('qbank');
        $field = new xmldb_field('name', XMLDB_TYPE_CHAR, '1333', null, XMLDB_NOTNULL, null, null, 'course');

        // Launch change of precision for field name.
        $dbman->change_field_precision($table, $field);

        // Qbank savepoint reached.
        upgrade_mod_savepoint(true, 2025041401, 'qbank');
    }

    // Automatically generated Moodle v5.1.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
