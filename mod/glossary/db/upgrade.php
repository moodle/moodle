<?php  //$Id$

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
    global $CFG, $DB;

    $dbman = $DB->get_manager();
    $result = true;

//===== 1.9.0 upgrade line ======//

    if ($result && $oldversion < 2008081700) {

        /////////////////////////////////////
        /// new file storage upgrade code ///
        /////////////////////////////////////

        $fs = get_file_storage();

        $empty = $DB->sql_empty(); // silly oracle empty string handling workaround

        $sqlfrom = "FROM {glossary_entries} ge
                    JOIN {glossary} g ON g.id = ge.glossaryid
                    JOIN {modules} m ON m.name = 'glossary'
                    JOIN {course_modules} cm ON (cm.module = m.id AND cm.instance = g.id)
                   WHERE ge.attachment <> '$empty' AND ge.attachment <> '1'
                ORDER BY g.course, g.id";

        $count = $DB->count_records_sql("SELECT COUNT('x') $sqlfrom");

        if ($rs = $DB->get_recordset_sql("SELECT ge.id, ge.userid, ge.attachment, ge.glossaryid, ge.sourceglossaryid, g.course, cm.id AS cmid $sqlfrom")) {

            $pbar = new progress_bar('migrateglossaryfiles', 500, true);

            $olddebug = $DB->get_debug();
            $DB->set_debug(false); // lower debug level, there might be very many files
            $i = 0;
            foreach ($rs as $entry) {
                $i++;
                upgrade_set_timeout(60); // set up timeout, may also abort execution
                $pbar->update($i, $count, "Migrating glossary entries - $i/$count.");

                $filepath = "$CFG->dataroot/$entry->course/$CFG->moddata/glossary/$entry->glossaryid/$entry->id/$entry->attachment";
                if (!is_readable($filepath)) {
                    //eh - try the second possible location
                    $filepath2 = $filepath;
                    $filepath = "$CFG->dataroot/$entry->course/$CFG->moddata/glossary/$entry->sourceglossaryid/$entry->id/$entry->attachment";
                    
                }
                if (!is_readable($filepath)) {
                    //file missing??
                    notify("File not readable, skipping: $filepath2 and $filepath");
                    $entry->attachment = '';
                    $DB->update_record('glossary_entries', $entry);
                    continue;
                }
                $context = get_context_instance(CONTEXT_MODULE, $entry->cmid);

                $filearea = 'glossary_attachment';
                $filename = clean_param($entry->attachment, PARAM_FILE);
                if ($filename === '') {
                    notify("Unsupported entry filename, skipping: ".$filepath);
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
            $DB->set_debug($olddebug); // reset debug level
            $rs->close();
        }

        upgrade_mod_savepoint($result, 2008081700, 'glossary');
    }

    return $result;
}

?>
