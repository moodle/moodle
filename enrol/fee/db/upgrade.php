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
 * Upgrade steps for Enrolment on payment
 *
 * Documentation: {@link https://moodledev.io/docs/guides/upgrade}
 *
 * @package    enrol_fee
 * @category   upgrade
 * @copyright  Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Execute the plugin upgrade steps from the given old version.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_enrol_fee_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2025011300) {
        // Move the contents of the 'name' field into 'customchar1', it will be used as a internal description.
        // Starting from this version the 'name' will be visible to the potential students on the enrolment page.
        $DB->execute('UPDATE {enrol} SET customchar1 = name, name = ? '.
            'WHERE name IS NOT NULL AND name <> ? AND enrol = ?',
            ['', '', 'fee']);
        upgrade_plugin_savepoint(true, 2025011300, 'enrol', 'fee');
    }

    // Automatically generated Moodle v5.0.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
