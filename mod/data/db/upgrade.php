<?php

// This file keeps track of upgrades to
// the data module
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installation to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the methods of database_manager class
//
// Please do not forget to use upgrade_set_timeout()
// before any action that may take longer time to finish.

function xmldb_data_upgrade($oldversion) {
    global $CFG, $DB, $OUTPUT;

    $dbman = $DB->get_manager();

//===== 1.9.0 upgrade line ======//

    if ($oldversion < 2007101512) {
    /// Launch add field asearchtemplate again if does not exists yet - reported on several sites

        $table = new xmldb_table('data');
        $field = new xmldb_field('asearchtemplate', XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'jstemplate');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2007101512, 'data');
    }

    if ($oldversion < 2007101513) {
        // Upgrade all the data->notification currently being
        // NULL to 0
        $sql = "UPDATE {data} SET notification=0 WHERE notification IS NULL";
        $DB->execute($sql);

        $table = new xmldb_table('data');
        $field = new xmldb_field('notification', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'editany');
        // First step, Set NOT NULL
        $dbman->change_field_notnull($table, $field);
        // Second step, Set default to 0
        $dbman->change_field_default($table, $field);
        upgrade_mod_savepoint(true, 2007101513, 'data');
    }

    if ($oldversion < 2008081400) {
        $pattern = '/\#\#delete\#\#(\s+)\#\#approve\#\#/';
        $replacement = '##delete##$1##approve##$1##export##';
        $rs = $DB->get_recordset('data');
        foreach ($rs as $data) {
            $data->listtemplate = preg_replace($pattern, $replacement, $data->listtemplate);
            $data->singletemplate = preg_replace($pattern, $replacement, $data->singletemplate);
            $DB->update_record('data', $data);
        }
        $rs->close();

        upgrade_mod_savepoint(true, 2008081400, 'data');
    }

    if ($oldversion < 2008091400) {

        /////////////////////////////////////
        /// new file storage upgrade code ///
        /////////////////////////////////////

        $fs = get_file_storage();

        $empty = $DB->sql_empty(); // silly oracle empty string handling workaround

        $sqlfrom = "FROM {data_content} c
                    JOIN {data_fields} f     ON f.id = c.fieldid
                    JOIN {data_records} r    ON r.id = c.recordid
                    JOIN {data} d            ON d.id = r.dataid
                    JOIN {modules} m         ON m.name = 'data'
                    JOIN {course_modules} cm ON (cm.module = m.id AND cm.instance = d.id)
                   WHERE ".$DB->sql_compare_text('c.content', 2)." <> '$empty' AND c.content IS NOT NULL
                         AND (f.type = 'file' OR f.type = 'picture')";

        $count = $DB->count_records_sql("SELECT COUNT('x') $sqlfrom");

        $rs = $DB->get_recordset_sql("SELECT c.id, f.type, r.dataid, c.recordid, f.id AS fieldid, r.userid, c.content, c.content1, d.course, r.userid, cm.id AS cmid $sqlfrom ORDER BY d.course, d.id");

        if ($rs->valid()) {
            $pbar = new progress_bar('migratedatafiles', 500, true);

            $i = 0;
            foreach ($rs as $content) {
                $i++;
                upgrade_set_timeout(60); // set up timeout, may also abort execution
                $pbar->update($i, $count, "Migrating data entries - $i/$count.");

                $filepath = "$CFG->dataroot/$content->course/$CFG->moddata/data/$content->dataid/$content->fieldid/$content->recordid/$content->content";
                $context = get_context_instance(CONTEXT_MODULE, $content->cmid);

                if (!file_exists($filepath)) {
                    continue;
                }

                $filearea = 'content';
                $oldfilename = $content->content;
                $filename    = clean_param($oldfilename, PARAM_FILE);
                if ($filename === '') {
                    continue;
                }
                if (!$fs->file_exists($context->id, 'mod_data', $filearea, $content->id, '/', $filename)) {
                    $file_record = array('contextid'=>$context->id, 'component'=>'mod_data', 'filearea'=>$filearea, 'itemid'=>$content->id, 'filepath'=>'/', 'filename'=>$filename, 'userid'=>$content->userid);
                    if ($fs->create_file_from_pathname($file_record, $filepath)) {
                        unlink($filepath);
                        if ($oldfilename !== $filename) {
                            // update filename if needed
                            $DB->set_field('data_content', 'content', $filename, array('id'=>$content->id));
                        }
                        if ($content->type == 'picture') {
                            // migrate thumb
                            $filepath = "$CFG->dataroot/$content->course/$CFG->moddata/data/$content->dataid/$content->fieldid/$content->recordid/thumb/$content->content";
                            if (file_exists($filepath)) {
                                if (!$fs->file_exists($context->id, 'mod_data', $filearea, $content->id, '/', 'thumb_'.$filename)) {
                                    $file_record['filename'] = 'thumb_'.$file_record['filename'];
                                    $fs->create_file_from_pathname($file_record, $filepath);
                                }
                                unlink($filepath);
                            }
                        }
                    }
                }

                // remove dirs if empty
                @rmdir("$CFG->dataroot/$content->course/$CFG->moddata/data/$content->dataid/$content->fieldid/$content->recordid/thumb");
                @rmdir("$CFG->dataroot/$content->course/$CFG->moddata/data/$content->dataid/$content->fieldid/$content->recordid");
                @rmdir("$CFG->dataroot/$content->course/$CFG->moddata/data/$content->dataid/$content->fieldid");
                @rmdir("$CFG->dataroot/$content->course/$CFG->moddata/data/$content->dataid");
                @rmdir("$CFG->dataroot/$content->course/$CFG->moddata/data");
                @rmdir("$CFG->dataroot/$content->course/$CFG->moddata");
            }
        }
        $rs->close();

        upgrade_mod_savepoint(true, 2008091400, 'data');
    }

    if ($oldversion < 2008112700) {
        if (!get_config('data', 'requiredentriesfixflag')) {
            $databases = $DB->get_records_sql("SELECT d.*, c.fullname
                                                 FROM {data} d, {course} c
                                                WHERE d.course = c.id
                                                      AND (d.requiredentries > 0 OR d.requiredentriestoview > 0)
                                             ORDER BY c.fullname, d.name");
            if (!empty($databases)) {
                $a = new stdClass();
                $a->text = '';
                foreach($databases as $database) {
                    $a->text .= $database->fullname." - " .$database->name. " (course id: ".$database->course." - database id: ".$database->id.")<br/>";
                }
                //TODO: MDL-17427 send this info to "upgrade log" which will be implemented in 2.0
                echo $OUTPUT->notification(get_string('requiredentrieschanged', 'admin', $a));
            }
        }
        unset_config('requiredentriesfixflag', 'data'); // remove old flag
        upgrade_mod_savepoint(true, 2008112700, 'data');
    }

    if ($oldversion < 2009042000) {

    /// Define field introformat to be added to data
        $table = new xmldb_table('data');
        $field = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'intro');

    /// Launch add field introformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // conditionally migrate to html format in intro
        if ($CFG->texteditors !== 'textarea') {
            $rs = $DB->get_recordset('data', array('introformat'=>FORMAT_MOODLE), '', 'id,intro,introformat');
            foreach ($rs as $d) {
                $d->intro       = text_to_html($d->intro, false, false, true);
                $d->introformat = FORMAT_HTML;
                $DB->update_record('data', $d);
                upgrade_set_timeout();
            }
            $rs->close();
        }

    /// data savepoint reached
        upgrade_mod_savepoint(true, 2009042000, 'data');
    }

    if ($oldversion < 2009111701) {
        upgrade_set_timeout(60*20);

    /// Define table data_comments to be dropped
        $table = new xmldb_table('data_comments');

    /// Conditionally launch drop table for data_comments
        if ($dbman->table_exists($table)) {
            $sql = "SELECT d.id AS dataid,
                           d.course AS courseid,
                           c.userid,
                           r.id AS itemid,
                           c.id AS commentid,
                           c.content AS commentcontent,
                           c.format AS format,
                           c.created AS timecreated
                      FROM {data_comments} c, {data_records} r, {data} d
                     WHERE c.recordid=r.id AND r.dataid=d.id
                  ORDER BY dataid, courseid";
            /// move data comments to comments table
            $lastdataid = null;
            $lastcourseid = null;
            $modcontext = null;
            $rs = $DB->get_recordset_sql($sql);
            foreach($rs as $res) {
                if ($res->dataid != $lastdataid || $res->courseid != $lastcourseid) {
                    $cm = get_coursemodule_from_instance('data', $res->dataid, $res->courseid);
                    if ($cm) {
                        $modcontext = get_context_instance(CONTEXT_MODULE, $cm->id);
                    }
                    $lastdataid = $res->dataid;
                    $lastcourseid = $res->courseid;
                }
                $cmt = new stdClass();
                $cmt->contextid   = $modcontext->id;
                $cmt->commentarea = 'database_entry';
                $cmt->itemid      = $res->itemid;
                $cmt->content     = $res->commentcontent;
                $cmt->format      = $res->format;
                $cmt->userid      = $res->userid;
                $cmt->timecreated = $res->timecreated;
                // comments class will throw an exception if error occurs
                $cmt_id = $DB->insert_record('comments', $cmt);
                if (!empty($cmt_id)) {
                    $DB->delete_records('data_comments', array('id'=>$res->commentid));
                }
            }
            $rs->close();
            // the default exception handler will stop the script if error occurs before
            $dbman->drop_table($table);
        }

    /// data savepoint reached
        upgrade_mod_savepoint(true, 2009111701, 'data');
    }

    if ($oldversion < 2010031602) {
        //add assesstimestart and assesstimefinish columns to data
        $table = new xmldb_table('data');

        $field = new xmldb_field('assesstimestart');
        if (!$dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'assessed');
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('assesstimefinish');
        if (!$dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'assesstimestart');
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2010031602, 'data');
    }

    if ($oldversion < 2010042800) {
        //migrate data ratings to the central rating table
        $table = new xmldb_table('data_ratings');
        if ($dbman->table_exists($table)) {
            //data ratings didnt store time created and modified so Im using the times from the record the rating was attached to
            $sql = "INSERT INTO {rating} (contextid, scaleid, itemid, rating, userid, timecreated, timemodified)

                    SELECT cxt.id, d.scale, r.recordid AS itemid, r.rating, r.userid, re.timecreated AS timecreated, re.timemodified AS timemodified
                      FROM {data_ratings} r
                      JOIN {data_records} re ON r.recordid=re.id
                      JOIN {data} d ON d.id=re.dataid
                      JOIN {course_modules} cm ON cm.instance=d.id
                      JOIN {context} cxt ON cxt.instanceid=cm.id
                      JOIN {modules} m ON m.id=cm.module
                     WHERE m.name = :modname AND cxt.contextlevel = :contextlevel";
            $params['modname'] = 'data';
            $params['contextlevel'] = CONTEXT_MODULE;

            $DB->execute($sql, $params);

            //now drop data_ratings
            $dbman->drop_table($table);
        }

        upgrade_mod_savepoint(true, 2010042800, 'data');
    }

    //rerun the upgrade see MDL-24470
    if ($oldversion < 2010100101) {
        // Upgrade all the data->notification currently being
        // NULL to 0
        $sql = "UPDATE {data} SET notification=0 WHERE notification IS NULL";
        $DB->execute($sql);

        $table = new xmldb_table('data');
        $field = new xmldb_field('notification', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'editany');
        // First step, Set NOT NULL
        $dbman->change_field_notnull($table, $field);
        // Second step, Set default to 0
        $dbman->change_field_default($table, $field);
        upgrade_mod_savepoint(true, 2010100101, 'data');
    }

    if ($oldversion < 2011052300) {
        // rating.component and rating.ratingarea have now been added as mandatory fields.
        // Presently you can only rate data entries so component = 'mod_data' and ratingarea = 'entry'
        // for all ratings with a data context.
        // We want to update all ratings that belong to a data context and don't already have a
        // component set.
        // This could take a while reset upgrade timeout to 5 min
        upgrade_set_timeout(60 * 20);
        $sql = "UPDATE {rating}
                SET component = 'mod_data', ratingarea = 'entry'
                WHERE contextid IN (
                    SELECT ctx.id
                      FROM {context} ctx
                      JOIN {course_modules} cm ON cm.id = ctx.instanceid
                      JOIN {modules} m ON m.id = cm.module
                     WHERE ctx.contextlevel = 70 AND
                           m.name = 'data'
                ) AND component = 'unknown'";
        $DB->execute($sql);

        upgrade_mod_savepoint(true, 2011052300, 'data');
    }

    // Moodle v2.1.0 release upgrade line
    // Put any upgrade step following this

    return true;
}


