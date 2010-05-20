<?php

// This file keeps track of upgrades to
// the glossary module
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installtion to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the methods of database_manager class
//
// Please do not forget to use upgrade_set_timeout()
// before any action that may take longer time to finish.

function xmldb_glossary_upgrade($oldversion) {
    global $CFG, $DB, $OUTPUT;

    $dbman = $DB->get_manager();
    $result = true;

//===== 1.9.0 upgrade line ======//

    if ($result && $oldversion < 2008081900) {

        /////////////////////////////////////
        /// new file storage upgrade code ///
        /////////////////////////////////////

        $fs = get_file_storage();

        $empty = $DB->sql_empty(); // silly oracle empty string handling workaround

        $sqlfrom = "FROM {glossary_entries} ge
                    JOIN {glossary} g ON g.id = ge.glossaryid
                    JOIN {modules} m ON m.name = 'glossary'
                    JOIN {course_modules} cm ON (cm.module = m.id AND cm.instance = g.id)
                   WHERE ge.attachment <> '$empty' AND ge.attachment <> '1'";

        $count = $DB->count_records_sql("SELECT COUNT('x') $sqlfrom");

        if ($rs = $DB->get_recordset_sql("SELECT ge.id, ge.userid, ge.attachment, ge.glossaryid, ge.sourceglossaryid, g.course, cm.id AS cmid $sqlfrom ORDER BY g.course, g.id")) {

            $pbar = new progress_bar('migrateglossaryfiles', 500, true);

            $i = 0;
            foreach ($rs as $entry) {
                $i++;
                upgrade_set_timeout(60); // set up timeout, may also abort execution
                $pbar->update($i, $count, "Migrating glossary entries - $i/$count.");

                $filepath = "$CFG->dataroot/$entry->course/$CFG->moddata/glossary/$entry->glossaryid/$entry->id/$entry->attachment";
                if ($entry->sourceglossaryid and !is_readable($filepath)) {
                    //eh - try the second possible location
                    $filepath = "$CFG->dataroot/$entry->course/$CFG->moddata/glossary/$entry->sourceglossaryid/$entry->id/$entry->attachment";

                }
                if (!is_readable($filepath)) {
                    //file missing??
                    echo $OUTPUT->notification("File not readable, skipping: $filepath");
                    $entry->attachment = '';
                    $DB->update_record('glossary_entries', $entry);
                    continue;
                }
                $context = get_context_instance(CONTEXT_MODULE, $entry->cmid);

                $filearea = 'glossary_attachment';
                $filename = clean_param($entry->attachment, PARAM_FILE);
                if ($filename === '') {
                    echo $OUTPUT->notification("Unsupported entry filename, skipping: ".$filepath);
                    $entry->attachment = '';
                    $DB->update_record('glossary_entries', $entry);
                    continue;
                }
                if (!$fs->file_exists($context->id, $filearea, $entry->id, '/', $filename)) {
                    $file_record = array('contextid'=>$context->id, 'filearea'=>$filearea, 'itemid'=>$entry->id, 'filepath'=>'/', 'filename'=>$filename, 'userid'=>$entry->userid);
                    if ($fs->create_file_from_pathname($file_record, $filepath)) {
                        $entry->attachment = '1';
                        if ($DB->update_record('glossary_entries', $entry)) {
                            unlink($filepath);
                        }
                    }
                }

                // remove dirs if empty
                @rmdir("$CFG->dataroot/$entry->course/$CFG->moddata/glossary/$entry->glossaryid/$entry->id");
                @rmdir("$CFG->dataroot/$entry->course/$CFG->moddata/glossary/$entry->glossaryid");
                @rmdir("$CFG->dataroot/$entry->course/$CFG->moddata/glossary");
            }
            $rs->close();
        }

        upgrade_mod_savepoint($result, 2008081900, 'glossary');
    }

    if ($result && $oldversion < 2009042000) {

    /// Rename field definitionformat on table glossary_entries to definitionformat
        $table = new xmldb_table('glossary_entries');
        $field = new xmldb_field('format', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'definition');

    /// Launch rename field definitionformat
        $dbman->rename_field($table, $field, 'definitionformat');

    /// glossary savepoint reached
        upgrade_mod_savepoint($result, 2009042000, 'glossary');
    }

    if ($result && $oldversion < 2009042001) {

    /// Define field definitiontrust to be added to glossary_entries
        $table = new xmldb_table('glossary_entries');
        $field = new xmldb_field('definitiontrust', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'definitionformat');

    /// Launch add field definitiontrust
        $dbman->add_field($table, $field);

    /// glossary savepoint reached
        upgrade_mod_savepoint($result, 2009042001, 'glossary');
    }

    if ($result && $oldversion < 2009042002) {

    /// Rename field format on table glossary_comments to NEWNAMEGOESHERE
        $table = new xmldb_table('glossary_comments');
        $field = new xmldb_field('format', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'entrycomment');

    /// Launch rename field format
        $dbman->rename_field($table, $field, 'entrycommentformat');

    /// glossary savepoint reached
        upgrade_mod_savepoint($result, 2009042002, 'glossary');
    }

    if ($result && $oldversion < 2009042003) {

    /// Define field entrycommenttrust to be added to glossary_comments
        $table = new xmldb_table('glossary_comments');
        $field = new xmldb_field('entrycommenttrust', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'entrycommentformat');

    /// Conditionally launch add field entrycommenttrust
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// glossary savepoint reached
        upgrade_mod_savepoint($result, 2009042003, 'glossary');
    }

    if ($result && $oldversion < 2009042004) {
        $trustmark = '#####TRUSTTEXT#####';
        $rs = $DB->get_recordset_sql("SELECT * FROM {glossary_entries} WHERE definition LIKE '$trustmark%'");
        foreach ($rs as $entry) {
            if (strpos($entry->definition, $trustmark) !== 0) {
                // probably lowercase in some DBs
                continue;
            }
            $entry->definition      = trusttext_strip($entry->definition);
            $entry->definitiontrust = 1;
            $DB->update_record('glossary_entries', $entry);
        }
        $rs->close();

    /// glossary savepoint reached
        upgrade_mod_savepoint($result, 2009042004, 'glossary');
    }

    if ($result && $oldversion < 2009042005) {
        $trustmark = '#####TRUSTTEXT#####';
        $rs = $DB->get_recordset_sql("SELECT * FROM {glossary_comments} WHERE entrycomment LIKE '$trustmark%'");
        foreach ($rs as $comment) {
            if (strpos($comment->entrycomment, $trustmark) !== 0) {
                // probably lowercase in some DBs
                continue;
            }
            $comment->entrycomment      = trusttext_strip($comment->entrycomment);
            $comment->entrycommenttrust = 1;
            $DB->update_record('glossary_comments', $comment);
        }
        $rs->close();

    /// glossary savepoint reached
        upgrade_mod_savepoint($result, 2009042005, 'glossary');
    }

    if ($result && $oldversion < 2009042006) {

    /// Define field introformat to be added to glossary
        $table = new xmldb_table('glossary');
        $field = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'intro');

    /// Conditionally launch add field introformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// glossary savepoint reached
        upgrade_mod_savepoint($result, 2009042006, 'glossary');
    }
    if ($result && $oldversion < 2009110800) {
        require_once($CFG->dirroot . '/comment/lib.php');
        upgrade_set_timeout(60*20);

    /// Define table glossary_comments to be dropped
        $table = new xmldb_table('glossary_comments');

    /// Conditionally launch drop table for glossary_comments
        if ($dbman->table_exists($table)) {
            $sql = "SELECT e.glossaryid AS glossaryid,
                           g.course AS courseid,
                           c.userid,
                           e.id AS itemid,
                           c.id AS old_id,
                           c.entrycomment AS commentcontent,
                           c.entrycommentformat AS format,
                           c.entrycommenttrust AS trust,
                           c.timemodified AS timemodified
                      FROM {glossary_comments} c, {glossary_entries} e, {glossary} g
                     WHERE c.entryid=e.id AND e.glossaryid=g.id
                  ORDER BY glossaryid, courseid";
            $lastglossaryid = null;
            $lastcourseid   = null;
            $modcontext     = null;

        /// move glossary comments to comments table
            if ($rs = $DB->get_recordset_sql($sql)) {
                foreach($rs as $res) {
                    if ($res->glossaryid != $lastglossaryid || $res->courseid != $lastcourseid) {
                        $cm = get_coursemodule_from_instance('glossary', $res->glossaryid, $res->courseid);
                        if ($cm) {
                            $modcontext = get_context_instance(CONTEXT_MODULE, $cm->id);
                        }
                        $lastglossaryid = $res->glossaryid;
                        $lastcourseid   = $res->courseid;
                    }
                    $cmt = new stdclass;
                    $cmt->contextid     = $modcontext->id;
                    $cmt->commentarea   = 'glossary_entry';
                    $cmt->itemid        = $res->itemid;
                    $cmt->content       = $res->commentcontent;
                    $cmt->format        = $res->format;
                    $cmt->userid        = $res->userid;
                    $cmt->timecreated    = $res->timemodified;
                    $cmt_id = $DB->insert_record('comments', $cmt);
                    if (!empty($cmt_id)) {
                        $DB->delete_records('glossary_comments', array('id'=>$res->old_id));
                    }
                }
            }
            $dbman->drop_table($table);
        }

    /// glossary savepoint reached
        upgrade_mod_savepoint($result, 2009110800, 'glossary');
    }

    if ($result && $oldversion < 2010042800) {
        //migrate glossary_ratings to the central rating table
        require_once($CFG->dirroot . '/lib/db/upgradelib.php');

        $table = new xmldb_table('glossary_ratings');
        if ($dbman->table_exists($table)) {
            //glossary ratings only have a single time column so use it for both time created and modified
            $sql = "INSERT INTO {rating} (contextid, scaleid, itemid, rating, userid, timecreated, timemodified)

                    SELECT cxt.id, g.scale, r.entryid AS itemid, r.rating, r.userid, r.time AS timecreated, r.time AS timemodified
                      FROM {glossary_ratings} r
                      JOIN {glossary_entries} ge ON ge.id=r.entryid
                      JOIN {glossary} g ON g.id=ge.glossaryid
                      JOIN {course_modules} cm ON cm.instance=g.id
                      JOIN {context} cxt ON cxt.instanceid=cm.id
                      JOIN {modules} m ON m.id=cm.module
                     WHERE m.name = :modname AND cxt.contextlevel = :contextlevel";

            $params['modname'] = 'glossary';
            $params['contextlevel'] = CONTEXT_MODULE;

            $DB->execute($sql, $params);

            //now drop glossary_ratings
            $dbman->drop_table($table);
        }

        upgrade_mod_savepoint($result, 2010042800, 'glossary');
    }

    return $result;
}


