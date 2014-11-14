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
 * Mandatory public API of imscp module
 *
 * @package mod_imscp
 * @copyright  2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * List of features supported in IMS CP module
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function imscp_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_ARCHETYPE:           return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_GROUPS:                  return false;
        case FEATURE_GROUPINGS:               return false;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE:         return false;
        case FEATURE_GRADE_OUTCOMES:          return false;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_SHOW_DESCRIPTION:        return true;

        default: return null;
    }
}

/**
 * Returns all other caps used in module
 * @return array
 */
function imscp_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function imscp_reset_userdata($data) {
    return array();
}

/**
 * List the actions that correspond to a view of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = 'r' and edulevel = LEVEL_PARTICIPATING will
 *       be considered as view action.
 *
 * @return array
 */
function imscp_get_view_actions() {
    return array('view', 'view all');
}

/**
 * List the actions that correspond to a post of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = ('c' || 'u' || 'd') and edulevel = LEVEL_PARTICIPATING
 *       will be considered as post action.
 *
 * @return array
 */
function imscp_get_post_actions() {
    return array('update', 'add');
}

/**
 * Add imscp instance.
 * @param object $data
 * @param object $mform
 * @return int new imscp instance id
 */
function imscp_add_instance($data, $mform) {
    global $CFG, $DB;
    require_once("$CFG->dirroot/mod/imscp/locallib.php");

    $cmid = $data->coursemodule;

    $data->timemodified = time();
    $data->revision     = 1;
    $data->structure    = null;

    $data->id = $DB->insert_record('imscp', $data);

    // we need to use context now, so we need to make sure all needed info is already in db
    $DB->set_field('course_modules', 'instance', $data->id, array('id'=>$cmid));
    $context = context_module::instance($cmid);
    $imscp = $DB->get_record('imscp', array('id'=>$data->id), '*', MUST_EXIST);

    if (!empty($data->package)) {
        // Save uploaded files to 'backup' filearea.
        $fs = get_file_storage();
        $fs->delete_area_files($context->id, 'mod_imscp', 'backup', 1);
        file_save_draft_area_files($data->package, $context->id, 'mod_imscp', 'backup',
            1, array('subdirs' => 0, 'maxfiles' => 1));
        // Get filename of zip that was uploaded.
        $files = $fs->get_area_files($context->id, 'mod_imscp', 'backup', 1, '', false);
        if ($files) {
            // Extract package content to 'content' filearea.
            $package = reset($files);
            $packer = get_file_packer('application/zip');
            $package->extract_to_storage($packer, $context->id, 'mod_imscp', 'content', 1, '/');
            $structure = imscp_parse_structure($imscp, $context);
            $imscp->structure = is_array($structure) ? serialize($structure) : null;
            $DB->update_record('imscp', $imscp);
        }
    }

    return $data->id;
}

/**
 * Update imscp instance.
 * @param object $data
 * @param object $mform
 * @return bool true
 */
function imscp_update_instance($data, $mform) {
    global $CFG, $DB;
    require_once("$CFG->dirroot/mod/imscp/locallib.php");

    $cmid = $data->coursemodule;

    $data->timemodified = time();
    $data->id           = $data->instance;
    $data->structure   = null; // better reparse structure after each update

    $DB->update_record('imscp', $data);

    $context = context_module::instance($cmid);
    $imscp = $DB->get_record('imscp', array('id'=>$data->id), '*', MUST_EXIST);

    if (!empty($data->package) && ($draftareainfo = file_get_draft_area_info($data->package)) &&
            $draftareainfo['filecount']) {
        $fs = get_file_storage();

        $imscp->revision++;
        $DB->update_record('imscp', $imscp);

        // get a list of existing packages before adding new package
        if ($imscp->keepold > -1) {
            $packages = $fs->get_area_files($context->id, 'mod_imscp', 'backup', false, "itemid ASC", false);
        } else {
            $packages = array();
        }

        file_save_draft_area_files($data->package, $context->id, 'mod_imscp', 'backup',
            $imscp->revision, array('subdirs' => 0, 'maxfiles' => 1));
        $files = $fs->get_area_files($context->id, 'mod_imscp', 'backup', $imscp->revision, '', false);
        $package = reset($files);

        // purge all extracted content
        $fs->delete_area_files($context->id, 'mod_imscp', 'content');

        // extract package content
        if ($package) {
            $packer = get_file_packer('application/zip');
            $package->extract_to_storage($packer, $context->id, 'mod_imscp', 'content', $imscp->revision, '/');
        }

        // cleanup old package files, keep current + keepold
        while ($packages and (count($packages) > $imscp->keepold)) {
            $package = array_shift($packages);
            $fs->delete_area_files($context->id, 'mod_imscp', 'backup', $package->get_itemid());
        }
    }

    $structure = imscp_parse_structure($imscp, $context);
    $imscp->structure = is_array($structure) ? serialize($structure) : null;
    $DB->update_record('imscp', $imscp);

    return true;
}

