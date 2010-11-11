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
 * Resource module upgrade related helper functions
 *
 * @package    mod
 * @subpackage scorm
 * @copyright  2010 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Migrate extracted scorm package from moddata to new area if found
 * @param stdClass $scorm
 * @return
 */
function scorm_migrate_moddata_files($scorm, $context) {
    global $CFG;

    // now migrate the extracted package
    $basepath = "$CFG->dataroot/$scorm->course/$CFG->moddata/scorm/$scorm->id";
    if (!is_dir($basepath)) {
        //no files?
        return;
    }

    scorm_migrate_moddata_subdir($context, $basepath, '/');
}
/**
 * Migrates physical scorm package files to proper new file area files
 * @param stdClass $context
 * @param string $base
 * @param string $path
 * @return void
 */
function scorm_migrate_moddata_subdir($context, $base, $path) {
    global $OUTPUT;

    $fullpathname = $base.$path;
    $fs           = get_file_storage();
    $filearea     = 'content';
    $items        = new DirectoryIterator($fullpathname);

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
                echo $OUTPUT->notification(" File not readable, skipping: ".$fullpathname.$item->getFilename());
                unset($item); // release file handle
                continue;
            }

            $filepath    = clean_param($path, PARAM_PATH);
            $filename    = clean_param($item->getFilename(), PARAM_FILE);
            $oldpathname = $fullpathname.$item->getFilename();

            if ($filename === '') {
                continue;
                unset($item); // release file handle
            }

            if (!$fs->file_exists($context->id, 'mod_scorm', $filearea, '0', $filepath, $filename)) {
                $file_record = array('contextid'=>$context->id, 'component'=>'mod_scorm', 'filearea'=>$filearea, 'itemid'=>0, 'filepath'=>$filepath, 'filename'=>$filename,
                                     'timecreated'=>$item->getCTime(), 'timemodified'=>$item->getMTime());
                unset($item); // release file handle
                if ($fs->create_file_from_pathname($file_record, $oldpathname)) {
                    @unlink($oldpathname);
                }
            } else {
                unset($item); // release file handle
            }

        } else {
            //migrate recursively all subdirectories
            $oldpathname = $fullpathname.$item->getFilename().'/';
            $subpath     = $path.$item->getFilename().'/';
            unset($item);  // release file handle
            scorm_migrate_moddata_subdir($context, $base, $subpath);
            @rmdir($oldpathname); // deletes dir if empty
        }
    }
    unset($items); //release file handles
}
