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
 * Resource module upgrade related helper functions
 *
 * @package    mod
 * @subpackage book
 * @copyright  2010 Petr Skoda  {@link http://skodak.org}
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
function book_migrate_moddata_dir_to_legacy($book, $context, $path) {
    global $OUTPUT, $CFG;

    $base = "$CFG->dataroot/$book->course/$CFG->moddata/book/$book->id";
    $fulldir = $base.$path;

    if (!is_dir($fulldir)) {
        // does not exist
        return;
    }

    $fs      = get_file_storage();
    $textlib = textlib_get_instance();
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
                //unsupported chars, sorry
                unset($item); // release file handle
                continue;
            }

            if ($textlib->strlen($filepath) > 255) {
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
            //migrate recursively all subdirectories
            $oldpathname = $base.$item->getFilename().'/';
            $subpath     = $path.$item->getFilename().'/';
            unset($item);  // release file handle
            book_migrate_moddata_dir_to_legacy($book, $context, $subpath);
            @rmdir($oldpathname); // deletes dir if empty
        }
    }
    unset($items); //release file handles
}