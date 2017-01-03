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
 * Mandatory public API of folder module
 *
 * @package   mod_folder
 * @copyright 2009 Petr Skoda  {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/** Display folder contents on a separate page */
define('FOLDER_DISPLAY_PAGE', 0);
/** Display folder contents inline in a course */
define('FOLDER_DISPLAY_INLINE', 1);

/**
 * List of features supported in Folder module
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function folder_supports($feature) {
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
function folder_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function folder_reset_userdata($data) {
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
function folder_get_view_actions() {
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
function folder_get_post_actions() {
    return array('update', 'add');
}

/**
 * Add folder instance.
 * @param object $data
 * @param object $mform
 * @return int new folder instance id
 */
function folder_add_instance($data, $mform) {
    global $DB;

    $cmid        = $data->coursemodule;
    $draftitemid = $data->files;

    $data->timemodified = time();
    $data->id = $DB->insert_record('folder', $data);

    // we need to use context now, so we need to make sure all needed info is already in db
    $DB->set_field('course_modules', 'instance', $data->id, array('id'=>$cmid));
    $context = context_module::instance($cmid);

    if ($draftitemid) {
        file_save_draft_area_files($draftitemid, $context->id, 'mod_folder', 'content', 0, array('subdirs'=>true));
    }

    return $data->id;
}

/**
 * Update folder instance.
 * @param object $data
 * @param object $mform
 * @return bool true
 */
function folder_update_instance($data, $mform) {
    global $CFG, $DB;

    $cmid        = $data->coursemodule;
    $draftitemid = $data->files;

    $data->timemodified = time();
    $data->id           = $data->instance;
    $data->revision++;

    $DB->update_record('folder', $data);

    $context = context_module::instance($cmid);
    if ($draftitemid = file_get_submitted_draft_itemid('files')) {
        file_save_draft_area_files($draftitemid, $context->id, 'mod_folder', 'content', 0, array('subdirs'=>true));
    }

    return true;
}

/**
 * Delete folder instance.
 * @param int $id
 * @return bool true
 */
function folder_delete_instance($id) {
    global $DB;

    if (!$folder = $DB->get_record('folder', array('id'=>$id))) {
        return false;
    }

    // note: all context files are deleted automatically

    $DB->delete_records('folder', array('id'=>$folder->id));

    return true;
}

/**
 * Lists all browsable file areas
 *
 * @package  mod_folder
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @return array
 */
function folder_get_file_areas($course, $cm, $context) {
    $areas = array();
    $areas['content'] = get_string('foldercontent', 'folder');

    return $areas;
}

/**
 * File browsing support for folder module content area.
 *
 * @package  mod_folder
 * @category files
 * @param file_browser $browser file browser instance
 * @param array $areas file areas
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param int $itemid item ID
 * @param string $filepath file path
 * @param string $filename file name
 * @return file_info instance or null if not found
 */
function folder_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    global $CFG;


    if ($filearea === 'content') {
        if (!has_capability('mod/folder:view', $context)) {
            return NULL;
        }
        $fs = get_file_storage();

        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;
        if (!$storedfile = $fs->get_file($context->id, 'mod_folder', 'content', 0, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($context->id, 'mod_folder', 'content', 0);
            } else {
                // not found
                return null;
            }
        }

        require_once("$CFG->dirroot/mod/folder/locallib.php");
        $urlbase = $CFG->wwwroot.'/pluginfile.php';

        // students may read files here
        $canwrite = has_capability('mod/folder:managefiles', $context);
        return new folder_content_file_info($browser, $context, $storedfile, $urlbase, $areas[$filearea], true, true, $canwrite, false);
    }

    // note: folder_intro handled in file_browser automatically

    return null;
}

/**
 * Serves the folder files.
 *
 * @package  mod_folder
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if file not found, does not return if found - just send the file
 */
function folder_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $CFG, $DB;

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_course_login($course, true, $cm);
    if (!has_capability('mod/folder:view', $context)) {
        return false;
    }

    if ($filearea !== 'content') {
        // intro is handled automatically in pluginfile.php
        return false;
    }

    array_shift($args); // ignore revision - designed to prevent caching problems only

    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = "/$context->id/mod_folder/content/0/$relativepath";
    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        return false;
    }

    // finally send the file
    // for folder module, we force download file all the time
    send_stored_file($file, 0, 0, true, $options);
}

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function folder_page_type_list($pagetype, $parentcontext, $currentcontext) {
    $module_pagetype = array('mod-folder-*'=>get_string('page-mod-folder-x', 'folder'));
    return $module_pagetype;
}

