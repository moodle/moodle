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
 * This file keeps track of upgrades to
 * the forum module
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
 * @package   mod_forum
 * @copyright 2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function xmldb_forum_upgrade($oldversion) {
    global $CFG, $DB, $OUTPUT;

    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

    // Moodle v2.2.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.3.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.4.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2013020500) {

        // Define field displaywordcount to be added to forum.
        $table = new xmldb_table('forum');
        $field = new xmldb_field('displaywordcount', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'completionposts');

        // Conditionally launch add field displaywordcount.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Forum savepoint reached.
        upgrade_mod_savepoint(true, 2013020500, 'forum');
    }

    // Forcefully assign mod/forum:allowforcesubscribe to frontpage role, as we missed that when
    // capability was introduced.
    if ($oldversion < 2013021200) {
        // If capability mod/forum:allowforcesubscribe is defined then set it for frontpage role.
        if (get_capability_info('mod/forum:allowforcesubscribe')) {
            assign_legacy_capabilities('mod/forum:allowforcesubscribe', array('frontpage' => CAP_ALLOW));
        }
        // Forum savepoint reached.
        upgrade_mod_savepoint(true, 2013021200, 'forum');
    }


    // Moodle v2.5.0 release upgrade line.
    // Put any upgrade step following this.
    if ($oldversion < 2013071000) {
        // Define table forum_digests to be created.
        $table = new xmldb_table('forum_digests');

        // Adding fields to table forum_digests.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('forum', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('maildigest', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '-1');

        // Adding keys to table forum_digests.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));
        $table->add_key('forum', XMLDB_KEY_FOREIGN, array('forum'), 'forum', array('id'));
        $table->add_key('forumdigest', XMLDB_KEY_UNIQUE, array('forum', 'userid', 'maildigest'));

        // Conditionally launch create table for forum_digests.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Forum savepoint reached.
        upgrade_mod_savepoint(true, 2013071000, 'forum');
    }

    // Moodle v2.6.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2014040400) {

        // Define index userid-postid (not unique) to be dropped form forum_read.
        $table = new xmldb_table('forum_read');
        $index = new xmldb_index('userid-postid', XMLDB_INDEX_NOTUNIQUE, array('userid', 'postid'));

        // Conditionally launch drop index userid-postid.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }


        // Define index postid-userid (not unique) to be added to forum_read.
        $index = new xmldb_index('postid-userid', XMLDB_INDEX_NOTUNIQUE, array('postid', 'userid'));

        // Conditionally launch add index postid-userid.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Forum savepoint reached.
        upgrade_mod_savepoint(true, 2014040400, 'forum');
    }

    return true;
}
