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
 * @copyright  2012 Petr Skoda {@link http://skodak.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_enrol_self_upgrade($oldversion) {
    global $CFG, $DB, $OUTPUT;

    $dbman = $DB->get_manager();

    // Moodle v2.3.0 release upgrade line
    // Put any upgrade step following this

    if ($oldversion < 2012101400) {
        // Set default expiry threshold to 1 day.
        $DB->execute("UPDATE {enrol} SET expirythreshold = 86400 WHERE enrol = 'self' AND expirythreshold = 0");
        upgrade_plugin_savepoint(true, 2012101400, 'enrol', 'self');
    }

    if ($oldversion < 2012120600) {
        // Enable new self enrolments everywhere.
        $DB->execute("UPDATE {enrol} SET customint6 = 1 WHERE enrol = 'self'");
        upgrade_plugin_savepoint(true, 2012120600, 'enrol', 'self');
    }


    // Moodle v2.4.0 release upgrade line
    // Put any upgrade step following this


    // Moodle v2.5.0 release upgrade line.
    // Put any upgrade step following this.


    // Moodle v2.6.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2013112100) {
        // Set customint1 (group enrolment key) to 0 if it was not set (null).
        $DB->execute("UPDATE {enrol} SET customint1 = 0 WHERE enrol = 'self' AND customint1 IS NULL");
        upgrade_plugin_savepoint(true, 2013112100, 'enrol', 'self');
    }

    // Moodle v2.7.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.8.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.9.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v3.0.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2015111601) {
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
        upgrade_plugin_savepoint(true, 2015111601, 'enrol', 'self');
    }

    // Moodle v3.1.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}