/**
 * Export folder resource contents
 *
 * @return array of file content
 */
function folder_export_contents($cm, $baseurl) {
    global $CFG, $DB;
    $contents = array();
    $context = context_module::instance($cm->id);
    $folder = $DB->get_record('folder', array('id'=>$cm->instance), '*', MUST_EXIST);

    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'mod_folder', 'content', 0, 'sortorder DESC, id ASC', false);

    foreach ($files as $fileinfo) {
        $file = array();
        $file['type'] = 'file';
        $file['filename']     = $fileinfo->get_filename();
        $file['filepath']     = $fileinfo->get_filepath();
        $file['filesize']     = $fileinfo->get_filesize();
        $file['fileurl']      = file_encode_url("$CFG->wwwroot/" . $baseurl, '/'.$context->id.'/mod_folder/content/'.$folder->revision.$fileinfo->get_filepath().$fileinfo->get_filename(), true);
        $file['timecreated']  = $fileinfo->get_timecreated();
        $file['timemodified'] = $fileinfo->get_timemodified();
        $file['sortorder']    = $fileinfo->get_sortorder();
        $file['userid']       = $fileinfo->get_userid();
        $file['author']       = $fileinfo->get_author();
        $file['license']      = $fileinfo->get_license();
        $contents[] = $file;
    }

    return $contents;
}

/**
 * Register the ability to handle drag and drop file uploads
 * @return array containing details of the files / types the mod can handle
 */
function folder_dndupload_register() {
    return array('files' => array(
                     array('extension' => 'zip', 'message' => get_string('dnduploadmakefolder', 'mod_folder'))
                 ));
}

/**
 * Handle a file that has been uploaded
 * @param object $uploadinfo details of the file / content that has been uploaded
 * @return int instance id of the newly created mod
 */
function folder_dndupload_handle($uploadinfo) {
    global $DB, $USER;

    // Gather the required info.
    $data = new stdClass();
    $data->course = $uploadinfo->course->id;
    $data->name = $uploadinfo->displayname;
    $data->intro = '<p>'.$uploadinfo->displayname.'</p>';
    $data->introformat = FORMAT_HTML;
    $data->coursemodule = $uploadinfo->coursemodule;
    $data->files = null; // We will unzip the file and sort out the contents below.

    $data->id = folder_add_instance($data, null);

    // Retrieve the file from the draft file area.
    $context = context_module::instance($uploadinfo->coursemodule);
    file_save_draft_area_files($uploadinfo->draftitemid, $context->id, 'mod_folder', 'temp', 0, array('subdirs'=>true));
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'mod_folder', 'temp', 0, 'sortorder', false);
    // Only ever one file - extract the contents.
    $file = reset($files);

    $success = $file->extract_to_storage(new zip_packer(), $context->id, 'mod_folder', 'content', 0, '/', $USER->id);
    $fs->delete_area_files($context->id, 'mod_folder', 'temp', 0);

    if ($success) {
        return $data->id;
    }

    $DB->delete_records('folder', array('id' => $data->id));
    return false;
}

/**
 * Given a coursemodule object, this function returns the extra
 * information needed to print this activity in various places.
 *
 * If folder needs to be displayed inline we store additional information
 * in customdata, so functions {@link folder_cm_info_dynamic()} and
 * {@link folder_cm_info_view()} do not need to do DB queries
 *
 * @param cm_info $cm
 * @return cached_cm_info info
 */
function folder_get_coursemodule_info($cm) {
    global $DB;
    if (!($folder = $DB->get_record('folder', array('id' => $cm->instance),
            'id, name, display, showexpanded, showdownloadfolder, intro, introformat'))) {
        return NULL;
    }
    $cminfo = new cached_cm_info();
    $cminfo->name = $folder->name;
    if ($folder->display == FOLDER_DISPLAY_INLINE) {
        // prepare folder object to store in customdata
        $fdata = new stdClass();
        $fdata->showexpanded = $folder->showexpanded;
        $fdata->showdownloadfolder = $folder->showdownloadfolder;
        if ($cm->showdescription && strlen(trim($folder->intro))) {
            $fdata->intro = $folder->intro;
            if ($folder->introformat != FORMAT_MOODLE) {
                $fdata->introformat = $folder->introformat;
            }
        }
        $cminfo->customdata = $fdata;
    } else {
        if ($cm->showdescription) {
            // Convert intro to html. Do not filter cached version, filters run at display time.
            $cminfo->content = format_module_intro('folder', $folder, $cm->id, false);
        }
    }
    return $cminfo;
}

