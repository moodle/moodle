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
 * Folder module upgrade code
 *
 * This file keeps track of upgrades to
 * the resource module
 *
 * Sometimes, changes between versions involve
 * alterations to database structures and other
 * major things that may break installations.
 *
 * The upgrade function in this file will attempt
 * to perform all the necessary actions to upgrade
 * your older installation to the current version.
 *
 * If there's something it cannot do itself, it
 * will tell you what you need to do.
 *
 * The commands in here will all be database-neutral,
 * using the methods of database_manager class
 *
 * Please do not forget to use upgrade_set_timeout()
 * before any action that may take longer time to finish.
 *
 * @package   mod_folder
 * @copyright 2009 Petr Skoda  {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_folder_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();


    // Moodle v2.2.0 release upgrade line
    // Put any upgrade step following this

    // Moodle v2.3.0 release upgrade line
    // Put any upgrade step following this

    // Moodle v2.4.0 release upgrade line
    // Put any upgrade step following this

    if ($oldversion < 2013012100) {

        // Define field display to be added to folder
        $table = new xmldb_table('folder');
        $field = new xmldb_field('display', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'timemodified');

        // Conditionally launch add field display
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // folder savepoint reached
        upgrade_mod_savepoint(true, 2013012100, 'folder');
    }

    if ($oldversion < 2013031500) {

        // Define field showexpanded to be added to folder
        $table = new xmldb_table('folder');
        $field = new xmldb_field('showexpanded', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'revision');

        // Conditionally launch add field showexpanded
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // folder savepoint reached
        upgrade_mod_savepoint(true, 2013031500, 'folder');
    }

    // Rename show_expanded to showexpanded (see MDL-38646).
    if ($oldversion < 2013040700) {

        // Rename site config setting.
        $showexpanded = get_config('folder', 'show_expanded');
        set_config('showexpanded', $showexpanded, 'folder');
        set_config('show_expanded', null, 'folder');

        // Rename table column.
        $table = new xmldb_table('folder');
        $field = new xmldb_field('show_expanded', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'revision');
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'showexpanded');
        }

        // folder savepoint reached
        upgrade_mod_savepoint(true, 2013040700, 'folder');
    }

    // Moodle v2.5.0 release upgrade line.
    // Put any upgrade step following this.


    // Moodle v2.6.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.7.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.8.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
