<?php
// This file is part of Book module for Moodle - http://moodle.org/
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
 * Book module upgrade related helper functions
 *
 * @package    mod_book
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Migrate book files stored in moddata folders.
 *
 * Please note it was a big mistake to store the files there in the first place!
 *
 * @param stdClass $book
 * @param stdClass $context
 * @param string $path
 * @return void
 */
function mod_book_migrate_moddata_dir_to_legacy($book, $context, $path) {
    global $OUTPUT, $CFG;

    $base = "$CFG->dataroot/$book->course/$CFG->moddata/book/$book->id";
    $fulldir = $base.$path;

    if (!is_dir($fulldir)) {
        // does not exist
        return;
    }

    $fs      = get_file_storage();
    $items   = new DirectoryIterator($fulldir);

    foreach ($items as $item) {
        if ($item->isDot()) {
            unset($item); // release file handle
            continue;
        }

        if ($item->isLink()) {
            // do not follow symlinks - they were never supported in moddata, sorry
            unset($item); // release file handle
            continue;
        }

        if ($item->isFile()) {
            if (!$item->isReadable()) {
                echo $OUTPUT->notification(" File not readable, skipping: ".$fulldir.$item->getFilename());
                unset($item); // release file handle
                continue;
            }

            $filepath = clean_param("/$CFG->moddata/book/$book->id".$path, PARAM_PATH);
            $filename = clean_param($item->getFilename(), PARAM_FILE);

            if ($filename === '') {
                // unsupported chars, sorry
                unset($item); // release file handle
                continue;
            }

            if (core_text::strlen($filepath) > 255) {
                echo $OUTPUT->notification(" File path longer than 255 chars, skipping: ".$fulldir.$item->getFilename());
                unset($item); // release file handle
                continue;
            }

            if (!$fs->file_exists($context->id, 'course', 'legacy', '0', $filepath, $filename)) {
                $file_record = array('contextid'=>$context->id, 'component'=>'course', 'filearea'=>'legacy', 'itemid'=>0, 'filepath'=>$filepath, 'filename'=>$filename,
                                     'timecreated'=>$item->getCTime(), 'timemodified'=>$item->getMTime());
                $fs->create_file_from_pathname($file_record, $fulldir.$item->getFilename());
            }
            $oldpathname = $fulldir.$item->getFilename();
            unset($item); // release file handle
            @unlink($oldpathname);

        } else {
            // migrate recursively all subdirectories
            $oldpathname = $base.$item->getFilename().'/';
            $subpath     = $path.$item->getFilename().'/';
            unset($item);  // release file handle
            mod_book_migrate_moddata_dir_to_legacy($book, $context, $subpath);
            @rmdir($oldpathname); // deletes dir if empty
        }
    }
    unset($items); // release file handles
}

/**
 * Migrate legacy files in intro and chapters
 * @return void
 */
function mod_book_migrate_all_areas() {
    global $DB, $OUTPUT;

    $rsbooks = $DB->get_recordset('book');
    foreach($rsbooks as $book) {
        upgrade_set_timeout(360); // set up timeout, may also abort execution
        $cm = get_coursemodule_from_instance('book', $book->id);
        if (empty($cm) || empty($cm->id)) {
             echo $OUTPUT->notification("Course module not found, skipping: {$book->name}");
             continue;
        }
        $context = context_module::instance($cm->id);
        mod_book_migrate_area($book, 'intro', 'book', $book->course, $context, 'mod_book', 'intro', 0);

        $rschapters = $DB->get_recordset('book_chapters', array('bookid'=>$book->id));
        foreach ($rschapters as $chapter) {
            mod_book_migrate_area($chapter, 'content', 'book_chapters', $book->course, $context, 'mod_book', 'chapter', $chapter->id);
        }
        $rschapters->close();
    }
    $rsbooks->close();
}

/**
 * Migrate one area, this should be probably part of moodle core...
 *
 * @param stdClass $record object to migrate files (book, chapter)
 * @param string $field field in the record we are going to migrate
 * @param string $table DB table containing the information to migrate
 * @param int $courseid id of the course the book module belongs to
 * @param context_module $context context of the book module
 * @param string $component component to be used for the migrated files
 * @param string $filearea filearea to be used for the migrated files
 * @param int $itemid id to be used for the migrated files
 * @return void
 */
function mod_book_migrate_area($record, $field, $table, $courseid, $context, $component, $filearea, $itemid) {
    global $CFG, $DB;

    $fs = get_file_storage();

    foreach(array(get_site()->id, $courseid) as $cid) {
        $matches = null;
        $ooldcontext = context_course::instance($cid);
        if (preg_match_all("|$CFG->wwwroot/file.php(\?file=)?/$cid(/[^\s'\"&\?#]+)|", $record->$field, $matches)) {
            $file_record = array('contextid'=>$context->id, 'component'=>$component, 'filearea'=>$filearea, 'itemid'=>$itemid);
            foreach ($matches[2] as $i=>$filepath) {
                if (!$file = $fs->get_file_by_hash(sha1("/$ooldcontext->id/course/legacy/0".$filepath))) {
                    continue;
                }
                try {
                    if (!$newfile = $fs->get_file_by_hash(sha1("/$context->id/$component/$filearea/$itemid".$filepath))) {
                        $fs->create_file_from_storedfile($file_record, $file);
                    }
                    $record->$field = str_replace($matches[0][$i], '@@PLUGINFILE@@'.$filepath, $record->$field);
                } catch (Exception $ex) {
                    // ignore problems
                }
                $DB->set_field($table, $field, $record->$field, array('id'=>$record->id));
            }
        }
    }
}