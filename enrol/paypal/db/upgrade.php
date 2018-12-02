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
 * This file keeps track of upgrades to the paypal enrolment plugin
 *
 * @package    enrol_paypal
 * @copyright  2010 Eugene Venter
 * @author     Eugene Venter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installation to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the methods of database_manager class
//
// Please do not forget to use upgrade_set_timeout()
// before any action that may take longer time to finish.

defined('MOODLE_INTERNAL') || die();

function xmldb_enrol_paypal_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // Automatically generated Moodle v3.2.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.3.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.4.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.5.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2018053000) {

        // Define field instanceid to be added to enrol_paypal.
        // For some reason, some Moodle instances that are upgraded from old versions do not have this field.
        $table = new xmldb_table('enrol_paypal');
        $field = new xmldb_field('instanceid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'userid');

        // Conditionally launch add field instanceid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Paypal savepoint reached.
        upgrade_plugin_savepoint(true, 2018053000, 'enrol', 'paypal');
    }

    if ($oldversion < 2018062500) {

        // Define key courseid (foreign) to be added to enrol_paypal.
        $table = new xmldb_table('enrol_paypal');
        $key = new xmldb_key('courseid', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));

        // Launch add key courseid.
        $dbman->add_key($table, $key);

        // Paypal savepoint reached.
        upgrade_plugin_savepoint(true, 2018062500, 'enrol', 'paypal');
    }

    if ($oldversion < 2018062501) {

        // Define key userid (foreign) to be added to enrol_paypal.
        $table = new xmldb_table('enrol_paypal');
        $key = new xmldb_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

        // Launch add key userid.
        $dbman->add_key($table, $key);

        // Paypal savepoint reached.
        upgrade_plugin_savepoint(true, 2018062501, 'enrol', 'paypal');
    }

    if ($oldversion < 2018062502) {

        // Define key instanceid (foreign) to be added to enrol_paypal.
        $table = new xmldb_table('enrol_paypal');
        $key = new xmldb_key('instanceid', XMLDB_KEY_FOREIGN, array('instanceid'), 'enrol', array('id'));

        // Launch add key instanceid.
        $dbman->add_key($table, $key);

        // Paypal savepoint reached.
        upgrade_plugin_savepoint(true, 2018062502, 'enrol', 'paypal');
    }

    if ($oldversion < 2018062503) {

        $table = new xmldb_table('enrol_paypal');

        // Define index business (not unique) to be added to enrol_paypal.
        $index = new xmldb_index('business', XMLDB_INDEX_NOTUNIQUE, array('business'));

        // Conditionally launch add index business.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Define index receiver_email (not unique) to be added to enrol_paypal.
        $index = new xmldb_index('receiver_email', XMLDB_INDEX_NOTUNIQUE, array('receiver_email'));

        // Conditionally launch add index receiver_email.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Paypal savepoint reached.
        upgrade_plugin_savepoint(true, 2018062503, 'enrol', 'paypal');
    }

    // Automatically generated Moodle v3.6.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
