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
 * IMS CP module upgrade related helper functions
 *
 * @package    mod
 * @subpackage imscp
 * @copyright  2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Migrate imscp module data from 1.9 resource_old table to new imscp table
 * @return void
 */
function imscp_20_migrate() {
    global $CFG, $DB, $OUTPUT;
    require_once("$CFG->libdir/filelib.php");
    require_once("$CFG->dirroot/course/lib.php");
    require_once("$CFG->dirroot/mod/imscp/locallib.php");

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

    $candidates = $DB->get_recordset('resource_old', array('type'=>'ims', 'migrated'=>0));
    if (!$candidates->valid()) {
        $candidates->close(); // Not going to iterate (but exit), close rs
        return;
    }

    $fs = get_file_storage();

    foreach ($candidates as $candidate) {
        upgrade_set_timeout(60);

        if ($CFG->texteditors !== 'textarea') {
            $intro       = text_to_html($candidate->intro, false, false, true);
            $introformat = FORMAT_HTML;
        } else {
            $intro       = $candidate->intro;
            $introformat = FORMAT_MOODLE;
        }

        $imscp = new stdClass();
        $imscp->course       = $candidate->course;
        $imscp->name         = $candidate->name;
        $imscp->intro        = $intro;
        $imscp->introformat  = $introformat;
        $imscp->revision     = 1;
        $imscp->keepold      = 1;
        $imscp->timemodified = time();

        if (!$imscp = resource_migrate_to_module('imscp', $candidate, $imscp)) {
            continue;
        }

        $context = get_context_instance(CONTEXT_MODULE, $candidate->cmid);
        $root = "$CFG->dataroot/$candidate->course/$CFG->moddata/resource/$candidate->oldid";

        // migrate package backup file
        if ($candidate->reference) {
            $package = basename($candidate->reference);
            $fullpath = $root.'/'.$package;
            if (file_exists($fullpath)) {
                $file_record = array('contextid' => $context->id,
                                     'component' => 'mod_imscp',
                                     'filearea'  => 'backup',
                                     'itemid'    => 1,
                                     'filepath'  => '/',
                                     'filename'  => $package);
                $fs->create_file_from_pathname($file_record, $fullpath);
            }
        }

        // migrate extracted package data
        $files = imsc_migrate_get_old_files($root, '');
        if (empty($files)) {
            // if ims package doesn't exist, continue loop
            echo $OUTPUT->notification("IMS package data cannot be found, failed migrating activity: \"$candidate->name\", please fix it manually");
            continue;
        }

        $file_record = array('contextid'=>$context->id, 'component'=>'mod_imscp', 'filearea'=>'content', 'itemid'=>1);
        $error = false;
        foreach ($files as $relname=>$fullpath) {
            $parts = explode('/', $relname);
            $file_record['filename'] = array_pop($parts);
            $parts[] = ''; // keep trailing slash
            $file_record['filepath'] = implode('/', $parts);

            try {
                $fs->create_file_from_pathname($file_record, $fullpath);
            } catch (Exception $e) {
                //continue on error, we can not recover anyway
                $error = true;
                echo $OUTPUT->notification("IMSCP: failed migrating file: $relname");
            }
        }
        unset($files);

        // parse manifest
        $structure = imscp_parse_structure($imscp, $context);
        $imscp->structure = is_array($structure) ? serialize($structure) : null;
        $DB->update_record('imscp', $imscp);

        // remove old moddata dir only if no error and manifest ok
        if (!$error and is_array($structure)) {
            fulldelete($root);
        }
    }
    $candidates->close();

    // clear all course modinfo caches
    rebuild_course_cache(0, true);
}

/**
 * Private function returning all extracted IMS content package file
 */
function imsc_migrate_get_old_files($path, $relative) {
    global $OUTPUT;
    $result = array();
    if (!file_exists($path)) {
        echo $OUTPUT->notification("File path doesn't exist: $path <br/> Please fix it manually.");
        return array();
    }
    $items = new DirectoryIterator($path);
    foreach ($items as $item) {
        if ($item->isDot() or $item->isLink()) {
            // symbolic links could create infinite loops or cause unintended file migration, sorry
            continue;
        }
        $pathname = $item->getPathname();
        $relname  = $relative.'/'.$item->getFilename();
        if ($item->isFile()) {
            $result[$relname] = $pathname;
        } else if ($item->isDir()) {
            $result = array_merge($result, imsc_migrate_get_old_files($pathname, $relname));
        }
        unset($item);
    }
    unset($items);
    return $result;
}
