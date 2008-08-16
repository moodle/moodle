<?php  //$Id$

// This file keeps track of upgrades to
// the assignment module
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

function xmldb_assignment_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();
    $result = true;

//===== 1.9.0 upgrade line ======//

    if ($result && $oldversion < 2007101511) {
        // change grade typo to text if no grades MDL-13920
        require_once $CFG->dirroot.'/mod/assignment/lib.php';
        assignment_upgrade_grades();
        upgrade_mod_savepoint($result, 2007101511, 'assignment');
    }

    if ($result && $oldversion < 2008073000) {

        /////////////////////////////////////
        /// new file storage upgrade code ///
        /////////////////////////////////////

        $fs = get_file_storage();

        $sql = "SELECT s.id, s.userid, s.teacher, s.assignment, a.course
                  FROM {assignment_submissions} s
                  JOIN {assignment} a ON a.id = s.assignment
              ORDER BY a.course, s.assignment";

        $count = $DB->count_records_sql($sql); 

        $lastcourse     = 0;
        $lastassignment = 0;

        if ($rs = $DB->get_recordset_sql($sql)) {

            $pbar = new progress_bar('migrateassignmentfiles', 500, true);

            $olddebug = $DB->get_debug();
            $DB->set_debug(false); // lower debug level, there might be many files
            $i = 0;
            foreach ($rs as $submission) {
                $i++;
                upgrade_set_timeout(180); // set up timeout, may also abort execution
                $basepath = "$CFG->dataroot/$submission->course/$CFG->moddata/assignment/$submission->assignment/$submission->userid/";
                if (!file_exists($basepath)) {
                    //no files
                    continue;
                }
                $context = get_context_instance(CONTEXT_MODULE, $submission->assignment);

                // migrate submitted files fisrt
                $path = $basepath;
                $filearea = 'assignment_submission';
                $items = new DirectoryIterator($path);
                foreach ($items as $item) {
                    if (!$item->isFile()) {
                        continue;
                    }
                    if (!$item->isReadable()) {
                        notify(" File not readable, skipping: ".$path.$item->getFilename());
                        continue;
                    }
                    $filename = clean_param($item->getFilename(), PARAM_FILE);
                    if ($filename === '') {
                        continue;
                    }
                    if (!$fs->file_exists($context->id, $filearea, '0', '/', $filename)) {
                        $file_record = array('contextid'=>$context->id, 'filearea'=>$filearea, 'itemid'=>$submission->userid, 'filepath'=>'/', 'filename'=>$filename, 'userid'=>$submission->userid);
                        if ($fs->create_file_from_pathname($file_record, $path.$item->getFilename())) {
                            unlink($path.$item->getFilename());
                        }
                    }
                }
                unset($items); //release file handles

                // migrate teacher response files
                $path = $basepath.'responses/';
                if (file_exists($path)) {
                    $filearea = 'assignment_response';
                    $items = new DirectoryIterator($path);
                    foreach ($items as $item) {
                        if (!$item->isFile()) {
                            continue;
                        }
                        $filename = clean_param($item->getFilename(), PARAM_FILE);
                        if ($filename === '') {
                            continue;
                        }
                        if (!$fs->file_exists($context->id, $filearea, '0', '/', $filename)) {
                            $file_record = array('contextid'=>$context->id, 'filearea'=>$filearea, 'itemid'=>$submission->userid, 'filepath'=>'/', 'filename'=>$filename,
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
                    @rmdir("$CFG->dataroot/$submission->course/$CFG->moddata/assignment/$submission->assignment/$submission->userid/responses/");
                }

                @rmdir("$CFG->dataroot/$submission->course/$CFG->moddata/assignment/$submission->assignment/$submission->userid/");

                if ($lastassignment and $lastassignment != $submission->assignment) {
                    @rmdir("$CFG->dataroot/$lastcourse/$CFG->moddata/assignment/$lastassignment");
                }

                if ($lastcourse and $lastcourse != $submission->course) {
                    @rmdir("$CFG->dataroot/$lastcourse/$CFG->moddata/assignment");
                    @rmdir("$CFG->dataroot/$lastcourse/$CFG->moddata");
                    @rmdir("$CFG->dataroot/$lastcourse");
                }
                $lastsubmission = $submission->assignment;
                $lastcourse     = $submission->course;

                $pbar->update($i, $count, "Migrated assignment submissions - $i/$count.");
            }
            $DB->set_debug($olddebug); // reset debug level
            $rs->close();

            // cleanup after the last submission
            if ($lastcourse) {
                @rmdir("$CFG->dataroot/$lastcourse/$CFG->moddata/assignment/$lastassignment");
                @rmdir("$CFG->dataroot/$lastcourse/$CFG->moddata/assignment");
                @rmdir("$CFG->dataroot/$lastcourse/$CFG->moddata");
                @rmdir("$CFG->dataroot/$lastcourse");
            }
        }

        upgrade_mod_savepoint($result, 2008073000, 'assignment');
    }

    return $result;
}

?>
