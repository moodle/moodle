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

    // Automatically generated Moodle v3.6.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2019031200) {
        // Define field privatereplyto to be added to forum_posts.
        $table = new xmldb_table('forum_posts');
        $field = new xmldb_field('privatereplyto', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'mailnow');

        // Conditionally launch add field privatereplyto.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2019031200, 'forum');
    }

    if ($oldversion < 2019040400) {

        $table = new xmldb_table('forum');

        // Define field duedate to be added to forum.
        $field = new xmldb_field('duedate', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'introformat');

        // Conditionally launch add field duedate.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field cutoffdate to be added to forum.
        $field = new xmldb_field('cutoffdate', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'duedate');

        // Conditionally launch add field cutoffdate.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2019040400, 'forum');
    }

    if ($oldversion < 2019040402) {
        // Define field deleted to be added to forum_posts.
        $table = new xmldb_table('forum_discussions');
        $field = new xmldb_field('timelocked', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'pinned');

        // Conditionally launch add field deleted.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Forum savepoint reached.
        upgrade_mod_savepoint(true, 2019040402, 'forum');
    }

    // Automatically generated Moodle v3.7.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2019071901) {

        // Define field wordcount to be added to forum_posts.
        $table = new xmldb_table('forum_posts');
        $field = new xmldb_field('wordcount', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'privatereplyto');

        // Conditionally launch add field wordcount.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field charcount to be added to forum_posts.
        $table = new xmldb_table('forum_posts');
        $field = new xmldb_field('charcount', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'wordcount');

        // Conditionally launch add field charcount.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Forum savepoint reached.
        upgrade_mod_savepoint(true, 2019071901, 'forum');
    }

    if ($oldversion < 2019071902) {
        // Create adhoc task for upgrading of existing forum_posts.
        $record = new \stdClass();
        $record->classname = '\mod_forum\task\refresh_forum_post_counts';
        $record->component = 'mod_forum';

        // Next run time based from nextruntime computation in \core\task\manager::queue_adhoc_task().
        $nextruntime = time() - 1;
        $record->nextruntime = $nextruntime;
        $DB->insert_record('task_adhoc', $record);

        // Main savepoint reached.
        upgrade_mod_savepoint(true, 2019071902, 'forum');
    }

    if ($oldversion < 2019081100) {

        // Define field grade_forum to be added to forum.
        $table = new xmldb_table('forum');
        $field = new xmldb_field('grade_forum', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'scale');

        // Conditionally launch add field grade_forum.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Forum savepoint reached.
        upgrade_mod_savepoint(true, 2019081100, 'forum');

    }

    if ($oldversion < 2019100100) {
        // Define table forum_grades to be created.
        $table = new xmldb_table('forum_grades');

        // Adding fields to table forum_grades.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('forum', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('itemnumber', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('grade', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table forum_grades.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('forum', XMLDB_KEY_FOREIGN, ['forum'], 'forum', ['id']);

        // Adding indexes to table forum_grades.
        $table->add_index('userid', XMLDB_INDEX_NOTUNIQUE, ['userid']);
        $table->add_index('forumusergrade', XMLDB_INDEX_UNIQUE, ['forum', 'itemnumber', 'userid']);

        // Conditionally launch create table for forum_grades.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Forum savepoint reached.
        upgrade_mod_savepoint(true, 2019100100, 'forum');
    }

    if ($oldversion < 2019100108) {

        // Define field sendstudentnotifications_forum to be added to forum.
        $table = new xmldb_table('forum');
        $field = new xmldb_field('sendstudentnotifications_forum', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0',
                'grade_forum');

        // Conditionally launch add field sendstudentnotifications_forum.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Forum savepoint reached.
        upgrade_mod_savepoint(true, 2019100108, 'forum');
    }

    if ($oldversion < 2019100109) {

        $table = new xmldb_table('forum');
        $field = new xmldb_field('sendstudentnotifications_forum');
        if ($dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'grade_forum');
            $dbman->rename_field($table, $field, 'grade_forum_notify');
        }

        // Forum savepoint reached.
        upgrade_mod_savepoint(true, 2019100109, 'forum');

    }

    // Automatically generated Moodle v3.8.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2019111801) {
        $sql = "SELECT d.id AS discussionid, p.userid AS correctuser
                FROM {forum_discussions} d
                INNER JOIN {forum_posts} p ON p.id = d.firstpost
                WHERE d.userid <> p.userid";
        $recordset = $DB->get_recordset_sql($sql);
        foreach ($recordset as $record) {
            $object = new stdClass();
            $object->id = $record->discussionid;
            $object->userid = $record->correctuser;
            $DB->update_record('forum_discussions', $object);
        }

        $recordset->close();

        // Forum savepoint reached.
        upgrade_mod_savepoint(true, 2019111801, 'forum');
    }

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

    if ($oldversion < 2022041901) {
        // Some very old discussions from early Moodle versions may have the usermodified set to zero.
        $DB->execute("UPDATE {forum_discussions} SET usermodified = userid WHERE usermodified = 0");

        upgrade_mod_savepoint(true, 2022041901, 'forum');
    }

    return true;
}
