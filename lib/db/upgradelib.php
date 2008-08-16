<?php  //$Id$

/*
 * This file is used for special upgrade functions - for example groups and gradebook.
 * These functions must use SQL and database related functions only- no other Moodle API,
 * because it might depend on db structures that are not yet present during upgrade.
 * (Do not use functions from accesslib.php, grades classes or group functions at all!)
 */

function upgrade_fix_category_depths() {
    global $CFG, $DB;

    // first fix incorrect parents
    $sql = "SELECT c.id
              FROM {course_categories} c
             WHERE c.parent > 0 AND c.parent NOT IN (SELECT pc.id FROM {course_categories} pc)";
    if ($rs = $DB->get_recordset_sql($sql)) {
        foreach ($rs as $cat) {
            $cat->depth  = 1;
            $cat->path   = '/'.$cat->id;
            $cat->parent = 0;
            $DB->update_record('course_categories', $cat);
        }
        $rs->close();
    }

    // now add path and depth to top level categories
    $sql = "UPDATE {course_categories}
               SET depth = 1, path = ".$DB->sql_concat("'/'", "id")."
             WHERE parent = 0";
    $DB->execute($sql);

    // now fix all other levels - slow but works in all supported dbs
    $parentdepth = 1;
    while ($DB->record_exists('course_categories', array('depth'=>0))) {
        $sql = "SELECT c.id, pc.path
                  FROM {course_categories} c, {course_categories} pc
                 WHERE c.parent=pc.id AND c.depth=0 AND pc.depth=?";
        if ($rs = $DB->get_recordset_sql($sql, array($parentdepth))) {
            $DB->set_debug(false);
            foreach ($rs as $cat) {
                $cat->depth = $parentdepth+1;
                $cat->path  = $cat->path.'/'.$cat->id;
                $DB->update_record('course_categories', $cat);
            }
            $rs->close();
            $DB->set_debug(false);
        }
        $parentdepth++;
        if ($parentdepth > 100) {
            //something must have gone wrong - nobody can have more than 100 levels of categories, right?
            debugging('Unknown error fixing category depths');
            break;
        }
    }
}

/**
 * Moves all course files except the moddata to new file storage
 *
 * Unfortunately this function uses core file related functions - it might be necessary to tweak it if something changes there :-(
 */
function upgrade_migrate_files_courses() {
    global $DB, $CFG;
    require_once($CFG->libdir.'/filelib.php');

    $count = $DB->count_records('course');
    $pbar = new progress_bar('migratecoursefiles', 500, true);

    $rs = $DB->get_recordset('course');
    $olddebug = $DB->get_debug();
    $DB->set_debug(false); // lower debug level, there might be many files
    $i = 0;
    foreach ($rs as $course) {
        $i++;
        upgrade_set_timeout(60*5); // set up timeout, may also abort execution
        $context = get_context_instance(CONTEXT_COURSE, $course->id);
        upgrade_migrate_files_course($context, '/', true);
        $pbar->update($i, $count, "Migrated course files - course $i/$count.");
    }
    $DB->set_debug($olddebug); // reset debug level
    $rs->close();

    return true;
}

/**
 * Internal function - do not use directly
 */
