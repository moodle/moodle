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
 * Folder module upgrade related helper functions
 *
 * @package    mod
 * @subpackage folder
 * @copyright  2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Migrate folder module data from 1.9 resource_old table to new older table
 * @return void
 */
function folder_20_migrate() {
    global $CFG, $DB;
    require_once("$CFG->libdir/filelib.php");
    require_once("$CFG->dirroot/course/lib.php");

    if (!file_exists("$CFG->dirroot/mod/resource/db/upgradelib.php")) {
        // bad luck, somebody deleted resource module
        return;
    }

    require_once("$CFG->dirroot/mod/resource/db/upgradelib.php");

    // create resource_old table and copy resource table there if needed
    if (!resource_20_prepare_migration()) {
        // no modules or fresh install
        return;
    }

    $candidates = $DB->get_recordset('resource_old', array('type'=>'directory', 'migrated'=>0));

    if (!$candidates->valid()) {
        $candidates->close(); // Not going to iterate (but exit), close rs
        return;
    }

    $fs = get_file_storage();

    foreach ($candidates as $candidate) {
        upgrade_set_timeout();

        $directory = '/'.trim($candidate->reference, '/').'/';
        $directory = str_replace('//', '/', $directory);

        if ($CFG->texteditors !== 'textarea') {
            $intro       = text_to_html($candidate->intro, false, false, true);
            $introformat = FORMAT_HTML;
        } else {
            $intro       = $candidate->intro;
            $introformat = FORMAT_MOODLE;
        }

        $folder = new stdClass();
        $folder->course       = $candidate->course;
        $folder->name         = $candidate->name;
        $folder->intro        = $intro;
        $folder->introformat  = $introformat;
        $folder->revision     = 1;
        $folder->timemodified = time();

        if (!$folder = resource_migrate_to_module('folder', $candidate, $folder)) {
            continue;
        }

        // copy files in given directory, skip moddata and backups!
        $context       = get_context_instance(CONTEXT_MODULE, $candidate->cmid);
        $coursecontext = get_context_instance(CONTEXT_COURSE, $candidate->course);
        $files = $fs->get_directory_files($coursecontext->id, 'course', 'legacy', 0, $directory, true, true);
        $file_record = array('contextid'=>$context->id, 'component'=>'mod_folder', 'filearea'=>'content', 'itemid'=>0);
        foreach ($files as $file) {
            $path = $file->get_filepath();
            if (stripos($path, '/backupdata/') === 0 or stripos($path, '/moddata/') === 0) {
                // do not publish protected data!
                continue;
            }
            $relpath = substr($path, strlen($directory) - 1); // keep only subfolder paths
            $file_record['filepath'] = $relpath;
            $fs->create_file_from_storedfile($file_record, $file);
        }
    }

    $candidates->close();

    // clear all course modinfo caches
    rebuild_course_cache(0, true);
}
