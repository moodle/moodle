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

defined('MOODLE_INTERNAL') || die();

function xmldb_forum_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

    // Automatically generated Moodle v3.9.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2020072100) {
        // Add index privatereplyto (not unique) to the forum_posts table.
        $table = new xmldb_table('forum_posts');
        $index = new xmldb_index('privatereplyto', XMLDB_INDEX_NOTUNIQUE, ['privatereplyto']);

        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_mod_savepoint(true, 2020072100, 'forum');
    }

    if ($oldversion < 2021101100) {
        // Add custom data to digest tasks to stop duplicates being created after this patch.
        $timenow = time();

        $sitetimezone = \core_date::get_server_timezone();
        $servermidnight = usergetmidnight($timenow, $sitetimezone);
        $digesttime = $servermidnight + ($CFG->digestmailtime * 3600);
        if ($digesttime < $timenow) {
            // Digest time is in the past. set for tomorrow.
            $servermidnight = usergetmidnight($timenow + DAYSECS, $sitetimezone);
        }

        $customdata = json_encode(['servermidnight' => $servermidnight]);

        $params = [
            'component' => 'mod_forum',
            'classname' => '\mod_forum\task\send_user_digests',
            'customdata' => '', // We do not want to overwrite any tasks that already have the custom data.
        ];

        $textfield = $DB->sql_compare_text('customdata', 1);

        $sql = "component = :component AND classname = :classname AND $textfield = :customdata";

        $DB->set_field_select('task_adhoc', 'customdata', $customdata, $sql, $params);

        upgrade_mod_savepoint(true, 2021101100, 'forum');
    }

    if ($oldversion < 2021101101) {
        // Remove the userid-forumid index as it gets replaces with forumid-userid.
        $table = new xmldb_table('forum_read');
        $index = new xmldb_index('userid-forumid', XMLDB_INDEX_NOTUNIQUE, ['userid', 'forumid']);

        // Conditionally launch drop index userid-forumid.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Remove the userid-discussionid index as it gets replaced with discussionid-userid.
        $table = new xmldb_table('forum_read');
        $index = new xmldb_index('userid-discussionid', XMLDB_INDEX_NOTUNIQUE, ['userid', 'discussionid']);

        // Conditionally launch drop index userid-discussionid.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Define index userid (not unique) to be added to forum_read.
        $table = new xmldb_table('forum_read');
        $index = new xmldb_index('userid', XMLDB_INDEX_NOTUNIQUE, ['userid']);

        // Conditionally launch add index userid.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Build replacement indexes to replace the two dropped earlier.
        // Define index forumid-userid (not unique) to be added to forum_read.
        $table = new xmldb_table('forum_read');
        $index = new xmldb_index('forumid-userid', XMLDB_INDEX_NOTUNIQUE, ['forumid', 'userid']);

        // Conditionally launch add index forumid-userid.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Define index discussionid-userid (not unique) to be added to forum_read.
        $table = new xmldb_table('forum_read');
        $index = new xmldb_index('discussionid-userid', XMLDB_INDEX_NOTUNIQUE, ['discussionid', 'userid']);

        // Conditionally launch add index discussionid-userid.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Forum savepoint reached.
        upgrade_mod_savepoint(true, 2021101101, 'forum');
    }

    // Automatically generated Moodle v4.0.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2022062700) {
        // Unset $CFG->forum_usecoursefullname.
        unset_config('forum_usecoursefullname');

        // Define field usecoursefullname to be dropped from forum.
        $table = new xmldb_table('forum');
        $field = new xmldb_field('usecoursefullname');

        // Conditionally launch drop field usecoursefullname.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Forum savepoint reached.
        upgrade_mod_savepoint(true, 2022062700, 'forum');
    }

    if ($oldversion < 2022072900) {
        // Define key usermodified (foreign) to be added to forum_discussions.
        $table = new xmldb_table('forum_discussions');
        $key = new xmldb_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
        // Launch add key usermodified.
        $dbman->add_key($table, $key);

        // Forum savepoint reached.
        upgrade_mod_savepoint(true, 2022072900, 'forum');
    }

    // Automatically generated Moodle v4.1.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2022112801) {
        // Some very old discussions from early Moodle versions may have the usermodified set to zero.
        $DB->execute("UPDATE {forum_discussions} SET usermodified = userid WHERE usermodified = 0");

        upgrade_mod_savepoint(true, 2022112801, 'forum');
    }

    return true;
}