function upgrade_migrate_files_course($context, $path, $delete) {
    global $CFG;

    $fullpathname = $CFG->dataroot.'/'.$context->instanceid.$path;
    if (!file_exists($fullpathname)) {
        return;
    }
    $items = new DirectoryIterator($fullpathname);
    $fs = get_file_storage();

    foreach ($items as $item) {
        if ($item->isDot()) {
            continue;
        }

        if ($item->isLink()) {
            // do not delete symbolic links or its children
            $delete_this = false;
        } else {
            $delete_this = $delete;
        }

        if (strpos($path, '/backupdata/') === 0) {
            $filearea = 'course_backup';
            $filepath = substr($path, strlen('/backupdata'));
        } else {
            $filearea = 'course_content';
            $filepath = $path;
        }

        if ($item->isFile()) {
            if (!$item->isReadable()) {
                notify(" File not readable, skipping: ".$fullpathname.$item->getFilename());
                continue;
            }

            $filepath = clean_param($filepath, PARAM_PATH);
            $filename = clean_param($item->getFilename(), PARAM_FILE);

            if ($filename === '') {
                continue;
            }

            if (!$fs->file_exists($context->id, $filearea, '0', $filepath, $filename)) {
                $file_record = array('contextid'=>$context->id, 'filearea'=>$filearea, 'itemid'=>0, 'filepath'=>$filepath, 'filename'=>$filename,
                                     'timecreated'=>$item->getCTime(), 'timemodified'=>$item->getMTime());
                if ($fs->create_file_from_pathname($file_record, $fullpathname.$item->getFilename())) {
                    if ($delete_this) {
                        @unlink($fullpathname.$item->getFilename());
                    }
                }
            }

        } else {
            if ($path == '/' and $item->getFilename() == 'moddata') {
                continue; // modules are responsible
            }

            $filepath = clean_param($filepath.$item->getFilename().'/', PARAM_PATH);
            if ($filepath !== '/backupdata/') {
                $fs->create_directory($context->id, $filearea, 0, $filepath);
            }

            //migrate recursively all subdirectories
            upgrade_migrate_files_course($context, $path.$item->getFilename().'/', $delete_this);
            if ($delete_this) {
                // delete dir if empty
                @rmdir($fullpathname.$item->getFilename());
            }
        }
    }
    unset($items); //release file handles
}

/**
 * Moves all block attachments
 *
 * Unfortunately this function uses core file related functions - it might be necessary to tweak it if something changes there :-(
 */
function upgrade_migrate_files_blog() {
    global $DB, $CFG;

    $fs = get_file_storage();

    $count = $DB->count_records_select('post', "module='blog' AND attachment IS NOT NULL AND attachment <> 1");

    if ($rs = $DB->get_recordset_select('post', "module='blog' AND attachment IS NOT NULL AND attachment <> 1")) {

        upgrade_set_timeout(60*20); // set up timeout, may also abort execution

        $pbar = new progress_bar('migrateblogfiles', 500, true);

        $olddebug = $DB->get_debug();
        $DB->set_debug(false); // lower debug level, there might be many files
        $i = 0;
        foreach ($rs as $entry) {
            $i++;
            $pathname = "$CFG->dataroot/blog/attachments/$entry->id/$entry->attachment";
            if (!file_exists($pathname)) {
                $entry->attachment = NULL;
                $DB->update_record('post', $entry);
                continue;
            }

            $filename = clean_param($entry->attachment, PARAM_FILE);
            if ($filename === '') {
                // weird file name, ignore it
                $entry->attachment = NULL;
                $DB->update_record('post', $entry);
                continue;
            }

            if (!is_readable($pathname)) {
                notify(" File not readable, skipping: ".$pathname);
                continue;
            }

            if (!$fs->file_exists(SYSCONTEXTID, 'blog', $entry->id, '/', $filename)) {
                $file_record = array('contextid'=>SYSCONTEXTID, 'filearea'=>'blog', 'itemid'=>$entry->id, 'filepath'=>'/', 'filename'=>$filename,
                                     'timecreated'=>filectime($pathname), 'timemodified'=>filemtime($pathname), 'userid'=>$entry->userid);
                $fs->create_file_from_pathname($file_record, $pathname);
            }
            @unlink($pathname);
            @rmdir("$CFG->dataroot/blog/attachments/$entry->id/");

            $entry->attachment = 1; // file name not needed there anymore
            $DB->update_record('post', $entry);
            $pbar->update($i, $count, "Migrated blog attachments - $i/$count.");
        }
        $DB->set_debug($olddebug); // reset debug level
        $rs->close();
    }

    @rmdir("$CFG->dataroot/blog/attachments/");
    @rmdir("$CFG->dataroot/blog/");
}
