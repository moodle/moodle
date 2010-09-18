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
 * @subpackage lesson
 * @copyright  2010 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Migrate lesson images incorrectly placed into moddata folder,
 * the images were stored in random directories ignoring all coding rules.
 *
 * @return void
 */
function lesson_20_migrate_moddata_mixture($courseid, $path) {
    global $CFG, $DB, $OUTPUT;

    $fullpathname = "$CFG->dataroot/$courseid".$path;

    if (!file_exists($fullpathname)) {
        return;
    }

    $context = get_context_instance(CONTEXT_COURSE, $courseid);
    $items = new DirectoryIterator($fullpathname);
    $fs = get_file_storage();

    foreach ($items as $item) {
        if ($item->isDot()) {
            // skip
            continue;
        }

        if ($item->isFile()) {
            if (!$item->isReadable()) {
                echo $OUTPUT->notification(" File not readable, skipping: ".$courseid.$path.$item->getFilename());
                continue;
            }

            $filepath = clean_param($path, PARAM_PATH);
            $filename = clean_param($item->getFilename(), PARAM_FILE);

            if ($filename === '') {
                //unsupported chars, sorry
                continue;
            }

            if (!$fs->file_exists($context->id, 'course', 'legacy', '0', $filepath, $filename)) {
                $file_record = array('contextid'=>$context->id, 'component'=>'course', 'filearea'=>'legacy', 'itemid'=>0, 'filepath'=>$filepath, 'filename'=>$filename,
                                     'timecreated'=>$item->getCTime(), 'timemodified'=>$item->getMTime());
                $fs->create_file_from_pathname($file_record, $fullpathname.$item->getFilename());
                @unlink($fullpathname.$item->getFilename());
            }

        } else {
            //migrate recursively all subdirectories
            lesson_20_migrate_moddata_mixture($courseid, $path.$item->getFilename().'/');
        }
    }

    unset($item); //release file handles
    unset($items); //release file handles

    // delete dir if empty
    @rmdir($fullpathname);
}