/**
 * Delete imscp instance.
 * @param int $id
 * @return bool true
 */
function imscp_delete_instance($id) {
    global $DB;

    if (!$imscp = $DB->get_record('imscp', array('id'=>$id))) {
        return false;
    }

    // note: all context files are deleted automatically

    $DB->delete_records('imscp', array('id'=>$imscp->id));

    return true;
}

/**
 * Lists all browsable file areas
 *
 * @package  mod_imscp
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @return array
 */
function imscp_get_file_areas($course, $cm, $context) {
    $areas = array();

    $areas['content'] = get_string('areacontent', 'imscp');
    $areas['backup']  = get_string('areabackup', 'imscp');

    return $areas;
}

/**
 * File browsing support for imscp module ontent area.
 *
 * @package  mod_imscp
 * @category files
 * @param stdClass $browser file browser
 * @param stdClass $areas file areas
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param int $itemid item ID
 * @param string $filepath file path
 * @param string $filename file name
 * @return file_info instance or null if not found
 */
function imscp_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    global $CFG, $DB;

    // note: imscp_intro handled in file_browser automatically

    if (!has_capability('moodle/course:managefiles', $context)) {
        // no peaking here for students!!
        return null;
    }

    if ($filearea !== 'content' and $filearea !== 'backup') {
        return null;
    }

    require_once("$CFG->dirroot/mod/imscp/locallib.php");

    if (is_null($itemid)) {
        return new imscp_file_info($browser, $course, $cm, $context, $areas, $filearea, $itemid);
    }

    $fs = get_file_storage();
    $filepath = is_null($filepath) ? '/' : $filepath;
    $filename = is_null($filename) ? '.' : $filename;
    if (!$storedfile = $fs->get_file($context->id, 'mod_imscp', $filearea, $itemid, $filepath, $filename)) {
        return null;
    }

    // do not allow manual modification of any files!
    $urlbase = $CFG->wwwroot.'/pluginfile.php';
    return new file_info_stored($browser, $context, $storedfile, $urlbase, $itemid, true, true, false, false); //no writing here!
}

/**
 * Serves the imscp files.
 *
 * @package  mod_imscp
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if file not found, does not return if found - justsend the file
 */
function imscp_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $CFG, $DB;

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_login($course, true, $cm);

    if ($filearea === 'content') {
        if (!has_capability('mod/imscp:view', $context)) {
            return false;
        }
        $revision = array_shift($args);
        $fs = get_file_storage();
        $relativepath = implode('/', $args);
        if ($relativepath === 'imsmanifest.xml') {
            if (!has_capability('moodle/course:managefiles', $context)) {
                // no stealing of detailed package info ;-)
                return false;
            }
        }
        $fullpath = "/$context->id/mod_imscp/$filearea/$revision/$relativepath";
        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            return false;
        }

        // finally send the file
        send_stored_file($file, null, 0, $forcedownload, $options);

    } else if ($filearea === 'backup') {
        if (!has_capability('moodle/course:managefiles', $context)) {
            // no stealing of package backups
            return false;
        }
        $revision = array_shift($args);
        $fs = get_file_storage();
        $relativepath = implode('/', $args);
        $fullpath = "/$context->id/mod_imscp/$filearea/$revision/$relativepath";
        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            return false;
        }

        // finally send the file
        send_stored_file($file, null, 0, $forcedownload, $options);

    } else {
        return false;
    }
}

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function imscp_page_type_list($pagetype, $parentcontext, $currentcontext) {
    $module_pagetype = array('mod-imscp-*'=>get_string('page-mod-imscp-x', 'imscp'));
    return $module_pagetype;
}
