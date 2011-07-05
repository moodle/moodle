<?php

// This file keeps track of upgrades to
// the assignment module
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

function xmldb_assignment_upgrade($oldversion) {
    global $CFG, $DB, $OUTPUT;

    $dbman = $DB->get_manager();

//===== 1.9.0 upgrade line ======//

    if ($oldversion < 2007101511) {
        // change grade typo to text if no grades MDL-13920
        require_once $CFG->dirroot.'/mod/assignment/lib.php';
        assignment_upgrade_grades();
        upgrade_mod_savepoint(true, 2007101511, 'assignment');
    }

    if ($oldversion < 2008081900) {

        /////////////////////////////////////
        /// new file storage upgrade code ///
        /////////////////////////////////////

        $fs = get_file_storage();

        $sqlfrom = "FROM {assignment_submissions} s
                    JOIN {assignment} a ON a.id = s.assignment
                    JOIN {modules} m ON m.name = 'assignment'
                    JOIN {course_modules} cm ON (cm.module = m.id AND cm.instance = a.id)";

        $count = $DB->count_records_sql("SELECT COUNT('x') $sqlfrom");

        $rs = $DB->get_recordset_sql("SELECT s.id, s.userid, s.teacher, s.assignment, a.course, cm.id AS cmid $sqlfrom ORDER BY a.course, s.assignment");

        if ($rs->valid()) {
            $pbar = new progress_bar('migrateassignmentfiles', 500, true);
            $i = 0;
            foreach ($rs as $submission) {
                $i++;
                upgrade_set_timeout(180); // set up timeout, may also abort execution
                $pbar->update($i, $count, "Migrating assignment submissions - $i/$count.");

                $basepath = "$CFG->dataroot/$submission->course/$CFG->moddata/assignment/$submission->assignment/$submission->userid/";
                if (!file_exists($basepath)) {
                    //no files
                    continue;
                }
                $context = get_context_instance(CONTEXT_MODULE, $submission->cmid);

                // migrate submitted files first
                $path = $basepath;
                $items = new DirectoryIterator($path);
                foreach ($items as $item) {
                    if (!$item->isFile()) {
                        continue;
                    }
                    if (!$item->isReadable()) {
                        echo $OUTPUT->notification(" File not readable, skipping: ".$path.$item->getFilename());
                        continue;
                    }
                    $filename = clean_param($item->getFilename(), PARAM_FILE);
                    if ($filename === '') {
                        continue;
                    }
                    if (!$fs->file_exists($context->id, 'mod_assignment', 'submission', $submission->id, '/', $filename)) {
                        $file_record = array('contextid'=>$context->id, 'component'=>'mod_assignment', 'filearea'=>'submission', 'itemid'=>$submission->id, 'filepath'=>'/', 'filename'=>$filename, 'userid'=>$submission->userid);
                        if ($fs->create_file_from_pathname($file_record, $path.$item->getFilename())) {
                            unlink($path.$item->getFilename());
                        }
                    }
                }
                unset($items); //release file handles

                // migrate teacher response files for "upload" subtype, unfortunately we do not
                $path = $basepath.'responses/';
                if (file_exists($path)) {
                    $items = new DirectoryIterator($path);
                    foreach ($items as $item) {
                        if (!$item->isFile()) {
                            continue;
                        }
                        $filename = clean_param($item->getFilename(), PARAM_FILE);
                        if ($filename === '') {
                            continue;
                        }
                        if (!$fs->file_exists($context->id, 'mod_assignment', 'response', $submission->id, '/', $filename)) {
                            $file_record = array('contextid'=>$context->id, 'component'=>'mod_assignment', 'filearea'=>'response', 'itemid'=>$submission->id, 'filepath'=>'/', 'filename'=>$filename,
                                                 'timecreated'=>$item->getCTime(), 'timemodified'=>$item->getMTime());
                            if ($submission->teacher) {
                                $file_record['userid'] = $submission->teacher;
                            }
                            if ($fs->create_file_from_pathname($file_record, $path.$item->getFilename())) {
                                unlink($path.$item->getFilename());
                            }
                        }
                    }
                    unset($items); //release file handles
                    @rmdir("$CFG->dataroot/$submission->course/$CFG->moddata/assignment/$submission->assignment/$submission->userid/responses");
                }

                // remove dirs if empty
                @rmdir("$CFG->dataroot/$submission->course/$CFG->moddata/assignment/$submission->assignment/$submission->userid");
                @rmdir("$CFG->dataroot/$submission->course/$CFG->moddata/assignment/$submission->assignment");
                @rmdir("$CFG->dataroot/$submission->course/$CFG->moddata/assignment");
            }
        }
        $rs->close();

        upgrade_mod_savepoint(true, 2008081900, 'assignment');
    }

    if ($oldversion < 2009042000) {

    /// Rename field description on table assignment to intro
        $table = new xmldb_table('assignment');
        $field = new xmldb_field('description', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, 'name');

    /// Launch rename field description
        $dbman->rename_field($table, $field, 'intro');

    /// assignment savepoint reached
        upgrade_mod_savepoint(true, 2009042000, 'assignment');
    }

    if ($oldversion < 2009042001) {

    /// Rename field format on table assignment to introformat
        $table = new xmldb_table('assignment');
        $field = new xmldb_field('format', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'intro');

    /// Launch rename field format
        $dbman->rename_field($table, $field, 'introformat');

    /// assignment savepoint reached
        upgrade_mod_savepoint(true, 2009042001, 'assignment');
    }

    // Moodle v2.1.0 release upgrade line
    // Put any upgrade step following this

    return true;
}


