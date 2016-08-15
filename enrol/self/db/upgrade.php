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
 * This file keeps track of upgrades to the self enrolment plugin
 *
 * @package    enrol_self
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_enrol_self_upgrade($oldversion) {
    global $CFG;

    // Moodle v2.8.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.9.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v3.0.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v3.1.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2016052301) {
        global $DB;
        // Get roles with manager archetype.
        $managerroles = get_archetype_roles('manager');
        if (!empty($managerroles)) {
            // Remove wrong CAP_PROHIBIT from self:holdkey.
            foreach ($managerroles as $role) {
                $DB->execute("DELETE
                                FROM {role_capabilities}
                               WHERE roleid = ? AND capability = ? AND permission = ?",
                        array($role->id, 'enrol/self:holdkey', CAP_PROHIBIT));
            }
        }
        upgrade_plugin_savepoint(true, 2016052301, 'enrol', 'self');

    }

    return true;
}
