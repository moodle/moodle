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
 * Keeps track of upgrades to the enrol_flatfile plugin
 *
 * @package    enrol
 * @subpackage flatfile
 * @copyright  2010 Aparup Banerjee <aparup@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_enrol_flatfile_upgrade($oldversion) {
    global $CFG, $DB;

    $result = TRUE;
    $dbman = $DB->get_manager();

    if ($oldversion < 2010091400) {

        // Define table enrol_flatfile to be created
        $table = new xmldb_table('enrol_flatfile');

        // Adding fields to table enrol_flatfile
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('action', XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null);
        $table->add_field('roleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('timestart', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('timeend', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

        // Adding keys to table enrol_flatfile
        $table->add_key('id', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('courseid-id', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));
        $table->add_key('userid-id', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));
        $table->add_key('roleid-id', XMLDB_KEY_FOREIGN, array('roleid'), 'role', array('id'));

        // Conditionally launch create table for enrol_flatfile
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // flatfile savepoint reached
        upgrade_plugin_savepoint(true, 2010091400, 'enrol', 'flatfile');
    }


    return $result;
}
