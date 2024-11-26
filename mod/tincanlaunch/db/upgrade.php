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
 * This file keeps track of upgrades to the tincanlaunch module
 *
 * Sometimes, changes between versions involve alterations to database
 * structures and other major things that may break installations. The upgrade
 * function in this file will attempt to perform all the necessary actions to
 * upgrade your older installation to the current version. If there's something
 * it cannot do itself, it will tell you what you need to do.  The commands in
 * here will all be database-neutral, using the functions defined in DLL libraries.
 *
 * @package mod_tincanlaunch
 * @copyright  2013 Andrew Downes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Execute tincanlaunch upgrade from the given old version
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_tincanlaunch_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2018103000) {
        $table = new xmldb_table('tincanlaunch_credentials');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table, $continue = true, $feedback = true);
        }

        $table = new xmldb_table('tincanlaunch_lrs');
        $field = new xmldb_field('watershedlogin', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field, $continue = true, $feedback = true);
        }

        $field = new xmldb_field('watershedpass', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field, $continue = true, $feedback = true);
        }

        upgrade_mod_savepoint(true, 2018103000, 'tincanlaunch');
    }

    if ($oldversion < 2023040300) {
        $table = new xmldb_table('tincanlaunch');
        $field = new xmldb_field('tincansimplelaunchnav', XMLDB_TYPE_INTEGER, '1', null,
            XMLDB_NOTNULL, null, 0, 'tincanmultipleregs');

        // Add field tincansimplelaunchnav.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2023040300, 'tincanlaunch');
    }

    // Final return of upgrade result (true, all went good) to Moodle.
    return true;
}