/**
 * Sets dynamic information about a course module
 *
 * This function is called from cm_info when displaying the module
 * mod_folder can be displayed inline on course page and therefore have no course link
 *
 * @param cm_info $cm
 */
function folder_cm_info_dynamic(cm_info $cm) {
    if ($cm->customdata) {
        // the field 'customdata' is not empty IF AND ONLY IF we display contens inline
        $cm->set_no_view_link();
    }
}

/**
 * Overwrites the content in the course-module object with the folder files list
 * if folder.display == FOLDER_DISPLAY_INLINE
 *
 * @param cm_info $cm
 */
function folder_cm_info_view(cm_info $cm) {
    global $PAGE;
    if ($cm->uservisible && $cm->customdata &&
            has_capability('mod/folder:view', $cm->context)) {
        // Restore folder object from customdata.
        // Note the field 'customdata' is not empty IF AND ONLY IF we display contens inline.
        // Otherwise the content is default.
        $folder = $cm->customdata;
        $folder->id = (int)$cm->instance;
        $folder->course = (int)$cm->course;
        $folder->display = FOLDER_DISPLAY_INLINE;
        $folder->name = $cm->name;
        if (empty($folder->intro)) {
            $folder->intro = '';
        }
        if (empty($folder->introformat)) {
            $folder->introformat = FORMAT_MOODLE;
        }
        // display folder
        $renderer = $PAGE->get_renderer('mod_folder');
        $cm->set_content($renderer->display_folder($folder));
    }
}

/**
 * Mark the activity completed (if required) and trigger the course_module_viewed event.
 *
 * @param  stdClass $folder     folder object
 * @param  stdClass $course     course object
 * @param  stdClass $cm         course module object
 * @param  stdClass $context    context object
 * @since Moodle 3.0
 */
function folder_view($folder, $course, $cm, $context) {

    // Trigger course_module_viewed event.
    $params = array(
        'context' => $context,
        'objectid' => $folder->id
    );

    $event = \mod_folder\event\course_module_viewed::create($params);
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('folder', $folder);
    $event->trigger();

    // Completion.
    $completion = new completion_info($course);
    $completion->set_module_viewed($cm);
}

/**
 * Check if the folder can be zipped and downloaded.
 * @param stdClass $folder
 * @param context_module $cm
 * @return bool True if the folder can be zipped and downloaded.
 * @throws \dml_exception
 */
function folder_archive_available($folder, $cm) {
    if (!$folder->showdownloadfolder) {
        return false;
    }

    $context = context_module::instance($cm->id);
    $fs = get_file_storage();
    $dir = $fs->get_area_tree($context->id, 'mod_folder', 'content', 0);

    $size = folder_get_directory_size($dir);
    $maxsize = get_config('folder', 'maxsizetodownload') * 1024 * 1024;

    if ($size == 0) {
        return false;
    }

    if (!empty($maxsize) && $size > $maxsize) {
        return false;
    }

    return true;
}

/**
 * Recursively measure the size of the files in a directory.
 * @param array $directory
 * @return int size of directory contents in bytes
 */
function folder_get_directory_size($directory) {
    $size = 0;

    foreach ($directory['files'] as $file) {
        $size += $file->get_filesize();
    }

    foreach ($directory['subdirs'] as $subdirectory) {
        $size += folder_get_directory_size($subdirectory);
    }

    return $size;
}

/**
 * Mark the activity completed (if required) and trigger the all_files_downloaded event.
 *
 * @param  stdClass $folder     folder object
 * @param  stdClass $course     course object
 * @param  stdClass $cm         course module object
 * @param  stdClass $context    context object
 * @since Moodle 3.1
 */
function folder_downloaded($folder, $course, $cm, $context) {
    $params = array(
        'context' => $context,
        'objectid' => $folder->id
    );
    $event = \mod_folder\event\all_files_downloaded::create($params);
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('folder', $folder);
    $event->trigger();

    // Completion.
    $completion = new completion_info($course);
    $completion->set_module_viewed($cm);
}
