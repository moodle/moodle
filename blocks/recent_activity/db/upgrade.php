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
 * This file keeps track of upgrades to the recent activity block
 *
 * Sometimes, changes between versions involve alterations to database structures
 * and other major things that may break installations.
 *
 * The upgrade function in this file will attempt to perform all the necessary
 * actions to upgrade your older installation to the current version.
 *
 * If there's something it cannot do itself, it will tell you what you need to do.
 *
 * The commands in here will all be database-neutral, using the methods of
 * database_manager class
 *
 * Please do not forget to use upgrade_set_timeout()
 * before any action that may take longer time to finish.
 *
 * @package   block_recent_activity
 * @copyright 2014 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade code for the recent activity block.
 *
 * @global moodle_database $DB
 * @param int $oldversion
 * @param object $block
 */
function xmldb_block_recent_activity_upgrade($oldversion, $block) {
    global $CFG, $DB;

    $dbman = $DB->get_manager(); // loads ddl manager and xmldb classes

    if ($oldversion < 2014012000) {

        // Define table block_recent_activity to be created.
        $table = new xmldb_table('block_recent_activity');

        // Adding fields to table block_recent_activity.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('cmid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('action', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);
        $table->add_field('modname', XMLDB_TYPE_CHAR, '20', null, null, null, null);

        // Adding keys to table block_recent_activity.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table block_recent_activity.
        $table->add_index('coursetime', XMLDB_INDEX_NOTUNIQUE, array('courseid', 'timecreated'));

        // Conditionally launch create table for block_recent_activity.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
            // Insert dummy log record for each existing course to notify that their logs need to be migrated.
            $DB->execute('INSERT INTO {block_recent_activity} (timecreated, userid, courseid, cmid, action) '.
                    'SELECT ?, 0, id, 0, 3 FROM {course}',
                    array(time()));
        }

        // Recent_activity savepoint reached.
        upgrade_block_savepoint(true, 2014012000, 'recent_activity');
    }

    // Moodle v2.7.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.8.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.9.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
