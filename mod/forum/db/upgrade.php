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
 * your older installtion to the current version.
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
 * @package mod-forum
 * @copyright 2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function xmldb_forum_upgrade($oldversion) {
    global $CFG, $DB, $OUTPUT;

    $dbman = $DB->get_manager(); // loads ddl manager and xmldb classes
    $result = true;

//===== 1.9.0 upgrade line ======//

    if ($result && $oldversion < 2007101511) {
        //MDL-13866 - send forum ratins to gradebook again
        require_once($CFG->dirroot.'/mod/forum/lib.php');
        forum_upgrade_grades();
        upgrade_mod_savepoint($result, 2007101511, 'forum');
    }

    if ($result && $oldversion < 2007101512) {

    /// Cleanup the forum subscriptions
        echo $OUTPUT->notification('Removing stale forum subscriptions', 'notifysuccess');

        $roles = get_roles_with_capability('moodle/course:participate', CAP_ALLOW);
        $roles = array_keys($roles);

        list($usql, $params) = $DB->get_in_or_equal($roles);
        $sql = "SELECT fs.userid, f.id AS forumid
                  FROM {forum} f
                       JOIN {course} c                 ON c.id = f.course
                       JOIN {context} ctx              ON (ctx.instanceid = c.id AND ctx.contextlevel = ".CONTEXT_COURSE.")
                       JOIN {forum_subscriptions} fs   ON fs.forum = f.id
                       LEFT JOIN {role_assignments} ra ON (ra.contextid = ctx.id AND ra.userid = fs.userid AND ra.roleid $usql)
                 WHERE ra.id IS NULL";

        if ($rs = $DB->get_recordset_sql($sql, $params)) {
            foreach ($rs as $remove) {
                $DB->delete_records('forum_subscriptions', array('userid'=>$remove->userid, 'forum'=>$remove->forumid));
                echo '.';
            }
            $rs->close();
        }

        upgrade_mod_savepoint($result, 2007101512, 'forum');
    }

    if ($result && $oldversion < 2008072800) {
    /// Define field completiondiscussions to be added to forum
        $table = new xmldb_table('forum');
        $field = new xmldb_field('completiondiscussions');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '9', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'blockperiod');

    /// Launch add field completiondiscussions
        if(!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('completionreplies');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '9', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'completiondiscussions');

    /// Launch add field completionreplies
        if(!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field completionposts to be added to forum
        $field = new xmldb_field('completionposts');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '9', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'completionreplies');

    /// Launch add field completionposts
        if(!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint($result, 2008072800, 'forum');
    }

    if ($result && $oldversion < 2008081900) {

        /////////////////////////////////////
        /// new file storage upgrade code ///
        /////////////////////////////////////

        $fs = get_file_storage();

        $empty = $DB->sql_empty(); // silly oracle empty string handling workaround

        $sqlfrom = "FROM {forum_posts} p
                    JOIN {forum_discussions} d ON d.id = p.discussion
                    JOIN {forum} f ON f.id = d.forum
                    JOIN {modules} m ON m.name = 'forum'
                    JOIN {course_modules} cm ON (cm.module = m.id AND cm.instance = f.id)
                   WHERE p.attachment <> '$empty' AND p.attachment <> '1'";

        $count = $DB->count_records_sql("SELECT COUNT('x') $sqlfrom");

        if ($rs = $DB->get_recordset_sql("SELECT p.id, p.attachment, d.forum, f.course, cm.id AS cmid $sqlfrom ORDER BY f.course, f.id, d.id")) {

            $pbar = new progress_bar('migrateforumfiles', 500, true);

            $i = 0;
            foreach ($rs as $post) {
                $i++;
                upgrade_set_timeout(60); // set up timeout, may also abort execution
                $pbar->update($i, $count, "Migrating forum posts - $i/$count.");

                $filepath = "$CFG->dataroot/$post->course/$CFG->moddata/forum/$post->forum/$post->id/$post->attachment";
                if (!is_readable($filepath)) {
                    //file missing??
                    echo $OUTPUT->notification("File not readable, skipping: ".$filepath);
                    $post->attachment = '';
                    $DB->update_record('forum_posts', $post);
                    continue;
                }
                $context = get_context_instance(CONTEXT_MODULE, $post->cmid);

                $filearea = 'forum_attachment';
                $filename = clean_param($post->attachment, PARAM_FILE);
                if ($filename === '') {
                    echo $OUTPUT->notification("Unsupported post filename, skipping: ".$filepath);
                    $post->attachment = '';
                    $DB->update_record('forum_posts', $post);
                    continue;
                }
                if (!$fs->file_exists($context->id, $filearea, $post->id, '/', $filename)) {
                    $file_record = array('contextid'=>$context->id, 'filearea'=>$filearea, 'itemid'=>$post->id, 'filepath'=>'/', 'filename'=>$filename, 'userid'=>$post->userid);
                    if ($fs->create_file_from_pathname($file_record, $filepath)) {
                        $post->attachment = '1';
                        if ($DB->update_record('forum_posts', $post)) {
                            unlink($filepath);
                        }
                    }
                }

                // remove dirs if empty
                @rmdir("$CFG->dataroot/$post->course/$CFG->moddata/forum/$post->forum/$post->id");
                @rmdir("$CFG->dataroot/$post->course/$CFG->moddata/forum/$post->forum");
                @rmdir("$CFG->dataroot/$post->course/$CFG->moddata/forum");
            }
            $rs->close();
        }

        upgrade_mod_savepoint($result, 2008081900, 'forum');
    }

    if ($result && $oldversion < 2008090800) {

    /// Define field maxattachments to be added to forum
        $table = new xmldb_table('forum');
        $field = new xmldb_field('maxattachments', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1', 'maxbytes');

    /// Conditionally launch add field maxattachments
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// forum savepoint reached
        upgrade_mod_savepoint($result, 2008090800, 'forum');
    }

    if ($result && $oldversion < 2009042000) {

    /// Rename field format on table forum_posts to messageformat
        $table = new xmldb_table('forum_posts');
        $field = new xmldb_field('format', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'message');

    /// Launch rename field format
        $dbman->rename_field($table, $field, 'messageformat');

    /// forum savepoint reached
        upgrade_mod_savepoint($result, 2009042000, 'forum');
    }

    if ($result && $oldversion < 2009042001) {

    /// Define field messagetrust to be added to forum_posts
        $table = new xmldb_table('forum_posts');
        $field = new xmldb_field('messagetrust', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'messageformat');

    /// Launch add field messagetrust
        $dbman->add_field($table, $field);

    /// forum savepoint reached
        upgrade_mod_savepoint($result, 2009042001, 'forum');
    }

    if ($result && $oldversion < 2009042002) {
        $trustmark = '#####TRUSTTEXT#####';
        $rs = $DB->get_recordset_sql("SELECT * FROM {forum_posts} WHERE message LIKE '$trustmark%'");
        foreach ($rs as $post) {
            if (strpos($post->entrycomment, $trustmark) !== 0) {
                // probably lowercase in some DBs
                continue;
            }
            $post->message      = trusttext_strip($post->message);
            $post->messagetrust = 1;
            $DB->update_record('forum_posts', $post);
        }
        $rs->close();

    /// forum savepoint reached
        upgrade_mod_savepoint($result, 2009042002, 'forum');
    }

    if ($result && $oldversion < 2009042003) {

    /// Define field introformat to be added to forum
        $table = new xmldb_table('forum');
        $field = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'intro');

    /// Launch add field introformat
        $dbman->add_field($table, $field);

    /// forum savepoint reached
        upgrade_mod_savepoint($result, 2009042003, 'forum');
    }

    if ($result && $oldversion < 2009042004) {
    /// set format to current
        $DB->set_field('forum', 'introformat', FORMAT_MOODLE, array());

    /// quiz savepoint reached
        upgrade_mod_savepoint($result, 2009042004, 'forum');
    }

    /// Dropping all enums/check contraints from core. MDL-18577
    if ($result && $oldversion < 2009042700) {

    /// Changing list of values (enum) of field type on table forum to none
        $table = new xmldb_table('forum');
        $field = new xmldb_field('type', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'general', 'course');

    /// Launch change of list of values for field type
        $dbman->drop_enum_from_field($table, $field);

    /// forum savepoint reached
        upgrade_mod_savepoint($result, 2009042700, 'forum');
    }

    if ($result && $oldversion < 2009050400) {

    /// Clean existing wrong rates. MDL-18227
        $DB->delete_records('forum_ratings', array('post' => 0));

    /// forum savepoint reached
        upgrade_mod_savepoint($result, 2009050400, 'forum');
    }

    if($result && $oldversion < 2010042800) {
        //migrate forumratings to the central rating table
        require_once($CFG->dirroot . '/lib/db/upgradelib.php');

        //forum ratings only have a single time column so use it for both time created and modified
        $sql = "INSERT INTO {rating} (contextid, scaleid, itemid, rating, userid, timecreated, timemodified)
SELECT cxt.id, f.scale, r.post AS itemid, r.rating, r.userid, r.time AS timecreated, r.time AS timemodified
FROM {forum_ratings} r
JOIN {forum_posts} p ON p.id=r.post
JOIN {forum_discussions} d ON d.id=p.discussion
JOIN {forum} f ON f.id=d.forum
JOIN {course_modules} cm ON cm.instance=f.id
JOIN {context} cxt ON cxt.instanceid=cm.id
JOIN {modules} m ON m.id=cm.module
WHERE m.name = :modname AND cxt.contextlevel = :contextlevel";
        $params['modname'] = 'forum';
        $params['contextlevel'] = CONTEXT_MODULE;

        $DB->execute($sql, $params);

        //now drop forum_ratings
        $table = new xmldb_table('forum_ratings');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        upgrade_mod_savepoint($result, 2010042800, 'forum');
    }

    return $result;
}


