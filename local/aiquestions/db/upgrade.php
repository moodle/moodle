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
 * @package     local_aiquestions
 * @category    upgrade
 * @copyright   2023 Ruthy Salomon <ruthy.salomon@gmail.com> , Yedidia Klein <yedidia@openapp.co.il>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/upgradelib.php');

/**
 * Execute local_aiquestions upgrade from the given old version.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_local_aiquestions_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();
    if ($oldversion < 2023043001) {
        // Define table local_aiquestions to be created.
        $table = new xmldb_table('local_aiquestions');

        // Adding fields to table.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('user', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('gift', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('tries', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('uniqid', XMLDB_TYPE_CHAR, '40', null, null, null, null);
        $table->add_field('success', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('datecreated', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('datemodified', XMLDB_TYPE_INTEGER, '20', null, null, null, null);

        // Adding keys to table local_aiquestions.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for local_aiquestions.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        upgrade_plugin_savepoint(true, 2023043001, 'local', 'aiquestions');
    }

    if ($oldversion < 2023050501) {
        // Add numoftries local_aiquestions.
        $table = new xmldb_table('local_aiquestions');
        $field = new xmldb_field('numoftries', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'tries');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true, 2023050501, 'local', 'aiquestions');
    }

    if ($oldversion < 2023053000) {
        // Rename user field to userid.
        $table = new xmldb_table('local_aiquestions');
        $field = new xmldb_field('user', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'course');
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'userid');
        }
        upgrade_plugin_savepoint(true, 2023053000, 'local', 'aiquestions');
    }

    return true;
}
