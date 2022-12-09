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
 * Standard log store upgrade.
 *
 * @package    logstore_standard
 * @copyright  2014 Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_logstore_standard_upgrade($oldversion) {
    global $CFG, $DB;

    require_once($CFG->libdir.'/db/upgradelib.php'); // Core Upgrade-related functions.

    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

    // Automatically generated Moodle v3.9.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.0.0 release upgrade line.
    // Put any upgrade step following this.
    if ($oldversion < 2022053000) {
        // Define index relateduserid (not unique) to be added to logstore_standard_log.
        $table = new xmldb_table('logstore_standard_log');

        // Launch add key userid.
        $key = new xmldb_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
        $dbman->add_key($table, $key);

        // Launch add key courseid.
        $key = new xmldb_key('courseid', XMLDB_KEY_FOREIGN, ['courseid'], 'course', ['id']);
        $dbman->add_key($table, $key);

        // Launch add key realuserid.
        $key = new xmldb_key('realuserid', XMLDB_KEY_FOREIGN, ['realuserid'], 'user', ['id']);
        $dbman->add_key($table, $key);

        // Launch add key relateduserid.
        $key = new xmldb_key('relateduserid', XMLDB_KEY_FOREIGN, ['relateduserid'], 'user', ['id']);
        $dbman->add_key($table, $key);

        // Standard savepoint reached.
        upgrade_plugin_savepoint(true, 2022053000, 'logstore', 'standard');
    }

    // Automatically generated Moodle v4.1.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
