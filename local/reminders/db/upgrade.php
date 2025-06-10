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
 * This file keeps track of upgrades to the reminder module.
 *
 * @package   local_reminders
 * @copyright 2012 Isuru Madushanka Weerarathna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Run the upgrade for the plugin.
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
 * @param int $oldversion older version of plugin.
 * @return void
 */
function xmldb_local_reminders_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

    // Moodle v2.2.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2014121301) {

        // Define table local_reminders_course to be created.
        $table = new xmldb_table('local_reminders_course');

        // Adding fields to table local_reminders_course.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('status_course', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('status_activities', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('status_group', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1');

        // Adding keys to table local_reminders_course.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('relatedcourse', XMLDB_KEY_FOREIGN_UNIQUE, ['courseid'], 'course', ['id']);

        // Conditionally launch create table for local_reminders_course.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Reminders savepoint reached.
        upgrade_plugin_savepoint(true, 2014121301, 'local', 'reminders');
    }

    if ($oldversion < 2020032000) {
        create_local_reminders_post_activity_table($dbman);

        create_local_reminders_activity_config_table($dbman);

        // Reminders savepoint reached.
        upgrade_plugin_savepoint(true, 2020032000, 'local', 'reminders');
    }

    // Migrate invalid db column in local_reminders table.
    // Converts existing char column to a number column.
    if ($oldversion < 2022110700) {
        $table = new xmldb_table('local_reminders');
        $field = new xmldb_field('time');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 10);
        $dbman->change_field_type($table, $field);

        // Reminders savepoint reached.
        upgrade_plugin_savepoint(true, 2022110700, 'local', 'reminders');
    }

    return true;
}

/**
 * Create local_reminders_activityconf table required for moodle plugin v2.
 *
 * @param object $dbman db manager class.
 */
function create_local_reminders_activity_config_table($dbman) {
    // Define table local_reminders_post_activity to be created.
    $table = new xmldb_table('local_reminders_activityconf');

    // Adding fields to table local_reminders_post_activity.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('eventid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('settingkey', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
    $table->add_field('settingvalue', XMLDB_TYPE_TEXT, null, null, null, null, null);

    // Adding keys to table local_reminders_post_activity.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

    // Adding indexes for faster access.
    $table->add_index('localremindercourses', XMLDB_INDEX_NOTUNIQUE, ['courseid']);

    // Conditionally launch create table for local_reminders_post_activity.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
}

/**
 * Create local_reminders_post_activity table required for moodle plugin v2.
 *
 * @param object $dbman db manager class.
 */
function create_local_reminders_post_activity_table($dbman) {
    // Define table local_reminders_post_activity to be created.
    $table = new xmldb_table('local_reminders_post_act');

    // Adding fields to table local_reminders_post_activity.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('sendtime', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('eventid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

    // Adding keys to table local_reminders_post_activity.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

    // Conditionally launch create table for local_reminders_post_activity.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
}
