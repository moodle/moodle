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
 * Upgrade code for airnotifier message processor
 *
 * @package   message_airnotifier
 * @copyright 2012 Jerome Mouneyrac
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade code for the airnotifier message processor
 *
 * @param int $oldversion The version that we are upgrading from
 */
function xmldb_message_airnotifier_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();


    if ($oldversion < 2012070500.05) {

        // Define table user_devices to be created
        $table = new xmldb_table('airnotifier_user_devices');

        // Adding fields to table user_devices
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('appname', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('devicename', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('devicetype', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('deviceos', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('deviceosversion', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('devicebrand', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('devicenotificationtoken', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('deviceuid', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('enable', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');

        // Adding keys to table user_devices
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for user_devices
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        // Main savepoint reached
        upgrade_plugin_savepoint(true, 2012070500.05);
    }

    return true;
}

