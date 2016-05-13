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
 * Upgrade script for the scorm module.
 *
 * @package    mod_scorm
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * @global moodle_database $DB
 * @param int $oldversion
 * @return bool
 */
function xmldb_scorm_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2014072500) {

        // Define field autocommit to be added to scorm.
        $table = new xmldb_table('scorm');
        $field = new xmldb_field('autocommit', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'displayactivityname');

        // Conditionally launch add field autocommit.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Scorm savepoint reached.
        upgrade_mod_savepoint(true, 2014072500, 'scorm');
    }

    // Moodle v2.8.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2015031800) {

        // Check to see if this site has any AICC packages - if so set the aiccuserid to pass the username
        // so that the data remains consistent with existing packages.
        $alreadyset = $DB->record_exists('config_plugins', array('plugin' => 'scorm', 'name' => 'aiccuserid'));
        if (!$alreadyset) {
            $hasaicc = $DB->record_exists('scorm', array('version' => 'AICC'));
            if ($hasaicc) {
                set_config('aiccuserid', 0, 'scorm');
            } else {
                // We set the config value to hide this from upgrades as most users will not know what AICC is anyway.
                set_config('aiccuserid', 1, 'scorm');
            }
        }
        // Scorm savepoint reached.
        upgrade_mod_savepoint(true, 2015031800, 'scorm');
    }

    // Moodle v2.9.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2015091400) {
        $table = new xmldb_table('scorm');

        // Changing the default of field forcecompleted on table scorm to 0.
        $field = new xmldb_field('forcecompleted', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'maxattempt');
        // Launch change of default for field forcecompleted.
        $dbman->change_field_default($table, $field);

        // Changing the default of field displaycoursestructure on table scorm to 0.
        $field = new xmldb_field('displaycoursestructure', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'displayattemptstatus');
        // Launch change of default for field displaycoursestructure.
        $dbman->change_field_default($table, $field);

        // Scorm savepoint reached.
        upgrade_mod_savepoint(true, 2015091400, 'scorm');
    }

    // Moodle v3.0.0 release upgrade line.
    // Put any upgrade step following this.

    // MDL-50620 Add mastery override option.
    if ($oldversion < 2016021000) {
        $table = new xmldb_table('scorm');

        $field = new xmldb_field('masteryoverride', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'lastattemptlock');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2016021000, 'scorm');
    }

    return true;
}
