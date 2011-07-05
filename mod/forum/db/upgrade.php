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
 * @package mod-forum
 * @copyright 2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function xmldb_forum_upgrade($oldversion) {
    global $CFG, $DB, $OUTPUT;

    $dbman = $DB->get_manager(); // loads ddl manager and xmldb classes

//===== 1.9.0 upgrade line ======//

    if ($oldversion < 2007101511) {
        //MDL-13866 - send forum ratins to gradebook again
        require_once($CFG->dirroot.'/mod/forum/lib.php');
        forum_upgrade_grades();
        upgrade_mod_savepoint(true, 2007101511, 'forum');
    }

    if ($oldversion < 2008072800) {
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
        upgrade_mod_savepoint(true, 2008072800, 'forum');
    }

    if ($oldversion < 2008081900) {

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

        $rs = $DB->get_recordset_sql("SELECT p.id, p.attachment, p.userid, d.forum, f.course, cm.id AS cmid $sqlfrom ORDER BY f.course, f.id, d.id");
        if ($rs->valid()) {

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

                $filearea = 'attachment';
                $filename = clean_param($post->attachment, PARAM_FILE);
                if ($filename === '') {
                    echo $OUTPUT->notification("Unsupported post filename, skipping: ".$filepath);
                    $post->attachment = '';
                    $DB->update_record('forum_posts', $post);
                    continue;
                }
                if (!$fs->file_exists($context->id, 'mod_forum', $filearea, $post->id, '/', $filename)) {
                    $file_record = array('contextid'=>$context->id, 'component'=>'mod_forum', 'filearea'=>$filearea, 'itemid'=>$post->id, 'filepath'=>'/', 'filename'=>$filename, 'userid'=>$post->userid);
                    if ($fs->create_file_from_pathname($file_record, $filepath)) {
                        $post->attachment = '1';
                        $DB->update_record('forum_posts', $post);
                        unlink($filepath);
                    }
                }

                // remove dirs if empty
                @rmdir("$CFG->dataroot/$post->course/$CFG->moddata/forum/$post->forum/$post->id");
                @rmdir("$CFG->dataroot/$post->course/$CFG->moddata/forum/$post->forum");
                @rmdir("$CFG->dataroot/$post->course/$CFG->moddata/forum");
            }
        }
        $rs->close();

        upgrade_mod_savepoint(true, 2008081900, 'forum');
    }

    if ($oldversion < 2008090800) {

    /// Define field maxattachments to be added to forum
        $table = new xmldb_table('forum');
        $field = new xmldb_field('maxattachments', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1', 'maxbytes');

    /// Conditionally launch add field maxattachments
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// forum savepoint reached
        upgrade_mod_savepoint(true, 2008090800, 'forum');
    }

    if ($oldversion < 2009042000) {

    /// Rename field format on table forum_posts to messageformat
        $table = new xmldb_table('forum_posts');
        $field = new xmldb_field('format', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'message');

    /// Launch rename field format
        $dbman->rename_field($table, $field, 'messageformat');

    /// forum savepoint reached
        upgrade_mod_savepoint(true, 2009042000, 'forum');
    }

    if ($oldversion < 2009042001) {

    /// Define field messagetrust to be added to forum_posts
        $table = new xmldb_table('forum_posts');
        $field = new xmldb_field('messagetrust', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'messageformat');

    /// Launch add field messagetrust
        $dbman->add_field($table, $field);

    /// forum savepoint reached
        upgrade_mod_savepoint(true, 2009042001, 'forum');
    }

    if ($oldversion < 2009042002) {
        $trustmark = '#####TRUSTTEXT#####';
        $rs = $DB->get_recordset_sql("SELECT * FROM {forum_posts} WHERE message LIKE ?", array($trustmark.'%'));
        foreach ($rs as $post) {
            if (strpos($post->message, $trustmark) !== 0) {
                // probably lowercase in some DBs?
                continue;
            }
            $post->message      = str_replace($trustmark, '', $post->message);
            $post->messagetrust = 1;
            $DB->update_record('forum_posts', $post);
        }
        $rs->close();

    /// forum savepoint reached
        upgrade_mod_savepoint(true, 2009042002, 'forum');
    }

    if ($oldversion < 2009042003) {

    /// Define field introformat to be added to forum
        $table = new xmldb_table('forum');
        $field = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'intro');

    /// Launch add field introformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // conditionally migrate to html format in intro
        if ($CFG->texteditors !== 'textarea') {
            $rs = $DB->get_recordset('forum', array('introformat'=>FORMAT_MOODLE), '', 'id,intro,introformat');
            foreach ($rs as $f) {
                $f->intro       = text_to_html($f->intro, false, false, true);
                $f->introformat = FORMAT_HTML;
                $DB->update_record('forum', $f);
                upgrade_set_timeout();
            }
            $rs->close();
        }

    /// forum savepoint reached
        upgrade_mod_savepoint(true, 2009042003, 'forum');
    }

    /// Dropping all enums/check contraints from core. MDL-18577
    if ($oldversion < 2009042700) {

    /// Changing list of values (enum) of field type on table forum to none
        $table = new xmldb_table('forum');
        $field = new xmldb_field('type', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'general', 'course');

    /// Launch change of list of values for field type
        $dbman->drop_enum_from_field($table, $field);

    /// forum savepoint reached
        upgrade_mod_savepoint(true, 2009042700, 'forum');
    }

    if ($oldversion < 2009050400) {

    /// Clean existing wrong rates. MDL-18227
        $DB->delete_records('forum_ratings', array('post' => 0));

    /// forum savepoint reached
        upgrade_mod_savepoint(true, 2009050400, 'forum');
    }

    if ($oldversion < 2010042800) {
        //migrate forumratings to the central rating table
        $table = new xmldb_table('forum_ratings');
        if ($dbman->table_exists($table)) {
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
            $dbman->drop_table($table);
        }

        upgrade_mod_savepoint(true, 2010042800, 'forum');
    }

    if ($oldversion < 2010070800) {

        // Remove the forum digests message provider MDL-23145
        $DB->delete_records('message_providers', array('name' => 'digests','component'=>'mod_forum'));

        // forum savepoint reached
        upgrade_mod_savepoint(true, 2010070800, 'forum');
    }

    if ($oldversion < 2010091900) {
        // rename files from borked upgrade in 2.0dev
        $fs = get_file_storage();
        $rs = $DB->get_recordset('files', array('component'=>'mod_form'));
        foreach ($rs as $oldrecord) {
            $file = $fs->get_file_instance($oldrecord);
            $newrecord = array('component'=>'mod_forum');
            if (!$fs->file_exists($oldrecord->contextid, 'mod_forum', $oldrecord->filearea, $oldrecord->itemid, $oldrecord->filepath, $oldrecord->filename)) {
                $fs->create_file_from_storedfile($newrecord, $file);
            }
            $file->delete();
        }
        $rs->close();
        upgrade_mod_savepoint(true, 2010091900, 'forum');
    }

    if ($oldversion < 2011052300) {
        // rating.component and rating.ratingarea have now been added as mandatory fields.
        // Presently you can only rate forum posts so component = 'mod_forum' and ratingarea = 'post'
        // for all ratings with a forum context.
        // We want to update all ratings that belong to a forum context and don't already have a
        // component set.
        // This could take a while reset upgrade timeout to 5 min
        upgrade_set_timeout(60 * 20);
        $sql = "UPDATE {rating}
                SET component = 'mod_forum', ratingarea = 'post'
                WHERE contextid IN (
                    SELECT ctx.id
                      FROM {context} ctx
                      JOIN {course_modules} cm ON cm.id = ctx.instanceid
                      JOIN {modules} m ON m.id = cm.module
                     WHERE ctx.contextlevel = 70 AND
                           m.name = 'forum'
                ) AND component = 'unknown'";
        $DB->execute($sql);

        upgrade_mod_savepoint(true, 2011052300, 'forum');
    }

    // Moodle v2.1.0 release upgrade line
    // Put any upgrade step following this

    return true;
}


